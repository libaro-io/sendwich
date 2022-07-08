<?php

namespace App\Http\Controllers;

use App\Actions\ChooseRunner;
use App\Models\Company;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
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

    public function order(Request $request)
    {
        $user = auth()->user();
        $product = Product::find($request->product_id);
        $order = Order::where('user_id', $user->id)->where('product_id', $product->id)->where('date', '>=', $this->getDate()->startOf('day'))->where('date', '<=', $this->getDate()->endOf('day'))->first();

        if ($order) {
            if (empty($request->type)) {
                $order->delete();
                $message = 'Uw bestelling is verwijderd';
            } else {
                $order->comment = $request->type;
                $order->save();
                $message = 'Uw bestelling is aangepast';
            }
        } else {
            $order = new Order();
            $order->user_id = auth()->user()->id;
            $order->product_id = $product->id;
            $order->company_id = $user->company->id;
            $order->quantity = 1;
            $order->total = $product->price * $order->quantity;
            $order->date = Carbon::now()->isBefore(Carbon::now()->hour(12)->minute(15)) ? Carbon::now()->hour(12)->minute(15) : Carbon::now()->addDay()->hour(12)->minute(15);
            $order->comment = $request->type;
            $order->save();
            $message = 'Pisto is besteld!';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }


}
