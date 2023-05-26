<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class HomeController extends Controller
{

    public function index()
    {
        if (Auth::check()) {
            return redirect()->to(route('dashboard'));
        }

        return Inertia::render('Home', []);
    }
}
