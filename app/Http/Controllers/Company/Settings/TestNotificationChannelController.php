<?php

namespace App\Http\Controllers\Company\Settings;

use Throwable;
use App\Http\Controllers\Controller;
use App\Models\CompanyNotificationChannel;
use App\Notifications\TestNotificationChannel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class TestNotificationChannelController extends Controller
{
    public function __invoke(CompanyNotificationChannel $channel): JsonResponse
    {
        try {
            Notification::route($channel->driver->channelClass(), $channel->configuration)
                ->notify(new TestNotificationChannel());

            return response()->json(['message' => 'Test notification sent!']);
        } catch (Throwable $e) {
            Log::error('Test notification failed', ['channel' => $channel->id, 'error' => $e->getMessage()]);

            return response()->json(['message' => 'Failed to send test notification.'], 500);
        }
    }
}
