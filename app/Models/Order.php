<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
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

    /**
     * Pseudo-product id used by PayoutController for settlement orders. These are not
     * deliveries, so they are excluded from delivery runs (and become Settlements in a
     * later phase).
     */
    public const PAYOUT_PRODUCT_ID = 65;

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
    ];

    // departed_at and delivered_at are virtual attributes selected from delivery_runs via getOrders().
    protected $casts = [
        'departed_at'  => 'datetime',
        'delivered_at' => 'datetime',
    ];

    protected $appends = ['store_name'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function deliveryRun(): BelongsTo
    {
        return $this->belongsTo(DeliveryRun::class);
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

    public function getStoreNameAttribute(): ?string
    {
        $product = $this->relationLoaded('product') ? $this->product : null;
        if ($product && $product->relationLoaded('store') && $product->store) {
            return $product->store->name;
        }

        $store = $this->relationLoaded('store') ? $this->store : null;

        return $store?->name;
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
                'orders.store_id',
                'orders.label',
                'orders.quantity',
                'orders.weight',
                'orders.total',
                'orders.comment',
                'orders.date',
                'orders.delivery_run_id',
                'delivery_runs.runner_id as paid_by',
                'delivery_runs.departed_at as departed_at',
                'delivery_runs.delivered_at as delivered_at',
            )
            ->leftJoin('delivery_runs', 'orders.delivery_run_id', '=', 'delivery_runs.id')
            ->where('orders.company_id', '=', $company->id)
            ->whereBetween('orders.date', [$from, $to])
            ->with([
                'user:id,name',
                'product:id,name,price,variable_price,store_id',
                'product.store:id,name',
                'store:id,name',
            ]);

        if ($doneOrders === true) {
            $query->whereNotNull('delivery_runs.runner_id');
        } elseif ($doneOrders === false) {
            $query->whereNull('delivery_runs.runner_id');
        }

        return $query;
    }

    public static function assignedRunnerId(Company $company, Carbon $deliveryDate): ?int
    {
        return DeliveryRun::query()
            ->where('company_id', '=', $company->id)
            ->whereNotNull('runner_id')
            ->whereNull('departed_at')
            ->whereNull('delivered_at')
            ->whereBetween('date', [
                $deliveryDate->copy()->startOfDay(),
                $deliveryDate->copy()->endOfDay(),
            ])
            ->value('runner_id');
    }
}
