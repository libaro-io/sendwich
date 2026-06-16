<?php

namespace Tests\Feature;

use App\Actions\BackfillDeliveryRuns;
use App\Models\Company;
use App\Models\DeliveryRun;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BackfillDeliveryRunsTest extends TestCase
{
    use RefreshDatabase;

    private Company $companyA;
    private Company $companyB;
    private User $u1;
    private User $u2;
    private User $u3;
    private User $u4;

    /**
     * Seed a deliberately awkward history: a day with two different runners, an
     * unassigned order, a delivered custom item without a runner, a departed-not-
     * delivered run, and a second company — the cases a naive backfill would break.
     */
    private function seedHistory(): void
    {
        $this->companyA = Company::forceCreate(['name' => 'A Corp', 'link' => 'a-corp', 'token' => 'token-a']);
        $this->companyB = Company::forceCreate(['name' => 'B Corp', 'link' => 'b-corp', 'token' => 'token-b']);

        $this->u1 = $this->user($this->companyA, 'u1@a.com');
        $this->u2 = $this->user($this->companyA, 'u2@a.com');
        $this->u3 = $this->user($this->companyA, 'u3@a.com');
        $this->u4 = $this->user($this->companyB, 'u4@b.com');

        $dayA = '2026-06-10 12:15:00';
        $dayB = '2026-06-11 12:15:00';

        // Company A, day A, runner U1 (delivered).
        $this->order($this->companyA, $this->u3, $this->u1->id, 5.00, $dayA);
        $this->order($this->companyA, $this->u2, $this->u1->id, 3.00, $dayA);
        // Company A, day A, runner U2 — same day, a SECOND runner.
        $this->order($this->companyA, $this->u1, $this->u2->id, 4.00, $dayA);
        // Company A, day A, never assigned (open) + a custom item without runner.
        $this->order($this->companyA, $this->u3, null, 2.00, $dayA);
        $this->order($this->companyA, $this->u2, null, 10.00, $dayA);
        // Company A, day B, runner U1.
        $this->order($this->companyA, $this->u2, $this->u1->id, 6.00, $dayB);
        $this->order($this->companyA, $this->u3, $this->u1->id, 1.00, $dayB);
        // Company B, day A, runner U4 — proves runs stay company-scoped.
        $this->order($this->companyB, $this->u4, $this->u4->id, 7.00, $dayA);
    }

    private function user(Company $company, string $email): User
    {
        return User::forceCreate([
            'name'       => $email,
            'email'      => $email,
            'password'   => bcrypt('password'),
            'company_id' => $company->id,
        ]);
    }

    private function order(Company $company, User $orderer, ?int $paidBy, float $total, string $date): Order
    {
        return Order::forceCreate([
            'user_id'    => $orderer->id,
            'company_id' => $company->id,
            'product_id' => null,
            'quantity'   => 1,
            'paid_by'    => $paidBy,
            'total'      => $total,
            'date'       => $date,
        ]);
    }

    /** Balances the way production computes them today: straight from orders.paid_by. */
    private function balancesViaPaidBy(): array
    {
        $balances = [];
        foreach (User::all() as $user) {
            $payments = (float) Order::query()->where('paid_by', '=', $user->id)->sum('total');
            $debt = (float) Order::query()->where('user_id', '=', $user->id)->whereNotNull('paid_by')->sum('total');
            $balances[$user->id] = round($payments - $debt, 2);
        }

        return $balances;
    }

    /** The same balances computed through the new delivery_runs.runner_id. */
    private function balancesViaRuns(): array
    {
        $balances = [];
        foreach (User::all() as $user) {
            $payments = (float) Order::query()
                ->join('delivery_runs', 'orders.delivery_run_id', '=', 'delivery_runs.id')
                ->where('delivery_runs.runner_id', '=', $user->id)
                ->sum('orders.total');
            $debt = (float) Order::query()
                ->join('delivery_runs', 'orders.delivery_run_id', '=', 'delivery_runs.id')
                ->where('orders.user_id', '=', $user->id)
                ->whereNotNull('delivery_runs.runner_id')
                ->sum('orders.total');
            $balances[$user->id] = round($payments - $debt, 2);
        }

        return $balances;
    }

    public function test_backfill_links_every_order_and_preserves_balances(): void
    {
        $this->seedHistory();
        $balancesBefore = $this->balancesViaPaidBy();

        $stats = (new BackfillDeliveryRuns())->execute();

        // 5 runs: (A, dayA, U1), (A, dayA, U2), (A, dayA, unassigned), (A, dayB, U1), (B, dayA, U4).
        $this->assertSame(5, $stats['runs']);
        $this->assertSame(8, $stats['orders_linked']);
        $this->assertSame(0, Order::query()->whereNull('delivery_run_id')->count(), 'Every order must belong to a run.');

        // Each order's run carries the exact original runner.
        Order::query()->with('deliveryRun')->get()->each(function (Order $order) {
            $expected = $order->paid_by === null ? null : (int) $order->paid_by;
            $actual = $order->deliveryRun->runner_id === null ? null : (int) $order->deliveryRun->runner_id;
            $this->assertSame($expected, $actual, "Order {$order->id} runner mismatch.");
        });

        // The financial history is identical computed either way.
        $this->assertEquals($balancesBefore, $this->balancesViaRuns());
    }

    public function test_multi_runner_day_keeps_each_runner_separate(): void
    {
        $this->seedHistory();

        (new BackfillDeliveryRuns())->execute();

        $dayARunsForA = DeliveryRun::query()
            ->where('company_id', '=', $this->companyA->id)
            ->whereDate('date', '=', '2026-06-10')
            ->whereNotNull('runner_id')
            ->pluck('runner_id')
            ->sort()
            ->values()
            ->all();

        $this->assertSame([$this->u1->id, $this->u2->id], $dayARunsForA, 'Both runners of that day keep their own run.');
    }

    public function test_backfill_is_idempotent(): void
    {
        $this->seedHistory();

        (new BackfillDeliveryRuns())->execute();
        $runCountAfterFirst = DeliveryRun::query()->count();

        $secondRun = (new BackfillDeliveryRuns())->execute();

        // Reconciling again is a no-op: same totals, no extra or duplicate runs.
        $this->assertSame($runCountAfterFirst, $secondRun['runs'], 'A second run creates no extra runs.');
        $this->assertSame(8, $secondRun['orders_linked']);
        $this->assertSame($runCountAfterFirst, DeliveryRun::query()->count());
    }

    public function test_dry_run_verifies_but_changes_nothing(): void
    {
        $this->seedHistory();

        $stats = (new BackfillDeliveryRuns())->execute(dryRun: true);

        $this->assertTrue($stats['dry_run']);
        $this->assertSame(5, $stats['runs'], 'A dry run still reports what it would create.');
        $this->assertSame(0, DeliveryRun::query()->count(), 'Dry run must leave no runs behind.');
        $this->assertSame(8, Order::query()->whereNull('delivery_run_id')->count(), 'Dry run must not link any order.');
    }

    public function test_payout_settlement_orders_are_excluded_from_runs(): void
    {
        $company = Company::forceCreate(['name' => 'C Corp', 'link' => 'c-corp', 'token' => 'token-c']);
        $runner = $this->user($company, 'runner@c.com');
        $orderer = $this->user($company, 'orderer@c.com');
        $day = '2026-06-10 12:15:00';

        // A real delivery (product_id null via helper) and a payout settlement order.
        $this->order($company, $orderer, $runner->id, 5.00, $day);
        Order::forceCreate([
            'user_id'    => $orderer->id,
            'company_id' => $company->id,
            'product_id' => Order::PAYOUT_PRODUCT_ID,
            'quantity'   => 1,
            'paid_by'    => $runner->id,
            'total'      => 9.00,
            'date'       => $day,
        ]);

        (new BackfillDeliveryRuns())->execute();

        $this->assertSame(1, DeliveryRun::query()->count(), 'Only the delivery gets a run.');
        $this->assertSame(1, Order::query()->whereNotNull('delivery_run_id')->count(), 'The payout order stays unlinked.');
        $this->assertSame(
            1,
            Order::query()->where('product_id', '=', Order::PAYOUT_PRODUCT_ID)->whereNull('delivery_run_id')->count(),
            'The payout settlement order must not belong to a delivery run.',
        );
    }
}