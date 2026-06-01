<?php

namespace App\Http\Controllers\Order;

use App\Actions\DeliverySchedule;
use App\Actions\ResolveCompany;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetSelectedRunnerController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $company = (new ResolveCompany())->execute($request);
        $order = Order::getOrders($company, (new DeliverySchedule())->deliveryDate())->first();

        return response()->json(['user' => $order?->deliverer]);
    }
}