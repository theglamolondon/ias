<?php

namespace App\Http\Controllers\Web\Order;

use App\Application;
use App\PieceComptable;
use App\Produit;
use App\Statut;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;

class BonLivraisonController extends Controller
{
	/**
	 * @param $id
	 *
	 * @return $this|\Illuminate\Http\RedirectResponse
	 */
    public function makeBonLivraison($id)
    {
        try{
            $pieceComptable = PieceComptable::findOrFail($id);

            if($pieceComptable->referencebl == null) {
                $pieceComptable->referencebl = Application::getNumeroBL(true);
                $pieceComptable->creationbl = Carbon::now()->toDateTimeString();
                $pieceComptable->etat = Statut::PIECE_COMPTABLE_FACTURE_AVEC_BL;
                $pieceComptable->save();

                //Livrer le matériel et déduire du stock
	            $this->updateStock($pieceComptable);
            }

            return redirect()->route("print.piececomptable", ["reference" => $pieceComptable->referencebl, "state" => PieceComptable::BON_LIVRAISON]);

        }catch (ModelNotFoundException $e){
            return back()->withErrors("La facture est introuvable ! Veuillez recommencer SVP");
        }
    }

    private function updateStock(PieceComptable $facture)
    {
    	foreach ($facture->lignes as $ligne){
    		try{
    			if(($ligne->modele == Produit::class) && ($ligne->quantite > 0) ){
				    $produit = Produit::findOrFail($ligne->modele_id);
				    $produit->stock -= $ligne->quantite;
				    $produit->save();
			    }
		    }catch (ModelNotFoundException $e){
    			logger($e->getMessage()
			           ." at file ". $e->getFile()
			           ." line ".$e->getLine());
		    }
	    }
    }
}