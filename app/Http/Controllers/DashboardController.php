<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class DashboardController extends Controller
{
    /**
     * @return InertiaResponse
     */
    public function dashboard(Request $request): InertiaResponse
    {
        $orderController = new OrderController();

        /** @var User $user */
        $user = auth()->user();
        $company = $user->company;


        $products = $company->getProducts();

        $search = $request->input('search');

        if($search){
            $products = $products->filter( fn ($product) => str_contains($product->name ,$search) );
        }

        $deliveryMoment = $orderController->getDeliveryMoment();

        $orders = Order::getOrders($company, $this->getDate());//->get();

        return Inertia::render('Dashboard',
            [
                'user' => $user,
                'company' => $company,
                'products' => $products,
                'deliveryMoment' => $deliveryMoment,
                'orders' => fn() => $orders->get(),
                'filters' => $request->only(['search']),
            ]);
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
    public function getTresholdDate()
    {
        return Carbon::now()->hour(12)->minute(15);
    }

}
