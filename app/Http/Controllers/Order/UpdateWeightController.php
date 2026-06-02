<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\UpdateWeightRequest;
use Illuminate\Http\JsonResponse;

class UpdateWeightController extends Controller
{
    public function __invoke(UpdateWeightRequest $request): JsonResponse
    {
        $order = $request->getOrder();

        $order->weight = $request->input('weight');
        $order->total  = $order->product->price * $request->input('weight');
        $order->save();

        return response()->json(['success' => true]);
    }
}