<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * A single delivery run for one company on one delivery day: the unit that has a
 * runner, departs and gets delivered. Order line-items belong to a run.
 *
 * @property int $id
 * @property int $company_id
 * @property int|null $runner_id
 * @property Carbon $date
 * @property Carbon|null $departed_at
 * @property Carbon|null $delivered_at
 */
class DeliveryRun extends Model
{
    protected $fillable = [
        'company_id',
        'runner_id',
        'date',
        'departed_at',
        'delivered_at',
    ];

    protected $casts = [
        'date'         => 'datetime',
        'departed_at'  => 'datetime',
        'delivered_at' => 'datetime',
        'runner_id'    => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function runner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'runner_id')->withTrashed();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public static function syncDay(int $companyId, Carbon $date): void
    {
        $from = $date->copy()->startOfDay();
        $to = $date->copy()->endOfDay();

        DB::transaction(function () use ($companyId, $from, $to) {
            // Only process orders that are not yet in a delivered run.
            // Orders already in a delivered run are historical records and must not be re-linked.
            $orders = Order::query()
                ->where('company_id', '=', $companyId)
                ->whereBetween('date', [$from, $to])
                ->where(fn ($query) => $query->whereNull('product_id')->orWhere('product_id', '!=', Order::PAYOUT_PRODUCT_ID))
                ->where(fn ($query) => $query
                    ->whereNull('delivery_run_id')
                    ->orWhereHas('deliveryRun', fn (Builder $run) => $run->whereNull('delivered_at'))
                )
                ->get();

            $keptRunIds = [];

            foreach ($orders->groupBy(fn (Order $order) => $order->paid_by ?? 'unassigned') as $group) {
                $runnerId = $group->first()->paid_by;

                $run = self::query()
                    ->where('company_id', '=', $companyId)
                    ->whereBetween('date', [$from, $to])
                    ->whereNull('delivered_at')
                    ->when(
                        $runnerId === null,
                        fn ($query) => $query->whereNull('runner_id'),
                        fn ($query) => $query->where('runner_id', '=', $runnerId),
                    )
                    ->first()
                    ?? self::query()->create([
                        'company_id' => $companyId,
                        'runner_id'  => $runnerId,
                        'date'       => $group->pluck('date')->map(fn (mixed $date) => Carbon::parse($date))->sort()->first(),
                    ]);


                Order::query()->whereIn('id', $group->pluck('id'))->update(['delivery_run_id' => $run->id]);
                $keptRunIds[] = $run->id;
            }

            // Drop undelivered runs for this day that no longer have any (non-payout) orders.
            // Delivered runs are historical records and must never be removed.
            self::query()
                ->where('company_id', '=', $companyId)
                ->whereBetween('date', [$from, $to])
                ->whereNull('delivered_at')
                ->when(count($keptRunIds) > 0, fn ($query) => $query->whereNotIn('id', $keptRunIds))
                ->delete();
        });
    }
}
