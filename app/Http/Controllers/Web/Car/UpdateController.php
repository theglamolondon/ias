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
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class UpdateController extends Controller
{
    use VehiculeServices;

	/**
	 * @param string $immatriculation
	 *
	 * @return $this|\Illuminate\Contracts\View\Factory|RedirectResponse
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
    public function modifier(string $immatriculation)
    {
	    $this->authorize(Actions::UPDATE, collect([Service::DG, Service::GESTIONNAIRE_PL, Service::GESTIONNAIRE_VL,
		    Service::INFORMATIQUE, Service::COMPTABILITE]));

        $vehicule =  Vehicule::with("genre")
                    ->where("immatriculation",$immatriculation)
                    ->firstOrFail();

        if($vehicule != null)
        {
            $genres = Genre::all();

	        $chauffeurs = Chauffeur::with('employe')->get();

	        $status = [
	        	Statut::VEHICULE_ACTIF => Statut::getStatut(Statut::VEHICULE_ACTIF),
	        	Statut::VEHICULE_ENDOMAGE => Statut::getStatut(Statut::VEHICULE_ENDOMAGE),
	        	Statut::VEHICULE_VENDU => Statut::getStatut(Statut::VEHICULE_VENDU),
	        	Statut::VEHICULE_RESERVE => Statut::getStatut(Statut::VEHICULE_RESERVE),
	        	Statut::VEHICULE_AU_GARAGE => Statut::getStatut(Statut::VEHICULE_AU_GARAGE),
	        	Statut::VEHICULE_EN_MISSION => Statut::getStatut(Statut::VEHICULE_EN_MISSION),
	        ];

            return view("car.modification", compact("genres","vehicule", "chauffeurs", "status"));
        }

        return back()->withErrors("Véhicule introuvable");
    }
}
