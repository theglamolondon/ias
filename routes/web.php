<?php

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

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');

// Registration Routes...
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');

Route::get('/accueil.html', 'HomeController@index')->name('home');
Route::get('/', 'HomeController@index');
Route::get('/checking', 'HomeController@checkState')->name('rappel');
Route::get('/update/run', 'Core\IasUpdate@runUpdate')->name('rappel');

//Véhicules
Route::prefix('vehicules')->middleware('auth')->group(function (){
    Route::get('liste.html','Car\RegisterController@index')->name('vehicule.liste');
    Route::get('nouveau.html','Car\RegisterController@showNewFormView')->name('vehicule.nouveau');
    Route::post('nouveau.html','Car\RegisterController@ajouter');
    Route::get('{immatriculation}/modifier.html','Car\UpdateController@modifier')->name('vehicule.modifier');
    Route::post('{immatriculation}/modifier.html','Car\UpdateController@update');
    Route::get('interventions.html','Car\ReparationController@index')->name('reparation.liste');
    Route::get('interventions/nouvelle.html','Car\ReparationController@nouvelle')->name("reparation.nouvelle");
    Route::post('interventions/nouvelle.html','Car\ReparationController@ajouter');
	Route::post("interventions/types/add","Car\ReparationController@addType")->name('reparation.type.add');
	Route::get("interventions/{id}/details.html","Car\ReparationController@details")->name('reparation.details');
    Route::get("{immatriculation}/details.html","Car\FicheController@details")->name("vehicule.details");
});

//Missions
Route::prefix('missions')->middleware('auth')->group(function (){
    Route::get('vl/nouvelle.html','Mission\CreateController@nouvelle')->name('mission.nouvelle');
	Route::post('vl/nouvelle.html','Mission\CreateController@ajouter');
    Route::get('pl/nouvelle-pl.html','Mission\PlController@nouvellePL')->name('mission.nouvelle-pl');
    Route::post('pl/nouvelle.html','Mission\PlController@ajouterPL');
    Route::get('vl/liste.html','Mission\MissionController@liste')->name('mission.liste');
    Route::get('pl/liste.html','Mission\MissionPlController@listePL')->name('mission.liste-pl');
    Route::get('vl/{reference}/details.html','Mission\MissionController@details')->name('mission.details');
    Route::post('vl/{reference}/details.html','Mission\UpdateController@updateAfterStart');
	Route::get('pl/{reference}/details.html','Mission\MissionPlController@details')->name('mission.details-pl');
	Route::post('pl/{reference}/details.html','Mission\UpdatePLController@updateAfterStart');
    Route::get('vl/{reference}/modifier.html','Mission\UpdateController@modifier')->name('mission.modifier');
    Route::post('vl/{reference}/modifier.html','Mission\UpdateController@update');
	Route::get('pl/{reference}/modifier.html','Mission\UpdatePLController@modifier')->name('mission.modifier-pl');
	Route::post('pl/{reference}/modifier.html','Mission\UpdatePLController@update');
    Route::get('vl/{reference}/{statut}/changer-statut.html','Mission\MissionController@changeStatus')->name('mission.changer-statut');
    Route::get('pl/{reference}/{statut}/changer-statut.html','Mission\MissionController@changeStatusPL')->name('mission.changer-statut-pl');
});

Route::prefix('administration')->middleware('auth')->group(function (){
    //Chauffeur
    Route::get('chauffeurs.html','Admin\ChauffeurController@liste')->name('admin.chauffeur.liste');
    Route::get('chauffeurs/ajouter.html','Admin\ChauffeurController@ajouter')->name('admin.chauffeur.ajouter');
    Route::post('chauffeurs/ajouter.html','Admin\ChauffeurController@register');
    Route::get('chauffeurs/{matricule}/situation.html','Admin\ChauffeurController@situation')->name("admin.chauffeur.situation");
    //Employé
    Route::get('employes.html','Admin\EmployeController@liste')->name('admin.employe.liste');
    Route::get('employes/ajouter.html','Admin\EmployeController@ajouter')->name('admin.employe.ajouter');
    Route::post('employes/ajouter.html','Admin\EmployeController@register');
    Route::get('employes/{matricule}/modifier.html','Admin\EmployeController@modifier')->name('admin.employe.modifier');
    Route::post('employes/{matricule}/modifier.html','Admin\EmployeController@update');
    Route::get('employe/{matricule}/fiche.html','Admin\EmployeController@fiche')->name('admin.employe.fiche');
    //Utilisateurs
    Route::get('utilisateurs.html', 'Admin\UtilisateurController@index')->name('admin.utilisateur.liste');
    Route::get('utilisateur/ajouter.html', 'Admin\UtilisateurController@ajouter')->name('admin.utilisateur.ajouter');
    Route::post('utilisateur/ajouter.html', 'Admin\UtilisateurController@register');
    Route::get('utilisateur/password/reset.html', 'Admin\UtilisateurController@reinitialiser')->name('admin.utilisateur.reset');
    Route::post('utilisateur/password/reset.html', 'Admin\UtilisateurController@reset');
});

