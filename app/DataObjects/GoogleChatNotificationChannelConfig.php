<?php

namespace App\DataObjects;

class GoogleChatNotificationChannelConfig extends NotificationChannelConfig
{
    public function webhookUrl(): string
    {
        return $this->data['webhook_url'] ?? '';
    }
}
