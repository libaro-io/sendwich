<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deliverer()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public static function getOrders(Company $company, $date)
    {
        return self::query()->where('company_id', $company->id)
            ->where('date', '>=', (clone $date)->startOfDay())
            ->where('date', '<=', (clone $date)->endOfDay())
            ->with(['user' => function ($query) {
                $query->select('id', 'name');
            }, 'product' => function ($query) {
                $query->select('id', 'name', 'price');
            }]);
    }
}
