<?php

use App\Http\Controllers\History\UpdateRunnerController;
use App\Http\Controllers\Order\AddCustomOrderController;
use App\Http\Controllers\Order\AddProductController;
use App\Http\Controllers\Order\AnalyzeReceiptController;
use App\Http\Controllers\Order\AssignToMeController;
use App\Http\Controllers\Order\CheckNewStoreController;
use App\Http\Controllers\Order\ConfirmDeliveryController;
use App\Http\Controllers\Order\DeleteOrderController;
use App\Http\Controllers\Order\DepartAsRunnerController;
use App\Http\Controllers\Order\EditOrderController;
use App\Http\Controllers\Order\GetDoneOrdersController;
use App\Http\Controllers\Order\GetOrdersByDateController;
use App\Http\Controllers\Order\GetOrdersController;
use App\Http\Controllers\Order\GetSelectedRunnerController;
use App\Http\Controllers\Order\GetSimulatedRunnerController;
use App\Http\Controllers\Order\RemoveProductController;
use App\Http\Controllers\Order\StoreReceiptStoreController;
use App\Http\Controllers\PayoutController;
use App\Http\Controllers\Store\AddStoreController;
use App\Http\Controllers\Store\DeleteProductController;
use App\Http\Controllers\Store\DeleteStoreController;
use App\Http\Controllers\Store\StoreProductController;
use App\Http\Controllers\Store\UpdateProductController;
use App\Http\Controllers\Store\UpdateStoreController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/order/check-new-store', CheckNewStoreController::class)->name('order.check-new-store');
Route::post('/order/add-product', AddProductController::class)->name('order.add-product');
Route::post('/order/remove-product', RemoveProductController::class)->name('order.remove-product');
Route::post('/users', [UserController::class, 'index'])->name('users.index');

Route::post('/orders', GetOrdersController::class)->name('orders.index');
Route::post('/done-orders', GetDoneOrdersController::class)->name('orders.done');
Route::post('/assign-to-me', AssignToMeController::class)->name('order.assign-to-me');
Route::patch('/order/depart', DepartAsRunnerController::class)->name('order.depart');
Route::patch('/order/edit', EditOrderController::class)->name('order.edit');
Route::post('/order/custom', AddCustomOrderController::class)->name('order.custom.add');
Route::delete('/order/{order}', DeleteOrderController::class)->name('order.delete');
Route::post('/order/receipt', AnalyzeReceiptController::class)->name('order.receipt.analyze');
Route::post('/order/receipt/store', StoreReceiptStoreController::class)->name('order.receipt.store');
Route::post('/order/confirm-delivery', ConfirmDeliveryController::class)->name('order.confirm-delivery');
Route::post('/selected-runner', GetSelectedRunnerController::class)->name('order.selected-runner');
Route::post('/simulated-runner', GetSimulatedRunnerController::class)->name('order.simulated-runner');

Route::post('/getAllOrdersByDateAndUser', GetOrdersByDateController::class)->name('orders.by-date');
Route::post('/updateOrderRunner', UpdateRunnerController::class)->name('history.update-runner');

Route::post('/payouts/handle', [PayoutController::class, 'payout'])->name('payouts.handle');

Route::middleware(['auth', 'verified', 'can:edit-store'])->group(function () {
    Route::put('/store/add', AddStoreController::class)->name('store.add');
    Route::post('/store/{id}', UpdateStoreController::class)->name('store.update');
    Route::delete('/store/{id}', DeleteStoreController::class)->name('store.delete');
    Route::post('/store/product/{product}', UpdateProductController::class)->name('store.product.update');
    Route::put('/store/product', StoreProductController::class)->name('store.product.add');
    Route::delete('/store/product/{product}', DeleteProductController::class)->name('store.product.delete');
});
