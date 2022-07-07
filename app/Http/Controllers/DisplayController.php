<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Inertia\Inertia;

class DisplayController extends Controller
{
    public function showDisplay($company_token)
    {
        $company = Company::query()->where('token', $company_token)->firstOrFail();
        $oc = new OrderController();
        $deliveryMoment = $oc->getDeliveryMoment();

        return Inertia::render('Display',
            [
                'company' => $company,
                'deliveryMoment' => $deliveryMoment,
            ]);
    }
}
