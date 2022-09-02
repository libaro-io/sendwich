<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class StoreController extends Controller
{
    /**
     * @return \Inertia\Response
     */
    public function index()
    {
        $user = auth()->user();
        $company = $user->company;

        return Inertia::render('Store/Index',
            [
                'stores' => $company->stores,
                'company' => $company,
            ]);
    }

    public function show($store_id)
    {
        $user = auth()->user();
        $company = $user->company;

        return Inertia::render('Store/Detail',
            [
                'store' => $company->stores()->where('id', $store_id)->with('products')->first(),
            ]);
    }
}
