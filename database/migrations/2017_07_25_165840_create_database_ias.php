<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDatabaseIas extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create("application", function (Blueprint $table){
			$table->string("version",3)->default('1');
			$table->string("sendmail",1)->default("Y");
			$table->string("mailcopy")->default("commercial@ivoireautoservices.net");
			$table->integer("numeroproforma")->default(1);
			$table->integer("numerobl")->default(1);
			$table->integer("numerofacture")->default(1);
			$table->integer("numeromission")->default(1);
			$table->integer("numerobc")->default(1);
			$table->string("prefix",10)->default("");
			$table->primary('version');
		});
		Schema::create("service", function(Blueprint $table){
			$table->increments("id");
			$table->string("code",5);
			$table->string("libelle");
		});
		Schema::create("employe", function(Blueprint $table){
			$table->increments("id");
			$table->string("matricule",25);
			$table->string("nom",30);
			$table->string("prenoms",60)->nullable();
			$table->string("photo",70)->default('user_default.png');
			$table->string('contact')->nullable();
			$table->date("datenaissance");
			$table->date("dateembauche");
			$table->date("datesortie")->nullable();
			$table->integer("basesalaire");
			$table->string("pieceidentite");
			$table->string("rib")->nullable();
			$table->string("cnps",100)->nullable();
			$table->smallInteger('status')->default(\App\Statut::PERSONNEL_ACTIF);
			$table->integer("service_id",false,true);
			$table->foreign("service_id")->references("id")->on("service");
		});
		Schema::create("utilisateur", function(Blueprint $table){
			$table->integer("employe_id",true,true);
			$table->string("login")->unique();
			$table->rememberToken();
			$table->string("password");
			$table->string("satut",3)->default(\App\Statut::TYPE_UTILISATEUR.\App\Statut::ETAT_ACTIF.\App\Statut::AUTRE_NON_DEFINI);
			$table->foreign("employe_id")->references("id")->on("employe");
		});
		Schema::create("chauffeur",function (Blueprint $table){
			$table->unsignedInteger('employe_id');
			$table->string('permis');
			$table->date('expiration_c')->nullable();
			$table->date('expiration_d')->nullable();
			$table->date('expiration_e')->nullable();
			$table->foreign("employe_id")->references("id")->on("employe");
		});
		Schema::create("typeintervention",function (Blueprint $table){
			$table->increments('id');
			$table->string('libelle');
		});
		Schema::create("genre",function (Blueprint $table){
			$table->increments('id');
			$table->string('libelle');
			$table->string('categorie',2)->default('VL'); //VL et PL
		});
		Schema::create("vehicule",function (Blueprint $table){
			$table->increments("id");
			$table->integer("coutachat")->nullable();
			$table->date("dateachat")->nullable();
			$table->string("cartegrise");
			$table->string("immatriculation");
			$table->string('marque')->nullable();
			$table->date('visite');
			$table->date('assurance');
			$table->string('typecommercial');
			$table->string('couleur')->nullable();
			$table->string('energie')->nullable();
			$table->integer('nbreplace')->default(5);
			$table->integer('puissancefiscale')->nullable();
			$table->smallInteger('status')->default(\App\Statut::VEHICULE_ACTIF);
			$table->unsignedInteger('genre_id');
			$table->unsignedInteger('chauffeur_id')->nullable();
			$table->foreign('chauffeur_id')->references('employe_id')->on('chauffeur');
			$table->foreign("genre_id")->references("id")->on("genre");
		});
		Schema::create("moyenreglement",function (Blueprint $table){
			$table->increments('id');
			$table->string('libelle');
		});
		Schema::create("partenaire",function (Blueprint $table){
			$table->increments('id');
			$table->string('raisonsociale');
			$table->string('comptecontribuable')->nullable();
			$table->string('telephone')->nullable();
			$table->boolean('isclient')->default(true);
			$table->boolean('isfournisseur')->default(false);
			$table->text('contact')->nullable();
		});
		Schema::create("reglement",function (Blueprint $table){
			$table->increments('id');
			$table->string('reference');
			$table->dateTime('datereglement');
			$table->integer('montant');
			$table->string('details')->nullable();
			$table->unsignedInteger('partenaire_id');
			$table->unsignedInteger('moyenreglement_id');
			$table->foreign('partenaire_id','fk_reglement_partenaire')->references('id')->on('partenaire');
			$table->foreign('moyenreglement_id','fk_reglement_moyreglmt')->references('id')->on('moyenreglement');
		});
		Schema::create("famille",function (Blueprint $table){
			$table->increments('id');
			$table->string('libelle');
		});
		Schema::create("produit",function (Blueprint $table){
			$table->increments('id');
			$table->string('reference')->unique();
			$table->string('libelle');
			$table->integer("prixunitaire")->default(0);
			$table->integer('stock')->default(0);
			$table->boolean('isdisponible')->default(true);
			$table->unsignedInteger('famille_id');
			$table->foreign('famille_id')->references('id')->on('famille');
			$table->dropSoftDeletes();
		});
		Schema::create("piececomptable",function (Blueprint $table){
			$table->increments('id');
			$table->smallInteger('type_piece')->nullable(false)->default(\App\PieceComptable::TYPE_FACTURE_PIECE);
			$table->string("objet",150);
			$table->string("referencebc")->nullable();
			$table->string('referenceproforma')->unique();
			$table->string('referencebl')->unique()->nullable();
			$table->string('referencefacture')->unique()->nullable();
			$table->dateTime('creationproforma');
			$table->dateTime('creationbl')->nullable();
			$table->dateTime('creationfacture')->nullable();
			$table->string("validite");
			$table->string("delailivraison");
			$table->string('etat');
			$table->integer('montantht');
			$table->float('remise',3,2)->default(1.00);
			$table->float('tva',3,2)->default(0.18);
			$table->boolean('isexonere');
			$table->string("conditions")->nullable();
			$table->date('datereglement')->nullable();
			$table->unsignedInteger('moyenpaiement_id')->nullable();
			$table->string('remarquepaiement')->nullable();
			$table->unsignedInteger('partenaire_id');
			$table->unsignedInteger('utilisateur_id');
			$table->foreign('moyenpaiement_id')->references('id')->on('moyenreglement');
			$table->foreign('partenaire_id')->references('id')->on('partenaire');
			$table->foreign('utilisateur_id')->references('employe_id')->on('utilisateur');
		});
		Schema::create("mission",function (Blueprint $table){
			$table->increments('id');
			$table->string('code')->unique();
			$table->date('debutprogramme');
			$table->date('debuteffectif');
			$table->date('finprogramme');
			$table->date('fineffective');
			$table->string('observation')->nullable();
			$table->integer('perdiem');
			$table->integer('perdiemverse')->default(0);
			$table->integer('montantjour')->default(0);
			$table->integer('soldeperdiem')->default(0);
			$table->boolean('soustraite')->default(false);
			$table->string('immat_soustraitance')->nullable();
			$table->string('status',4);
			$table->unsignedInteger('chauffeur_id')->nullable();
			$table->unsignedInteger('vehicule_id')->nullable();
			$table->unsignedInteger('client');
			$table->unsignedInteger('soustraitant')->nullable();
			$table->unsignedInteger('piececomptable_id')->nullable();
			$table->foreign('chauffeur_id')->references('employe_id')->on('chauffeur');
			$table->foreign('vehicule_id')->references('id')->on('vehicule');
			$table->foreign('client')->references('id')->on('partenaire');
			$table->foreign('soustraitant')->references('id')->on('partenaire');
			$table->foreign('piececomptable_id')->references('id')->on('piececomptable');
		});
		Schema::create("mission_pl", function (Blueprint $table){
			$table->increments("id");
			$table->string('code')->unique();
			$table->date("datedebut");
			$table->date("datefin")->nullable();
			$table->string('destination');
			$table->integer('kilometrage');
			$table->integer('carburant');
			$table->string('status',4);
			$table->string('observation')->nullable();
			$table->boolean('soustraite')->default(false);
			$table->unsignedInteger('partenaire_id');
			$table->unsignedInteger('chauffeur_id')->nullable();
			$table->unsignedInteger('vehicule_id')->nullable();
			$table->unsignedInteger('piececomptable_id')->nullable();
			$table->foreign('partenaire_id')->references('id')->on('partenaire');
			$table->foreign('vehicule_id')->references('id')->on('vehicule');
			$table->foreign('chauffeur_id')->references('employe_id')->on('chauffeur');
			$table->foreign('piececomptable_id')->references('id')->on('piececomptable');
		});
		Schema::create("versement", function (Blueprint $table){
			$table->unsignedInteger("mission_id");
			$table->unsignedInteger("employe_id");
			$table->dateTime("dateversement");
			$table->unsignedInteger("moyenreglement_id");
			$table->integer("montant");
			$table->string("commentaires")->nullable();
			$table->unsignedInteger('operateur_id')->nullable();
			$table->foreign('mission_id')->references('id')->on('mission');
			$table->foreign('employe_id')->references('employe_id')->on('chauffeur');
			$table->foreign('moyenreglement_id')->references('id')->on('moyenreglement');
			$table->foreign('operateur_id')->references('employe_id')->on('utilisateur');
			$table->primary(["mission_id", "employe_id", "dateversement"]);
		});
		Schema::create("lignepiece",function (Blueprint $table){
			$table->bigIncrements('id');
			$table->string('reference',150)->default("#");
			$table->string('designation');
			$table->integer('prixunitaire');
			$table->string('modele');
			$table->integer('modele_id');
			$table->integer('quantite');
			$table->integer('quantite_periode')->default(1);
			$table->string('periode')->nullable(true);
			$table->integer('quantitelivree')->default(0);
			$table->unsignedInteger('piececomptable_id');
			$table->foreign('piececomptable_id')->references('id')->on('piececomptable');
		});
		Schema::create("compte", function (Blueprint $table){
			$table->increments('id');
			$table->string('libelle');
			$table->string('slug')->unique();
			$table->dateTime('datecreation');
			$table->string('commentaire')->nullable();
			$table->unsignedInteger("employe_id")->nullable();
			$table->foreign('employe_id')->references('employe_id')->on('utilisateur');
			$table->softDeletes();
		});
		Schema::create("lignecompte", function (Blueprint $table){
			$table->bigIncrements('id');
			$table->date("dateecriture");
			$table->dateTime("dateaction");
			$table->string('objet');
			$table->integer('montant')->default(0);
			$table->integer('balance');
			$table->string('observation')->nullable();
			$table->unsignedInteger("employe_id");
			$table->unsignedInteger("compte_id");
			$table->foreign('employe_id')->references('employe_id')->on('utilisateur');
			$table->foreign('compte_id')->references('id')->on('compte');
			$table->softDeletes();
		});
		Schema::create("piecefournisseur", function (Blueprint $table){
			$table->bigIncrements('id');
			$table->date("datepiece");
			$table->string("reference")->nullable()->unique();
			$table->string("numerobc")->nullable()->unique();
			$table->string("objet");
			$table->integer('montanttva')->default(0);
			$table->integer('montantht')->default(0);
			$table->string('observation')->nullable();
			$table->string('statut');
			$table->date('datereglement')->nullable();
			$table->unsignedInteger('moyenpaiement_id')->nullable();
			$table->string('remarquepaiement')->nullable();
			$table->unsignedInteger("partenaire_id");
			$table->unsignedInteger("employe_id");
			$table->foreign('partenaire_id')->references('id')->on('partenaire');
			$table->foreign('employe_id')->references('employe_id')->on('utilisateur');
		});
		Schema::create("lignepiecefournisseur", function (Blueprint $table) {
			$table->increments('id');
			$table->integer("prix");
			$table->integer("quantite");
			$table->string("modele",100)->default(\App\Produit::class);
			$table->string("designation");
			$table->unsignedBigInteger("piecefournisseur_id");
			$table->unsignedInteger("produit_id");
			$table->foreign('piecefournisseur_id', "fk_piecefour")->references('id')->on('piecefournisseur');
			//$table->foreign('produit_id')->references('id')->on('produit');
		});
		Schema::create("intervention",function (Blueprint $table){
			$table->increments('id');
			$table->dateTime('debut');
			$table->dateTime('fin');
			$table->integer('cout');
			$table->text('details')->nullable();
			$table->unsignedInteger('typeintervention_id');
			$table->unsignedInteger('vehicule_id');
			$table->unsignedInteger("partenaire_id")->nullable();
			$table->unsignedBigInteger('piecefournisseur_id')->nullable();
			$table->foreign("piecefournisseur_id")->references('id')->on('piecefournisseur');
			$table->foreign("partenaire_id")->references('id')->on('partenaire');
			$table->foreign("vehicule_id")->references("id")->on("vehicule");
			$table->foreign("typeintervention_id")->references("id")->on("typeintervention");
		});
		Schema::create("salaire", function (Blueprint $table){
			$table->integer('mois');
			$table->integer('annee');
			$table->smallInteger('statut');
			$table->primary(['mois','annee']);
		});
		Schema::create("bulletin", function (Blueprint $table){
			$table->text('lignes');
			$table->integer('nap')->default(0);
			$table->integer('mois');
			$table->integer('annee');
			$table->unsignedInteger('employe_id');
			$table->primary(['mois','annee','employe_id']);
			$table->foreign('employe_id')->references('id')->on('employe');
		});
		Schema::create("rappels", function (Blueprint $table){
			$table->id('id');
			$table->integer('nap')->default(0);
			$table->integer('mois');
			$table->integer('annee');
			$table->unsignedInteger('employe_id');
			$table->foreign('employe_id')->references('id')->on('employe');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('bulletin');
		Schema::dropIfExists('salaire');
		Schema::dropIfExists('lignepiecefournisseur');
		Schema::dropIfExists('piecefournisseur');
		Schema::dropIfExists('lignecompte');
		Schema::dropIfExists('compte');
		Schema::dropIfExists('lignepiece');
		Schema::dropIfExists('versement');
		Schema::dropIfExists('mission');
		Schema::dropIfExists('mission_pl');
		Schema::dropIfExists('piececomptable');
		Schema::dropIfExists('produit');
		Schema::dropIfExists('famille');
		Schema::dropIfExists('reglement');
		Schema::dropIfExists('partenaire');
		Schema::dropIfExists('moyenreglement');
		Schema::dropIfExists('intervention');
		Schema::dropIfExists('vehicule');
		Schema::dropIfExists('genre');
		Schema::dropIfExists('typeintervention');
		Schema::dropIfExists('chauffeur');
		Schema::dropIfExists('utilisateur');
		Schema::dropIfExists('employe');
		Schema::dropIfExists('service');
		Schema::dropIfExists('application');
	}
}