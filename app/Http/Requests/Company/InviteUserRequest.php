<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class InviteUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermissionTo('edit-company');
    }

    public function rules(): array
    {
        return [
            'name'  => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users'],
        ];
    }
}