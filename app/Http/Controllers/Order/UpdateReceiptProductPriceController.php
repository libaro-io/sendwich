<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\UpdateReceiptProductPriceRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class UpdateReceiptProductPriceController extends Controller
{
    public function __invoke(UpdateReceiptProductPriceRequest $request): JsonResponse
    {
        Product::query()
            ->where('id', '=', $request->input('product_id'))
            ->where('company_id', '=', auth()->user()->company->id)
            ->update(['price' => $request->input('price')]);

        return response()->json(['success' => true]);
    }
}