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

    protected $fillable = [
        'product_id',
        'total',
        'user_id',
        'paid_by',
        'company_id',
        'quantity',
        'total',
        'date'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function deliverer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by')->withTrashed();
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @param Company $company
     * @param $date
     * @param $addTomorrow
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getOrders(Company $company, $date, $addTomorrow = false, $doneOrders = false)
    {
        $from = (clone $date)->startOfDay();
        $to = (clone $date)->endOfDay();
        if ($addTomorrow) {
            $to->addDay();
        }

        $query = self::query()
            ->where('company_id', $company->id)
            ->whereBetween('date', [$from, $to])
            ->with(['user' => function ($query) {
                $query->select('id', 'name');
            }, 'product' => function ($query) {
                $query->select('id', 'name', 'price');
            }]);

        if ($doneOrders) {
            $query->whereNotNull('paid_by');
        } else {
            $query->whereNull('paid_by');
        }

        return $query;
    }
}
