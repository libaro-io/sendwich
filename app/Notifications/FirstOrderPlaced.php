<?php

namespace App\Notifications;

use App\Http\Controllers\DashboardController;
use App\Models\Company;
use App\Models\User;
use App\Notifications\Channels\GoogleChatChannel;
use App\Notifications\Messages\GoogleChatMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class FirstOrderPlaced extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        private Company $company,
        private User $orderer,
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
            ->header('Sendwich', 'First order in!')
            ->icon('RESTAURANT_ICON')
            ->text("<b>{$this->orderer->name}</b> has placed the first order — who's next?")
            ->button('Place your order →', $dashboardUrl, [
                'red' => 0.24,
                'green' => 0.65,
                'blue' => 0.45,
                'alpha' => 1.0,
            ]);
    }
}