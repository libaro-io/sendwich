<?php

namespace App\Actions;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResolveCompany
{
    public function execute(Request $request): Company
    {
        if (Auth::check()) {
            return Auth::user()->company;
        }

        return Company::query()
            ->where('token', '=', $request->input('company_token'))
            ->firstOrFail();
    }
}