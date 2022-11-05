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
use App\Vehicule;
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

  public function add(Request $request){
      #dd($request);
  return $this->ajouter($request);

  }

  public function modified(string $immatriculation, Request $request){
      return $this->update($request);

  }


    /**
     * @return array
     */
    protected function validateRules($withID = false)
    {
        $rules = [
            'immatriculation' =>'required|regex:/([0-9]{1,4})([A-Z]{2})([0-2]{2})/',
            'genre_id' => 'required|numeric|exists:genre,id',
            'cartegrise' => 'required',
            'marque' => 'required',
            'typecommercial' => 'present',
            'couleur' => 'required',
            'energie' => 'required',
            'nbreplace' => 'required|numeric',
            'puissancefiscale' => 'present',
            'dateachat' => "required|date_format:d/m/Y",
            'coutachat' => "required|numeric",
            'chauffeur_id' => "required|exists:chauffeur,employe_id",
        ];

        if($withID){
            $rules['id'] = 'required|numeric';
        }
        return $rules;
    }
}