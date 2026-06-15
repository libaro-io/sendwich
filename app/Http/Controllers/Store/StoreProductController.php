<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProduct;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\JsonResponse;

class StoreProductController extends Controller
{
    public function __invoke(UpdateProduct $request): JsonResponse
    {
        $company = auth()->user()->company;
        $data = $request->input('product');
        $store = Store::find($request->input('store_id'));

        Product::create([
            'name'           => $data['name'],
            'description'    => $data['description'],
            'price'          => $data['price'],
            'variable_price' => $data['variable_price'] ?? false,
            'store_id'       => $store->id,
        ]);

        return response()->json([
            'message'  => 'Product saved',
            'products' => $store->products,
        ]);
    }
}