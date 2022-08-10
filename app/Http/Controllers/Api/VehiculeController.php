<?php
/**
 * Created by PhpStorm.
 * User: Wilfried KOFFI
 * Date: 07/07/2022
 * Time: 01:18
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Metier\Security\Actions;
use App\Service;
use App\Services\VehiculeServices;
use App\Statut;
use Illuminate\Http\Request;

class VehiculeController extends Controller
{
  use VehiculeServices;

  public function searchVehiculeVL(Request $request){
    //$this->authorize(Actions::READ, collect([Service::DG, Service::ADMINISTRATION, Service::INFORMATIQUE, Service::GESTIONNAIRE_VL, Service::GESTIONNAIRE_PL]));
    return $this->getListeVehiculeActif();
  }

  public function listeDisponible(Request $request){
    //$this->authorize(Actions::READ, collect([Service::DG, Service::ADMINISTRATION, Service::INFORMATIQUE, Service::GESTIONNAIRE_VL, Service::GESTIONNAIRE_PL]));
    return $this->getListeVehiculesByStatus([Statut::VEHICULE_ACTIF, Statut::VEHICULE_EN_MISSION, Statut::VEHICULE_RESERVE]);
  }

  public function listeIndisponible(Request $request){
    //$this->authorize(Actions::READ, collect([Service::DG, Service::ADMINISTRATION, Service::INFORMATIQUE, Service::GESTIONNAIRE_VL, Service::GESTIONNAIRE_PL]));
    return $this->getListeVehiculesByStatus([Statut::VEHICULE_ENDOMAGE, Statut::VEHICULE_VENDU, Statut::VEHICULE_AU_GARAGE]);
  }

  public function details(string $immatriculation){
    //$this->authorize(Actions::READ, collect([Service::DG, Service::ADMINISTRATION, Service::INFORMATIQUE, Service::GESTIONNAIRE_VL, Service::GESTIONNAIRE_PL]));
    return $this->getDetailsFromImmatriculation($immatriculation);
  }

  public function listeGenre(){
    return $this->getListeGenreVéhicule();
  }
}