<?php

namespace App\Http\Controllers\Order;

use App\Actions\DeliverySchedule;
use App\Actions\NotifyCompany;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\AddRequest;
use App\Models\Company;
use App\Models\DeliveryRun;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductOption;
use App\Notifications\FirstOrderPlaced;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;

class AddProductController extends Controller
{
    public function __invoke(AddRequest $request): RedirectResponse
    {
        $user = auth()->user();
        $data = $request->validated();

        /** @var Product|null $product */
        $product = Product::query()->find($data['product_id']);

        if ($product === null) {
            abort(400);
        }

        $deliveryDate = new DeliverySchedule()->deliveryDate();
        $isFirstOrderToday = Order::isFirstForDay($user->company, $deliveryDate);

        $order = new Order();
        $order->user_id = $user->id;
        $order->company_id = $user->company->id;
        $order->date = $deliveryDate;
        $order->quantity = 1;
        $order->product_id = $product->id;
        $order->paid_by = Order::assignedRunnerId($user->company, $deliveryDate);
        $order->total = $product->price * $order->quantity;

        /** @var ProductOption[]|Collection $options */
        $options = ProductOption::query()
            ->whereIn('id', $data['options'])
            ->get();

        foreach ($options as $option) {
            $order->total += $option->price;
        }

        $comment = $options->pluck('name')->join(', ');
        if (!empty($data['comment'])) {
            $comment = $comment ? $comment . ' — ' . $data['comment'] : $data['comment'];
        }
        $order->comment = $comment ?: null;
        $order->save();

        DeliveryRun::syncDay($user->company->id, $deliveryDate);

        // First order of the day nudges the team. Atomically claim the day's single
        // notification slot — gated on having an enabled channel — so two concurrent
        // first orders can never both notify. Only send when we won the claim.
        if ($isFirstOrderToday && $user->company->reminder_enabled) {
            $claimed = Company::query()
                ->where('id', '=', $user->company->id)
                ->whereHas('notificationChannels', fn ($query) => $query->where('enabled', '=', true))
                ->where(fn ($query) => $query
                    ->whereNull('daily_notification_sent_date')
                    ->orWhereDate('daily_notification_sent_date', '!=', $deliveryDate->toDateString()))
                ->update(['daily_notification_sent_date' => $deliveryDate->toDateString()]);

            if ($claimed === 1) {
                new NotifyCompany($user->company)->execute(new FirstOrderPlaced($user->company, $user));
            }
        }

        return redirect()->back()->with(['success' => 'Order placed!']);
    }
}
