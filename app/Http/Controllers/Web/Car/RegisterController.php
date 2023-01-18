<?php

namespace App\Http\Controllers\Web\Car;

use App\Chauffeur;
use App\Genre;
use App\Metier\Processing\VehiculeManager;
use App\Metier\Security\Actions;
use App\Service;
use App\Services\VehiculeServices;
use App\Statut;
use App\Vehicule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    use VehiculeServices;

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
    public function index()
    {
	    $this->authorize(Actions::READ, collect([Service::DG, Service::ADMINISTRATION, Service::INFORMATIQUE, Service::GESTIONNAIRE_VL, Service::GESTIONNAIRE_PL]));

      $vehicules = $this->getListeVehiculeActif();
      $titre = "Véhicules actifs";

      return view('car.liste',compact('vehicules', "titre"));
    }

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
    public function indexReformes()
    {
	    $this->authorize(Actions::READ, collect([Service::DG, Service::ADMINISTRATION, Service::INFORMATIQUE, Service::GESTIONNAIRE_VL, Service::GESTIONNAIRE_PL]));

      $vehicules = $this->getListeVehiculeInactif();
      $titre = "Véhicules reformés";

      return view('car.liste',compact('vehicules', "titre"));
    }

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
    public function showNewFormView()
    {
	    $this->authorize(Actions::CREATE, collect([Service::DG, Service::ADMINISTRATION, Service::INFORMATIQUE, Service::GESTIONNAIRE_VL, Service::GESTIONNAIRE_PL]));

        $genres = Genre::all();

        $chauffeurs = Chauffeur::with('employe')->get();

        return view('car.nouveau', compact('genres', 'chauffeurs'));
    }
}
