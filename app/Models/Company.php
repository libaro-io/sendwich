<?php

namespace App\Models;

use App\Http\Controllers\OrderController;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Company extends Model
{
    use HasFactory;

    protected $casts = [
        'reminder_enabled' => 'boolean',
        'reminder_days' => 'array',
        'auto_assign_runner' => 'boolean',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notificationChannels()
    {
        return $this->hasMany(CompanyNotificationChannel::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stores()
    {
        return $this->hasMany(Store::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getProducts(Carbon $tresHoldDate = null)
    {
        $products = $this->products()
            ->with('options')
            ->with('orders')
            ->with('store')
            ->get()
            ->map(function ($product) {
                if ($product->orders->count()) {
                    $product->selected = $product->orders->first()->comment;
                    $product->orderCount = $product->orders->count();
                }
                return $product;
            });

        $productsSorted = $products->sortByDesc('orderCount');
        return new Collection($productsSorted->values()->all());
    }
}
