<?php

namespace Tests\Feature;

use App\Models\Company;
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
}
