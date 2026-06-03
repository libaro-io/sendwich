<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\UpdateOrderPriceRequest;
use Illuminate\Http\JsonResponse;

class UpdateOrderPriceController extends Controller
{
    public function __invoke(UpdateOrderPriceRequest $request): JsonResponse
    {
        $order = $request->getOrder();

        $order->total = $request->input('total');
        $order->save();

        return response()->json(['success' => true]);
    }
}
