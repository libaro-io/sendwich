<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class HomeController extends Controller
{
    /**
     * @return InertiaResponse
     */
    public function index(): InertiaResponse
    {
        return Inertia::render('Home', []);
    }
}
