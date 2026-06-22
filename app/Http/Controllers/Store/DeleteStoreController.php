<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DeleteStoreController extends Controller
{
    public function __invoke(int $id): JsonResponse
    {
        $company = auth()->user()->company;
        $store = $company->stores()->where('id', '=', $id)->firstOrFail();

        DB::transaction(function () use ($store): void {
            $store->products()->each(function ($product): void {
                // Preserve order history before the product disappears. Past orders
                // only hold product_id (no FK), so snapshot the name onto orders that
                // have none, then drop the now-dangling product reference everywhere.
                Order::query()
                    ->where('product_id', '=', $product->id)
                    ->where(function ($query): void {
                        $query->whereNull('label')->orWhere('label', '=', '');
                    })
                    ->update(['label' => $product->name]);

                Order::query()
                    ->where('product_id', '=', $product->id)
                    ->update(['product_id' => null]);

                $product->options()->delete();
                $product->delete();
            });

            $store->delete();
        });

        return response()->json([
            'message' => 'Store deleted',
        ]);
    }
}
