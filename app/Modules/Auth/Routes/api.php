<?php

use App\Modules\Auth\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('guest:api')
        ->name('auth.login');
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('guest:api')
        ->name('auth.register');

    Route::group(['middleware' => ['auth:api', 'jwt.auth']], function () {
        Route::post('/logout', [AuthController::class, 'logout'])
            ->name('auth.logout');
        Route::post('/refresh', [AuthController::class, 'refresh'])
            ->name('auth.refresh');
        Route::get('/me', [AuthController::class, 'me'])
            ->name('auth.me');
    });
});
