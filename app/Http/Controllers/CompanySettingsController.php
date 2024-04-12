<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanySettingsController extends Controller
{
    public function settings()
    {
        $company = Auth::user()->company;

        return inertia('Settings', ['company' => $company]);
    }

    public function save(Request $request)
    {
        $company = Auth::user()->company;
        $company->select_runner_at = $request->get('time');
        $company->save();

        return inertia('Settings', ['company' => $company]);
    }
}
