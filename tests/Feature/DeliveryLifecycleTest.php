<?php

namespace Tests\Feature;

use App\Actions\ChooseRunner;
use App\Models\Company;
use App\Models\DeliveryRun;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class DeliveryLifecycleTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    private User $runner;
    private User $orderer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::forceCreate(['name' => 'Corp', 'link' => 'corp', 'token' => 'token']);
        $this->runner = $this->user('runner@corp.com');
        $this->orderer = $this->user('orderer@corp.com');
    }

    private function user(string $email): User
    {
        return User::forceCreate([
            'name' => $email, 'email' => $email, 'password' => bcrypt('x'), 'company_id' => $this->company->id,
        ]);
    }

    private function order(?int $paidBy, float $total, Carbon $date): Order
    {
        return Order::forceCreate([
            'user_id'    => $this->orderer->id,
            'company_id' => $this->company->id,
            'product_id' => null,
            'quantity'   => 1,
            'paid_by'    => $paidBy,
            'total'      => $total,
            'date'       => $date,
        ]);
    }

    public function test_choosing_a_runner_creates_a_run_and_links_the_days_orders(): void
    {
        Mail::fake();
        $today = Carbon::now()->setTime(12, 15);
        $this->order(null, 5.0, $today);
        $this->order(null, 3.0, $today);

        new ChooseRunner($this->company, $this->runner)->execute();

        $run = DeliveryRun::query()->where('company_id', '=', $this->company->id)->first();
        $this->assertNotNull($run);
        $this->assertSame($this->runner->id, $run->runner_id);
        $this->assertSame(2, $run->orders()->count());
        $this->assertSame(0, Order::query()->where('company_id', '=', $this->company->id)->whereNull('paid_by')->count());
    }

    public function test_departure_and_delivery_are_recorded_on_the_run(): void
    {
        $day = Carbon::now()->setTime(12, 15);
        $this->order($this->runner->id, 5.0, $day);
        DeliveryRun::syncDay($this->company->id, $day);

        $run = DeliveryRun::query()->where('company_id', '=', $this->company->id)->first();
        $this->assertNull($run->departed_at);
        $this->assertNull($run->delivered_at);

        $run->update(['departed_at' => now()]);
        $this->assertNotNull($run->fresh()->departed_at);
        $this->assertNull($run->fresh()->delivered_at);

        $run->update(['delivered_at' => now()]);
        $this->assertNotNull($run->fresh()->delivered_at);
    }

    public function test_removing_the_last_order_drops_the_run(): void
    {
        $day = Carbon::now()->setTime(12, 15);
        $order = $this->order($this->runner->id, 5.0, $day);
        DeliveryRun::syncDay($this->company->id, $day);
        $this->assertSame(1, DeliveryRun::query()->where('company_id', '=', $this->company->id)->count());

        $order->delete();
        DeliveryRun::syncDay($this->company->id, $day);
        $this->assertSame(0, DeliveryRun::query()->where('company_id', '=', $this->company->id)->count());
    }
}