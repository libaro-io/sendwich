<?php

namespace App\Actions;

use App\Exceptions\DeliveryRunBackfillException;
use App\Exceptions\DryRunRollback;
use App\Models\DeliveryRun;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

final class BackfillDeliveryRuns
{
    /**
     * @return array{runs:int, orders_linked:int, dry_run:bool}
     *
     * @throws DeliveryRunBackfillException|\Throwable when the result fails verification
     */
    public function execute(bool $dryRun = false): array
    {
        $stats = ['runs' => 0, 'orders_linked' => 0, 'dry_run' => $dryRun];

        try {
            DB::transaction(function () use ($dryRun, &$stats) {
                $this->reconcileEveryDay();

                $stats['runs'] = DeliveryRun::query()->count();
                $stats['orders_linked'] = Order::query()->whereNotNull('delivery_run_id')->count();

                $this->verify();

                if ($dryRun) {
                    // Rehearsal: undo every change but keep the verified stats.
                    throw new DryRunRollback();
                }
            });
        } catch (DryRunRollback) {
            // Expected for --dry-run: the transaction has rolled back, stats stand.
        }

        return $stats;
    }

    /**
     * Reconcile each distinct (company, delivery day) that has at least one delivery order.
     */
    private function reconcileEveryDay(): void
    {
        Order::query()
            ->where(fn ($query) => $query->whereNull('product_id')->orWhere('product_id', '!=', Order::PAYOUT_PRODUCT_ID))
            ->get(['company_id', 'date'])
            ->groupBy(fn (Order $order) => $order->company_id . '|' . Carbon::parse($order->date)->toDateString())
            ->each(function ($ordersOnDay) {
                $first = $ordersOnDay->first();
                DeliveryRun::syncDay($first->company_id, Carbon::parse($first->date));
            });
    }

    /**
     * @throws DeliveryRunBackfillException
     */
    private function verify(): void
    {
        // Every delivery (non-payout) order must belong to a run.
        $orphans = Order::query()
            ->whereNull('delivery_run_id')
            ->where(fn ($query) => $query->whereNull('product_id')->orWhere('product_id', '!=', Order::PAYOUT_PRODUCT_ID))
            ->count();
        if ($orphans > 0) {
            throw new DeliveryRunBackfillException("Backfill incomplete: {$orphans} delivery order(s) without a run.");
        }

        $mismatch = Order::query()
            ->whereNotNull('delivery_run_id')
            ->with('deliveryRun:id,runner_id')
            ->get(['id', 'paid_by', 'delivery_run_id'])
            ->first(function (Order $order) {
                $orderRunner = $order->paid_by === null ? null : (int) $order->paid_by;
                $runRunner = $order->deliveryRun?->runner_id === null ? null : (int) $order->deliveryRun->runner_id;

                return $orderRunner !== $runRunner;
            });

        if ($mismatch !== null) {
            throw new DeliveryRunBackfillException(
                "Order {$mismatch->id} paid_by ({$mismatch->paid_by}) does not match its run runner; aborting."
            );
        }
    }
}
