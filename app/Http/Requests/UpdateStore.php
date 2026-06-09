<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStore extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'store.name' => ['required', 'string'],
            'store.address' => ['nullable', 'string'],
            'store.zip' => ['nullable', 'string'],
            'store.city' => ['nullable', 'string'],
            'store.phone' => ['nullable', 'string'],
            'store.email' => ['nullable', 'string', 'email'],
            'store.website' => ['nullable', 'string'],
        ];
    }
}