Route::prefix('rh')->middleware('auth')->group(function (){
	Route::get('salaire/demarrer.html','RH\SalaireController@demarrage')->name('rh.salaire');
	Route::post('salaire/demarrer.html','RH\SalaireController@start');
	Route::get('salaire/{annee}/{mois}/reset','RH\SalaireController@reset')->name('rh.salaire.reset');
	Route::get('salaire/{annee}/{mois}/confirm.html','RH\SalaireController@confirm')->name('rh.salaire.confirm');
	Route::post('salaire/{annee}/{mois}/confirm.html','RH\SalaireController@cloturer');
	Route::get('bulletin-paie/{annee}/{mois}/fiche.html','RH\PaieController@fichePaie')->name('rh.paie');
	Route::post('bulletin-paie/{annee}/{mois}/fiche.html','RH\PaieController@savePaie');
});

Route::get('/test.html','Mission\MissionController@reminder');

//Facturation
Route::prefix('factures')->middleware('auth')->group(function (){
    Route::get("liste.html","Order\FactureController@liste")->name("facturation.liste.all");
    Route::get("normale/liste.html","Order\FactureController@listeFacture")->name("facturation.liste.facture");
    Route::get("proforma/liste.html","Order\FactureController@listeProforma")->name("facturation.liste.proforma");
    Route::get("proforma/nouvelle.html","Order\ProformaController@nouvelle")->name("facturation.proforma.nouvelle");
    Route::post("proforma/nouvelle.html","Order\ProformaController@ajouter");
    Route::post("proforma/update","Order\UpdateProforma@modifier")->name("facturation.proforma.modifier");
    Route::post("normale/make","Order\FactureController@makeNormal")->name("facturation.switch.normal");
    Route::get("{reference}/annuler","Order\FactureController@annuler")->name("facturation.switch.annuler");
    Route::get("{id}/livraison/make","Order\BonLivraisonController@makeBonLivraison")->name("facturation.switch.livraison");
    Route::get("{reference}/option-email.html","Order\SenderController@choice")->name("facturation.envoie.emailchoice");
    Route::get("{reference}/details.html","Order\FactureController@details")->name("facturation.details");
});

//Versement
Route::prefix("versement")->middleware("auth")->group(function (){
    Route::get("mission/{code}/nouveau.html","Money\VersementController@nouveauVersement")->name("versement.mission.ajouter");
    Route::post("mission/{code}/nouveau.html","Money\VersementController@ajouter");
    Route::post('facture/partenaire/client.html','Money\ReglementController@reglementClient')->name('versement.facture.client');
    Route::post('facture/partenaire/fournisseur.html','Money\ReglementController@reglementFournisseur')->name('versement.facture.fournisseur');
});

//Compte
Route::prefix('compte')->middleware('auth')->group(function (){
    Route::get('livre.html','Money\CompteController@registre')->name('compte.registre');
	Route::post('livre.html','Money\CompteController@addNewSousCompte');
    Route::get('sous-compte/synthese.html','Money\CompteController@syntheseSousCompte')->name('compte.synthese');
    Route::get('sous-compte/{slug}/registre.html','Money\CompteController@detailsSousCompte')->name('compte.souscompte');
	Route::post('sous-compte/{slug}/registre.html','Money\CompteController@addNewLine');
    Route::post('sous-compte/{slug}/modifier-line.html','Money\CompteController@updateLine')->name('compte.modifier');
    Route::get('sous-compte/{slug}/reset.html','Money\CompteController@showReset')->name('compte.reset');
    Route::post('sous-compte/{slug}/reset.html','Money\CompteController@reset');
    Route::get('sous-compte/{slug}/modifier.html','Money\CompteController@modifier')->name("compte.update");
    Route::post('sous-compte/{slug}/modifier.html','Money\CompteController@updateCompte');
    Route::get('nouvelle-depense.html','Money\CompteController@newSortieCompte')->name('compte.sortie.new');
    Route::post('nouvelle-depense.html','Money\CompteController@addNewLine');
});

