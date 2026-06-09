<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreReceiptStoreRequest;
use App\Models\Store;
use Illuminate\Http\JsonResponse;

class StoreReceiptStoreController extends Controller
{
    public function __invoke(StoreReceiptStoreRequest $request): JsonResponse
    {
        $store = Store::query()->create([
            'name'       => $request->input('name'),
            'company_id' => auth()->user()->company_id,
        ]);

        return response()->json([
            'id'   => $store->id,
            'name' => $store->name,
        ]);
    }
}