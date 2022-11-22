<?php
/**
 * Created by PhpStorm.
 * User: Wilfried KOFFI
 * Date: 14/07/2022
 * Time: 00:04
 */

namespace App\Services;


use App\Metier\Behavior\Notifications;
use App\Metier\Security\Actions;
use App\Mission;
use App\Mission\Process;
use App\MissionPL;
use App\Service;
use App\Statut;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

trait MissionPLServices
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function ajouterPoidLourd(Request $request)
    {
        $this->authorize(Actions::READ, collect([Service::DG, Service::INFORMATIQUE,
            Service::COMPTABILITE, Service::GESTIONNAIRE_PL]));

        $this->validate($request, $this->validateMissionPL());
        $missionPL = $this->createPoidLourd($request);

        if(!$request->expectsJson()) {
            $notification = new Notifications();
            $notification->add(Notifications::SUCCESS, "Votre mission a été prise en compte. Voulez aller définir le bon de commande");

            $request->session()->put(Notifications::MISSION_OBJECT, $missionPL);

        return redirect()->route("mission.liste-pl")->with(Notifications::NOTIFICATION_KEYS_SESSION, $notification);
        }
    }

    private function createPoidLourd(Request $request)
    {
        $data = $request->except("_token");
        $this->generateCodeMission($data);

        $data["status"] = Statut::MISSION_COMMANDEE;
        $data["datedebut"] = Carbon::createFromFormat("d/m/Y",$request->input("datedebut"))->toDateString();

        return MissionPL::create($data);
    }

}