<?php

namespace App\Http\Controllers\Web\Mission;

use App\Chauffeur;
use App\Metier\Behavior\Notifications;
use App\Metier\Security\Actions;
use App\Mission;
use App\Partenaire;
use App\Service;
use App\Statut;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UpdateController extends Controller
{
    use Process;

	/**
	 * @param $reference
	 * @param Request $request
	 *
	 * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
    public function modifier($reference, Request $request)
    {
	    $this->authorize(Actions::READ, collect([Service::DG, Service::INFORMATIQUE,
		    Service::ADMINISTRATION, Service::COMPTABILITE, Service::GESTIONNAIRE_VL]));

        try {
            $mission = $this->missionBuilder()
                ->where("code", $reference)
                ->firstOrFail();

            if(! empty($mission->piececomptable_id) || $mission->status != Statut::MISSION_COMMANDEE)
                return back()->withErrors("Impossible de modifier la mission #{$reference}. Celle-ci a déjà fait l'objet de facture ou son statut à changé.");

        }catch (ModelNotFoundException $e){
            return back()->withErrors("La mission référencée est introuvable, vous l'avaez peut-être supprimée");
        }

        $vehicules = $this->listeVehiculeSelection($request);

        $chauffeurs = Chauffeur::with('employe')->get();

        $partenaires = Partenaire::where("isclient",true)
            ->orderBy("raisonsociale")
            ->get();

        return view("mission.vl.modifier", compact('vehicules','chauffeurs', "partenaires", "mission"));
    }

	/**
	 * @param $reference
	 * @param Request $request
	 *
	 * @return $this|\Illuminate\Http\RedirectResponse
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
    public function update($reference, Request $request)
    {
	    $this->authorize(Actions::READ, collect([Service::DG, Service::INFORMATIQUE,
		    Service::ADMINISTRATION, Service::COMPTABILITE, Service::GESTIONNAIRE_VL]));

        $this->validate($request, $this->validateMissionMaj());

        try {
            $mission = $this->missionBuilder()
                ->where("code", $reference)
                ->firstOrFail();

            if(! empty($mission->piececomptable_id) || $mission->status != Statut::MISSION_COMMANDEE){
	            return back()->withErrors("Impossible de modifier la mission #{$reference}. Celle-ci a déjà fait l'objet de facture ou son statut à changé.");
            }

            $this->maj($mission, $request);

        }catch (ModelNotFoundException $e){
            return back()->withErrors("La mission référencée est introuvable, vous l'avaez peut-être supprimée");
        }

        $notification = new Notifications();
        $notification->add(Notifications::SUCCESS,"Mission modifiée avec succès !");

        return redirect()->route("mission.liste")->with(Notifications::NOTIFICATION_KEYS_SESSION, $notification);
    }

    /**
     * @param Mission $mission
     * @param Request $request
     */
    private function maj(Mission $mission, Request $request)
    {
        $data = $request->except("_token");

        //Check si mission est sous traitée
        if(key_exists("soustraite", $data) && $data['soustraite']){
            $data['chauffeur_id'] = null;
            $data['vehicule_id'] = null;

            unset($data["soustraite"]);
        }

        $data["debuteffectif"] = Carbon::createFromFormat("d/m/Y",$request->input("debuteffectif"))->toDateString();
        $data["fineffective"] = Carbon::createFromFormat("d/m/Y",$request->input("fineffective"))->toDateString();

        $mission->update($data);
    }

	/**
	 * @param Request $request
	 * @param string $reference
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 * @throws \Throwable
	 */

    public function updateAfterStart(Request $request, string $reference)
    {
	    $this->authorize(Actions::READ, collect([Service::DG, Service::INFORMATIQUE,
		    Service::ADMINISTRATION, Service::COMPTABILITE, Service::GESTIONNAIRE_VL]));

    	$this->validate($request, [
			"observation" => "present",
		    "debuteffectif" => "required",
		    "fineffective" => "required|date_format:d/m/Y",
	    ]);

	    try {
		    $mission = $this->missionBuilder()
		                    ->where( "code", $reference)
		                    ->firstOrFail();

		    $mission->debuteffectif = Carbon::createFromFormat("d/m/Y",$request->input("debuteffectif"))->toDateString();
		    $mission->fineffective = Carbon::createFromFormat("d/m/Y",$request->input("fineffective"))->toDateString();
		    $mission->observation = $request->input("observation");

		    $mission->saveOrFail();

	    }catch (ModelNotFoundException $e){

	    }

	    $notification = new Notifications();
	    $notification->add(Notifications::SUCCESS,"Mission modifiée avec succès !");
	    return redirect()->back()->with(Notifications::NOTIFICATION_KEYS_SESSION, $notification);
    }
}
