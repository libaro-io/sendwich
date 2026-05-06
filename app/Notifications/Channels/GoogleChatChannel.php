<?php

namespace App\Notifications\Channels;

use App\DataObjects\GoogleChatNotificationChannelConfig;
use App\Notifications\Messages\GoogleChatMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleChatChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        /** @var GoogleChatNotificationChannelConfig|null $config */
        $config = $notifiable->routes[self::class] ?? null;

        if (!$config || !$config->webhookUrl()) {
            Log::warning('Google Chat channel missing config or webhook URL');
            return;
        }

        /** @var GoogleChatMessage $message */
        $message = $notification->toGoogleChat($notifiable);

        $response = Http::post($config->webhookUrl(), $message->toArray());

        if ($response->failed()) {
            Log::error('Google Chat webhook failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }
    }
}
