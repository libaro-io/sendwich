<?php

namespace App\Http\Controllers\Order;

use App\Actions\DeliverySchedule;
use App\Actions\ResolveCompany;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetOrdersController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $company = (new ResolveCompany())->execute($request);
        $orders = Order::getOrders($company, (new DeliverySchedule())->deliveryDate())->get();

        return response()->json([
            'orders' => $orders,
            'user'   => $orders->count() ? $orders[0]->deliverer : null,
        ]);
    }
}