<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    public function getOrders()
    {
        if (Carbon::now() < $this->getTresholdDate()) {
            $orders = (new OrderController())->ordersToday();
        } else {
            $orders = (new OrderController())->ordersTomorrow();
        }

        return $orders;
    }

    public function getDeliveryMoment()
    {
        $now = Carbon::now();
        $noon = $this>$this->getTresholdDate();
        if ($now < $noon) {
            $deliveryMoment = 'Vandaag';
        } else {
            $deliveryMoment = 'Morgen';
        }
        return $deliveryMoment;
    }

    public function getTresholdDate()
    {
       return Carbon::now()->hour(12)->minute(15);
    }

    public function ordersToday()
    {
        return Order::getOrders(Carbon::now())->get();
    }

    public function ordersTomorrow()
    {
        return Order::getOrders(Carbon::now()->addDay())->get();
    }

    public function order(Request $request)
    {
        $user = auth()->user();
        $product = Product::find($request->product_id);
        $order = Order::where('user_id', $user->id)->where('product_id', $product->id)->where('date', '>=', Carbon::now()->startOf('day'))->where('date', '<=', Carbon::now()->endOf('day'))->first();

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
