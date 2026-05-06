<?php

namespace App\Console\Commands;

use App\Actions\NotifyCompany;
use App\Models\Company;
use App\Notifications\PlaceOrderReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendReminderNotifications extends Command
{
    protected $signature = 'app:send-reminder-notifications
        {--test-for-company= : Send a test notification immediately for a specific company ID}';

    protected $description = 'Send order reminder notifications to companies at their configured time';

    public function handle(): int
    {
        $companies = Company::query()
            ->whereHas('notificationChannels', fn ($q) => $q->where('enabled', true))
            ->when($this->option('test-for-company'), fn ($q, $id) => $q->where('id', $id))
            ->when(!$this->option('test-for-company'), fn ($q) => $q
                ->where('reminder_enabled', true)
                ->where('reminder_time', now()->format('H:i'))
                ->whereJsonContains('reminder_days', now()->dayOfWeek)
            )
            ->get();

        if ($companies->isEmpty()) {
            $this->info('No companies to notify.');
            return self::SUCCESS;
        }

        foreach ($companies as $company) {
            $this->info("Sending reminder to {$company->name}...");
            (new NotifyCompany($company))->execute(new PlaceOrderReminder($company));
        }

        $this->info("Done — notified {$companies->count()} company(s).");

        return self::SUCCESS;
    }
}
