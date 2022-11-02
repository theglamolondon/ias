<?php
/**
 * Created by PhpStorm.
 * User: Wilfried KOFFI
 * Date: 02/07/2022
 * Time: 12:57
 */

namespace App\Services;

use App\Genre;
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

  private function getListeAll(){
    return $this->triVehicule(Vehicule::with("genre"));
  }

  private function getListeVehiculesByStatus(array $statuts){
    return $this->triVehicule(
      Vehicule::with("genre")->whereIn("status", $statuts)
    );
  }

  private function getListeVehicule(string $type, Builder $builder){

    $vehicules = $builder->join("genre","genre.id","=","vehicule.genre_id")
      ->where("genre.categorie", "=", $type)->select('vehicule.*');
    return $this->triVehicule($vehicules, 15);
  }

  private function triVehicule(Builder $builder, int $page = 25){
    if(\request()->has("immatriculation") && !empty(\request()->query("immatriculation"))){
      $builder = $builder->where("immatriculation","like","%".\request()->query("immatriculation")."%");
    }

    return $builder->paginate($page);
  }

  private function getDetailsFromImmatriculation(string $immatriculation){
    return Vehicule::with("genre","chauffeur","interventions")->where("immatriculation", $immatriculation)->firstOrFail();
  }

  private function getListeGenreVehicule(){
    return Genre::all();
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