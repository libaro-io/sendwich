<?php

namespace App\Http\Requests\History;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        $data = $this->input('data', []);

        $orderIds = collect($data)
            ->flatten(1)
            ->pluck('id')
            ->filter()
            ->values()
            ->toArray();

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
            'data'                       => ['required', 'array'],
            'data.*'                     => ['array'],
            'data.*.*'                   => ['array'],
            'data.*.*.id'                => ['required', 'integer'],
            'data.*.*.product'           => ['required', 'array'],
            'data.*.*.product.id'        => ['required', 'integer'],
            'data.*.*.quantity'          => ['required', 'integer'],
        ];
    }
}