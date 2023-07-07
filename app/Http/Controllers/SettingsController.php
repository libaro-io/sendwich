<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $company = $user->company;

        return Inertia::render('Settings',
            [
                'user' => $user,
                'company' => $company,
            ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $company = $user->company;


        $company = $request->get('company');
        return Inertia::render('Settings',
            [
                'user' => $user,
                'company' => $company,
            ]);
    }
}
