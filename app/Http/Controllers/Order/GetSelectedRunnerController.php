<?php

namespace App\Http\Controllers\Order;

use App\Actions\DeliverySchedule;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GetSelectedRunnerController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        if (Auth::check()) {
            $company = Auth::user()->company;
        } else {
            $company = Company::query()->where('token', '=', $request->input('company_token'))->firstOrFail();
        }

        $order = Order::getOrders($company, (new DeliverySchedule())->deliveryDate())->first();

        return response()->json(['user' => $order?->deliverer]);
    }
}