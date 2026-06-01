<?php

namespace App\Http\Controllers\Order;

use App\Actions\ChooseRunner;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AssignToMeController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        $user = Auth::user();

        new ChooseRunner($user->company, $user, true)->execute();

        return redirect()->back()->with(['success' => 'You have been assigned']);
    }
}
