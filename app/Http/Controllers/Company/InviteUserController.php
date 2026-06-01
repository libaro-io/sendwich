<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\InviteUserRequest;
use App\Mail\InviteNewVictim;
use App\Models\InvitedUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class InviteUserController extends Controller
{
    public function __invoke(InviteUserRequest $request): RedirectResponse
    {
        $user = auth()->user();

        $pendingInvite = InvitedUser::create(array_merge($request->validated(), [
            'invited_by' => $user->id,
            'company_id' => $user->company->id,
        ]));

        $signupUrl = URL::temporarySignedRoute('signup', now()->addWeeks(3), ['id' => $pendingInvite->id]);

        Mail::to($pendingInvite->email)->send(new InviteNewVictim($pendingInvite, $signupUrl));

        return redirect()->back();
    }
}