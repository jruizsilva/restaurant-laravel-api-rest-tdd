<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\PlateController;
use App\Http\Controllers\Restaurant\RestaurantController;
use Illuminate\Support\Facades\Route;

Route::get('hello-world', function () {
    return response()->json([
        'message' => "Hello world",
    ]);
});

Route::controller(RegisterController::class)->group(function () {
    Route::post("users", 'store');
});

Route::controller(LoginController::class)->group(function () {
    Route::post("login", 'login');
});

Route::controller(ProfileController::class)->group(function () {
    Route::put('profile', 'update');
});

Route::controller(PasswordController::class)->group(function () {
    Route::put('password', 'update');
});

Route::controller(ResetPasswordController::class)->group(function () {
    Route::post('forgot-password', 'sendResetLinkEmail');
    Route::put('reset-password', 'resetPassword');
});


Route::middleware('auth:api')->group(function () {
    Route::apiResource('restaurants', RestaurantController::class);
    Route:: as('restaurant.')->group(function () {
        Route::apiResource('restaurants/{restaurant:id}/plates', PlateController::class);
    });
});