<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles & permissions
        $editStore = Permission::firstOrCreate(['name' => 'edit-store']);
        $editCompany = Permission::firstOrCreate(['name' => 'edit-company']);
        $adminRole = Role::firstOrCreate(['name' => 'Administrator']);
        $adminRole->syncPermissions([$editStore, $editCompany]);
    }

    public function test_administrator_can_update_store(): void
    {
        $company = Company::forceCreate([
            'name' => 'Test Corp',
            'link' => 'test-corp',
            'token' => 'some-random-token',
        ]);
        $store = Store::forceCreate([
            'name' => 'Old Name',
            'company_id' => $company->id,
        ]);
        $user = User::forceCreate([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'company_id' => $company->id,
        ]);
        $user->assignRole('Administrator');

        $response = $this->actingAs($user)
            ->postJson(route('store.update', ['id' => $store->id]), [
                'store' => [
                    'name' => 'New Name',
                    'address' => 'New Address',
                    'zip' => '1234',
                    'city' => 'New City',
                    'phone' => '123456789',
                    'email' => 'store@example.com',
                    'website' => 'https://store.example.com',
                ]
            ]);

        $response->assertOk();
        $response->assertJsonFragment(['message' => 'Store updated']);

        $store->refresh();
        $this->assertSame('New Name', $store->name);
        $this->assertSame('New Address', $store->address);
    }

    public function test_unauthorized_user_cannot_update_store(): void
    {
        $company = Company::forceCreate([
            'name' => 'Test Corp',
            'link' => 'test-corp',
            'token' => 'some-random-token',
        ]);
        $store = Store::forceCreate([
            'name' => 'Old Name',
            'company_id' => $company->id,
        ]);
        $user = User::forceCreate([
            'name' => 'Regular User',
            'email' => 'user@test.com',
            'password' => bcrypt('password'),
            'company_id' => $company->id,
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('store.update', ['id' => $store->id]), [
                'store' => [
                    'name' => 'New Name',
                ]
            ]);

        $response->assertForbidden();
    }

    public function test_administrator_can_delete_store_and_its_products(): void
    {
        $company = Company::forceCreate([
            'name' => 'Test Corp',
            'link' => 'test-corp',
            'token' => 'some-random-token',
        ]);
        $store = Store::forceCreate([
            'name' => 'Store to delete',
            'company_id' => $company->id,
        ]);
        $product = Product::forceCreate([
            'name' => 'Product to delete',
            'price' => 10,
            'store_id' => $store->id,
        ]);
        $productOption = $product->options()->create([
            'name' => 'Product option to delete',
            'price' => 2,
            'is_enabled_by_default' => false,
        ]);
        $user = User::forceCreate([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'company_id' => $company->id,
        ]);
        $user->assignRole('Administrator');

        $response = $this->actingAs($user)
            ->deleteJson(route('store.delete', ['id' => $store->id]));

        $response->assertOk();
        $response->assertJsonFragment(['message' => 'Store deleted']);
        $this->assertModelMissing($store);
        $this->assertModelMissing($product);
        $this->assertModelMissing($productOption);
    }

    public function test_administrator_cannot_delete_store_from_another_company(): void
    {
        $company = Company::forceCreate([
            'name' => 'Test Corp',
            'link' => 'test-corp',
            'token' => 'some-random-token',
        ]);
        $otherCompany = Company::forceCreate([
            'name' => 'Other Corp',
            'link' => 'other-corp',
            'token' => 'another-random-token',
        ]);
        $store = Store::forceCreate([
            'name' => 'Other Store',
            'company_id' => $otherCompany->id,
        ]);
        $user = User::forceCreate([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'company_id' => $company->id,
        ]);
        $user->assignRole('Administrator');

        $response = $this->actingAs($user)
            ->deleteJson(route('store.delete', ['id' => $store->id]));

        $response->assertNotFound();
        $this->assertModelExists($store);
    }
}
