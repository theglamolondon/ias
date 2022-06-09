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
Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class,'loginApi']);
Route::post('/auth/token/refresh', [\App\Http\Controllers\Auth\LoginController::class,'refreshToken']);

//Facture
Route::prefix('factures')->middleware('auth-api')->group(function (){
  Route::get('liste.html',[\App\Http\Controllers\Web\Car\RegisterController::class,"index"]);
});
//Partenaire
Route::prefix('partenaires')->middleware('auth-api')->group(function (){
  Route::get('/clients',[\App\Http\Controllers\Api\PartnerController::class,"listeClient"]);
  Route::get('/fournisseurs',[\App\Http\Controllers\Api\PartnerController::class,"listeFournisseur"]);
  Route::get('/{id}/details',[\App\Http\Controllers\Api\PartnerController::class,"getPartnerDetails"]);
});