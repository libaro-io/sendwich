<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateStore extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'store.name' => 'required|string',
            'store.address' => 'nullable|string',
            'store.zip' => 'nullable|string',
            'store.city' => 'nullable|string',
            'store.phone' => 'nullable|string',
            'store.email' => 'nullable|string',
            'store.website' => 'nullable|string',
        ];
    }
}
