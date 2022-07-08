<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property float $price
 * @property bool $is_enabled_by_default
 * @property int $product_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class ProductOption extends Model
{
    protected $fillable = [
        'name',
        'price',
        'is_enabled_by_default',
    ];

    protected $casts = [
        'is_enabled_by_default' => 'boolean',
    ];
}
