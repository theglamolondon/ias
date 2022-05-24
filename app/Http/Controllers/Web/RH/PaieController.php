<?php
/**
 * Created by PhpStorm.
 * User: SUPPORT.IT
 * Date: 11/09/2018
 * Time: 15:39
 */

namespace App\Http\Controllers\Web\RH;


use App\Bulletin;
use App\Employe;
use App\Http\Controllers\Controller;
use App\Metier\Behavior\Notifications;
use App\Metier\Security\Actions;
use App\Mission;
use App\Salaire;
use App\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

class PaieController extends Controller
{
	use Tools;

	/**
	 * PaieController constructor.
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
	public function fichePaie(int $annee, int $mois)
	{
		$this->authorize(Actions::CREATE, collect([Service::DG, Service::ADMINISTRATION, Service::COMPTABILITE, Service::INFORMATIQUE]));

		$employe = null;

		$employe = Employe::with("service")->paginate(1);

		$periode = new Carbon();
		$periode->setDate($annee, $mois, 01);

		$missions = $this->getMissionLines($employe[0], $periode);

		return view("rh.fiche", compact("employe", "missions", "periode"));
	}

	private function getMissionLines(Employe $employe, Carbon $date)
	{
		return Mission::with('vehicule')
			->whereRaw("(month(debuteffectif) = {$date->month} or month(fineffective) = {$date->month}) AND chauffeur_id = {$employe->id}")
			->select( DB::raw("CASE WHEN month(debuteffectif) = month(fineffective) then datediff(fineffective, debuteffectif)
WHEN  month(debuteffectif) < month(fineffective) && month(debuteffectif) = {$date->month} THEN datediff('{$date->endOfMonth()->toDateString()}', debuteffectif)
WHEN    month(debuteffectif) < month(fineffective) && month(debuteffectif) < {$date->month} THEN datediff(fineffective, '{$date->firstOfMonth()->toDateString()}')
END + 1 as nbre_jours ,debuteffectif , fineffective, code, destination, perdiem"))
			->get();
	}

	/**
	 * @param Request $request
	 * @param $annee
	 * @param $mois
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 * @throws \Throwable
	 */
	public function savePaie(Request $request, $annee, $mois)
	{
		$this->authorize(Actions::CREATE, collect([Service::DG, Service::ADMINISTRATION, Service::COMPTABILITE, Service::INFORMATIQUE]));

		$this->validate($request, [
			"libelle" => "required|array",
			"base" => "required|array",
			"base.*" => "integer",
			"taux" => "required|array",
			"taux.*" => "integer",
			"employe_id" => "required|exists:employe,id",
			"url" => "present"
		]);

		$bulletin = new Bulletin();
		$lines = []; $nap = 0;

		for ($i=0; $i < count($request->input('libelle') ) ; $i++)
		{
			$lines[] = [
				Bulletin::LIBELLE => $request->input('libelle')[$i],
				Bulletin::BASE => $request->input('base')[$i],
				Bulletin::TAUX => $request->input('taux')[$i],
			];

			if($i == 0){
				$nap = intval($request->input('base')[$i]);
			}else{
				$nap += intval($request->input('taux')[$i]) * intval($request->input('base')[$i]);
			}
		}

		$bulletin->lignes = $lines;
		$bulletin->nap = $nap;
		$bulletin->mois = $mois;
		$bulletin->annee = $annee;
		$bulletin->employe_id = $request->input('employe_id') ;
		$bulletin->saveOrFail();

		if($request->url){
			return redirect()->to($request->url);
		}else{

			$salaire = Salaire::where('annee','=',$annee)
				->where('mois','=',$mois)
				->first();

			$salaire->statut = Salaire::ETAT_VALIDE;
			$salaire->saveOrFail();

			$notif = new Notifications();
			$notif->add(Notifications::SUCCESS,Lang::get("message.rh.salaire.valide"));
			return redirect()->route('rh.salaire')->with(Notifications::NOTIFICATION_KEYS_SESSION, $notif);
		}
	}
}