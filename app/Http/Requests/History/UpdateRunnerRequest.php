<?php

namespace App\Http\Requests\History;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRunnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        $orderIds = $this->input('order_ids', []);

        if (empty($orderIds)) {
            return false;
        }

        return !Order::query()
            ->whereIn('id', $orderIds)
            ->where('company_id', '!=', auth()->user()->company->id)
            ->exists();
    }

    public function rules(): array
    {
        return [
            'order_ids'   => ['required', 'array'],
            'order_ids.*' => ['integer', Rule::exists('orders', 'id')],
            'runner_id'   => ['nullable', 'integer', Rule::exists('users', 'id')],
        ];
    }
}