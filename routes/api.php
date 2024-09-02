<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['prefix' => 'v1'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [UserController::class, 'store']);
    Route::post('reset-password', [AuthController::class, 'sendingResetLinkEmail']);
    Route::patch('reset-password', [AuthController::class, 'resetPassword']);

    /** Protected Routes */
    Route::middleware('auth:api')->group(function (){
       // Users
       Route::patch('profile', [UserController::class, 'update']);
       Route::patch('password', [UserController::class, 'updatePassword']);

    });

});
