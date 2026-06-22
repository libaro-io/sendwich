<?php

namespace App\Console\Commands;

use App\Actions\DeliverySchedule;
use App\Actions\NotifyCompany;
use App\Models\Company;
use App\Notifications\PlaceOrderReminder;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class SendReminderNotifications extends Command
{
    protected $signature = 'app:send-reminder-notifications
        {--test-for-company= : Send a test notification immediately for a specific company ID}';

    protected $description = 'Send order reminder notifications to companies at their configured time';

    public function handle(): int
    {
        $deliveryDate = new DeliverySchedule()->deliveryDate();

        $companies = Company::query()
            ->whereHas('notificationChannels', fn ($q) => $q->where('enabled', true))
            ->when($this->option('test-for-company'), fn ($q, $id) => $q->where('id', $id))
            ->when(!$this->option('test-for-company'), fn ($q) => $q
                ->where('reminder_enabled', true)
                ->where('reminder_time', now()->format('H:i'))
                ->whereHas('reminderDays', fn (Builder $query) => $query->where('day', '=', now()->dayOfWeek))
                // Once someone has placed the first order for the day, the reminder is redundant.
                ->whereDoesntHave('orders', fn (Builder $query) => $query->whereBetween('date', [
                    $deliveryDate->copy()->startOfDay(),
                    $deliveryDate->copy()->endOfDay(),
                ]))
                // Max one notification per day: skip if the day's notification already went out
                // (either this reminder or the first-order notification).
                ->where(fn (Builder $query) => $query
                    ->whereNull('daily_notification_sent_date')
                    ->orWhereDate('daily_notification_sent_date', '!=', $deliveryDate->toDateString()))
            )
            ->get();

        if ($companies->isEmpty()) {
            $this->info('No companies to notify.');
            return self::SUCCESS;
        }

        $isTest = (bool) $this->option('test-for-company');

        foreach ($companies as $company) {
            $this->info("Sending reminder to {$company->name}...");
            $channelsNotified = (new NotifyCompany($company))->execute(new PlaceOrderReminder($company));

            // Record the send so the first-order notification stands down for the rest of the day.
            if (!$isTest && $channelsNotified > 0) {
                $company->daily_notification_sent_date = $deliveryDate->toDateString();
                $company->save();
            }
        }

        $this->info("Done — notified {$companies->count()} company(s).");

        return self::SUCCESS;
    }
}
