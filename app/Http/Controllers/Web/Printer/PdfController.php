<?php

namespace App\Http\Controllers\Web\Printer;

use App\Http\Controllers\Web\Money\Tresorerie;
use App\LigneCompte;
use App\Partenaire;
use App\Pdf\PdfMaker;
use App\PieceComptable;
use App\PieceFournisseur;
use App\Produit;
use App\Service;
use App\Services\FactureServices;
use App\Statut;
use App\Vehicule;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class PdfController extends Controller
{
    use PdfMaker, Tresorerie, FactureServices;

    public function imprimerVehicule()
    {
	    $d = date("d_m_Y");
	    $vehicules = Vehicule::with('genre')->where("status", Statut::VEHICULE_ACTIF)->get();

	    $liste = Pdf::loadView('pdf.vehicules',compact("vehicules"))->setPaper('a4','portrait');
	    return $liste->stream("Liste des véhicule au $d .pdf");
    }

    public function imprimerInventaire()
    {
    	$d = date("d_m_Y");

	    $produits = Produit::with("famille")->orderBy("reference")->get();

	    $liste = Pdf::loadView('pdf.produits',compact("produits"))->setPaper('a4','portrait');
	    return $liste->stream("Inventaire stock au $d .pdf");
    }

    public function imprimerSousCompte(string $slug)
    {
	    $debut = null;
	    $fin = null;

	    $souscompte = $this->getSousCompteFromSlug($slug);

	    $lignes = LigneCompte::with('utilisateur.employe')
	                         ->where('compte_id','=', $souscompte->id)
	                         ->orderBy('dateaction', 'desc');

	    $lignes = $this->extractData($lignes, \request(), $debut, $fin)->get();

	    $liste = Pdf::loadView('pdf.souscompte', compact("lignes", "souscompte"))->setPaper('a4','portrait');
	    return $liste->stream("Situation sous-compte {$souscompte->libelle}.pdf");
    }

    public function imprimerSyntheseCompte(){
	    $debut = null;
	    $fin = null;

	    $lignes = LigneCompte::with('utilisateur.employe',"compte")
	                         ->join("compte", "compte.id","=","compte_id")
	                         ->join("employe","employe.id", "=", "compte.employe_id")
	                         ->join("service", "service.id", "=", "employe.service_id");

	    if(Auth::user()->employe->service->code != Service::DG){
		    $lignes = $lignes->where('service.code','<>', Service::DG);
	    }

	    $lignes = $this->extractData($lignes, \request(), $debut, $fin)->get();

	    $liste = Pdf::loadView('pdf.synthese', compact("lignes"))->setPaper('a4','portrait');
	    return $liste->stream("Synyhèse compte.pdf");
    }

    public function imprimerPointClient(int $id){
	    $partenaire = Partenaire::find($id);
	    $pieces = PieceComptable::with('utilisateur','moyenPaiement')
	                            ->where("partenaire_id", $partenaire->id);

	    $this->getParameters($pieces);

	    $pieces = $pieces->orderBy('creationproforma')
	                     ->whereNotNull("referencefacture")
	                     ->orderBy('creationfacture')
	                     ->get();

	    $liste = Pdf::loadView('pdf.point-client', compact("partenaire","pieces"))->setPaper('a4','portrait');
	    return $liste->stream("Point client {$partenaire->raisonsociale}.pdf");
    }

    public function imprimerBC(string $id){

    	$bc = PieceFournisseur::with("lignes","partenaire","utilisateur.employe")->find($id);

	    $liste = Pdf::loadView('pdf.boncommande', compact("bc"))->setPaper('a4','portrait');
	    return $liste->stream("Bon de commande {$bc->numerobc}.pdf");
    }
}
