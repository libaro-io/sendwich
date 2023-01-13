<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HistoryController extends Controller
{
    public function showHistory()
    {
        return Inertia::render('History',
            [
            ]);
    }
}
