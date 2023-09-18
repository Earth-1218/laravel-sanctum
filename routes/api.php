<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//normal auth
Route::post('/auth/register', [AuthController::class, 'createUser']);
Route::post('/auth/login', [AuthController::class, 'loginUser']);

//otp auth
Route::post('/auth/verify_otp', [AuthController::class, 'verifyOtp']);
Route::post('/auth/request_otp', [AuthController::class, 'requestOtp']);

//if authenticated
Route::middleware('auth:sanctum')->group(function(){
    Route::get('/helloworld', [AuthController::class, 'helloWorld'])->name('helloworld');
});



