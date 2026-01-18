<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::middleware('throttle:10,1')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

Route::prefix('restaurants')->group(function () {
    // Public: قائمة المطاعم + تفاصيل مطعم
    Route::get('/', [RestaurantController::class, 'index']);
    Route::get('{restaurant:slug}', [RestaurantController::class, 'show']);
});

Route::middleware('auth:sanctum')->group(function () {
    // Customer Bookings within a restaurant
    Route::prefix('restaurants/{restaurant:slug}')->group(function () {
        Route::get('bookings', [CustomerBookingController::class, 'index']);     // حجوزاتي في هذا المطعم
        Route::post('bookings', [CustomerBookingController::class, 'store'])->middleware('throttle:20,1');    // إنشاء حجز
        Route::delete('bookings/{booking}', [CustomerBookingController::class, 'destroy']); // إلغاء حجز
    });
});
