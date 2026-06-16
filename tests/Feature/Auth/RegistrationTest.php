<?php

namespace Tests\Feature\Auth;

use App\Models\Company;
use App\Models\InvitedUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_users_can_register()
    {
        $company = Company::forceCreate(['name' => 'Corp', 'link' => 'corp', 'token' => 'token']);
        $invite = InvitedUser::forceCreate([
            'name'       => 'Test User',
            'email'      => 'test@example.com',
            'invited_by' => 1,
            'company_id' => $company->id,
        ]);

        $response = $this->post('/register', [
            'id'                   => $invite->id,
            'name'                 => $invite->name,
            'email'                => $invite->email,
            'company_link'         => $company->link,
            'password'             => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect();
    }
}
