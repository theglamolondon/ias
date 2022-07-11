<?php
/**
 * Created by PhpStorm.
 * User: Wilfried KOFFI
 * Date: 02/07/2022
 * Time: 12:57
 */

namespace App\Services;

use App\Intervention;
use App\PieceFournisseur;
use App\Service;
use App\Statut;
use App\Vehicule;
use Illuminate\Database\Eloquent\Builder;

trait VehiculeServices
{

  private function getListeVehiculeActif(){
    $vehiculesBuilder = Vehicule::with('genre')->where("status", Statut::VEHICULE_ACTIF);
    return $this->getListeVehicule(Vehicule::VL, $vehiculesBuilder);
  }

  private function getListeVehicule(string $type, Builder $builder){

      $vehicules = $builder->join("genre","genre.id","=","vehicule.genre_id")
        ->where("genre.categorie", "=", $type);

    if(\request()->has("immatriculation") && !empty(\request()->query("immatriculation"))){
      $vehicules = $vehicules->where("immatriculation","like","%".\request()->query("immatriculation")."%");
    }

    return $vehicules->paginate();
  }

  private function updateIntervention(int $interventionId, PieceFournisseur $piece)
  {
    $intervention = Intervention::find($interventionId);
    if($intervention)
    {
      $intervention->piecefournisseur_id = $piece->id;
      $intervention->save();
    }
  }
}