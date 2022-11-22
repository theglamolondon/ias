<?php

namespace App\Http\Controllers\Web\Mission;

use App\Chauffeur;
use App\Metier\Behavior\Notifications;
use App\Metier\Security\Actions;
use App\MissionPL;
use App\Partenaire;
use App\Service;
use App\Services\MissionPLServices;
use App\Statut;
use App\Vehicule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PlController extends Controller
{
	use Process, MissionPLServices;

	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
	public function nouvellePL(Request $request)
	{
		$this->authorize(Actions::READ, collect([Service::DG, Service::INFORMATIQUE,
			Service::COMPTABILITE, Service::GESTIONNAIRE_PL]));

		$vehicules = $this->listeVehiculeSelection($request, Vehicule::PL);
		$chauffeurs = Chauffeur::with('employe')->get();

		$partenaires = Partenaire::where("isclient",true)
		                         ->orderBy("raisonsociale")
		                         ->get();
		return view('mission.pl.nouvelle',compact('vehicules','chauffeurs', "partenaires"));
	}

	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
	public function ajouterPL(Request $request)
	{
		$this->authorize(Actions::READ, collect([Service::DG, Service::INFORMATIQUE,
			Service::COMPTABILITE, Service::GESTIONNAIRE_PL]));

           return $this->ajouterPoidLourd($request);
	}

	private function createPL(Request $request)
	{
		return $this->createPoidLourd($request);
	}
}
