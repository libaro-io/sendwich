<?php

namespace App\Http\Controllers\Order;

use App\Actions\AnalyzeReceipt;
use App\Exceptions\ReceiptAnalysisException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\AnalyzeReceiptRequest;
use Illuminate\Http\JsonResponse;

class AnalyzeReceiptController extends Controller
{
    public function __invoke(AnalyzeReceiptRequest $request): JsonResponse
    {
        $orders = $request->getUndeliveredOrders();
        $stores = auth()->user()->company->stores()->select('id', 'name')->get();
        $image = $request->file('image');

        try {
            $result = new AnalyzeReceipt(file_get_contents($image->getRealPath()), $image->getMimeType())
                ->execute($orders, $stores);
        } catch (ReceiptAnalysisException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        $prices = $result['prices'];

        $matched = $orders->map(fn ($order) => [
            'order_id' => $order->id,
            'total'    => $prices[$order->id] ?? null,
        ])->values();

        $extras = collect($result['extras'])->map(fn ($extra) => [
            'label' => $extra['label'],
            'total' => $extra['price'],
        ])->values();

        return response()->json([
            'prices' => $matched,
            'extras' => $extras,
            'store'  => $result['store'],
        ]);
    }
}
