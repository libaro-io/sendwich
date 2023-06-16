<?php

namespace App\Http\Controllers;

use App\Actions\ChooseRunner;
use App\Http\Requests\Order\AddRequest;
use App\Http\Requests\Order\RemoveRequest;
use App\Models\Company;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        $orders = Order::getOrders($company, $this->getDate())->get();
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

    public function getSelectedRunner(Request $request)
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

    public function getSimulatedRunner(Request $request)
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

    public function getDeliveryMoment()
    {
        if (Carbon::now() < $this->getTresholdDate()) {
            $deliveryMoment = 'today';
        } else {
            $deliveryMoment = 'tomorrow';
        }
        return $deliveryMoment;
    }

    public function getTresholdDate()
    {
        return Carbon::now()->hour(12)->minute(15);
    }

    public function getDate()
    {
        if (Carbon::now() < $this->getTresholdDate()) {
            $date = Carbon::now();
        } else {
            $date = Carbon::now()->addDay();
        }
        return $date->setHour(12)->setMinutes(15)->setSecond(00);
    }

    /**
     * @param AddRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addProduct(AddRequest $request): \Illuminate\Http\RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $data = $request->validated();


        /** @var Product|null $product */
        $product = Product::query()->find($data['product_id']);

        if (is_null($product)) {
            abort(400);
        }


        //$order = $this->getProductOrderForUser($user, $product);


        $message = 'Uw bestelling is aangepast';

        //if (is_null($order)) {
            $order = new Order();
            $order->user_id = auth()->user()->id;
            $order->company_id = $user->company->id;
            $order->date = Carbon::now()->isBefore(Carbon::now()->hour(12)->minute(15)) ? Carbon::now()->hour(12)->minute(15) : Carbon::now()->addDay()->hour(12)->minute(15);

            $message = 'Bestelling geplaatst!';
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

        $order->comment = $options->pluck('name')->join(', ');
        $order->save();

        return redirect()->back()->with(['success'=> $message]);
    }

    /**
     * @param RemoveRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeProduct(RemoveRequest $request): \Illuminate\Http\RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $data = $request->validated();

        /** @var Product|null $product */
        $product = Product::query()->find($data['product_id']);

        $this->getProductOrderForUser($user, $product)?->delete();

        return redirect()->back()->with(['success' => 'Uw bestelling is verwijderd']);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assignToMe(): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();
        $action = new ChooseRunner($user->company, $user, true);
        $action->execute();

        return redirect()->back()->with(['success'=>'You have been assigned']);

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
        $orders = Order::query()
            ->with([
                'deliverer',
                'user',
                'product'
                ]
            )
            ->orderBy('date','DESC')
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
}
