<?php

namespace App\Console\Commands;

use App\Models\DeliveryRun;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DiagnoseOrders extends Command
{
    protected $signature = 'orders:diagnose';

    protected $description = 'Read-only report on order/runner data shape (run before the delivery-run backfill)';

    public function handle(): int
    {
        $total = Order::query()->count();
        $assigned = Order::query()->whereNotNull('paid_by')->count();
        $unassigned = Order::query()->whereNull('paid_by')->count();

        $this->info('Orders overview');
        $this->table(['Metric', 'Count'], [
            ['Total orders', $total],
            ['Assigned (paid_by set)', $assigned],
            ['Unassigned (paid_by null)', $unassigned],
        ]);

        $multiRunnerDays = Order::query()
            ->whereNotNull('paid_by')
            ->get(['company_id', 'date', 'paid_by'])
            ->groupBy(fn (Order $order) => $order->company_id . '|' . Carbon::parse($order->date)->toDateString())
            ->map(fn ($group) => [
                'company_id' => $group->first()->company_id,
                'day'        => Carbon::parse($group->first()->date)->toDateString(),
                'runners'    => $group->pluck('paid_by')->unique()->count(),
            ])
            ->filter(fn ($row) => $row['runners'] > 1)
            ->values();

        if ($multiRunnerDays->isEmpty()) {
            $this->info('✓ No company/day has more than one runner — "one run per day" holds historically.');
        } else {
            $this->warn("⚠ {$multiRunnerDays->count()} company/day group(s) have multiple runners:");
            $this->table(
                ['company_id', 'day', 'distinct runners'],
                $multiRunnerDays->map(fn ($row) => [$row['company_id'], $row['day'], $row['runners']])->all(),
            );
            $this->line('  → These remain lossless (one run per runner), but a UNIQUE(company_id, date) cannot be added.');
        }

        $deliveredWithoutRunner = DeliveryRun::query()->whereNull('runner_id')->whereNotNull('delivered_at')->count();
        $departedWithoutRunner = DeliveryRun::query()->whereNull('runner_id')->whereNotNull('departed_at')->count();

        $this->newLine();
        $this->info('Consistency of lifecycle fields');
        $this->table(['Check', 'Count', 'Note'], [
            ['runs: delivered_at set but no runner', $deliveredWithoutRunner, 'expected for custom items'],
            ['runs: departed_at set but no runner', $departedWithoutRunner, $departedWithoutRunner > 0 ? 'UNEXPECTED — inspect' : 'ok'],
        ]);

        return self::SUCCESS;
    }
}