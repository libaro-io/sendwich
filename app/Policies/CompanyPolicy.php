<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Administrator');
    }

    public function view(User $user, Company $company): bool
    {
        return $user->hasRole('Administrator');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Administrator');
    }

    public function update(User $user, Company $company): bool
    {
        return $user->hasRole('Administrator');
    }

    public function delete(User $user, Company $company): bool
    {
        return $user->hasRole('Administrator');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('Administrator');
    }
}