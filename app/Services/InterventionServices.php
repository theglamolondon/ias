<?php
/**
 * Created by PhpStorm.
 * User: Wilfried KOFFI
 * Date: 12/07/2022
 * Time: 19:06
 */

namespace App\Services;


use App\Intervention;
use App\Vehicule;
use Illuminate\Database\Eloquent\Model;

trait InterventionServices
{


  /**
   * @param Model|Vehicule $vehicule
   * @return \Illuminate\Database\Eloquent\Collection|static[]
   */
  private function getVehiculeInterventions(Vehicule $vehicule)
  {
    return Intervention::with("typeIntervention")
      ->where("vehicule_id", $vehicule->id)
      ->select("intervention.*","debut as date_")
      ->get();
  }

}