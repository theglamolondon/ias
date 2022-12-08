<?php

namespace App\Http\Controllers\Web\Mission;

use App\Chauffeur;
use App\Mail\MissionReminder;
use App\Metier\Behavior\Notifications;
use App\Metier\Security\Actions;
use App\Mission;
use App\Partenaire;
use App\Service;
use App\Services\MissionServices;
use App\Statut;
use App\Utilisateur;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\MessageBag;

class MissionController extends Controller
{
    use MissionServices;

    /**
     * @var Carbon $debut_periode
     */
    private $debut_periode;

    /**
     * @var Carbon $fin_periode
     */
    private $fin_periode;


	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
	public function liste(Request $request)
    {
	    $this->authorize(Actions::READ, collect([Service::DG, Service::INFORMATIQUE,
		    Service::ADMINISTRATION, Service::COMPTABILITE, Service::GESTIONNAIRE_VL]));

        $this->getPeriode($request);

        if($this->debut_periode && $this->fin_periode){
	        $debut = $this->debut_periode->format("d/m/Y");
	        $fin = $this->fin_periode->format("d/m/Y");
        }else{
        	$debut = Carbon::now()->firstOfMonth()->format("d/m/Y");
        	$fin = Carbon::now()->format("d/m/Y");
        }


        $missions = $this->missionBuilder();
        if($this->debut_periode && $this->fin_periode){
	        $missions = $missions->whereBetween("debuteffectif",[$this->debut_periode->toDateString(), $this->fin_periode->toDateString()]);
        }


        $missions = $missions->orderBy("debuteffectif","desc")->paginate(30);

        $chauffeurs = Chauffeur::with("employe")->get();

        $status = collect([
           Statut::MISSION_COMMANDEE => Statut::getStatut(Statut::MISSION_COMMANDEE),
           Statut::MISSION_EN_COURS  => Statut::getStatut(Statut::MISSION_EN_COURS),
           Statut::MISSION_TERMINEE  => Statut::getStatut(Statut::MISSION_TERMINEE),
           Statut::MISSION_ANNULEE   => Statut::getStatut(Statut::MISSION_ANNULEE),
        ]);

        return view("mission.vl.liste",compact("missions", "debut", "fin", "chauffeurs", "status"));
    }


    public function details($reference)
    {
	    $this->authorize(Actions::READ, collect([Service::DG, Service::INFORMATIQUE,
		    Service::ADMINISTRATION, Service::COMPTABILITE, Service::GESTIONNAIRE_VL]));

      return $this->detailsMission($reference);
    }

    public function reminder()
    {
    	$utilisateurs = Utilisateur::with('employe.service')
                      ->join('employe','employe.id','=', 'utilisateur.employe_id')
                      ->join('service','service.id','=', 'employe.service_id')
                      ->whereIn('service.code',[Service::GESTIONNAIRE_PL, Service::GESTIONNAIRE_VL, Service::ADMINISTRATION])
                      ->get();

	    $senders = $this->sortEmail($utilisateurs);

	    $missions = Mission::with('chauffeur.employe','vehicule','clientPartenaire')
	                       ->whereRaw('datediff(debuteffectif, sysdate()) > 0')
	                       ->whereRaw('datediff(debuteffectif, sysdate()) <= '.env('APP_MISSION_REMINDER',5))
		                   ->get();

	    //return view('mail.mission', compact("missions"));

	    try{
	    	if($missions->count() <= 1 )
		    {
			    Mail::to($senders->get('to'))
			        ->cc($senders->get('cc'))
			        ->send(new MissionReminder($missions ));
		    }
		}catch (\Exception $e){
			Log::error($e->getMessage()."\r\n".$e->getTraceAsString());
	    }
    }

    private function sortEmail(Collection $users)
    {
		$collection = new Collection();

	    $to = [];
	    $cc = [];
		foreach ($users as $user)
		{
			if( in_array($user->employe->service->code, [Service::GESTIONNAIRE_VL, Service::GESTIONNAIRE_PL]) )
			{
				$to[] = ['name' => $user->nom.' '.$user->prenoms, 'email' => $user->login];
			}else{
				$cc[] = ['name' => $user->nom.' '.$user->prenoms, 'email' => $user->login];
			}
		}

	    $collection->put('to', $to);
	    $collection->put('cc', $cc);

	    return $collection;
    }


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

        return $this->ajouterMission($request);
    }



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
     * @param Request $request
     * @param string $reference
     *
     */
    public function update($reference, Request $request)
    {
        $this->authorize(Actions::READ, collect([Service::DG, Service::INFORMATIQUE,
            Service::ADMINISTRATION, Service::COMPTABILITE, Service::GESTIONNAIRE_VL]));

       return $this->updateMission($reference, $request);
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

       return $this->updateAfterStartMission($request,$reference);
    }
}
