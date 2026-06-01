<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProduct;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class UpdateProductController extends Controller
{
    public function __invoke(UpdateProduct $request, Product $product): JsonResponse
    {
        $data = $request->input('product');

        $product->update([
            'name'           => $data['name'],
            'description'    => $data['description'],
            'price'          => $data['price'],
            'variable_price' => $data['variable_price'] ?? false,
        ]);

        return response()->json(['message' => 'Product updated']);
    }
}