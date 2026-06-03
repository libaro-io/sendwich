<?php

namespace App\Http\Controllers\Order;

use App\Actions\DeliverySchedule;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\ConfirmDeliveryPricesRequest;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class ConfirmDeliveryPricesController extends Controller
{
    /**
     * @throws Throwable
     */
    public function __invoke(ConfirmDeliveryPricesRequest $request): JsonResponse
    {
        $user = auth()->user();
        $deliveryDate = new DeliverySchedule()->deliveryDate();
        $storeId = $request->input('store_id');

        DB::transaction(function () use ($request, $user, $deliveryDate, $storeId) {
            foreach ($request->getPrices() as $price) {
                Order::query()
                    ->where('id', '=', $price['order_id'])
                    ->update([
                        'total' => $price['total'],
                    ]);
            }

            // Extra items scanned from the receipt become orders billed to the chosen user.
            foreach ($request->getExtraItems() as $extra) {
                Order::query()->create([
                    'user_id'    => $extra['user_id'],
                    'company_id' => $user->company->id,
                    'paid_by'    => $user->id,
                    'product_id' => null,
                    'store_id'   => $storeId,
                    'label'      => $extra['label'],
                    'quantity'   => 1,
                    'total'      => $extra['total'],
                    'date'       => $deliveryDate,
                ]);
            }
        });

        return response()->json(['success' => true]);
    }
}
