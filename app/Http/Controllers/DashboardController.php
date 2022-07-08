<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class DashboardController extends Controller
{
    public function dashboard(): InertiaResponse
    {
        $orderController = new OrderController();

        /** @var User $user */
        $user = auth()->user();
        $company = $user->company;

        $products = $company
            ->products()
            ->with('options')
            ->with('orders', function ($query) use ($orderController) {
                if(now() < $orderController->getTresholdDate()) {
                    $query->where('date', '>=', Carbon::now()->startOf('day'));
                    $query->where('date', '<=', Carbon::now()->endOf('day'));
                } else {
                    $query->where('date', '>=', Carbon::now()->addDay()->startOf('day'));
                    $query->where('date', '<=', Carbon::now()->addDay()->endOf('day'));
                }
                $query->where('user_id', '=', auth()->user()->id);
            })->get();

        foreach ($products as $product) {
            if ($product->orders->count() > 0) {
                $product->selected = $product->orders->first()->comment;
            }
        }

        $deliveryMoment = $orderController->getDeliveryMoment();

        return Inertia::render('Dashboard',
            [
                'user' => $user,
                'company' => $company,
                'products' => $products,
                'deliveryMoment' => $deliveryMoment,
            ]);
    }
}
