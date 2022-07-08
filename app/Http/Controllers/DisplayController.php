<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DisplayController extends Controller
{
    public function showDisplayPublic($company_token)
    {
        $company = Company::query()->where('token', '=', $company_token)->firstOrFail();
        $oc = new OrderController();
        $deliveryMoment = $oc->getDeliveryMoment();

        return Inertia::render('Display',
            [
                'company' => $company,
                'deliveryMoment' => $deliveryMoment,
            ]);
    }

    public function showDisplayPrivate()
    {
        $company = Auth::user()->company;
        return redirect()->to(route('displays.public.show', ['company_token' => $company->token]));
    }
}
