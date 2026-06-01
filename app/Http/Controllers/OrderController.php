<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use App\Actions\ChooseRunner;
use App\Actions\DeliverySchedule;
use App\Http\Requests\Order\AddRequest;
use App\Http\Requests\Order\RemoveRequest;
use App\Http\Requests\Order\UpdateWeightRequest;
use App\Models\Company;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        if(Auth::check()){
            $company = Auth::user()->company;
        }else{
            $company = Company::query()->where('token', '=', $request->input('company_token'))->firstOrFail();
        }
        $orders = Order::getOrders($company, $this->getDate())
            ->get();
        if($orders->count()){
            $user = $orders[0]->deliverer;
        }
        return response()->json(['orders' => $orders, 'user' => $user ?? null]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getDoneOrders(Request $request): JsonResponse
    {
        if(Auth::check()){
            $company = Auth::user()->company;
        }else{
            $company = Company::query()->where('token', '=', $request->input('company_token'))->firstOrFail();
        }
        $formattedOrders = collect();
        $orders = Order::getOrders($company, $this->getDate(), false, true)
            ->get()
            ->groupBy('paid_by');
        foreach($orders as $userId => $orderGroup){
                $formattedOrders[$orderGroup->first()->deliverer->name] = $orderGroup;
        }
        return response()->json(['orders' => $formattedOrders, 'user' => $user ?? null]);
    }

    public function getSelectedRunner(Request $request): JsonResponse
    {
        if(Auth::check()){
            $company = Auth::user()->company;
        }else{
            $company = Company::query()->where('token','=', $request->input('company_token'))->firstOrFail();
        }
        $order = Order::getOrders($company, $this->getDate())->first();
        if($order){
            $user = $order->deliverer;
        }else{
            $user = null;
        }
        return response()->json(['user' => $user]);
    }

    public function getSimulatedRunner(Request $request): JsonResponse
    {
        if(Auth::check()){
            $company = Auth::user()->company;
        }else{
            $company = Company::query()->where('token', $request->input('company_token'))->firstOrFail();
        }
        $action = new ChooseRunner($company);
        $runner = $action->getSimulatedRunner();
        return response()->json(['runner' => $runner ?? null]);
    }

    public function getDeliveryMoment(): string
    {
        return (new DeliverySchedule())->moment();
    }

    public function getTresholdDate(): Carbon
    {
        return (new DeliverySchedule())->thresholdDate();
    }

    public function getDate(): Carbon
    {
        return (new DeliverySchedule())->deliveryDate();
    }

    /**
     * @param AddRequest $request
     * @return RedirectResponse
     */
    public function addProduct(AddRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $data = $request->validated();


        /** @var Product|null $product */
        $product = Product::query()->find($data['product_id']);

        if (is_null($product)) {
            abort(400);
        }

        $departed = Order::query()
            ->where('company_id', $user->company->id)
            ->whereNotNull('departed_at')
            ->whereBetween('date', [
                $this->getDate()->startOfDay(),
                $this->getDate()->endOfDay(),
            ])
            ->exists();

        abort_if($departed, 403, 'The runner has already departed.');

        $message = 'Your order has been updated';

        //if (is_null($order)) {
            $order = new Order();
            $order->user_id = auth()->user()->id;
            $order->company_id = $user->company->id;
            $order->date = Carbon::now()->isBefore(Carbon::now()->hour(12)->minute(15)) ? Carbon::now()->hour(12)->minute(15) : Carbon::now()->addDay()->hour(12)->minute(15);

            $message = 'Order placed!';
        //}

        $order->quantity = 1;
        $order->total = $product->price * $order->quantity;
        $order->product_id = $product->id;

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

        return redirect()->back()->with(['success'=> $message]);
    }

    /**
     * @param RemoveRequest $request
     * @return RedirectResponse
     */
    public function removeProduct(RemoveRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $data = $request->validated();

        /** @var Product|null $product */
        $product = Product::query()->find($data['product_id']);

        if (is_null($product)) {
            abort(404);
        }

        $order = $this->getProductOrderForUser($user, $product);

        abort_if(is_null($order), 404);
        abort_if($order->user_id !== $user->id, 403);

        $order->delete();

        return redirect()->back()->with(['success' => 'Your order has been deleted']);
    }

    /**
     * @return RedirectResponse
     */
    public function assignToMe(): RedirectResponse
    {
        $user = Auth::user();
        $action = new ChooseRunner($user->company, $user, true);
        $action->execute();

        return redirect()->back()->with(['success'=>'You have been assigned']);

    }

    public function departAsRunner(): RedirectResponse
    {
        $user = Auth::user();

        Order::query()
            ->where('paid_by', $user->id)
            ->whereNull('departed_at')
            ->whereBetween('date', [
                $this->getDate()->startOfDay(),
                $this->getDate()->endOfDay(),
            ])
            ->update(['departed_at' => now()]);

        return redirect()->back()->with(['success' => 'On the way!']);
    }

    public function updateWeight(UpdateWeightRequest $request): JsonResponse
    {
        $order = $request->getOrder();

        $order->weight = $request->input('weight');
        $order->total  = $order->product->price * $request->input('weight');
        $order->save();

        return response()->json(['success' => true]);
    }

    public function markAsDelivered(): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();

        $updatedCount = Order::query()
            ->where('paid_by', $user->id)
            ->whereNull('delivered_at')
            ->whereBetween('date', [
                $this->getDate()->startOfDay(),
                $this->getDate()->endOfDay(),
            ])
            ->update(['delivered_at' => now()]);

        abort_if($updatedCount === 0, 404);

        return redirect()->back()->with(['success' => 'Orders marked as delivered']);
    }

    private function getProductOrderForUser(User $user, Product $product): ?Order
    {
        /** @var Order|null $order */
        $order = Order::query()
            ->where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->where('date', '>=', $this->getDate()->startOf('day'))
            ->where('date', '<=', $this->getDate()->endOf('day'))
            ->first();

        return $order;
    }

    /**
     * @return JsonResponse
     */
    public function getAllOrdersByDateAndUser(): JsonResponse
    {
        $user = auth()->user();
        $company = $user->company;

        $orders = $company->orders()
            ->with([
                'deliverer',
                'user',
                'product.store',
                ]
            )
            ->orderBy('date', 'DESC')
            ->where('date', '>', Carbon::now()->subMonth())
            ->get()
            ->groupBy([function($order) {
                return Carbon::parse($order->date)->format('Ymd');
            },'paid_by'])
            ->map(function ($value, $key){
                return ['date'=> $key, 'data'=>$value];
            })
            ->values();
        return response()->json($orders);
    }

    public function checkForDifferentStore(AddRequest $request): JsonResponse
    {

        $user = auth()->user();

        $data = $request->validated();


        /** @var Product|null $product */
        $product = Product::query()->find($data['product_id']);

        if (is_null($product)) {
            abort(400);
        }
        //get the orders for the company
        $orders = Order::getOrders($user->company, $this->getDate());

        //get different stores from the orders
        $stores = $orders->get()->map(fn($order) => $order->product->store_id);

        //when there are check if the $store of the new order is already
        if($stores->count() > 0 && !$stores->contains($product->store_id)){
            return response()->json(false,Response::HTTP_CONFLICT);
        }

        return response()->json(true,200);
    }
}
