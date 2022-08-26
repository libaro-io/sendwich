<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class StoreController extends Controller
{
    public function detail($store_id)
    {
        $user = auth()->user();
        $company = $user->company;
        $store = $company->stores()->where('id', $store_id)->with('products')->first();


        return Inertia::render('Store/Detail',
            [
                'store' => $store,
            ]);
    }
}
