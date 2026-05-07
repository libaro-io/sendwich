<?php

namespace App\Notifications;

use App\Http\Controllers\DashboardController;
use App\Models\Company;
use App\Notifications\Channels\GoogleChatChannel;
use App\Notifications\Messages\GoogleChatMessage;
use Illuminate\Notifications\Notification;

class PlaceOrderReminder extends Notification
{
    public function __construct(
        private Company $company,
    ) {
    }

    public function via(object $notifiable): array
    {
        return [GoogleChatChannel::class];
    }

    public function toGoogleChat(object $notifiable): GoogleChatMessage
    {
        $dashboardUrl = action([DashboardController::class, 'dashboard']);

        return GoogleChatMessage::create()
            ->header('Sendwich', 'Order Reminder')
            ->icon('RESTAURANT_ICON')
            ->text("Hey <b>{$this->company->name}</b> — it's time to place your sendwich orders!")
            ->button('Place your order →', $dashboardUrl, [
                'red' => 0.24,
                'green' => 0.65,
                'blue' => 0.45,
                'alpha' => 1.0,
            ]);
    }
}
