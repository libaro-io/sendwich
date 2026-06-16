<?php

namespace App\Http\Requests\Order;

use App\Actions\DeliverySchedule;
use App\Models\DeliveryRun;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddRequest extends FormRequest
{
    public function authorize(): bool
    {
        $schedule = new DeliverySchedule();
        $deliveryDate = $schedule->deliveryDate();

        return !DeliveryRun::query()
            ->where('company_id', '=', auth()->user()->company->id)
            ->whereNotNull('departed_at')
            ->whereNull('delivered_at')
            ->whereBetween('date', [
                $deliveryDate->copy()->startOfDay(),
                $deliveryDate->copy()->endOfDay(),
            ])
            ->exists();
    }

    public function rules(): array
    {
        return [
            'product_id' => [
                'required',
                'integer',
                Rule::exists('products', 'id'),
            ],
            'options' => [
                'present',
                'array',
            ],
            'options.*' => [
                'integer',
                Rule::exists('product_options', 'id'),
            ],
            'comment' => [
                'nullable',
                'string',
                'max:500',
            ],
            'weight' => [
                'nullable',
                'numeric',
                'min:0',
            ],
        ];
    }
}