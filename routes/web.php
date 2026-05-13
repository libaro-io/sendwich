<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\Company\Settings\SaveNotificationSettingsController;
use App\Http\Controllers\Company\Settings\SaveRunnerSettingsController;
use App\Http\Controllers\Company\Settings\ShowSettingsController;
use App\Http\Controllers\Company\Settings\TestNotificationChannelController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisplayController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;


Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dashboard',[DashboardController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/display',[DisplayController::class, 'showDisplayPrivate'])->middleware(['auth', 'verified'])->name('displays.private.show');
Route::get('/history',[HistoryController::class, 'showHistory'])->middleware(['auth', 'verified'])->name('history');

//storesettings
Route::get('/stores', [StoreController::class, 'index'])->middleware(['auth', 'verified', 'can:edit-store'])->name('store.index');
Route::get('/store/{id}', [StoreController::class, 'show'])->middleware(['auth', 'verified', 'can:edit-store'])->name('store.show');

//companysettings
Route::get('/company',  [CompanyController::class , 'show'])->middleware(['auth', 'verified','can:edit-company'])->name('company.show');
Route::post('/user/invite',  [CompanyController::class , 'inviteUser'])->middleware(['auth', 'verified','can:edit-company'])->name('invite');
Route::post('/user/permission',  [CompanyController::class , 'editUserPermission'])->middleware(['auth', 'verified','can:edit-company'])->name('user.permissions');
Route::post('/user/delete',  [CompanyController::class , 'deleteUser'])->middleware(['auth', 'verified','can:edit-company'])->name('user.delete');


Route::get('/company/settings',  ShowSettingsController::class)->middleware(['auth', 'verified','can:edit-company'])->name('settings.show');
Route::post('/company/settings/runner',  SaveRunnerSettingsController::class)->middleware(['auth', 'verified','can:edit-company'])->name('settings.runner.update');
Route::post('/company/settings/notifications',  SaveNotificationSettingsController::class)->middleware(['auth', 'verified','can:edit-company'])->name('settings.notifications.update');
Route::post('/company/settings/notifications/channels/{channel}/test',  TestNotificationChannelController::class)->middleware(['auth', 'verified','can:edit-company', 'can:test,channel'])->name('settings.notifications.channels.test');


/*public routes*/
Route::get('/display/{company_token}',[DisplayController::class, 'showDisplayPublic'])->name('displays.public.show');

Route::get('/privacy',[LegalController::class, 'privacy'])->name('legal.privacy');
Route::get('/cookies',[LegalController::class, 'cookies'])->name('legal.cookies');
Route::get('/general',[LegalController::class, 'general'])->name('legal.general');

require __DIR__.'/auth.php';
