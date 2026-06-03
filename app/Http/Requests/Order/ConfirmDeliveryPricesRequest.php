<?php

namespace App\Http\Requests\Order;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConfirmDeliveryPricesRequest extends FormRequest
{
    public function authorize(): bool
    {
        $orderIds = collect($this->input('prices', []))
            ->pluck('order_id')
            ->filter()
            ->unique()
            ->values();

        if ($orderIds->isEmpty()) {
            return false;
        }

        $owned = once(fn () => Order::query()
            ->whereIn('id', $orderIds->all())
            ->where('company_id', '=', auth()->user()->company->id)
            ->where('paid_by', '=', auth()->id())
            ->count());

        return $owned === $orderIds->count();
    }

    public function rules(): array
    {
        $companyId = auth()->user()->company->id;

        return [
            'prices'            => ['required', 'array'],
            'prices.*.order_id' => ['required', 'integer', Rule::exists('orders', 'id')],
            'prices.*.total'    => ['required', 'numeric', 'min:0'],
            'store_id'          => ['nullable', 'integer', Rule::exists('stores', 'id')->where('company_id', $companyId)],
            'extra_items'           => ['present', 'array'],
            'extra_items.*.label'   => ['required', 'string', 'max:255'],
            'extra_items.*.total'   => ['required', 'numeric', 'min:0'],
            'extra_items.*.user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('company_id', $companyId),
            ],
        ];
    }

    /**
     * @return array<int, array{order_id: int, total: float}>
     */
    public function getPrices(): array
    {
        return $this->validated('prices');
    }

    /**
     * @return array<int, array{label: string, total: float, user_id: int}>
     */
    public function getExtraItems(): array
    {
        return $this->validated('extra_items') ?? [];
    }
}