<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisplayController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\StoreController;

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

Route::get('/dashboard', [DashboardController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/my-company', [CompanyController::class, 'index'])->middleware(['auth', 'verified'])->name('company.index');
Route::get('/store/edit/{id}', [StoreController::class, 'detail'])->middleware(['auth', 'verified'])->name('store.show');
Route::get('/display', [DisplayController::class, 'showDisplayPrivate'])->middleware(['auth', 'verified'])->name('displays.private.show');

/*public routes*/
Route::get('/display/{company_token}', [DisplayController::class, 'showDisplayPublic'])->name('displays.public.show');

require __DIR__ . '/auth.php';
