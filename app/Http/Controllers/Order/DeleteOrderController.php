<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\DeleteOrderRequest;
use App\Models\DeliveryRun;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class DeleteOrderController extends Controller
{
    public function __invoke(DeleteOrderRequest $request, Order $order): JsonResponse
    {
        $companyId = $order->company_id;
        $date = Carbon::parse($order->date);

        $order->delete();

        // Reconcile the day: the order leaves its run, which is dropped if now empty.
        DeliveryRun::syncDay($companyId, $date);

        return response()->json(['success' => true]);
    }
}
