<?php

use App\Http\Controllers\Api\AuthController;
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
Route::post('/register', [AuthController::class, 'createUser']);
Route::post('/login', [AuthController::class, 'loginUser']);

//otp auth
Route::post('/verify_otp', [AuthController::class, 'verifyOtp']);
Route::post('/request_otp', [AuthController::class, 'requestOtp']);

//if authenticated
Route::middleware('auth:sanctum')->group(function(){
    Route::get('/helloworld', [AuthController::class, 'helloWorld'])->name('helloworld');
});

