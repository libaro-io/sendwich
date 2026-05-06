<?php

namespace App\Casts;

use App\DataObjects\NotificationChannelConfig;
use App\Enums\NotificationDriver;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class NotificationChannelConfiguration implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): NotificationChannelConfig
    {
        $driver = NotificationDriver::tryFrom($attributes['driver'] ?? '');

        if (!$driver) {
            throw new \RuntimeException("Unknown notification driver: {$attributes['driver']}");
        }

        $configClass = $driver->configClass();
        $json = Crypt::decryptString($value);

        return new $configClass(json_decode($json, true) ?? []);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        $json = $value instanceof NotificationChannelConfig
            ? json_encode($value->toArray())
            : json_encode($value);

        return Crypt::encryptString($json);
    }
}
