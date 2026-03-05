<?php

use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\TravelRequestController;
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

    Route::middleware('auth:api')->group(function () {
        Route::prefix('travel-requests')->group(function () {
            Route::get('/', [TravelRequestController::class, 'index'])->name('api.v1.travel-requests.index');
            Route::post('/', [TravelRequestController::class, 'store'])->name('api.v1.travel-requests.store');
            Route::get('/{uuid}', [TravelRequestController::class, 'show'])->name('api.v1.travel-requests.show');
        });
    });
});
