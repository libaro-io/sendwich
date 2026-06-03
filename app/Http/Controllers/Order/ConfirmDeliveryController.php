<?php

namespace App\Http\Controllers\Order;

use App\Actions\DeliverySchedule;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\ConfirmDeliveryRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class ConfirmDeliveryController extends Controller
{
    /**
     * Confirm a delivery in one atomic step: correct order prices, register extra items,
     * apply the chosen catalogue changes (new products + price updates), and mark delivered.
     *
     * @throws Throwable
     */
    public function __invoke(ConfirmDeliveryRequest $request): RedirectResponse
    {
        $user = auth()->user();
        $companyId = $user->company->id;
        $deliveryDate = new DeliverySchedule()->deliveryDate();

        DB::transaction(function () use ($request, $user, $companyId, $deliveryDate) {
            // 1. Correct the prices of the ordered items.
            foreach ($request->getPrices() as $price) {
                Order::query()
                    ->where('id', '=', $price['order_id'])
                    ->update(['total' => $price['total']]);
            }

            // 2. Update indicative/catalogue prices the runner changed.
            foreach ($request->getPriceUpdates() as $update) {
                Product::query()
                    ->where('id', '=', $update['product_id'])
                    ->where('company_id', '=', $companyId)
                    ->update(['price' => $update['price']]);
            }

            // 3. Add new products to a store's catalogue.
            foreach ($request->getNewProducts() as $product) {
                Product::query()->create([
                    'name'           => $product['name'],
                    'description'    => null,
                    'price'          => $product['price'],
                    'variable_price' => false,
                    'company_id'     => $companyId,
                    'store_id'       => $product['store_id'],
                ]);
            }

            // 4. Extra items become orders billed to the chosen user, tied to their store.
            foreach ($request->getExtraItems() as $extra) {
                Order::query()->create([
                    'user_id'    => $extra['user_id'],
                    'company_id' => $companyId,
                    'paid_by'    => $user->id,
                    'product_id' => null,
                    'store_id'   => $extra['store_id'] ?? null,
                    'label'      => $extra['label'],
                    'quantity'   => 1,
                    'total'      => $extra['total'],
                    'date'       => $deliveryDate,
                ]);
            }

            // 5. Mark this runner's undelivered orders for the date as delivered (incl. the new extras).
            Order::query()
                ->where('paid_by', '=', $user->id)
                ->whereNull('delivered_at')
                ->whereBetween('date', [
                    $deliveryDate->copy()->startOfDay(),
                    $deliveryDate->copy()->endOfDay(),
                ])
                ->update(['delivered_at' => now()]);
        });

        return redirect()->back()->with(['success' => 'Orders confirmed and marked as delivered']);
    }
}
