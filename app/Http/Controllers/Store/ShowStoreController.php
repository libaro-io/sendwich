<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class ShowStoreController extends Controller
{
    public function __invoke(int $id): Response
    {
        $company = auth()->user()->company;

        return Inertia::render('Store/Products', [
            'store' => $company->stores()->where('id', '=', $id)->with('products')->first(),
        ]);
    }
}