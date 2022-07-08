<?php

use App\Http\Controllers\InvitesController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use \App\Http\Controllers\DashboardController;
use \App\Http\Controllers\DisplayController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect('/', '/dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/display', [DisplayController::class, 'showDisplayPrivate'])->name('displays.private.show');

    Route::get('/invites', [InvitesController::class, 'index'])->name('invites.show');
});

/*public routes*/
Route::get('/display/{company_token}', [DisplayController::class, 'showDisplayPublic'])->name('displays.public.show');

require __DIR__ . '/auth.php';
