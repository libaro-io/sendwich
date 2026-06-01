<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Administrator');
    }

    public function view(User $user, User $target): bool
    {
        return $user->hasRole('Administrator');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Administrator');
    }

    public function update(User $user, User $target): bool
    {
        return $user->hasRole('Administrator');
    }

    public function delete(User $user, User $target): bool
    {
        return $user->hasRole('Administrator') && $user->id !== $target->id;
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('Administrator');
    }
}