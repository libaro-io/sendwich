<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class ShowCompanyController extends Controller
{
    public function __invoke(): Response
    {
        $user = auth()->user();

        /** @var Collection<User> $users */
        $users = $user->company
            ->users()
            ->with('roles', 'permissions')
            ->get()
            ->map(function (User $companyUser) {
                $companyUser->canEditStore   = $companyUser->hasPermissionTo('edit-store');
                $companyUser->canEditCompany = $companyUser->hasPermissionTo('edit-company');
                return $companyUser;
            });

        return Inertia::render('Company', [
            'user'    => $user,
            'users'   => $users,
            'company' => $user->company,
        ]);
    }
}