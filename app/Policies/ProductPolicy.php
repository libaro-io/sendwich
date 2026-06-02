<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Administrator');
    }

    public function view(User $user, Product $product): bool
    {
        return $user->hasRole('Administrator');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Administrator');
    }

    public function update(User $user, Product $product): bool
    {
        return $user->hasRole('Administrator');
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->hasRole('Administrator');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('Administrator');
    }
}
