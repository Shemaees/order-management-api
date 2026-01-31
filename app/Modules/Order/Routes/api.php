<?php

use App\Modules\Order\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api', 'jwt.auth']], function () {
    Route::apiResource('orders', OrderController::class);
    Route::patch('orders/{order}/status', [OrderController::class, 'updateOrderStatus'])->name('orders.status.update');
});
