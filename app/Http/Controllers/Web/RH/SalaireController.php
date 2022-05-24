<?php

namespace App\Http\Controllers\Web\RH;

use App\Bulletin;
use App\Employe;
use App\Metier\Behavior\Notifications;
use App\Metier\Security\Actions;
use App\Salaire;
use App\Service;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SalaireController extends Controller
{
	use Tools;

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
    public function demarrage()
    {
    	$this->authorize(Actions::CREATE, collect([Service::DG, Service::INFORMATIQUE, Service::ADMINISTRATION, Service::COMPTABILITE]));
	    $annees = $this->getYears();
	    $months = $this->getMonths();

	    $salaires = Salaire::orderBy('annee','desc')
		            ->orderBy('mois','desc')
		            ->paginate(10);
    	return view('rh.salaire', compact("annees", "months", "salaires"));
    }

	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 * @throws \Throwable
	 */
    public function start(Request $request)
    {
	    $this->authorize(Actions::CREATE, collect([Service::DG, Service::INFORMATIQUE, Service::ADMINISTRATION, Service::COMPTABILITE]));

	    $salaire = new Salaire();
	    $salaire->mois = $request->input('mois');
	    $salaire->annee = $request->input('annee');
	    $salaire->statut = Salaire::ETAT_DEMARRE;

	    try{
		    $salaire->saveOrFail();
	    }catch (QueryException $e){
	    	return $this->handleSalaireError($request);
	    }

	    $annee =$request->input('annee');
	    $mois = $request->input('mois');

	    return redirect()->route('rh.paie', compact("annee", "mois") );
    }

	private function handleSalaireError(Request $request)
	{
		$salaire = $this->getSalaire($request->input('annee'), $request->input('mois'));

		$pageId = $this->getLastEmployePage($request->input('annee'), $request->input('mois'));

		return redirect()->route('rh.salaire', ['state' => $salaire->statut, "target"=>$pageId])
			->withInput()
			->withErrors('Les salaires du mois de '.self::getMonthsById($request->input('mois')).' '
			             .$request->input('annee').' ont déjà été '.strtolower(Salaire::getStateToString($salaire->statut)).'s.');
	}

	private function getLastEmployePage(int $annee, int $mois)
	{
		return Bulletin::where('annee','=', $annee)
		                  ->where('mois','=', $mois)
		                  ->count() + 1;
	}

	/**
	 * @param int $annee
	 * @param int $mois
	 *
	 * @return Salaire
	 */
	private function getSalaire(int $annee, int $mois)
	{
		return Salaire::where('mois', '=', $mois)
		              ->where('annee', '=', $annee)
		              ->firstOrFail();
	}

	/**
	 * @param int $annee
	 * @param int $mois
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 * @throws \Exception
	 */
	public function reset(int $annee, int $mois)
	{
		Bulletin::where('annee','=',$annee)
			->where('mois','=',$mois)
			->delete();

		return redirect()->route('rh.paie', compact("annee", "mois") );
	}

	public function confirm(int $annee, int $mois)
	{

		$salaire = $this->getSalaire($annee, $mois);
		$bulletins = Bulletin::where("annee",'=', $annee)
			->where("mois",'=', $mois)
			->selectRaw("count(employe_id) as total, sum(nap) as somme")
			->firstOrFail();

		$personnel = Employe::count();

		//dd($personnel, $salaire, $bulletins);

		return view("rh.confirm",compact("salaire", "bulletins", "personnel"));
	}

	/**
	 * @param Request $request
	 *
	 * @return $this|\Illuminate\Http\RedirectResponse
	 * @throws \Throwable
	 */
	public function cloturer(Request $request)
	{
		$this->validate($request, [
			"annee" => "numeric",
			"mois" => "numeric",
		]);

		if($request->input('total') != $request->input("payes"))
		{
			return back()->withErrors("Impossible de cloturer cette paie. Veuillez terminer le la paie de tous les employés.");
		}

		$salaire = $this->getSalaire($request->input('annee'), $request->input('mois'));
		$salaire->statut = Salaire::ETAT_CLOTURE;
		$salaire->saveOrFail();

		$notif = new Notifications();
		$notif->add(Notifications::SUCCESS,sprintf("La paie du mois de ".PaieController::getMonthsById($salaire->mois)." ".$salaire->annee." a été clôturée avec succès"));
		return redirect()->route('rh.salaire')->with(Notifications::NOTIFICATION_KEYS_SESSION, $notif);
	}
}