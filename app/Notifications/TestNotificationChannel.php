<?php

namespace App\Notifications;

use App\Notifications\Channels\GoogleChatChannel;
use App\Notifications\Messages\GoogleChatMessage;
use Illuminate\Notifications\Notification;

class TestNotificationChannel extends Notification
{
    public function via(object $notifiable): array
    {
        return [GoogleChatChannel::class];
    }

    public function toGoogleChat(object $notifiable): GoogleChatMessage
    {
        return GoogleChatMessage::create()
            ->header('Sendwich', 'Test Notification')
            ->icon('CONFIRMATION_NUMBER_ICON')
            ->text('If you see this, your channel is working correctly!');
    }
}
