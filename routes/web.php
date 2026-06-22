<?php

use App\Http\Controllers\Company\DeleteUserController;
use App\Http\Controllers\Company\EditUserPermissionController;
use App\Http\Controllers\Company\InviteUserController;
use App\Http\Controllers\Company\Settings\SaveNotificationSettingsController;
use App\Http\Controllers\Company\Settings\SaveRunnerSettingsController;
use App\Http\Controllers\Company\Settings\ShowSettingsController;
use App\Http\Controllers\Company\Settings\TestNotificationChannelController;
use App\Http\Controllers\Company\ShowCompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisplayController;
use App\Http\Controllers\History\ShowHistoryController;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\Store\ListStoresController;
use App\Http\Controllers\Store\ShowStoreController;
use Illuminate\Support\Facades\Route;

// Marketing landing page — reference design for the app-wide restyle.
Route::get('/', [HomepageController::class, 'show'])->name('home');

Route::get('/dashboard', [DashboardController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/display', [DisplayController::class, 'showDisplayPrivate'])->middleware(['auth', 'verified'])->name('displays.private.show');
Route::get('/history', ShowHistoryController::class)->middleware(['auth', 'verified'])->name('history');

// Store settings
Route::get('/stores', ListStoresController::class)->middleware(['auth', 'verified', 'can:edit-store'])->name('store.index');
Route::get('/store/{id}', ShowStoreController::class)->middleware(['auth', 'verified', 'can:edit-store'])->name('store.show');

// Company settings
Route::get('/company', ShowCompanyController::class)->middleware(['auth', 'verified', 'can:edit-company'])->name('company.show');
Route::post('/user/invite', InviteUserController::class)->middleware(['auth', 'verified', 'can:edit-company'])->name('invite');
Route::post('/user/permission', EditUserPermissionController::class)->middleware(['auth', 'verified', 'can:edit-company'])->name('user.permissions');
Route::post('/user/delete', DeleteUserController::class)->middleware(['auth', 'verified', 'can:edit-company'])->name('user.delete');

Route::get('/company/settings', ShowSettingsController::class)->middleware(['auth', 'verified', 'can:edit-company'])->name('settings.show');
Route::post('/company/settings/runner', SaveRunnerSettingsController::class)->middleware(['auth', 'verified', 'can:edit-company'])->name('settings.runner.update');
Route::post('/company/settings/notifications', SaveNotificationSettingsController::class)->middleware(['auth', 'verified', 'can:edit-company'])->name('settings.notifications.update');
Route::post('/company/settings/notifications/channels/{channel}/test', TestNotificationChannelController::class)->middleware(['auth', 'verified', 'can:edit-company', 'can:test,channel'])->name('settings.notifications.channels.test');

// Public routes
Route::get('/display/{company_token}', [DisplayController::class, 'showDisplayPublic'])->name('displays.public.show');

Route::get('/privacy', [LegalController::class, 'privacy'])->name('legal.privacy');
Route::get('/cookies', [LegalController::class, 'cookies'])->name('legal.cookies');
Route::get('/general', [LegalController::class, 'general'])->name('legal.general');

require __DIR__.'/auth.php';