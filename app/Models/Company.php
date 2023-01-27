<?php

namespace App\Models;

use App\Http\Controllers\OrderController;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;


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

    public function getProducts(Carbon $tresHoldDate = null)
    {
        if ($tresHoldDate === null) {
            $tresHoldDate = (new OrderController())->getTresholdDate();
        }
        $products = $this->products()
            ->with('options')
            ->with('orders', function ($query) use ($tresHoldDate) {
                if (now() < $tresHoldDate) {
                    $query->where('date', '>=', Carbon::now()->startOf('day'));
                    $query->where('date', '<=', Carbon::now()->endOf('day'));
                } else {
                    $query->where('date', '>=', Carbon::now()->addDay()->startOf('day'));
                    $query->where('date', '<=', Carbon::now()->addDay()->endOf('day'));
                }
                $query->where('user_id', '=', auth()->user()->id);
            })
            ->with('store')
            ->get()
            ->map(function ($product) {
                if ($product->orders->count() > 0) {
                    $product->selected = $product->orders->first()->comment;
                }
                return $product;
            });

        return $products;
    }
}
