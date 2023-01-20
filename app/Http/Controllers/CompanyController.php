<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class CompanyController extends Controller
{
    public function show(){
        $user = auth()->user() ;
        $company = $user->company;
        $users = $company->users;

        return Inertia::render('Company',
            [
                'user' => $user,
                'users' => $users,
                'company' => $company,
            ]);
    }

    public function inviteUser(Request $request){

        $data = $request->validate([
            'name'=>'required',
            'email'=>'required|email|unique:users',
        ]);

        dd('passed');
    }
}
