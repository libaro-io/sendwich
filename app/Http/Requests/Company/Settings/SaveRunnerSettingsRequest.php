<?php

namespace App\Http\Requests\Company\Settings;

use Illuminate\Foundation\Http\FormRequest;

class SaveRunnerSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'time' => ['required', 'date_format:H:i'],
        ];
    }
}
