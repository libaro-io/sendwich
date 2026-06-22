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

    /**
     * Dispatch the notification to each of the company's enabled channels.
     *
     * @return int Number of enabled channels the notification was dispatched to.
     *             With queued notifications this reflects "we had somewhere to
     *             send to", not confirmed delivery.
     */
    public function execute(Notification $notification): int
    {
        $channels = $this->getEnabledChannels();

        foreach ($channels as $channel) {
            NotificationFacade::route($channel->driver->channelClass(), $channel->configuration)
                ->notify($notification);
        }

        return $channels->count();
    }

    private function getEnabledChannels()
    {
        return $this->company->notificationChannels()->where('enabled', true)->get();
    }
}
