<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ForgotPasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register')->name('api.register');
    Route::post('login', 'login')->name('api.login');

    Route::middleware(['auth:api'])->group(function () {
        Route::get('profile', 'profile')->name('api.profile');
        Route::get('refresh', 'refreshToken')->name('api.refresh');
        Route::get('logout', 'logout')->name('api.logout');
    });
});

Route::controller(ForgotPasswordController::class)->group(function () {
    Route::post('send-otp', 'sendOtp')->name('api.send-otp');
    Route::post('reset-password', 'resetPassword')->name('api.reset-password');
});