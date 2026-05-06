<?php

namespace App\Actions;

use App\Models\Company;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;

final class NotifyCompany
{
    public function __construct(
        private Company $company,
    ) {
    }

    public function execute(Notification $notification): void
    {
        foreach ($this->getEnabledChannels() as $channel) {
            NotificationFacade::route($channel->driver->channelClass(), $channel->configuration)
                ->notify($notification);
        }
    }

    private function getEnabledChannels()
    {
        return $this->company->notificationChannels()->where('enabled', true)->get();
    }
}
