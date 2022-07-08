<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property float $price
 * @property int $company_id
 * @property int $store_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property ProductOption[]|Collection $options
 */
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'company_id',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(ProductOption::class);
    }
}
