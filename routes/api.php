<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use Illuminate\Http\Request;
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

Route::controller(AuthController::class)->group(function () {
    Route::post("logout", 'logout');
    Route::post("refresh", 'refresh');
    Route::post("me", 'me');
});