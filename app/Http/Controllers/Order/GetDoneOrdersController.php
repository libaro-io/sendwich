<?php

namespace App\Http\Controllers\Order;

use App\Actions\DeliverySchedule;
use App\Actions\ResolveCompany;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetDoneOrdersController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $company = (new ResolveCompany())->execute($request);

        $formattedOrders = collect();

        $orders = Order::getOrders($company, (new DeliverySchedule())->deliveryDate(), false, true)
            ->get()
            ->groupBy('paid_by');

        foreach ($orders as $orderGroup) {
            $formattedOrders[$orderGroup->first()->deliverer->name] = $orderGroup;
        }

        return response()->json(['orders' => $formattedOrders]);
    }
}