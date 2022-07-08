<?php
declare(strict_types=1);


namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Inertia\Inertia;
use Inertia\Response;

final class InvitesController extends Controller
{
    public function index(): Redirector|Response|RedirectResponse|Application
    {
        if (!auth()->check() || !auth()->user()) {
            return redirect('/login');
        }

        $users = User::query()
            ->whereNull('email_verified_at')
            ->where('company_id', '=', auth()->user()->company_id)
            ->get();

        return Inertia::render('Invites/Index',
            [
                'users' => $users,
                'companyId' => auth()->user()->company_id,
            ]);
    }
}
