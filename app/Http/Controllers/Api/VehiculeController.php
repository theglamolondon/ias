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

  public function liste(Request $request){
    //$this->authorize(Actions::READ, collect([Service::DG, Service::ADMINISTRATION, Service::INFORMATIQUE, Service::GESTIONNAIRE_VL, Service::GESTIONNAIRE_PL]));
    return $this->getListeVehiculeActif();
  }
}