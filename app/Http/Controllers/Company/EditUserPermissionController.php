<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EditUserPermissionController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
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
}