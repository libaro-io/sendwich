<?php

namespace App\Http\Controllers;

use App\Actions\ChooseRunner;
use App\Actions\DeliverySchedule;
use App\Actions\UsersWithDept;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class DashboardController extends Controller
{
    public function dashboard(Request $request): InertiaResponse
    {
        $schedule = new DeliverySchedule();
        $deptAction = new UsersWithDept();

        /** @var User $user */
        $user = auth()->user();
        $user->load('roles');
        $company = $user->company;
        $deliveryMoment = $schedule->moment();

//        $products = $company
//            ->products()
//            ->with(['orders', 'options', 'store'])
//            ->when($search, fn($query) => $query->where('name', 'like', '%' . Str::lower($search) . '%'));

        $stores = $company->stores()->select('name', 'id')->withCount('products')->with(['products.options'])->get();

        $deliveryDate = $schedule->deliveryDate();
        $orders = Order::getOrders($company, $deliveryDate, false, null);

        $assignedOrder = Order::getOrders($company, $deliveryDate, false, true)->first();

        $simulated = false;

        $selectedRunner = $assignedOrder?->deliverer ?? null;

        if (!$selectedRunner && $company->auto_assign_runner) {
            $action = new ChooseRunner($company);
            $selectedRunner = $action->getSimulatedRunner();
            $simulated = true;
        }

        return Inertia::render('Dashboard',
            [
                'user' => $user,
                'users' => fn() => $deptAction->execute(),
                'companyUsers' => fn() => $company->users()->select('id', 'name')->get(),
                'userCount' => $company->users()->count(),
                'productCount' => $company->products()->count(),
                'company' => $company,
                'selectedRunner' => $selectedRunner,
                'simulated' => $simulated,
//                'products' => fn() => $products->get(),
                'deliveryMoment' => $deliveryMoment,
                'orders' => $orders->get(),
                'filters' => $request->only(['search']),
                'totalPrice' => $orders->sum('total'),
                'stores' => $stores
            ]);
    }

}