//PDF
Route::prefix('impression')->middleware('auth')->group(function (){
    Route::get("{reference}/{state}/pdf.html","Printer\PdfController@imprimerPieceComptable")->name("print.piececomptable");
    Route::get("vehicules","Printer\PdfController@imprimerVehicule")->name("print.vehicule");
    Route::get("produits","Printer\PdfController@imprimerInventaire")->name("print.produits");
    Route::get("sous-compte/{slug}/print","Printer\PdfController@imprimerSousCompte")->name("print.souscompte");
    Route::get("client/{id}/point","Printer\PdfController@imprimerPointClient")->name("print.pointclient");
    Route::get("fournisseur/{id}/bc","Printer\PdfController@imprimerBC")->name("print.bc");
    Route::get("compte/synthese","Printer\PdfController@imprimerSyntheseCompte")->name("print.compte.synthese");
});

//Partenaires
Route::prefix('partenaires')->middleware('auth')->group(function (){
    Route::get('{type}/liste.html','Partenaire\RegisterController@liste')->name('partenaire.liste');
    Route::get('nouveau.html','Partenaire\RegisterController@nouveau')->name('partenaire.nouveau');
    Route::post('nouveau.html','Partenaire\RegisterController@ajouter');
    Route::get('{id}/modifier.html','Partenaire\RegisterController@modifier')->name('partenaire.modifier');
    Route::post('{id}/modifier.html','Partenaire\RegisterController@update');

    Route::get("client/{id}/details.html","Partenaire\DetailsController@ficheClient")->name("partenaire.client");
    Route::get("fournisseur/{id}/details.html","Partenaire\DetailsController@ficheFournisseur")->name("partenaire.fournisseur");

    Route::get("fournisseurs/factures/nouvelle.html","Partenaire\FournisseurController@newOrder")->name("partenaire.fournisseur.new");
    Route::post("fournisseurs/factures/nouvelle.html","Partenaire\FournisseurController@addOrder");
    Route::get("fournisseurs/factures.html","Partenaire\FournisseurController@liste")->name("partenaire.fournisseur.factures");
    Route::get("fournisseurs/factures/{id}/details.html","Partenaire\FournisseurController@details")->name("partenaire.fournisseur.factures.details");
    Route::post("fournisseur/facture/switch","Partenaire\FournisseurController@switchPiece")->name("partenaire.bc.switch");
    Route::get("fournisseur/facture/{id}/valide","Partenaire\FournisseurController@validePiece")->name("partenaire.bc.valide");
});

//Stock
Route::prefix('stock')->middleware('auth')->group(function (){
	Route::get("mouvements.html","Stock\MouvementController@index")->name("stock.produit.mouvement");
    Route::get("produit/nouveau.html","Stock\ProduitController@ajouter")->name('stock.produit.ajouter');
    Route::post("produit/nouveau.html","Stock\ProduitController@addProduct");
    Route::get("produits/inventaire.html","Stock\ProduitController@liste")->name('stock.produit.liste');
    Route::get("produits","Stock\ProduitController@liste");
    Route::get("produits/{reference}/modifier.html","Stock\ProduitController@modifier")->name("stock.produit.modifier");
    Route::post("produits/{reference}/modifier.html","Stock\ProduitController@update");
    Route::get("produits/{reference}/supprimer.html","Stock\ProduitController@delete")->name("stock.produit.supprimer");

    Route::post("produits/famille/add","Stock\FamilleController@addFamille")->name("stock.produit.famille.ajouter");
    Route::get("produits/famille/liste.html","Stock\FamilleController@liste")->name("stock.produit.famille");
    Route::get("produits/famille/{id}/modifier.html","Stock\FamilleController@modifier")->name("stock.produit.famille.modifier");
    Route::post("produits/famille/{id}/modifier.html","Stock\FamilleController@update");
	Route::get("produits/ratio-commande.html","Stock\ProduitController@classProduct")->name("stock.produit.ratio");
});

//Email
Route::prefix('email')->middleware('auth')->group(function (){
    Route::post('proforma/{reference}/send.html','Mail\OrderController@envoyerProforma')->name("email.proforma");
});