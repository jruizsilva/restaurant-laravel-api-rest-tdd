<?php

use App\Enums\Roles;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Menu\MenuController;
use App\Http\Controllers\Plate\PlateController;
use App\Http\Controllers\Restaurant\RestaurantController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\RestaurantMustBelongsToTheAuthenticatedUser;
use Illuminate\Support\Facades\Route;

Route::get('hello-world', function () {
    return response()->json([
        'message' => "Hello world",
    ]);
});

Route::controller(UserController::class)->middleware("role:" . Roles::OWNER->name)->group(function () {
    Route::delete('users/{user}', 'destroy')->name('users.destroy');
});

Route::controller(RegisterController::class)->group(function () {
    Route::post("users", 'store');
});

Route::controller(LoginController::class)->group(function () {
    Route::post("login", 'login');
});

Route::controller(ResetPasswordController::class)->group(function () {
    Route::post('forgot-password', 'sendResetLinkEmail');
    Route::put('reset-password', 'resetPassword');
});


Route::middleware('auth:api')->group(function () {
    Route:: as('user.')->group(function () {
        Route::put('user/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('user/password', [PasswordController::class, 'update'])->name('password.update');
    });
    Route::apiResource('restaurants', RestaurantController::class);

    Route::middleware(RestaurantMustBelongsToTheAuthenticatedUser::class)->as("restaurant.")->group(function () {
        Route::apiResource('restaurants/{restaurant:id}/plates', PlateController::class);
        Route::apiResource('restaurants/{restaurant:id}/menus', MenuController::class);
    });
});