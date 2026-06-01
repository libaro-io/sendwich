<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Administrator');
    }

    public function view(User $user, Order $order): bool
    {
        return $user->hasRole('Administrator');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Administrator');
    }

    public function update(User $user, Order $order): bool
    {
        return $user->hasRole('Administrator');
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->hasRole('Administrator');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('Administrator');
    }
}