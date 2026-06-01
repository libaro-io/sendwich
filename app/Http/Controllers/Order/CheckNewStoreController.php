<?php

namespace App\Http\Controllers\Order;

use App\Actions\DeliverySchedule;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\AddRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CheckNewStoreController extends Controller
{
    public function __invoke(AddRequest $request): JsonResponse
    {
        $user = auth()->user();
        $data = $request->validated();

        /** @var Product|null $product */
        $product = Product::query()->find($data['product_id']);

        if ($product === null) {
            abort(400);
        }

        $deliveryDate = (new DeliverySchedule())->deliveryDate();
        $stores = Order::getOrders($user->company, $deliveryDate)
            ->whereNull('delivered_at')
            ->get()
            ->map(fn (Order $order) => $order->product->store_id);

        if ($stores->count() > 0 && !$stores->contains($product->store_id)) {
            return response()->json(false, Response::HTTP_CONFLICT);
        }

        return response()->json(true, Response::HTTP_OK);
    }
}