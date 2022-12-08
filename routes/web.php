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
Route::get('login', [\App\Http\Controllers\Auth\LoginController::class,'showLoginForm'])->name('login');
Route::post('login', [\App\Http\Controllers\Auth\LoginController::class,'login']);
Route::get('logout', [\App\Http\Controllers\Auth\LoginController::class,'logout'])->name('logout');

// Registration Routes...
Route::get('register', [\App\Http\Controllers\Auth\RegisterController::class,'showRegistrationForm'])->name('register');
Route::post('register', [\App\Http\Controllers\Auth\RegisterController::class,'register']);

// Password Reset Routes...
Route::get('password/reset', [\App\Http\Controllers\Auth\ForgotPasswordController::class,'showLinkRequestForm'])->name('password.request');
Route::post('password/email',[\App\Http\Controllers\Auth\ForgotPasswordController::class,'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}',[\App\Http\Controllers\Auth\ResetPasswordController::class,'showResetForm'])->name('password.reset');
Route::post('password/reset', [\App\Http\Controllers\Auth\ResetPasswordController::class,'reset']);

Route::get('/accueil.html', [\App\Http\Controllers\HomeController::class,'index'])->name('home');
Route::get('/', [\App\Http\Controllers\HomeController::class,'index']);
Route::get('/checking',[\App\Http\Controllers\HomeController::class,'checkState'])->name('rappel');
Route::get('/update/run', [\App\Http\Controllers\Web\Core\IasUpdate::class,'runUpdate'])->name('maj');

  //Véhicules
Route::prefix('vehicules')->middleware('auth')->group(function (){
    Route::get('liste.html',[\App\Http\Controllers\Web\Car\RegisterController::class,"index"])->name('vehicule.liste');
    Route::get('nouveau.html',[\App\Http\Controllers\Web\Car\RegisterController::class,'showNewFormView'])->name('vehicule.nouveau');
    Route::post('nouveau.html',[\App\Http\Controllers\Web\Car\RegisterController::class,'ajouter']);
    Route::get('{immatriculation}/modifier.html',[\App\Http\Controllers\Web\Car\UpdateController::class,'modifier'])->name('vehicule.modifier');
    Route::post('{immatriculation}/modifier.html',[\App\Http\Controllers\Web\Car\UpdateController::class,'update']);
    Route::get('interventions.html',[\App\Http\Controllers\Web\Car\ReparationController::class,'index'])->name('reparation.liste');
    Route::get('interventions/nouvelle.html',[\App\Http\Controllers\Web\Car\ReparationController::class,'nouvelle'])->name("reparation.nouvelle");
    Route::post('interventions/nouvelle.html',[\App\Http\Controllers\Web\Car\ReparationController::class,'ajouter']);
	Route::post("interventions/types/add",[\App\Http\Controllers\Web\Car\ReparationController::class,'addType'])->name('reparation.type.add');
	Route::get("interventions/{id}/details.html",[\App\Http\Controllers\Web\Car\ReparationController::class,"details"])->name('reparation.details');
    Route::get("{immatriculation}/details.html",[\App\Http\Controllers\Web\Car\FicheController::class,'details'])->name("vehicule.details");
});

//Missions
Route::prefix('missions')->middleware('auth')->group(function (){
    Route::get('vl/nouvelle.html',[\App\Http\Controllers\Web\Mission\MissionController::class,'nouvelle'])->name('mission.nouvelle');
	Route::post('vl/nouvelle.html',[\App\Http\Controllers\Web\Mission\MissionController::class,'ajouter']);
    Route::get('pl/nouvelle-pl.html',[\App\Http\Controllers\Web\Mission\PlController::class,'nouvellePL'])->name('mission.nouvelle-pl');
    Route::post('pl/nouvelle-pl.html', [\App\Http\Controllers\Web\Mission\PlController::class,'ajouterPL']);
    Route::get('vl/liste.html', [\App\Http\Controllers\Web\Mission\MissionController::class,'liste'])->name('mission.liste');
    Route::get('pl/liste.html',[\App\Http\Controllers\Web\Mission\MissionPlController::class,'listePL'])->name('mission.liste-pl');
    Route::get('vl/{reference}/details.html',[\App\Http\Controllers\Web\Mission\MissionController::class,'details'])->name('mission.details');
    Route::post('vl/{reference}/details.html',[\App\Http\Controllers\Web\Mission\MissionController::class,'updateAfterStart']);
	Route::get('pl/{reference}/details.html',[\App\Http\Controllers\Web\Mission\MissionPlController::class,'details'])->name('mission.details-pl');
	Route::post('pl/{reference}/details.html',[\App\Http\Controllers\Web\Mission\UpdatePLController::class,'updateAfterStart']);
    Route::get('vl/{reference}/modifier.html',[\App\Http\Controllers\Web\Mission\MissionController::class,'modifier'])->name('mission.modifier');
    Route::post('vl/{reference}/modifier.html',[\App\Http\Controllers\Web\Mission\MissionController::class,'update']);
	Route::get('pl/{reference}/modifier.html',[\App\Http\Controllers\Web\Mission\UpdatePLController::class,'modifier'])->name('mission.modifier-pl');
	Route::post('pl/{reference}/modifier.html',[\App\Http\Controllers\Web\Mission\UpdatePLController::class,'update']);
    Route::get('vl/{reference}/{statut}/changer-statut.html',[\App\Http\Controllers\Web\Mission\MissionController::class,'changeStatus'])->name('mission.changer-statut');
    Route::get('pl/{reference}/{statut}/changer-statut.html',[\App\Http\Controllers\Web\Mission\MissionController::class,'changeStatusPL'])->name('mission.changer-statut-pl');
});

Route::prefix('administration')->middleware('auth')->group(function (){
    //Chauffeur
    Route::get('chauffeurs.html',[\App\Http\Controllers\Web\Admin\ChauffeurController::class,'liste'])->name('admin.chauffeur.liste');
    Route::get('chauffeurs/ajouter.html',[\App\Http\Controllers\Web\Admin\ChauffeurController::class,'ajouter'])->name('admin.chauffeur.ajouter');
    Route::post('chauffeurs/ajouter.html',[\App\Http\Controllers\Web\Admin\ChauffeurController::class,'register']);
    Route::get('chauffeurs/{matricule}/situation.html',[\App\Http\Controllers\Web\Admin\ChauffeurController::class,'situation'])->name("admin.chauffeur.situation");
    //Employé
    Route::get('employes.html',[\App\Http\Controllers\Web\Admin\EmployeController::class,'liste'])->name('admin.employe.liste');
    Route::get('employes/ajouter.html',[\App\Http\Controllers\Web\Admin\EmployeController::class,'ajouter'])->name('admin.employe.ajouter');
    Route::post('employes/ajouter.html',[\App\Http\Controllers\Web\Admin\EmployeController::class,'register']);
    Route::get('employes/{matricule}/modifier.html',[\App\Http\Controllers\Web\Admin\EmployeController::class,'modifier'])->name('admin.employe.modifier');
    Route::post('employes/{matricule}/modifier.html',[\App\Http\Controllers\Web\Admin\EmployeController::class,'update']);
    Route::get('employe/{matricule}/fiche.html',[\App\Http\Controllers\Web\Admin\EmployeController::class,'fiche'])->name('admin.employe.fiche');
    //Utilisateurs
    Route::get('utilisateurs.html',[\App\Http\Controllers\Web\Admin\UtilisateurController::class,'index'])->name('admin.utilisateur.liste');
    Route::get('utilisateur/ajouter.html',[\App\Http\Controllers\Web\Admin\UtilisateurController::class,'ajouter'])->name('admin.utilisateur.ajouter');
    Route::post('utilisateur/ajouter.html',[\App\Http\Controllers\Web\Admin\UtilisateurController::class,'register']);
    Route::get('utilisateur/password/reset.html',[\App\Http\Controllers\Web\Admin\UtilisateurController::class,'reinitialiser'])->name('admin.utilisateur.reset');
    Route::post('utilisateur/password/reset.html', [\App\Http\Controllers\Web\Admin\UtilisateurController::class,'reset']);
});

Route::prefix('rh')->middleware('auth')->group(function (){
	Route::get('salaire/demarrer.html',[\App\Http\Controllers\Web\RH\SalaireController::class,'demarrage'])->name('rh.salaire');
	Route::post('salaire/demarrer.html',[\App\Http\Controllers\Web\RH\SalaireController::class,'start']);
	Route::get('salaire/{annee}/{mois}/reset',[\App\Http\Controllers\Web\RH\SalaireController::class,'reset'])->name('rh.salaire.reset');
	Route::get('salaire/{annee}/{mois}/confirm.html',[\App\Http\Controllers\Web\RH\SalaireController::class,'confirm'])->name('rh.salaire.confirm');
	Route::post('salaire/{annee}/{mois}/confirm.html',[\App\Http\Controllers\Web\RH\SalaireController::class,'cloturer']);
	Route::get('bulletin-paie/{annee}/{mois}/fiche.html',[\App\Http\Controllers\Web\RH\PaieController::class,'fichePaie'])->name('rh.paie');
	Route::post('bulletin-paie/{annee}/{mois}/fiche.html',[\App\Http\Controllers\Web\RH\PaieController::class,'savePaie']);
});

Route::get('/test.html','Mission\MissionController@reminder');

//Facturation
Route::prefix('factures')->middleware('auth')->group(function (){
    Route::get("liste.html",[\App\Http\Controllers\Web\Order\FactureController::class,"liste"])->name("facturation.liste.all");
    Route::get("normale/liste.html",[\App\Http\Controllers\Web\Order\FactureController::class,"listeFacture"])->name("facturation.liste.facture");
    Route::get("proforma/liste.html",[\App\Http\Controllers\Web\Order\FactureController::class,"listeProforma"])->name("facturation.liste.proforma");
    Route::get("proforma/nouvelle.html",[\App\Http\Controllers\Web\Order\ProformaController::class,"nouvelle"])->name("facturation.proforma.nouvelle");
    Route::post("proforma/nouvelle.html",[\App\Http\Controllers\Web\Order\ProformaController::class,"ajouter"]);
    Route::post("proforma/update",[\App\Http\Controllers\Web\Order\UpdateProforma::class,"modifier"])->name("facturation.proforma.modifier");
    Route::post("normale/make",[\App\Http\Controllers\Web\Order\FactureController::class,"makeNormal"])->name("facturation.switch.normal");
    Route::get("{reference}/annuler",[\App\Http\Controllers\Web\Order\FactureController::class,"annuler"])->name("facturation.switch.annuler");
    Route::get("{id}/livraison/make",[\App\Http\Controllers\Web\Order\BonLivraisonController::class,"makeBonLivraison"])->name("facturation.switch.livraison");
    Route::get("{reference}/option-email.html",[\App\Http\Controllers\Web\Order\SenderController::class,"choice"])->name("facturation.envoie.emailchoice");
    Route::get("{reference}/details.html",[\App\Http\Controllers\Web\Order\FactureController::class,"details"])->name("facturation.details");
});

//Versement
Route::prefix("versement")->middleware("auth")->group(function (){
    Route::get("mission/{code}/nouveau.html",[\App\Http\Controllers\Web\Money\VersementController::class,"nouveauVersement"])->name("versement.mission.ajouter");
    Route::post("mission/{code}/nouveau.html",[\App\Http\Controllers\Web\Money\VersementController::class,"ajouter"]);
    Route::post('facture/partenaire/client.html',[\App\Http\Controllers\Web\Money\ReglementController::class,'reglementClient'])->name('versement.facture.client');
    Route::post('facture/partenaire/fournisseur.html',[\App\Http\Controllers\Web\Money\ReglementController::class,'reglementFournisseur'])->name('versement.facture.fournisseur');
});

//Compte
Route::prefix('compte')->middleware('auth')->group(function (){
    Route::get('livre.html',[\App\Http\Controllers\Web\Money\CompteController::class,'registre'])->name('compte.registre');
	Route::post('livre.html',[\App\Http\Controllers\Web\Money\CompteController::class,'addNewSousCompte']);
    Route::get('sous-compte/synthese.html',[\App\Http\Controllers\Web\Money\CompteController::class,'syntheseSousCompte'])->name('compte.synthese');
    Route::get('sous-compte/{slug}/registre.html',[\App\Http\Controllers\Web\Money\CompteController::class,'detailsSousCompte'])->name('compte.souscompte');
	Route::post('sous-compte/{slug}/registre.html',[\App\Http\Controllers\Web\Money\CompteController::class,'addNewLine']);
    Route::post('sous-compte/{slug}/modifier-line.html',[\App\Http\Controllers\Web\Money\CompteController::class,'updateLine'])->name('compte.modifier');
    Route::get('sous-compte/{slug}/reset.html',[\App\Http\Controllers\Web\Money\CompteController::class,'showReset'])->name('compte.reset');
    Route::post('sous-compte/{slug}/reset.html',[\App\Http\Controllers\Web\Money\CompteController::class,'reset']);
    Route::get('sous-compte/{slug}/modifier.html',[\App\Http\Controllers\Web\Money\CompteController::class,'modifier'])->name("compte.update");
    Route::post('sous-compte/{slug}/modifier.html',[\App\Http\Controllers\Web\Money\CompteController::class,'updateCompte']);
    Route::get('nouvelle-depense.html',[\App\Http\Controllers\Web\Money\CompteController::class,'newSortieCompte'])->name('compte.sortie.new');
    Route::post('nouvelle-depense.html',[\App\Http\Controllers\Web\Money\CompteController::class,'addNewLine']);
});

//PDF
Route::prefix('impression')->middleware('auth')->group(function (){
    Route::get("{reference}/{state}/pdf.html",[\App\Http\Controllers\Web\Printer\PdfController::class,"imprimerPieceComptable"])->name("print.piececomptable");
    Route::get("vehicules",[\App\Http\Controllers\Web\Printer\PdfController::class,"imprimerVehicule"])->name("print.vehicule");
    Route::get("produits",[\App\Http\Controllers\Web\Printer\PdfController::class,"imprimerInventaire"])->name("print.produits");
    Route::get("sous-compte/{slug}/print",[\App\Http\Controllers\Web\Printer\PdfController::class,"imprimerSousCompte"])->name("print.souscompte");
    Route::get("client/{id}/point",[\App\Http\Controllers\Web\Printer\PdfController::class,"imprimerPointClient"])->name("print.pointclient");
    Route::get("fournisseur/{id}/bc",[\App\Http\Controllers\Web\Printer\PdfController::class,"imprimerBC"])->name("print.bc");
    Route::get("compte/synthese",[\App\Http\Controllers\Web\Printer\PdfController::class,"imprimerSyntheseCompte"])->name("print.compte.synthese");
});

//Partenaires
Route::prefix('partenaires')->middleware('auth')->group(function (){
    Route::get('{type}/liste.html',[\App\Http\Controllers\Web\Partenaire\RegisterController::class,'liste'])->name('partenaire.liste');
    Route::get('nouveau.html',[\App\Http\Controllers\Web\Partenaire\RegisterController::class,'nouveau'])->name('partenaire.nouveau');
    Route::post('nouveau.html',[\App\Http\Controllers\Web\Partenaire\RegisterController::class,'ajouter']);
    Route::get('{id}/modifier.html',[\App\Http\Controllers\Web\Partenaire\RegisterController::class,'modifier'])->name('partenaire.modifier');
    Route::post('{id}/modifier.html',[\App\Http\Controllers\Web\Partenaire\RegisterController::class,'update']);

    Route::get("client/{id}/details.html",[\App\Http\Controllers\Web\Partenaire\DetailsController::class,"ficheClient"])->name("partenaire.client");
    Route::get("fournisseur/{id}/details.html",[\App\Http\Controllers\Web\Partenaire\DetailsController::class,"ficheFournisseur"])->name("partenaire.fournisseur");

    Route::get("fournisseurs/factures/nouvelle.html",[\App\Http\Controllers\Web\Partenaire\FournisseurController::class,"newOrder"])->name("partenaire.fournisseur.new");
    Route::post("fournisseurs/factures/nouvelle.html",[\App\Http\Controllers\Web\Partenaire\FournisseurController::class,"addOrder"]);
    Route::get("fournisseurs/factures.html",[\App\Http\Controllers\Web\Partenaire\FournisseurController::class,"liste"])->name("partenaire.fournisseur.factures");
    Route::get("fournisseurs/factures/{id}/details.html",[\App\Http\Controllers\Web\Partenaire\FournisseurController::class,"details"])->name("partenaire.fournisseur.factures.details");
    Route::post("fournisseur/facture/switch",[\App\Http\Controllers\Web\Partenaire\FournisseurController::class,"switchPiece"])->name("partenaire.bc.switch");
    Route::get("fournisseur/facture/{id}/valide",[\App\Http\Controllers\Web\Partenaire\FournisseurController::class,"validePiece"])->name("partenaire.bc.valide");
});

//Stock
Route::prefix('stock')->middleware('auth')->group(function (){
	Route::get("mouvements.html",[\App\Http\Controllers\Web\Stock\MouvementController::class,"index"])->name("stock.produit.mouvement");
    Route::get("produit/nouveau.html",[\App\Http\Controllers\Web\Stock\ProduitController::class,"ajouter"])->name('stock.produit.ajouter');
    Route::post("produit/nouveau.html",[\App\Http\Controllers\Web\Stock\ProduitController::class,"addProduct"]);
    Route::get("produits/inventaire.html",[\App\Http\Controllers\Web\Stock\ProduitController::class,"liste"])->name('stock.produit.liste');
    Route::get("produits",[\App\Http\Controllers\Web\Stock\ProduitController::class,"liste"]);
    Route::get("produits/{reference}/modifier.html",[\App\Http\Controllers\Web\Stock\ProduitController::class,"modifier"])->name("stock.produit.modifier");
    Route::post("produits/{reference}/modifier.html",[\App\Http\Controllers\Web\Stock\ProduitController::class,"update"]);
    Route::get("produits/{reference}/supprimer.html",[\App\Http\Controllers\Web\Stock\ProduitController::class,"delete"])->name("stock.produit.supprimer");

    Route::post("produits/famille/add",[\App\Http\Controllers\Web\Stock\FamilleController::class,"addFamille"])->name("stock.produit.famille.ajouter");
    Route::get("produits/famille/liste.html",[\App\Http\Controllers\Web\Stock\FamilleController::class,"liste"])->name("stock.produit.famille");
    Route::get("produits/famille/{id}/modifier.html",[\App\Http\Controllers\Web\Stock\FamilleController::class,"modifier"])->name("stock.produit.famille.modifier");
    Route::post("produits/famille/{id}/modifier.html",[\App\Http\Controllers\Web\Stock\FamilleController::class,"update"]);
	Route::get("produits/ratio-commande.html",[\App\Http\Controllers\Web\Stock\ProduitController::class,"classProduct"])->name("stock.produit.ratio");
});

//Email
Route::prefix('email')->middleware('auth')->group(function (){
    Route::post('proforma/{reference}/send.html',[\App\Http\Controllers\Web\Mail\OrderController::class,'envoyerProforma'])->name("email.proforma");
});