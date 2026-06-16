<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\DeliveryRun;
use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetOrdersReadTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_orders_reads_lifecycle_from_the_run_and_store_from_relations(): void
    {
        $company = Company::forceCreate(['name' => 'Corp', 'link' => 'corp', 'token' => 'token']);
        $runner = User::forceCreate(['name' => 'R', 'email' => 'r@c', 'password' => bcrypt('x'), 'company_id' => $company->id]);
        $orderer = User::forceCreate(['name' => 'O', 'email' => 'o@c', 'password' => bcrypt('x'), 'company_id' => $company->id]);
        $store = Store::forceCreate(['name' => 'Bakery', 'company_id' => $company->id]);
        $product = Product::forceCreate(['name' => 'Sandwich', 'price' => 5.0, 'store_id' => $store->id, 'variable_price' => false]);

        $day = Carbon::now()->setTime(12, 15);
        $departed = $day->copy()->setTime(13, 0);
        $delivered = $day->copy()->setTime(13, 30);

        $run = DeliveryRun::query()->create([
            'company_id'   => $company->id,
            'runner_id'    => $runner->id,
            'date'         => $day,
            'departed_at'  => $departed,
            'delivered_at' => $delivered,
        ]);

        // Order columns are deliberately left empty: getOrders must take the lifecycle from the run.
        $order = Order::forceCreate([
            'user_id'         => $orderer->id,
            'company_id'      => $company->id,
            'product_id'      => $product->id,
            'quantity'        => 1,
            'paid_by'         => null,
            'total'           => 5.0,
            'date'            => $day,
            'delivery_run_id' => $run->id,
        ]);

        $result = Order::getOrders($company, $day, false, null)->where('orders.id', '=', $order->id)->first();

        $this->assertSame($runner->id, $result->paid_by);
        $this->assertSame($departed->toDateTimeString(), $result->departed_at->toDateTimeString());
        $this->assertSame($delivered->toDateTimeString(), $result->delivered_at->toDateTimeString());
        $this->assertSame('Bakery', $result->store_name);
    }

    public function test_done_orders_filter_uses_the_runs_runner(): void
    {
        $company = Company::forceCreate(['name' => 'Corp', 'link' => 'corp', 'token' => 'token']);
        $runner = User::forceCreate(['name' => 'R', 'email' => 'r@c', 'password' => bcrypt('x'), 'company_id' => $company->id]);
        $orderer = User::forceCreate(['name' => 'O', 'email' => 'o@c', 'password' => bcrypt('x'), 'company_id' => $company->id]);
        $day = Carbon::now()->setTime(12, 15);

        $assignedRun = DeliveryRun::query()->create(['company_id' => $company->id, 'runner_id' => $runner->id, 'date' => $day]);
        $openRun = DeliveryRun::query()->create(['company_id' => $company->id, 'runner_id' => null, 'date' => $day]);

        $assigned = Order::forceCreate(['user_id' => $orderer->id, 'company_id' => $company->id, 'product_id' => null, 'quantity' => 1, 'total' => 5.0, 'date' => $day, 'delivery_run_id' => $assignedRun->id]);
        $open = Order::forceCreate(['user_id' => $orderer->id, 'company_id' => $company->id, 'product_id' => null, 'quantity' => 1, 'total' => 3.0, 'date' => $day, 'delivery_run_id' => $openRun->id]);

        $done = Order::getOrders($company, $day, false, true)->get()->pluck('id');
        $todo = Order::getOrders($company, $day, false, false)->get()->pluck('id');

        $this->assertTrue($done->contains($assigned->id));
        $this->assertFalse($done->contains($open->id));
        $this->assertTrue($todo->contains($open->id));
        $this->assertFalse($todo->contains($assigned->id));
    }
}