<?php

namespace Tests\Feature;

use App\Actions\BackfillSettlements;
use App\Actions\UsersWithDept;
use App\Models\Company;
use App\Models\Order;
use App\Models\Settlement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BackfillSettlementsTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    private User $u1;
    private User $u2;
    private User $u3;

    private function seedScenario(): void
    {
        $this->company = Company::forceCreate(['name' => 'Corp', 'link' => 'corp', 'token' => 'token']);
        $this->u1 = $this->user('u1@c');
        $this->u2 = $this->user('u2@c');
        $this->u3 = $this->user('u3@c');

        $day = Carbon::now()->setTime(12, 15);

        // Deliveries: U1 is the runner who paid for U2 and U3.
        $this->order($this->u2->id, $this->u1->id, 10.0, $day);
        $this->order($this->u3->id, $this->u1->id, 5.0, $day);

        // A payout settlement: U2 pays U1 back 10 (legacy representation as an order).
        Order::forceCreate([
            'user_id'    => $this->u1->id,
            'company_id' => $this->company->id,
            'product_id' => Order::PAYOUT_PRODUCT_ID,
            'quantity'   => 1,
            'paid_by'    => $this->u2->id,
            'total'      => 10.0,
            'date'       => $day,
        ]);
    }

    private function user(string $email): User
    {
        return User::forceCreate(['name' => $email, 'email' => $email, 'password' => bcrypt('x'), 'company_id' => $this->company->id]);
    }

    private function order(int $ordererId, int $runnerId, float $total, Carbon $date): Order
    {
        return Order::forceCreate([
            'user_id'    => $ordererId,
            'company_id' => $this->company->id,
            'product_id' => null,
            'quantity'   => 1,
            'paid_by'    => $runnerId,
            'total'      => $total,
            'date'       => $date,
        ]);
    }

    private function legacyBalances(): array
    {
        $balances = [];
        foreach (User::all() as $user) {
            $paid = (float) Order::query()->where('paid_by', '=', $user->id)->sum('total');
            $owed = (float) Order::query()->where('user_id', '=', $user->id)->whereNotNull('paid_by')->sum('total');
            $balances[$user->id] = round($paid - $owed, 2);
        }

        return $balances;
    }

    private function deptBalances(): array
    {
        $action = new UsersWithDept();
        $action->setCompany($this->company);

        return $action->execute()->mapWithKeys(fn (User $user) => [$user->id => $user->dept])->all();
    }

    public function test_backfill_preserves_every_balance(): void
    {
        $this->seedScenario();
        $legacy = $this->legacyBalances();

        $stats = (new BackfillSettlements())->execute();

        $this->assertSame(1, $stats['settlements']);
        $this->assertSame(1, Settlement::query()->count());

        $afterBackfill = $this->deptBalances();
        foreach ($legacy as $userId => $balance) {
            $this->assertEqualsWithDelta($balance, $afterBackfill[$userId] ?? 0.0, 0.001, "Balance drifted for user {$userId}.");
        }

        // Sanity: the known expected balances.
        $this->assertEqualsWithDelta(5.0, $afterBackfill[$this->u1->id], 0.001);
        $this->assertEqualsWithDelta(0.0, $afterBackfill[$this->u2->id], 0.001);
        $this->assertEqualsWithDelta(-5.0, $afterBackfill[$this->u3->id], 0.001);
    }

    public function test_backfill_is_idempotent(): void
    {
        $this->seedScenario();

        (new BackfillSettlements())->execute();
        $second = (new BackfillSettlements())->execute();

        $this->assertSame(0, $second['settlements'], 'A second run migrates nothing new.');
        $this->assertSame(1, Settlement::query()->count());
    }

    public function test_dry_run_changes_nothing(): void
    {
        $this->seedScenario();

        $stats = (new BackfillSettlements())->execute(dryRun: true);

        $this->assertSame(1, $stats['settlements'], 'Dry run still reports what it would migrate.');
        $this->assertSame(0, Settlement::query()->count(), 'Dry run must leave no settlements behind.');
    }
}