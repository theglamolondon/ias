<?php
/**
 * Created by PhpStorm.
 * User: Wilfried KOFFI
 * Date: 12/08/2022
 * Time: 17:10
 */

namespace App\Services;


use App\TypeIntervention;

trait SettingsServices
{

  function getAllTypeIntervention(){
    return TypeIntervention::all();
  }
}