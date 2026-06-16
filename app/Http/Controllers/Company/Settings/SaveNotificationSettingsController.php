<?php

namespace App\Http\Controllers\Company\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\Settings\SaveNotificationSettingsRequest;
use App\Models\Company;
use App\Models\CompanyNotificationChannel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class SaveNotificationSettingsController extends Controller
{
    public function __invoke(SaveNotificationSettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $company = Auth::user()->company;
        $company->reminder_enabled = $validated['reminder_enabled'];
        $company->reminder_time = $validated['reminder_time'];
        $company->save();

        $days = $validated['reminder_days'] ?? [];
        $company->reminderDays()->delete();
        foreach ($days as $day) {
            $company->reminderDays()->create(['day' => $day]);
        }

        $this->syncNotificationChannels($company, $validated['notification_channels'] ?? []);

        return redirect()->action(ShowSettingsController::class);
    }

    private function syncNotificationChannels(Company $company, array $channels): void
    {
        $existingIds = $company->notificationChannels()->pluck('id')->toArray();
        $incomingIds = array_filter(array_column($channels, 'id'));

        $toDelete = array_diff($existingIds, $incomingIds);
        CompanyNotificationChannel::destroy($toDelete);

        foreach ($channels as $channelData) {
            $company->notificationChannels()->updateOrCreate(
                ['id' => $channelData['id'] ?? null],
                [
                    'driver' => $channelData['driver'],
                    'configuration' => $channelData['configuration'],
                    'enabled' => $channelData['enabled'],
                ],
            );
        }
    }
}
