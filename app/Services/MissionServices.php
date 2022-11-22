<?php
/**
 * Created by PhpStorm.
 * User: Wilfried KOFFI
 * Date: 14/07/2022
 * Time: 00:04
 */

namespace App\Services;


use App\Mission;
use Illuminate\Http\Request;

trait MissionServices
{
  public function getMissionsListe($withVehicule = false, $withPartenaire = false){
    $builder = Mission::with("chauffeur")->orderBy("debuteffectif", "desc");

    if($withVehicule){
      $builder->with("vehicule");
    }
    if($withPartenaire){
      $builder->with("clientPartenaire");
    }
    return $builder->paginate(25);
  }


}