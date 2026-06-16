<?php

namespace App\Actions;

use App\Exceptions\DryRunRollback;
use App\Exceptions\SettlementBackfillException;
use App\Models\Order;
use App\Models\Settlement;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class BackfillSettlements
{
    /**
     * @return array{settlements:int, dry_run:bool}
     *
     * @throws SettlementBackfillException when a user's balance is not preserved
     * @throws \Throwable on any database failure inside the transaction
     */
    public function execute(bool $dryRun = false): array
    {
        $stats = ['settlements' => 0, 'dry_run' => $dryRun];

        try {
            DB::transaction(function () use ($dryRun, &$stats) {
                $stats['settlements'] = $this->migratePayoutOrders();

                $this->verify();

                if ($dryRun) {
                    throw new DryRunRollback();
                }
            });
        } catch (DryRunRollback) {
        }

        return $stats;
    }

    private function migratePayoutOrders(): int
    {
        $alreadyMigrated = Settlement::query()->whereNotNull('source_order_id')->pluck('source_order_id')->all();

        $created = 0;
        Order::query()
            ->where('product_id', '=', Order::PAYOUT_PRODUCT_ID)
            ->whereNotIn('id', $alreadyMigrated)
            ->get()
            ->each(function (Order $payout) use (&$created) {
                Settlement::query()->create([
                    'company_id'      => $payout->company_id,
                    'payer_id'        => $payout->paid_by,
                    'receiver_id'     => $payout->user_id,
                    'amount'          => $payout->total,
                    'date'            => $payout->date,
                    'source_order_id' => $payout->id,
                ]);
                $created++;
            });

        return $created;
    }

    /**
     * @throws SettlementBackfillException
     */
    private function verify(): void
    {
        foreach (User::query()->get() as $user) {
            $legacy = $this->legacyBalance($user->id);
            $split = $this->splitBalance($user->id);

            if (abs($legacy - $split) > 0.001) {
                throw new SettlementBackfillException(
                    "User {$user->id} balance drift: {$legacy} (orders incl. payouts) vs {$split} (orders + settlements)."
                );
            }
        }
    }

    private function legacyBalance(int $userId): float
    {
        $paid = (float) Order::query()->where('paid_by', '=', $userId)->sum('total');
        $owed = (float) Order::query()->where('user_id', '=', $userId)->whereNotNull('paid_by')->sum('total');

        return round($paid - $owed, 2);
    }

    private function splitBalance(int $userId): float
    {
        $notPayout = fn ($query) => $query->where(
            fn ($query) => $query->whereNull('product_id')->orWhere('product_id', '!=', Order::PAYOUT_PRODUCT_ID)
        );

        $paid = (float) Order::query()->where('paid_by', '=', $userId)->where($notPayout)->sum('total')
            + (float) Settlement::query()->where('payer_id', '=', $userId)->sum('amount');

        $owed = (float) Order::query()->where('user_id', '=', $userId)->whereNotNull('paid_by')->where($notPayout)->sum('total')
            + (float) Settlement::query()->where('receiver_id', '=', $userId)->sum('amount');

        return round($paid - $owed, 2);
    }
}
