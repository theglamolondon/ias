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
use Illuminate\Http\Request;

class PersonnelController extends Controller
{
  use PersonnelServices;

  public function liste(){
    /*$this->authorize(Actions::READ, collect([Service::DG, Service::ADMINISTRATION, Service::GESTIONNAIRE_VL,
      Service::GESTIONNAIRE_PL, Service::COMPTABILITE, Service::INFORMATIQUE])); */

    return $this->listePersonnel();
  }

    public function fiche($matricule)
    {
           //$this->authorize(Actions::READ, collect([Service::DG, Service::ADMINISTRATION, Service::GESTIONNAIRE_VL,
           // Service::GESTIONNAIRE_PL, Service::COMPTABILITE, Service::INFORMATIQUE]));
        return $this->fichePersonnel($matricule);
    }

    public function register(Request $request)
    {
           //$this->authorize(Actions::CREATE, collect([Service::DG, Service::ADMINISTRATION, Service::GESTIONNAIRE_VL,
           // Service::GESTIONNAIRE_PL, Service::COMPTABILITE, Service::INFORMATIQUE]));
        return $this->registerPersonnel($request);
    }

    public function update(Request $request, $matricule)
    {
        $this->authorize(Actions::UPDATE, collect([Service::DG, Service::ADMINISTRATION, Service::GESTIONNAIRE_VL,
            Service::GESTIONNAIRE_PL, Service::COMPTABILITE, Service::INFORMATIQUE]));

        return $this->updatePersonnel($request, $matricule);
    }

}