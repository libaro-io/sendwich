<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property int $user_id
 * @property int $company_id
 * @property int|null $product_id
 * @property int|null $store_id
 * @property string|null $label
 * @property int $quantity
 * @property int|null $paid_by
 * @property float $total
 * @property string $comment
 * @property Carbon|null $delivered_at
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
        'store_id',
        'label',
        'total',
        'user_id',
        'paid_by',
        'company_id',
        'quantity',
        'weight',
        'date',
        'delivered_at',
        'departed_at',
    ];

    protected $casts = [
        'delivered_at' => 'datetime',
        'departed_at'  => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function deliverer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by')->withTrashed();
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * @param Company $company
     * @param $date
     * @param $addTomorrow
     * @return Builder
     */
    public static function getOrders(Company $company, $date, $addTomorrow = false, $doneOrders = false)
    {
        $from = (clone $date)->startOfDay();
        $to = (clone $date)->endOfDay();
        if ($addTomorrow) {
            $to->addDay();
        }

        $query = self::query()
            ->select(
                'orders.id',
                'orders.user_id',
                'orders.company_id',
                'orders.product_id',
                'orders.label',
                'quantity',
                'weight',
                'paid_by',
                'total',
                'comment',
                'delivered_at',
                'departed_at',
                'date',
                DB::raw('COALESCE(product_stores.name, order_stores.name) as store_name'),
            )
            ->leftJoin('products', 'orders.product_id', '=', 'products.id')
            ->leftJoin('stores as product_stores', 'products.store_id', '=', 'product_stores.id')
            ->leftJoin('stores as order_stores', 'orders.store_id', '=', 'order_stores.id')
            ->where('orders.company_id', $company->id)
            ->whereBetween('date', [$from, $to])
            ->with(['user' => function ($query) {
                $query->select('id', 'name');
            }, 'product' => function ($query) {
                $query->select('id', 'name', 'price', 'variable_price', 'store_id');
            }, 'deliverer' => function ($query) {
                $query->select('id', 'name');
            }]);

        if ($doneOrders === true) {
            $query->whereNotNull('paid_by');
        } elseif ($doneOrders === false) {
            $query->whereNull('paid_by');
        }

        return $query;
    }
}
