<?php

namespace App\Http\Controllers;

use App\Mail\InformVictim;
use App\Mail\InviteNewVictim;
use App\Models\InvitedUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;

class CompanyController extends Controller
{
    public function show()
    {
        $user = auth()->user();

        /** @var Collection<User> $users */
        $users = $user
            ->company
            ->users()
            ->with('roles', 'permissions')
            ->get()
            ->map(function (User $user) {
                $user->canEditStore = $user->hasPermissionTo('edit-store');
                $user->canEditCompany = $user->hasPermissionTo('edit-company');

                return $user;
            });

        return Inertia::render(
            'Company',
            [
                'user' => $user,
                'users' => $users,
                'company' => $user->company,
            ],
        );
    }

    public function editUserPermission(Request $request)
    {
        $user = User::find($request->get('user_id'));
        $type = $request->get('type');

        if ($user->can($type)) {
            $user->revokePermissionTo($type);
        } else {
            $user->givePermissionTo($type);
        }

        return redirect()->back()->with(['success' => 'Permissions edited']);
    }

    public function deleteUser(Request $request)
    {
        $user = User::find($request->get('user_id'));
        $user->delete();

        return redirect()->back()->with(['success' => 'User deleted']);
    }

    public function inviteUser(Request $request)
    {

        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
        ]);

        $user = auth()->user();
        $company = $user->company;

        $data = array_merge($data, ['invited_by' => $user->id, 'company_id' => $company->id]);

        $pendingInvite = InvitedUser::create($data);

        $signupUrl = URL::temporarySignedRoute('signup', now()->addWeeks(3), ['id' => $pendingInvite->id]);

        Mail::to($pendingInvite->email)->send(new InviteNewVictim($pendingInvite, $signupUrl));

        return redirect()->back();

    }
}
