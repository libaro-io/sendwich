<?php

namespace App\Providers;

use App\Models\Company;
use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Policies\CompanyPolicy;
use App\Policies\OrderPolicy;
use App\Policies\ProductPolicy;
use App\Policies\StorePolicy;
use App\Policies\UserPolicy;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Order::class   => OrderPolicy::class,
        Company::class => CompanyPolicy::class,
        Product::class => ProductPolicy::class,
        Store::class   => StorePolicy::class,
        User::class    => UserPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}