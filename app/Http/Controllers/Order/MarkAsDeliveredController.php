<?php

namespace App\Http\Controllers\Order;

use App\Actions\DeliverySchedule;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class MarkAsDeliveredController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        $user = Auth::user();
        $deliveryDate = new DeliverySchedule()->deliveryDate();

        $updatedCount = Order::query()
            ->where('paid_by', '=', $user->id)
            ->whereNull('delivered_at')
            ->whereBetween('date', [
                $deliveryDate->copy()->startOfDay(),
                $deliveryDate->copy()->endOfDay(),
            ])
            ->update(['delivered_at' => now()]);

        abort_if($updatedCount === 0, 404);

        return redirect()->back()->with(['success' => 'Orders marked as delivered']);
    }
}
