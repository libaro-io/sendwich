<?php

namespace App\Http\Requests\Company\Settings;

use App\Enums\NotificationDriver;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveNotificationSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reminder_enabled' => ['required', 'boolean'],
            'reminder_time' => ['nullable', 'date_format:H:i'],
            'reminder_days' => ['nullable', 'array'],
            'reminder_days.*' => ['integer', 'between:0,6'],
            'notification_channels' => ['nullable', 'array'],
            'notification_channels.*.id' => ['nullable', 'integer'],
            'notification_channels.*.driver' => ['required', Rule::in(array_column(NotificationDriver::cases(), 'value'))],
            'notification_channels.*.configuration' => ['required', 'array'],
            'notification_channels.*.configuration.webhook_url' => ['required', 'url'],
            'notification_channels.*.enabled' => ['required', 'boolean'],
        ];
    }
}
