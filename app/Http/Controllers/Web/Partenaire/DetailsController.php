<?php

namespace App\Http\Controllers\Web\Partenaire;

use App\MoyenReglement;
use App\Partenaire;
use App\PieceFournisseur;
use App\Services\FactureServices;
use App\Services\PartnerServices;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DetailsController extends Controller
{
	use FactureServices, PartnerServices;

    public function ficheClient(int $id)
    {
        $partenaire = Partenaire::find($id);

        $pieces = $this->getDetailsByClientPartner($partenaire->id);

        $moyenReglements = MoyenReglement::all();

        $status = $this->getStatus();

        return view('partenaire.client', compact("partenaire","pieces", 'moyenReglements', "status"));
    }

    public function ficheFournisseur(int $id)
    {
        $partenaire = Partenaire::find($id);

        $pieces = PieceFournisseur::with('utilisateur','moyenPaiement')
	        ->where("partenaire_id", $partenaire->id);

        $this->getParameters($pieces);

	    $pieces = $pieces->orderBy('datepiece','desc')
            ->paginate(30);

	    $CA = $this->getSommeTotale($partenaire);

        $moyenReglements = MoyenReglement::all();

	    $status = $this->getStatus();

        return view('partenaire.fournisseur', compact("partenaire", "pieces", "moyenReglements", "status","CA"));
    }
}
