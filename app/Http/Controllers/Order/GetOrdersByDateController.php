<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class GetOrdersByDateController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $company = auth()->user()->company;

        $orders = $company->orders()
            ->with(['deliverer', 'user', 'product.store', 'store'])
            ->orderBy('date', 'DESC')
            ->where('date', '>', Carbon::now()->subMonth())
            ->get()
            ->groupBy(fn ($order) => Carbon::parse($order->date)->format('Ymd'))
            ->map(fn ($value, $key) => ['date' => $key, 'data' => $value->values()])
            ->values();

        return response()->json($orders);
    }
}