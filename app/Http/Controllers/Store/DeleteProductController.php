<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class DeleteProductController extends Controller
{
    public function __invoke(Product $product): JsonResponse
    {
        $store = $product->store;
        $product->delete();

        return response()->json([
            'message'  => 'Product deleted',
            'products' => $store->products,
        ]);
    }
}