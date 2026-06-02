<?php

namespace App\Policies;

use App\Models\Store;
use App\Models\User;

class StorePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Administrator');
    }

    public function view(User $user, Store $store): bool
    {
        return $user->hasRole('Administrator');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Administrator');
    }

    public function update(User $user, Store $store): bool
    {
        return $user->hasRole('Administrator');
    }

    public function delete(User $user, Store $store): bool
    {
        return $user->hasRole('Administrator');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('Administrator');
    }
}