<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $company_id
 * @property int $product_id
 * @property int $quantity
 * @property int|null $paid_by
 * @property float $total
 * @property string $comment
 * @property string $date
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property User $user
 * @property User|null $deliverer
 * @property Product $product
 */
class Order extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deliverer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public static function getOrders(Company $company, $date, $addTomorrow = false)
    {
        $from = (clone $date)->startOfDay();
        $to = (clone $date)->endOfDay();
        if($addTomorrow){
            $to->addDay();
        }

        return self::query()
            ->where('company_id', $company->id)
            ->where('date', '>=', $from)
            ->where('date', '<=', $to)
            ->whereNull('paid_by')
            ->with(['user' => function ($query) {
                $query->select('id', 'name');
            }, 'product' => function ($query) {
                $query->select('id', 'name', 'price');
            }]);
    }
}
