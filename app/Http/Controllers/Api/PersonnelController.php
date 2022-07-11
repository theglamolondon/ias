<?php
/**
 * Created by PhpStorm.
 * User: Wilfried KOFFI
 * Date: 11/07/2022
 * Time: 13:11
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Metier\Security\Actions;
use App\Service;
use App\Services\Personnel\PersonnelServices;

class PersonnelController extends Controller
{
  use PersonnelServices;

  public function liste(){
    /*$this->authorize(Actions::READ, collect([Service::DG, Service::ADMINISTRATION, Service::GESTIONNAIRE_VL,
      Service::GESTIONNAIRE_PL, Service::COMPTABILITE, Service::INFORMATIQUE])); */

    return $this->listePersonnel();
  }

}