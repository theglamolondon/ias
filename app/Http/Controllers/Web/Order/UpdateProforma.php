<?php

namespace App\Http\Controllers\Web\Order;

use App\Metier\Security\Actions;
use App\Service;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UpdateProforma extends Controller
{
	use Process;

	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 * @throws \Throwable
	 */
    public function modifier(Request $request)
    {
	    $this->authorize(Actions::UPDATE, collect([Service::DG, Service::ADMINISTRATION, Service::COMPTABILITE, Service::INFORMATIQUE, Service::LOGISTIQUE]));

	    $this->validateProformaRequest($request);

	    try{
		    $proforma = $this->getPieceComptableFromId($request->input("id"));

		    $this->updatePieceComptable($proforma, collect($request->except('lines','_token')));

            $this->deleteAllLines($proforma);

		    //Suppression des lignes de la facture
		    /*if(!$this->deleteAllLines($proforma)){
		    	throw new ModelNotFoundException("Erreur de suppresion des ligne de la proforma");
		    }*/

		    //Ajout des nouvelles lignes
		    $this->addLineToPieceComptable($proforma, $request->input('lines'));

		    return response()->json([
			    "code" => 1,
			    "message" => "Nouvelle pro forma référence $proforma->referenceproforma enregistrée avec succès !",
			    "action" => route("facturation.envoie.emailchoice",["reference" => urlencode($proforma->referenceproforma)])
		    ],200, [],JSON_UNESCAPED_UNICODE);

	    }catch(\Exception $e){

		    return response()->json(["code" => 0, "message" => $e->getMessage() ],400);

	    }
    }
}
