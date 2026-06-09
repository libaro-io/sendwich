<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateStore;
use Illuminate\Http\JsonResponse;

class UpdateStoreController extends Controller
{
    public function __invoke(UpdateStore $request, int $id): JsonResponse
    {
        $company = auth()->user()->company;
        $store = $company->stores()->where('id', '=', $id)->firstOrFail();

        $data = $request->input('store');

        $store->update([
            'name' => $data['name'],
            'address' => $data['address'] ?? null,
            'zip' => $data['zip'] ?? null,
            'city' => $data['city'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'website' => $data['website'] ?? null,
        ]);

        return response()->json([
            'message' => 'Store updated',
            'store' => $store,
        ]);
    }
}
