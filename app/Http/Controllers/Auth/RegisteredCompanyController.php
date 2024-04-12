<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCompany;
use App\Models\Company;
use App\Models\InvitedUser;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class RegisteredCompanyController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Auth/RegisterCompany');
    }

    /**
     * @param CreateCompany $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCompany $request)
    {
        $request->validate([
            'companyName' => 'required|unique:companies,name',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'name' => 'required',
        ]);
        
        $company = new Company();

        $company->name = $request->get('companyName');
        $company->link = str_replace(' ', '-', $company->name);
        $company->token = Str::random(32);

        $company->save();

        $user = $this->createUser($request->except('companyName'), $company);
        event(new Registered($user));
        Auth::login($user);

        return Inertia::location(RouteServiceProvider::HOME);
    }

    /**
     * @param array $userdata
     * @param Company $company
     * @return mixed
     */
    private function createUser(array $userdata, Company $company)
    {
        $user = User::create($userdata);

        $user->company_id = $company->id;
        $user->save();

        $user->assignRole('company-owner');

        return $user;
    }
}
