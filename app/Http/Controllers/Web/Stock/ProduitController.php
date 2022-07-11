<?php

namespace App\Http\Controllers\Web\Stock;

use App\Famille;
use App\Http\Controllers\Controller;
use App\Metier\Behavior\Notifications;
use App\Metier\Security\Actions;
use App\Produit;
use App\Service;
use App\Services\Products\ProduitServices;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ProduitController extends Controller
{
  use ProduitServices;

	public function ajouter()
  {
      $familles = Famille::orderBy("libelle")->get();
      return view("produit.nouveau", compact("familles"));
  }

	/**
	 * @param Request $request
	 *
	 * @return $this|\Illuminate\Http\RedirectResponse|null
	 * @throws \Throwable
	 */
    public function addProduct(Request $request)
    {
	    $this->authorize(Actions::READ, collect([Service::DG, Service::INFORMATIQUE, Service::COMPTABILITE, Service::LOGISTIQUE, Service::ADMINISTRATION]));

	    $this->valideRequest($request);

        //On vérifie la que la référence n'est pas utilisée par un autre produit
        if($this->checkReferenceExist($request->input("reference"))){
            return redirect()->back()->withErrors("La référence du produit existe déjà.")
                ->withInput();
        }

	    $produit = $this->persitProduct($request->input());

        if($request->query("from") == "proforma"){ //On provient du popup de la fenêtre de nouvelle pro forma
            $json = json_encode([
                "id" => $produit->id,
                "modele" => $produit->getRealModele(),
                "price" => $produit->getPrice(),
                "libelle" => $produit->detailsForCommande(),
                "reference" => $produit->getReference(),
            ],JSON_UNESCAPED_UNICODE);

            $request->session()->flash("produit", str_replace("'","\'",str_replace("\\\\","\\", $json) ));
            return redirect()->route("stock.produit.ajouter", ["product" => "added"]);
        }else{
	        $notification = new Notifications();
	        $notification->add(Notifications::SUCCESS,"Produit ajouté avec succès !");
	        return redirect()->route("stock.produit.ajouter",["from" => "newOrder"])->with(Notifications::NOTIFICATION_KEYS_SESSION, $notification);
        }

    }

    /**
     * @param Request $request
     */
    protected function valideRequest(Request $request)
    {
        $this->validate($request,[
            "reference" => "required",
            "libelle" => "required",
            "prixunitaire" => "integer",
            "famille_id" => "required|exists:famille,id"
        ]);
    }

	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
    public function liste(Request $request)
    {
	    $this->authorize(Actions::READ, collect([Service::DG, Service::INFORMATIQUE, Service::COMPTABILITE, Service::LOGISTIQUE, Service::ADMINISTRATION]));

	    $keyword = null;

      $familles = Famille::orderBy("libelle")->get();

      $produits = Produit::with("famille")
          ->orderBy("reference", 'asc');

      $this->filter($produits, $request);

      $produits = $produits->paginate(25);

      return view("produit.liste", compact("produits","familles"));
    }

    public function classProduct(Request $request)
    {
	    $familles = Famille::orderBy("libelle")->get();

	    $produits = $this->getProduitRatio();

	    $graph = [];
		foreach ($this->getDataToCharts() as $line)
		{
			$graph['labels'][] = $line->raisonsociale;
			$graph['data'][] = $line->nbre;
		}

	    return view("produit.ratio", compact("produits","familles", "graph"));
    }


	/**
	 * @param $reference
	 *
	 * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
    public function modifier($reference)
    {
	    $this->authorize(Actions::READ, collect([Service::DG, Service::INFORMATIQUE, Service::COMPTABILITE, Service::LOGISTIQUE, Service::ADMINISTRATION]));

        $familles = Famille::orderBy("libelle")->get();
        try{
            $produit = Produit::with("famille")->where("reference", $reference)->firstOrFail();
        }catch (ModelNotFoundException $e){
           return back()->withErrors("Le produit spécifié n'existe pas.");
        }

        $ventes = $this->getVente($produit);

        return view("produit.modifier", compact("produit","familles", "ventes"));
    }

	/**
	 * @param $reference
	 * @param Request $request
	 *
	 * @return $this|\Illuminate\Http\RedirectResponse
	 * @throws \Throwable
	 */
    public function update($reference, Request $request)
    {
        $this->valideRequest($request);

        try{
            $produit = Produit::with("famille")->where("reference", $reference)->firstOrFail();
            $this->persitProduct($request->except('_token'), $produit);
        }catch (ModelNotFoundException $e){
            return back()->withErrors("Une erreur de mise à jour a été rencontrée.");
        }

        $notification = new Notifications();
        $notification->add(Notifications::SUCCESS,"Produit modifié avec succès !");
        return redirect()->route("stock.produit.liste")->with(Notifications::NOTIFICATION_KEYS_SESSION, $notification);
    }

    public function delete($reference, Request $request){
	    try{
		    $produit = Produit::with("famille")->where("reference", $reference)->firstOrFail();
		    $produit->delete();
	    }catch (ModelNotFoundException $e){
		    return back()->withErrors("Une erreur de suppression a été rencontrée.");
	    }

	    $notification = new Notifications();
	    $notification->add(Notifications::SUCCESS,"Produit supprimé avec succès !");
	    return back()->with(Notifications::NOTIFICATION_KEYS_SESSION, $notification);
    }
}
