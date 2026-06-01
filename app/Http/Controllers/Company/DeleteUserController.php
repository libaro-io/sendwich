<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DeleteUserController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $user = User::find($request->get('user_id'));
        $user->delete();

        return redirect()->back()->with(['success' => 'User deleted']);
    }
}