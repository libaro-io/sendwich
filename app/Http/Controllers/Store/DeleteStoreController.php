<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
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
