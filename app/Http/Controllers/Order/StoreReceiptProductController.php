<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreReceiptProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class StoreReceiptProductController extends Controller
{
    public function __invoke(StoreReceiptProductRequest $request): JsonResponse
    {
        $product = Product::query()->create([
            'name'           => $request->input('name'),
            'description'    => null,
            'price'          => $request->input('price'),
            'variable_price' => $request->boolean('variable_price'),
            'company_id'     => auth()->user()->company->id,
            'store_id'       => $request->input('store_id'),
        ]);

        return response()->json([
            'success'    => true,
            'product_id' => $product->id,
        ]);
    }
}
