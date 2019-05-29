<?php

namespace App\Http\Controllers\Car;

use App\Chauffeur;
use App\Genre;
use App\Metier\Processing\VehiculeManager;
use App\Metier\Security\Actions;
use App\Service;
use App\Vehicule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UpdateController extends Controller
{
    use VehiculeManager;

	/**
	 * @param string $immatriculation
	 *
	 * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
    public function modifier(string $immatriculation)
    {
	    $this->authorize(Actions::UPDATE, collect([Service::DG, Service::ADMINISTRATION, Service::INFORMATIQUE, Service::COMPTABILITE]));

        $vehicule =  Vehicule::with("genre")
                    ->where("immatriculation",$immatriculation)
                    ->firstOrFail();

        if($vehicule != null)
        {
            $genres = Genre::all();

	        $chauffeurs = Chauffeur::with('employe')->get();

            return view("car.modification", compact("genres","vehicule", "chauffeurs"));
        }

        return back()->withErrors("Véhicule introuvable");
    }
}
