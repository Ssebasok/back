<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\profile\PasswordController;
use App\Http\Controllers\Api\transaction\regTransactionController;
use App\Http\Controllers\Api\profile\profileController;


use App\Http\Requests\RegistationRequest;


# verificacion de token
Route::middleware(['auth:api'])->group(function () {
    Route::get('/profile/home', [profileController::class, 'home']);
    // Otras rutas protegidas...
});



//transaction router

Route::middleware(['auth:api'])->group(function () {
    Route::post('/transaction/create', [regTransactionController::class, 'createTransaction']);
});


#Auth Routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/verify_user_email', [AuthController::class, 'verifyUserEmail']);
Route::post('/auth/resend_verification_link', [AuthController::class, 'ResendEmailVerificationLink']);






Route::middleware(['auth'])->group(function(){
    Route::post('change_password', [PasswordController::class, 'changePassword']);

});