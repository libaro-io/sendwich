<?php

namespace App\Http\Controllers\Order;

use App\Actions\AddCustomItemToCatalogue;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\AddCustomOrderRequest;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class AddCustomOrderController extends Controller
{
    public function __invoke(AddCustomOrderRequest $request): JsonResponse
    {
        $date = Carbon::createFromFormat('Ymd', $request->input('date'))->setTime(12, 0);
        $companyId = auth()->user()->company->id;

        // An existing product was picked from the store — link it instead of creating a custom item.
        $product = null;
        if ($request->input('product_id')) {
            $product = Product::query()
                ->where('id', '=', $request->input('product_id'))
                ->where('company_id', '=', $companyId)
                ->first();
        }

        $order = Order::query()->create([
            'user_id'      => $request->input('user_id'),
            'company_id'   => $companyId,
            'paid_by'      => $request->input('paid_by'),
            'product_id'   => $product?->id,
            'store_id'     => $product ? $product->store_id : $request->input('store_id'),
            'label'        => $product ? null : $request->input('label'),
            'quantity'     => 1,
            'total'        => $request->input('total'),
            'date'         => $date,
            'delivered_at' => $date,
        ]);

        // Optionally add the custom item to the chosen store's catalogue.
        if (!$order->product_id && $request->boolean('add_to_catalogue') && $order->store_id) {
            $cataloguePrice = (float) ($request->input('catalogue_price') ?? $order->total);
            new AddCustomItemToCatalogue()->execute($companyId, $order->store_id, $order->label, $cataloguePrice);
        }

        return response()->json(['success' => true]);
    }
}
