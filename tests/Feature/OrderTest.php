<?php

namespace Tests\Feature;

use App\Actions\DeliverySchedule;
use App\Models\Company;
use App\Models\DeliveryRun;
use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    private function makeCompany(): Company
    {
        return Company::forceCreate([
            'name'  => 'Test Corp',
            'link'  => 'test-corp',
            'token' => 'some-random-token',
        ]);
    }

    private function makeUser(Company $company, string $email): User
    {
        return User::forceCreate([
            'name'       => $email,
            'email'      => $email,
            'password'   => bcrypt('password'),
            'company_id' => $company->id,
        ]);
    }

    private function makeProduct(Company $company): Product
    {
        $store = Store::forceCreate([
            'name'       => 'Test Store',
            'company_id' => $company->id,
        ]);

        return Product::forceCreate([
            'name'           => 'Sandwich',
            'price'          => 5.0,
            'store_id'       => $store->id,
            'variable_price' => false,
        ]);
    }

    public function test_item_added_while_runner_assigned_joins_that_runner(): void
    {
        $company = $this->makeCompany();
        $runner = $this->makeUser($company, 'runner@test.com');
        $orderer = $this->makeUser($company, 'orderer@test.com');
        $product = $this->makeProduct($company);
        $deliveryDate = new DeliverySchedule()->deliveryDate();

        // An existing order for today is already appointed to the runner, who has not departed.
        Order::forceCreate([
            'user_id'    => $orderer->id,
            'company_id' => $company->id,
            'product_id' => $product->id,
            'quantity'   => 1,
            'paid_by'    => $runner->id,
            'total'      => 5.0,
            'date'       => $deliveryDate,
        ]);
        DeliveryRun::syncDay($company->id, $deliveryDate);

        $response = $this->actingAs($orderer)
            ->postJson(route('order.add-product'), [
                'product_id' => $product->id,
                'options'    => [],
            ]);

        $response->assertRedirect();

        $newOrder = Order::query()
            ->where('user_id', '=', $orderer->id)
            ->where('paid_by', '=', $runner->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($newOrder, 'A new item must join the already-appointed runner.');
        $this->assertSame($runner->id, $newOrder->paid_by);
    }

    public function test_item_added_without_a_runner_stays_unassigned(): void
    {
        $company = $this->makeCompany();
        $orderer = $this->makeUser($company, 'orderer@test.com');
        $product = $this->makeProduct($company);

        $response = $this->actingAs($orderer)
            ->postJson(route('order.add-product'), [
                'product_id' => $product->id,
                'options'    => [],
            ]);

        $response->assertRedirect();

        $newOrder = Order::query()->where('user_id', '=', $orderer->id)->latest('id')->first();

        $this->assertNotNull($newOrder);
        $this->assertNull($newOrder->paid_by, 'Without an appointed runner the item stays open.');
    }

    public function test_item_cannot_be_added_after_the_runner_departed(): void
    {
        $company = $this->makeCompany();
        $runner = $this->makeUser($company, 'runner@test.com');
        $orderer = $this->makeUser($company, 'orderer@test.com');
        $product = $this->makeProduct($company);
        $deliveryDate = new DeliverySchedule()->deliveryDate();

        // The runner already left: create the order, link it to a run, mark the run departed.
        Order::forceCreate([
            'user_id'    => $orderer->id,
            'company_id' => $company->id,
            'product_id' => $product->id,
            'quantity'   => 1,
            'paid_by'    => $runner->id,
            'total'      => 5.0,
            'date'       => $deliveryDate,
        ]);
        DeliveryRun::syncDay($company->id, $deliveryDate);
        DeliveryRun::query()
            ->where('company_id', '=', $company->id)
            ->where('runner_id', '=', $runner->id)
            ->update(['departed_at' => now()]);

        $response = $this->actingAs($orderer)
            ->postJson(route('order.add-product'), [
                'product_id' => $product->id,
                'options'    => [],
            ]);

        $response->assertForbidden();
    }
}