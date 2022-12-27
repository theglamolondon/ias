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
  Route::post('/ajouter',[\App\Http\Controllers\Api\FactureController::class,"ajouterNouvelleFacture"]);
  Route::get('/liste',[\App\Http\Controllers\Api\FactureController::class,"getFactures"]);
  Route::get('/{reference}/details',[\App\Http\Controllers\Api\FactureController::class,"getDetailsFacture"]);
});
//Partenaire
Route::prefix('partenaires')->middleware('auth-api')->group(function (){
  Route::get('/clients',[\App\Http\Controllers\Api\PartnerController::class,"listeClient"]);
  Route::get('/fournisseurs',[\App\Http\Controllers\Api\PartnerController::class,"listeFournisseur"]);
  Route::get('/factures/{id}/details',[\App\Http\Controllers\Api\PartnerController::class,"getPartnerDetailsWithOrders"]);
  Route::get('/{id}/details',[\App\Http\Controllers\Api\PartnerController::class,"getPartnerDetails"]);
  Route::get('/search',[\App\Http\Controllers\Api\PartnerController::class,"recherche"]);
});
//Produit
Route::prefix('produits')->middleware('auth-api')->group(function (){
  Route::get('/famille/liste',[\App\Http\Controllers\Api\ProductController::class,"familles"]);
  Route::get('/search',[\App\Http\Controllers\Api\ProductController::class,"recherche"]);
  Route::get('/liste',[\App\Http\Controllers\Api\ProductController::class,"liste"]);
  Route::get('/{reference}/details',[\App\Http\Controllers\Api\ProductController::class,"details"]);
});
//Vehicule
Route::prefix('vehicules')->middleware('auth-api')->group(function (){
  Route::get('/search',[\App\Http\Controllers\Api\VehiculeController::class,"searchVehiculeVL"]);
  Route::get('/liste',[\App\Http\Controllers\Api\VehiculeController::class,"liste"]);
  Route::get('/liste/disponibles',[\App\Http\Controllers\Api\VehiculeController::class,"listeDisponible"]);
  Route::get('/liste/indisponibles',[\App\Http\Controllers\Api\VehiculeController::class,"listeIndisponible"]);
  Route::get('/{immatriculation}/details',[\App\Http\Controllers\Api\VehiculeController::class,"details"]);
  Route::get('/genres',[\App\Http\Controllers\Api\VehiculeController::class,"listeGenre"]);
  Route::get('/reparations/liste',[\App\Http\Controllers\Api\InterventionController::class, "getListe"]);
  Route::post('/ajout',[\App\Http\Controllers\Api\VehiculeController::class,"add"]);
  Route::put('/update/{immatriculation}',[\App\Http\Controllers\Api\VehiculeController::class,"modified"]);
  Route::get('/interventions',[\App\Http\Controllers\Api\InterventionController::class,"getListe"]);
  Route::get('/interventions/{vehicule}',[\App\Http\Controllers\Api\InterventionController::class,"getVehiculeIntervention"]);
  Route::post('/nouvelle/intervention',[\App\Http\Controllers\Api\InterventionController::class,"Add"]);
  Route::post('/type/intervention',[\App\Http\Controllers\Api\InterventionController::class,"addType"]);
});
//Mission
Route::prefix('missions')->middleware('auth-api')->group(function (){
  Route::get('/liste',[\App\Http\Controllers\Api\MissionController::class,"liste"]);
  Route::post('/add',[\App\Http\Controllers\Api\MissionController::class,"ajouter"]);
  Route::get('/details/{reference}',[\App\Http\Controllers\Api\MissionController::class,"details"]);
  Route::post('/ajout',[\App\Http\Controllers\Api\MissionController::class,"addMission"]);
  Route::put('/update/{reference}',[\App\Http\Controllers\Api\MissionController::class,"update"]);
  Route::post('pl/{reference}',[\App\Http\Controllers\Api\MissionController::class,'updateMissionAfterStart']);
  Route::get('vl/{reference}/{statut}/',[\App\Http\Controllers\Api\MissionController::class,'changeStatus'])->name('mission.changer-statut');

});
//Employe
Route::prefix('employes')->middleware('auth-api')->group(function (){
  Route::get('/chauffeurs/liste',[\App\Http\Controllers\Api\ChauffeurController::class,"liste"]);
  Route::post('chauffeurs/add',[\App\Http\Controllers\Api\ChauffeurController::class,'register']);
  Route::get('/liste',[\App\Http\Controllers\Api\PersonnelController::class,"liste"]);
  Route::get('chauffeurs/{matricule}',[\App\Http\Controllers\Api\ChauffeurController::class,'situation']);
  Route::post('/add',[\App\Http\Controllers\Api\PersonnelController::class,'register']);
  Route::get('employe/{matricule}',[\App\Http\Controllers\Api\PersonnelController::class,'fiche']);
  Route::post('/{matricule}/update',[\App\Http\Controllers\Api\PersonnelController::class,'update']);
});
//Settings
Route::prefix('settings')->middleware('auth-api')->group(function (){
    Route::get('/intervention/types',[\App\Http\Controllers\Api\SettingController::class,"listeTypeIntervention"]);
});

//Administration user
Route::prefix('administration')->middleware('auth-api')->group(function (){
    Route::get('utilisateurs',[\App\Http\Controllers\Api\UtilisateurController::class,'getListe']);
    Route::post('utilisateur/add',[\App\Http\Controllers\Api\UtilisateurController::class,'register']);
    Route::post('utilisateur/password/reset', [\App\Http\Controllers\Api\UtilisateurController::class,'reset']);


});