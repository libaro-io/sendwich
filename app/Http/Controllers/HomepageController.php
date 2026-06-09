<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class HomepageController extends Controller
{
    /**
     * Render the marketing landing page — the reference design for the
     * app-wide restyle. Authenticated users go straight to their dashboard.
     */
    public function show(): RedirectResponse|InertiaResponse
    {
        if (Auth::check()) {
            return redirect()->to(route('dashboard'));
        }

        return Inertia::render('Welcome');
    }
}