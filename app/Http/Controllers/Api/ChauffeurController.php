<?php
/**
 * Created by PhpStorm.
 * User: Wilfried KOFFI
 * Date: 07/07/2022
 * Time: 11:28
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Metier\Security\Actions;
use App\Service;
use App\Services\Personnel\ChauffeurServices;
use Illuminate\Http\Request;

class ChauffeurController extends Controller
{
  use ChauffeurServices;

  public function liste(Request $request){
    return $this->getListe();
  }

    public function register(Request $request)
    {
        return $this->registerChauffeur($request);
    }


    public function situation( Request $request,$matricule)
    {
        //$this->authorize(Actions::READ, collect([Service::DG, Service::INFORMATIQUE, Service::ADMINISTRATION, Service::GESTIONNAIRE_VL, Service::GESTIONNAIRE_PL]));

        return $this->situationChauffeur($matricule);
    }
}