<?php

namespace App\Http\Controllers\History;

use App\Http\Controllers\Controller;
use App\Http\Requests\History\UpdateOrderRequest;
use App\Jobs\NotifyOnHistoryEdit;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class UpdateOrderController extends Controller
{
    public function __invoke(UpdateOrderRequest $request): JsonResponse
    {
        foreach ($request->input('data') as $orderGroup) {
            foreach ($orderGroup as $newOrder) {
                $order = Order::find($newOrder['id']);

                if ($newOrder['product']['id'] !== $order->product_id) {
                    $oldProduct = $order->product;
                    $newProduct = Product::find($newOrder['product']['id']);
                    $order->update([
                        'product_id' => $newProduct->id,
                        'total'      => $newProduct->price * $newOrder['quantity'],
                    ]);
                    NotifyOnHistoryEdit::dispatch($order, $oldProduct->name, $newProduct->name);
                }
            }
        }

        return response()->json();
    }
}