<?php

namespace App\Http\Controllers\Order;

use App\Metier\Behavior\Notifications;
use App\Metier\Security\Actions;
use App\Mission;
use App\Partenaire;
use App\PieceComptable;
use App\Produit;
use App\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;

class ProformaController extends Controller
{
    use Process;

	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
    public function nouvelle(Request $request)
    {
	    $this->authorize(Actions::CREATE, collect([Service::DG, Service::ADMINISTRATION, Service::COMPTABILITE, Service::INFORMATIQUE, Service::LOGISTIQUE]));

        $lignes = new Collection();
        $commercializables = null;
        $proforma = null;

        if($request->query(Notifications::SOURCE) == Notifications::CREATE_FROM_PROFORMA
          || $request->query(Notifications::SOURCE) == Notifications::UPDATE_FROM_PROFORMA)
        {
			$proforma = $this->getPieceComptableFromReference($request->query('ID'));

			$proforma->lignes->each(function ($item, $key) use ($lignes){
				$produit = new Produit();
		        $produit->reference = $item->reference;
		        $produit->libelle = $item->designation;
				$produit->prixunitaire = $item->prixunitaire;
				$produit->modele = $item->modele;
				$produit->modele_id = $item->modele_id;
				$produit->quantite = $item->quantite;
				$produit->remise = $item->remise;

				$produit->id = $item->modele_id; //ID du produit et non de la ligne
		        $lignes->push($produit);
	        });
        }

        if($commercializables == null)//Facture sans intention préalable
        {
            $commercializables = $this->getCommercializableList($request);
        }

	    $updateUrl = null;
        if($request->query(Notifications::SOURCE) == Notifications::UPDATE_FROM_PROFORMA ){
       	    $updateUrl = route("facturation.proforma.modifier");
        }

        $partenaires = $this->getPartenaireList($request);

        return view('order.proforma', compact("commercializables", "partenaires", "lignes", "proforma", "updateUrl"));
    }

	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Throwable
	 */
    public function ajouter(Request $request)
    {
	    $this->authorize(Actions::CREATE, collect([Service::DG, Service::ADMINISTRATION, Service::COMPTABILITE, Service::INFORMATIQUE, Service::LOGISTIQUE]));

        $this->validateProformaRequest($request);

        try{

	        $partenaire = new Partenaire(['id' => $request->input("partenaire_id")]);

	        $piececomptable = $this->createPieceComptable(PieceComptable::PRO_FORMA, $partenaire, collect($request->only(["montantht", "isexonere", "conditions", "validite", "delailivraison", "objet", "type_piece"])));

	        $this->addLineToPieceComptable($piececomptable, $request->input("lines"));

	        $notification = new Notifications();
	        $notification->add(Notifications::SUCCESS,"Votre proforma n° $piececomptable->referenceproforma a été prise en compte.");

	        return response()->json([
		        "code" => 1,
		        "message" => "Nouvelle pro forma référence $piececomptable->referenceproforma enregistrée avec succès !",
		        "action" => route("facturation.envoie.emailchoice",["reference" => urlencode($piececomptable->referenceproforma)])
	        ],200, [],JSON_UNESCAPED_UNICODE);

        }catch(\Exception $e){

	        return response()->json(["code" => 0, "message" => $e->getMessage() ],400);

        }
    }
}