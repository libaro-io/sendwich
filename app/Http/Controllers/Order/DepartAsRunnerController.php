<?php

namespace App\Http\Controllers\Order;

use App\Actions\DeliverySchedule;
use App\Http\Controllers\Controller;
use App\Models\DeliveryRun;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DepartAsRunnerController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        $user = Auth::user();
        $deliveryDate = new DeliverySchedule()->deliveryDate();

        DeliveryRun::syncDay($user->company->id, $deliveryDate);

        DeliveryRun::query()
            ->where('company_id', '=', $user->company->id)
            ->where('runner_id', '=', $user->id)
            ->whereNull('departed_at')
            ->whereBetween('date', [
                $deliveryDate->copy()->startOfDay(),
                $deliveryDate->copy()->endOfDay(),
            ])
            ->update(['departed_at' => now()]);

        return redirect()->back()->with(['success' => 'On the way!']);
    }
}
