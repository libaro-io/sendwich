<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\DeleteOrderRequest;
use App\Models\Order;
use Illuminate\Http\JsonResponse;

class DeleteOrderController extends Controller
{
    public function __invoke(DeleteOrderRequest $request, Order $order): JsonResponse
    {
        $order->delete();

        return response()->json(['success' => true]);
    }
}
