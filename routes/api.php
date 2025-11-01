<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\profile\PasswordController;
use App\Http\Controllers\Api\transaction\regTransactionController;
use App\Http\Controllers\Api\profile\profileController;
use App\Http\Controllers\Api\transaction\delTransactionController;




use App\Http\Requests\RegistationRequest;


# Home 
Route::middleware(['auth:api'])->group(function () {
    Route::get('/profile/home', [profileController::class, 'home']);
});



//transactions routes

Route::middleware(['auth:api'])->group(function () {
    Route::post('/transaction/create', [regTransactionController::class, 'createTransaction']);
});

Route::middleware(['auth:api'])->group(function(){
    Route::delete('/transaction/delete', [delTransactionController::class, 'deleteTransaction']);
});

//Route::middleware(['auth:api'])->group(function(){
//    Route::update('/transaction/update', [updateTransactionController::class, 'updateTransaction']);
//});



#Auth Routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/verify_user_email', [AuthController::class, 'verifyUserEmail']);
Route::post('/auth/resend_verification_link', [AuthController::class, 'ResendEmailVerificationLink']);






Route::middleware(['auth'])->group(function(){
    Route::post('change_password', [PasswordController::class, 'changePassword']);

});