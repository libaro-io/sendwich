<?php

namespace App\Http\Controllers;

use App\Mail\InformVictim;
use App\Mail\InviteNewVictim;
use App\Models\InvitedUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
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

        $user = auth()->user();
        $company = $user->company;

        $data = array_merge($data , ['invited_by' => $user->id ,'company_id' => $company->id]);

        $pendingInvite = InvitedUser::create($data);

        $signupUrl = URL::temporarySignedRoute('signup',now()->addWeeks(3) ,['id' => $pendingInvite->id]);

        Mail::to($pendingInvite->email)->send(new InviteNewVictim($pendingInvite,$signupUrl));

        return redirect()->back();

    }
}
