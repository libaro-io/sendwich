<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class DashboardController extends Controller
{
    /**
     * @return InertiaResponse
     */
    public function dashboard(): InertiaResponse
    {
        $orderController = new OrderController();

        /** @var User $user */
        $user = auth()->user();
        $company = $user->company;


        $products = $company->getProducts();

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
