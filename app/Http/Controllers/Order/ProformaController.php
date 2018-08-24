<?php

namespace App\Http\Controllers\Order;

use App\Metier\Behavior\Notifications;
use App\Mission;
use App\Partenaire;
use App\PieceComptable;
use App\Produit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;

class ProformaController extends Controller
{
    use Process;

    public function nouvelle(Request $request)
    {
        $lignes = new Collection();
        $commercializables = null;
        $proforma = null;

        //Si une mission est passée en session
        if( $request->query("from") == Notifications::MISSION_OBJECT )
        {
            $commercializables = $request->session()->get(Notifications::MISSION_OBJECT);

            if(!is_array($commercializables)){
                $commercializables = collect([$commercializables]);
            }

            $lignes = $commercializables;

        }elseif($request->query("from") == Notifications::CREATE_FROM_PROFORMA){

			$proforma = $this->getPieceComptableFromReference($request->query('ID'));

			$proforma->lignes->each(function ($item, $key) use ($lignes){
				$produit = new Produit();
		        $produit->reference = $item->reference;
		        $produit->libelle = $item->designation;
				$produit->prixunitaire = $item->prixunitaire;
				$produit->modele = $item->modele;
				$produit->modele_id = $item->modele_id;
				$produit->quantite = $item->quantite;

				$produit->id = $item->modele_id; //ID du produit et non de la ligne
		        $lignes->push($produit);
	        });

	        //dd($lignes);
        }

        if($commercializables == null){ //Facture sans intention préalable

            $commercializables = $this->getCommercializableList($request);
        }


        $partenaires = $this->getPartenaireList($request);

        return view('order.proforma', compact("commercializables", "partenaires", "lignes", "proforma"));
    }

    public function ajouter(Request $request)
    {
        $validator = $this->validateProformaRequest($request);

        if($validator->fails()){
            return response()->json(["code" => 0, "message" => "La validation de la pro forma a échouée"],400);
        }

        $paretenaire = new Partenaire(['id' => $request->input("partenaire_id")]);

        $piececomptable = $this->createPieceComptable(PieceComptable::PRO_FORMA, $paretenaire, collect($request->only(["montantht", "isexonere", "conditions", "validite", "delailivraison", "objet"])));

        $this->addLineToPieceComptable($piececomptable,$request->input("lignes"));

        $notification = new Notifications();
        $notification->add(Notifications::SUCCESS,"Votre proforma n° $piececomptable->referenceproforma a été prise en compte.");

        return response()->json([
            "code" => 1,
            "message" => "Nouvelle pro forma référence $piececomptable->referenceproforma enregistrée avec succès !",
            "action" => route("facturation.envoie.emailchoice",["reference" => urlencode($piececomptable->referenceproforma)])
        ],200, [],JSON_UNESCAPED_UNICODE);
    }
}