<?php

namespace App\Http\Controllers\Order;

use App\Actions\AddCustomItemToCatalogue;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\EditOrderRequest;
use App\Jobs\NotifyOnHistoryEdit;
use App\Models\DeliveryRun;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class EditOrderController extends Controller
{
    public function __invoke(EditOrderRequest $request): JsonResponse
    {
        $order = $request->getOrder();
        $productId = $request->input('product_id');
        $oldProduct = null;
        $newProduct = null;

        if ($productId === null) {
            // Custom item — possibly converted from a product order.
            $order->product_id = null;
            $order->label = $request->input('label');
            $order->store_id = $request->input('store_id');
        } elseif ((int) $productId !== $order->product_id) {
            $oldProduct = $order->product;
            $newProduct = Product::query()
                ->where('id', '=', $productId)
                ->whereHas('store', fn (Builder $query) => $query->where('company_id', '=', $order->company_id))
                ->first();

            if ($newProduct) {
                $order->product_id = $newProduct->id;
            }
        }

        $order->total = $request->input('total');
        $order->save();

        // An edit can change whether the order is a delivery (e.g. its product), so keep
        // the day's run in sync.
        DeliveryRun::syncDay($order->company_id, Carbon::parse($order->date));

        // Optionally add the custom item to the chosen store's catalogue.
        if ($order->product_id === null && $request->boolean('add_to_catalogue') && $order->label && $order->store_id) {
            $cataloguePrice = (float) ($request->input('catalogue_price') ?? $order->total);
            new AddCustomItemToCatalogue()->execute($order->store_id, $order->label, $cataloguePrice);
        }

        // Best-effort notification — a mail failure must never break the edit itself.
        if ($oldProduct && $newProduct) {
            try {
                NotifyOnHistoryEdit::dispatch($order, $oldProduct->name, $newProduct->name);
            } catch (Throwable $exception) {
                Log::warning('Could not send history-edit notification', ['message' => $exception->getMessage()]);
            }
        }

        return response()->json(['success' => true]);
    }
}