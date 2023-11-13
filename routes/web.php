<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GoogleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/apm', '\Done\LaravelAPM\ApmController@index')->name('apm');
