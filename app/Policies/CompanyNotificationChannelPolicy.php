<?php

namespace App\Policies;

use App\Models\CompanyNotificationChannel;
use App\Models\User;

class CompanyNotificationChannelPolicy
{
    public function test(User $user, CompanyNotificationChannel $channel): bool
    {
        return $user->company_id === $channel->company_id;
    }
}
