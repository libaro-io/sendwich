<?php

namespace App\Http\Controllers\Order;

use App\Actions\DeliverySchedule;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GetDoneOrdersController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        if (Auth::check()) {
            $company = Auth::user()->company;
        } else {
            $company = Company::query()->where('token', '=', $request->input('company_token'))->firstOrFail();
        }

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