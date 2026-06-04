<?php

namespace App\Http\Controllers\History;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class ShowHistoryController extends Controller
{
    public function __invoke(): Response
    {
        $user = auth()->user();
        $company = $user->company;

        return Inertia::render('History', [
            'company' => $company,
            'products' => $company->getProducts(),
            'users'    => $company->users()->select('id', 'name')->get(),
            'stores'   => $company->stores()->select('id', 'name')->get(),
        ]);
    }
}