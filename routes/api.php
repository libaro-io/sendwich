<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StoreController;

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

//Route::middleware('auth')->group(function () {
    Route::post('/order/add-product', [OrderController::class,  'addProduct']);
    Route::post('/order/remove-product', [OrderController::class,  'removeProduct']);
    Route::post('/users', [UserController::class,  'index']);
//});
Route::post('/orders', [OrderController::class, 'index']);
Route::post('/done-orders', [OrderController::class, 'getDoneOrders']);
Route::post('/assign-to-me', [OrderController::class, 'assignToMe']);
Route::post('/selected-runner', [OrderController::class,  'getSelectedRunner']);
Route::post('/simulated-runner', [OrderController::class,  'getSimulatedRunner']);
Route::post('/getAllOrdersByDateAndUser', [OrderController::class,  'getAllOrdersByDateAndUser']);

Route::group(
    ['controller' => StoreController::class,
        'middleware' => ['auth', 'verified']
    ], function () {
    Route::post('/store/product/{product}', 'update')->name('store.update');
    Route::put('/store/product', 'store')->name('store.add');
    Route::delete('/store/product/{product}', 'delete')->name('store.delete');
});
