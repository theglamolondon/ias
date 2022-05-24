<?php

namespace App\Http\Controllers\Web\Mission;

use App\Application;
use App\Chauffeur;
use App\Metier\Behavior\Notifications;
use App\Metier\Finance\InvoiceFrom;
use App\Metier\Security\Actions;
use App\Mission;
use App\MissionPL;
use App\Partenaire;
use App\Service;
use App\Statut;
use App\Vehicule;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;

class CreateController extends Controller
{
    use Process;

	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
	public function nouvelle(Request $request)
    {
	    $this->authorize(Actions::READ, collect([Service::DG, Service::INFORMATIQUE,
		    Service::ADMINISTRATION, Service::COMPTABILITE, Service::GESTIONNAIRE_VL]));

        $vehicules = $this->listeVehiculeSelection($request);

        $chauffeurs = Chauffeur::with('employe')->get();

        $partenaires = Partenaire::where("isclient",true)
            ->orderBy("raisonsociale")
            ->get();

        return view('mission.vl.nouvelle',compact('vehicules','chauffeurs', "partenaires"));
    }

	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
    public function ajouter(Request $request)
    {
	    $this->authorize(Actions::READ, collect([Service::DG, Service::INFORMATIQUE,
		    Service::ADMINISTRATION, Service::COMPTABILITE, Service::GESTIONNAIRE_VL]));


        $this->validate($request, $this->validateMission());

        $mission =  $this->create($request);

        $notification = new Notifications();
        $notification->add(Notifications::SUCCESS,"Votre mission a été prise en compte. Voulez aller définir le bon de commande");

        $request->session()->put(Notifications::MISSION_OBJECT, $mission);

        return redirect()->route("mission.liste")->with(Notifications::NOTIFICATION_KEYS_SESSION, $notification);
    }

    /**
     * @param Request $request
     * @return Mission
     */
    private function create(Request $request)
    {
        $data = $request->except("_token","vehicule");

        $this->generateCodeMission($data);

        //Check si mission est sous traitée
        if(isset($data['soustraite'])){
            $data['chauffeur_id'] = null;
            $data['vehicule_id'] = null;
        }

        $data["status"] = Statut::MISSION_COMMANDEE;

        $data["debutprogramme"] = Carbon::createFromFormat("d/m/Y",$request->input("debutprogramme"))->toDateString();
        $data["debuteffectif"] = $data["debutprogramme"];

        $data["finprogramme"] = Carbon::createFromFormat("d/m/Y",$request->input("finprogramme"))->toDateString();
        $data["fineffective"] = $data["finprogramme"];

        return Mission::create($data);
    }
}
