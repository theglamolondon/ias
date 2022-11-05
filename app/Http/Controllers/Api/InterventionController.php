<?php
/**
 * Created by PhpStorm.
 * User: Wilfried KOFFI
 * Date: 09/08/2022
 * Time: 23:42
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Services\InterventionServices;
use App\Services\ReparationServices;
use Illuminate\Http\Request;

class InterventionController extends Controller
{
  use ReparationServices;

  public function getListe(Request $request){
    return $this->getListeAll($request, 20);
  }

    public function Add(Request $request)
    {
        return $this->DoReparation($request);
    }

    public function addType(Request $request){
        return $this->addTypeIntervention($request);

    }

    public function getVehiculeIntervention(Request $request){
      return $this->getVehiculeInterventions();
    }
}