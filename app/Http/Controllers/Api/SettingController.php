<?php
/**
 * Created by PhpStorm.
 * User: Wilfried KOFFI
 * Date: 12/08/2022
 * Time: 17:08
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Services\SettingsServices;

class SettingController extends Controller
{
  use SettingsServices;

  public function listeTypeIntervention(){
    return $this->getAllTypeIntervention();
  }

}