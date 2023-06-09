<?php

namespace App\Http\Controllers;

use App\Actions\ChooseRunner;
use App\Actions\UsersWithDept;
use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class DashboardController extends Controller
{
    public bool $simulated = false;

    /**
     * @return InertiaResponse
     */
    public function dashboard(Request $request): InertiaResponse
    {
        $deptAction = new UsersWithDept();
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

        $order = ( clone $orders)->first();

        $simulated = false;

        $selectedRunner = $order?->deliverer ?? null;

        $formattedOrders = collect();
        $doneOrders = Order::getOrders($company, $this->getDate(), false, true)
            ->get()
            ->groupBy('paid_by');

        foreach($doneOrders as $userId => $orderGroup){
            $formattedOrders[$orderGroup->first()->deliverer->name] = $orderGroup;
        }

        if(!$selectedRunner){
            $action = new ChooseRunner($company);
            $selectedRunner = $action->getSimulatedRunner();
            $simulated = true;
        }


        return Inertia::render('Dashboard',
            [
                'user' => $user,
                'users' => fn() => $deptAction->execute(),
                'company' => $company,
                'selectedRunner' => $selectedRunner,
                'simulated' => $simulated,
                'products' => fn() => $products->get(),
                'deliveryMoment' => $deliveryMoment,
                'orders' => $orders->get(),
                'filters' => $request->only(['search']),
                'formattedOrders' => $formattedOrders,
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
