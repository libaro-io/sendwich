<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class LocalSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment() !== 'local') {
            return;
        }

        $this->seedRolesAndPermissions();
        $company = $this->seedCompany();
        $this->seedAdmin($company);
        $this->seedUser($company);

        $this->command->info('LocalSeeder: TestCorp seeded.');
    }

    private function seedRolesAndPermissions(): void
    {
        $editStore = Permission::firstOrCreate(['name' => 'edit-store']);
        $editCompany = Permission::firstOrCreate(['name' => 'edit-company']);

        $ownerRole = Role::firstOrCreate(['name' => 'company-owner']);
        $ownerRole->syncPermissions([$editStore, $editCompany]);

        Role::firstOrCreate(['name' => 'Administrator']);
    }

    private function seedCompany(): Company
    {
        return Company::firstOrCreate(
            ['name' => 'TestCorp'],
            [
                'link' => 'testcorp',
                'token' => Str::random(32),
            ],
        );
    }

    private function seedAdmin(Company $company): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'a@a'],
            [
                'name' => 'Admin',
                'password' => Hash::make('pass'),
                'email_verified_at' => now(),
                'company_id' => $company->id,
            ],
        );

        $admin->syncRoles(['company-owner', 'Administrator']);
    }

    private function seedUser(Company $company): void
    {
        User::firstOrCreate(
            ['email' => 'u@u'],
            [
                'name' => 'User',
                'password' => Hash::make('pass'),
                'email_verified_at' => now(),
                'company_id' => $company->id,
            ],
        );
    }
}
