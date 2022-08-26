<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class CompanyController extends Controller
{
    /**
     * @return \Inertia\Response
     */
    public function index()
    {
        $user = auth()->user();
        $company = $user->company;
        $stores = $company->stores;


        return Inertia::render('Company/Index',
            [
                'stores' => $stores,
                'company' => $company,
            ]);
    }
}
