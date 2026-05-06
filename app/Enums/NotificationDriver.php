<?php

namespace App\Enums;

use App\DataObjects\GoogleChatNotificationChannelConfig;
use App\Notifications\Channels\GoogleChatChannel;

enum NotificationDriver: string
{
    case GoogleChat = 'google_chat';

    public function channelClass(): string
    {
        return match ($this) {
            self::GoogleChat => GoogleChatChannel::class,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::GoogleChat => 'Google Chat',
        };
    }

    public function configClass(): string
    {
        return match ($this) {
            self::GoogleChat => GoogleChatNotificationChannelConfig::class,
        };
    }

    public function requiredConfigKeys(): array
    {
        return match ($this) {
            self::GoogleChat => ['webhook_url'],
        };
    }

    public static function toArray(): array
    {
        return array_map(fn (self $driver) => [
            'value' => $driver->value,
            'label' => $driver->label(),
        ], self::cases());
    }
}
