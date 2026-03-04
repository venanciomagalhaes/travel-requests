<?php

use App\Http\Controllers\V1\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::prefix('auth')->group(function () {

        Route::post('register', [AuthController::class, 'register'])->name('api.v1.auth.register');
        Route::post('login', [AuthController::class, 'login'])->name('api.v1.auth.login');

        Route::middleware('auth:api')->group(function () {
            Route::get('me', [AuthController::class, 'me'])->name('api.v1.auth.me');
            Route::get('refresh', [AuthController::class, 'refresh'])->name('api.v1.auth.refresh');
            Route::delete('logout', [AuthController::class, 'logout'])->name('api.v1.auth.logout');
        });

    });

});
