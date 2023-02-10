<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\InvitedUser;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Inertia\Response
     */
    public function create($companyLink)
    {

        $company = Company::where('link', $companyLink)->first();
        return Inertia::render('Auth/Register',
            [
                'companyLink' => $companyLink ?? null,
                'companyName' => $company ? $company->name : '',
            ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $invitee = InvitedUser::query()->findOrFail($request->get('id'));
        if($invitee->email !== $request->get('email')){
            abort(403);
        }

        $user = User::create([
            'name' => $invitee->name,
            'email' => $invitee->email,
            'password' => Hash::make($request->password),
        ]);

        if ($request->company_link) {
            $company = Company::where('link',$request->company_link)->first();
            if($company) {
                $user->company_id = $company->id;
                $user->save();
            }
        }

        $invitee->delete();

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }

    /**
     * Display the registration view.
     *
     * @return \Inertia\Response
     */
    public function createPassword($companyLink , $invitee)
    {

        $company = Company::where('link', $companyLink)->first();
        return Inertia::render('Auth/Register',
            [
                'companyLink' => $companyLink ?? null,
                'companyName' => $company ? $company->name : '',
            ]);
    }

    public function signup($id){
        $invitee = InvitedUser::query()->findOrFail($id);
        $company = Company::query()->findOrFail($invitee->company_id);

        return Inertia::render('Auth/CreatePassword',
            [
                'companyLink' => $company->link ?? null,
                'companyName' => $company ? $company->name : '',
                'name' => $invitee->name,
                'email' => $invitee->email,
                'id' => $invitee->id,
            ]);
    }
}
