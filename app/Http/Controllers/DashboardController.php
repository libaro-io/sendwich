<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
        $search = $request->input('search');
        $deliveryMoment = $orderController->getDeliveryMoment();

        $products = $company
            ->products()
            ->with(['orders', 'options', 'store'])
            ->when($search, fn($query) => $query->where('name', 'like', '%' . Str::lower($search) . '%'));

        $orders = Order::getOrders($company, $this->getDate());

        return Inertia::render('Dashboard',
            [
                'user' => $user,
                'company' => $company,
                'products' => fn() => $products->get(),
                'deliveryMoment' => $deliveryMoment,
                'orders' => fn() => $orders->get(),
                'filters' => $request->only(['search']),
                'totalPrice' => $orders->sum('total'),
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
