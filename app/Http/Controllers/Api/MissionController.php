<?php
/**
 * Created by PhpStorm.
 * User: Wilfried KOFFI
 * Date: 14/07/2022
 * Time: 00:18
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;

use App\Metier\Security\Actions;
use App\Service;
use App\Services\MissionServices;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MissionController extends Controller
{
  use MissionServices;

  public function liste(Request $request){
    return $this->getMissionsListe(true, true);
  }
/**
  public function ajouter(Request $request){
    sleep(5);
    return array_merge($request->input(), ['parse' => Carbon::parse($request->input("dateDebut"))->toRfc850String()]);
    return $this->ajouterMission($request);
  }**/

    public function details($reference)
    {
        //$this->authorize(Actions::READ, collect([Service::DG, Service::INFORMATIQUE,
            //Service::ADMINISTRATION, Service::COMPTABILITE, Service::GESTIONNAIRE_VL]));

        return $this->detailsMission($reference);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function addMission(Request $request)
    {
        //$this->authorize(Actions::READ, collect([Service::DG, Service::INFORMATIQUE,
            //Service::ADMINISTRATION, Service::COMPTABILITE, Service::GESTIONNAIRE_VL]));

        return $this->ajouterMission($request);
    }

    /**
     * @param Request $request
     * @param string $reference
     *
     */
    public function update($reference, Request $request)
    {
        //$this->authorize(Actions::READ, collect([Service::DG, Service::INFORMATIQUE,
           // Service::ADMINISTRATION, Service::COMPTABILITE, Service::GESTIONNAIRE_VL]));
        return $this->updateMisspartenaireion($reference, $request);
    }

    public function updateMissionAfterStart(Request $request, string $reference)
    {
        //$this->authorize(Actions::READ, collect([Service::DG, Service::INFORMATIQUE,
           // Service::ADMINISTRATION, Service::COMPTABILITE, Service::GESTIONNAIRE_VL]));
        return $this->updateAfterStartMission($request,$reference);
    }
}