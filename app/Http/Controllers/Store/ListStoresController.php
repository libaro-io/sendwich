<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class ListStoresController extends Controller
{
    public function __invoke(): Response
    {
        $user = auth()->user();
        $company = $user->company;

        return Inertia::render('Store/Stores', [
            'stores'  => $company->stores,
            'company' => $company,
        ]);
    }
}