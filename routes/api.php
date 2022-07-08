<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth')->group(function () {
    Route::post('/order', [OrderController::class, 'order']);
    Route::post('/users', [UserController::class, 'index']);
});
Route::post('/orders', [OrderController::class, 'index']);
Route::post('/selected-runner', [OrderController::class, 'getSelectedRunner']);
Route::post('/simulated-runner', [OrderController::class, 'getSimulatedRunner']);
