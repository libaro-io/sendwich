<?php

namespace App\Http\Controllers\History;

use App\Http\Controllers\Controller;
use App\Http\Requests\History\UpdateRunnerRequest;
use App\Models\Order;
use Illuminate\Http\JsonResponse;

class UpdateRunnerController extends Controller
{
    public function __invoke(UpdateRunnerRequest $request): JsonResponse
    {
        $data = $request->validated();

        Order::query()
            ->whereIn('id', $data['order_ids'])
            ->where('company_id', '=', auth()->user()->company->id)
            ->update(['paid_by' => $data['runner_id']]);

        return response()->json(['success' => true]);
    }
}