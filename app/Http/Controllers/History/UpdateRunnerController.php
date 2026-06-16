<?php

namespace App\Http\Controllers\History;

use App\Http\Controllers\Controller;
use App\Http\Requests\History\UpdateRunnerRequest;
use App\Models\DeliveryRun;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class UpdateRunnerController extends Controller
{
    public function __invoke(UpdateRunnerRequest $request): JsonResponse
    {
        $data = $request->validated();
        $companyId = auth()->user()->company->id;

        Order::query()
            ->whereIn('id', $data['order_ids'])
            ->where('company_id', '=', $companyId)
            ->update(['paid_by' => $data['runner_id']]);

        // Reconcile every day touched: the orders move to the new runner's run.
        Order::query()
            ->whereIn('id', $data['order_ids'])
            ->where('company_id', '=', $companyId)
            ->get(['date'])
            ->pluck('date')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->unique()
            ->each(fn ($day) => DeliveryRun::syncDay($companyId, Carbon::parse($day)));

        return response()->json(['success' => true]);
    }
}