<?php

use App\Modules\Payment\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api', 'jwt.auth']], function () {
    Route::post('orders/{order}/payment', [PaymentController::class, 'store'])->name('payments.order.pay');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::get('/payments/order/{order}', [PaymentController::class, 'orderPayments'])->name('payments.order.show');
});
