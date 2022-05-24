<?php
/**
 * Created by PhpStorm.
 * User: SUPPORT.IT
 * Date: 26/08/2018
 * Time: 10:39
 */

namespace App\Http\Controllers\Web\Stock;


use App\Famille;
use App\Http\Controllers\Controller;
use App\Metier\Security\Actions;
use App\Service;

class MouvementController extends Controller {

	/**
	 * ProduitController constructor.
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
	public function index() {
		$this->authorize(Actions::CREATE, collect([Service::DG, Service::INFORMATIQUE, Service::COMPTABILITE, Service::LOGISTIQUE, Service::ADMINISTRATION]));

		$familles = Famille::orderBy("libelle")->get();
		return view('produit.mouvement', compact("familles"));
	}
}