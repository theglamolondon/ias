<?php

use Illuminate\Http\Request;

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
Route::post('login', [\App\Http\Controllers\Auth\LoginController::class,'login']);

Route::prefix('factures')->middleware('auth-api')->group(function (){

    Route::get('liste.html',[\App\Http\Controllers\Web\Car\RegisterController::class,"index"]);
});