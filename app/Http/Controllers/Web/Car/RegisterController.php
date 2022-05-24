<?php

namespace App\Http\Controllers\Web\Car;

use App\Chauffeur;
use App\Genre;
use App\Metier\Processing\VehiculeManager;
use App\Metier\Security\Actions;
use App\Service;
use App\Statut;
use App\Vehicule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    use VehiculeManager;

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
    public function index()
    {
	    $this->authorize(Actions::READ, collect([Service::DG, Service::ADMINISTRATION, Service::INFORMATIQUE, Service::GESTIONNAIRE_VL, Service::GESTIONNAIRE_PL]));

        $vehicules = Vehicule::with('genre')->where("status", Statut::VEHICULE_ACTIF);

        if(Auth::user()->employe->service->code == Service::GESTIONNAIRE_PL){
	        $vehicules = $vehicules->join("genre","genre.id","=","vehicule.genre_id")
	                     ->where("genre.categorie", "=", "PL");
        }elseif(Auth::user()->employe->service->code == Service::GESTIONNAIRE_VL){
	        $vehicules = $vehicules->join("genre","genre.id","=","vehicule.genre_id")
	                     ->where("genre.categorie", "=", "VL");
        }

        if(\request()->has("immatriculation") && !empty(\request()->query("immatriculation"))){
			$vehicules = $vehicules->where("immatriculation","like","%".\request()->query("immatriculation")."%");
        }

        $vehicules = $vehicules->paginate();

        return view('car.liste',compact('vehicules'));
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
