<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\RestaurantController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::middleware('throttle:10,1')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
    });
});

// Public restaurants (works for guests)
// If user is authenticated, resource will include is_favorited automatically
Route::get('restaurants', [RestaurantController::class, 'index']);
Route::get('restaurants/{id}/details', [RestaurantController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::get('profile', [AuthController::class, 'profile']);
    Route::post('logout', [AuthController::class, 'logout']);

    // Bookings
    Route::get('bookings', [BookingController::class, 'index']);
    Route::get('mybookings', [BookingController::class, 'index']); // legacy
    Route::post('bookings', [BookingController::class, 'store']);

    // Favorites
    Route::get('favorites', [FavoriteController::class, 'index']);
    Route::post('restaurants/{restaurant}/favorite', [FavoriteController::class, 'store']);
    Route::delete('restaurants/{restaurant}/favorite', [FavoriteController::class, 'destroy']);
});
