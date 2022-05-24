<?php

namespace App\Http\Controllers\Web\Mission;

use App\Chauffeur;
use App\Metier\Security\Actions;
use App\Service;
use App\Statut;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MissionPlController extends Controller
{

	use Process;

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
	public function listePL(Request $request)
	{
		$this->authorize(Actions::READ, collect([Service::DG, Service::INFORMATIQUE,
			Service::COMPTABILITE, Service::GESTIONNAIRE_PL]));

		$this->getPeriode($request);

		if($this->debut_periode && $this->fin_periode){
			$debut = $this->debut_periode->format("d/m/Y");
			$fin = $this->fin_periode->format("d/m/Y");
		}else{
			$debut = Carbon::now()->firstOfMonth()->format("d/m/Y");
			$fin = Carbon::now()->format("d/m/Y");
		}


		$missions = $this->missionPLBuilder();
		if($this->debut_periode && $this->fin_periode){
			$missions = $missions->whereBetween("datedebut",[$this->debut_periode->toDateString(), $this->fin_periode->toDateString()]);
		}


		$missions = $missions->orderBy("datedebut","desc")->paginate(30);

		$chauffeurs = Chauffeur::with("employe")->get();

		$status = collect([
			Statut::MISSION_COMMANDEE => Statut::getStatut(Statut::MISSION_COMMANDEE),
			Statut::MISSION_EN_COURS  => Statut::getStatut(Statut::MISSION_EN_COURS),
			Statut::MISSION_TERMINEE  => Statut::getStatut(Statut::MISSION_TERMINEE),
			Statut::MISSION_ANNULEE   => Statut::getStatut(Statut::MISSION_ANNULEE),
		]);

		return view("mission.pl.liste",compact("missions", "debut", "fin", "chauffeurs", "status"));
	}

	/**
	 * @param $reference
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
	public function details($reference)
	{
		$this->authorize(Actions::READ, collect([Service::DG, Service::INFORMATIQUE,
			Service::COMPTABILITE, Service::GESTIONNAIRE_PL]));

		$mission = $this->missionPLBuilder()
		                ->where("code",$reference)
		                ->firstOrFail();

		return view("mission.pl.details",compact("mission"));
	}
}
