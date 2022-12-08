<?php

namespace App\Http\Controllers\Web\Admin;

use App\Bulletin;
use App\Employe;
use App\Http\Controllers\RH\Tools;
use App\Metier\Behavior\Notifications;
use App\Metier\Security\Actions;
use App\Mission;
use App\Salaire;
use App\Service;
use App\Services\Personnel\PersonnelServices;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;

class EmployeController extends Controller
{
    use PersonnelServices;
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
    public function liste()
    {
	    $this->authorize(Actions::READ, collect([Service::DG, Service::ADMINISTRATION, Service::GESTIONNAIRE_VL,
		    Service::GESTIONNAIRE_PL, Service::COMPTABILITE, Service::INFORMATIQUE]));

        $employes = Employe::orderBy('nom')->orderBy('prenoms')
            ->with('service')
            ->paginate(15);

        return view('admin.employe.liste',compact('employes'));
    }

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
    public function ajouter()
    {
	    $this->authorize(Actions::CREATE, collect([Service::DG, Service::ADMINISTRATION, Service::GESTIONNAIRE_VL,
		    Service::GESTIONNAIRE_PL, Service::COMPTABILITE, Service::INFORMATIQUE]));
        $services = Service::orderBy('libelle')->get();
        return view('admin.employe.ajouter',compact('services'));
    }

	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 * @throws \Throwable
	 */
    public function register(Request $request)
    {
    	$this->authorize(Actions::CREATE, collect([Service::DG, Service::ADMINISTRATION, Service::GESTIONNAIRE_VL,
		    Service::GESTIONNAIRE_PL, Service::COMPTABILITE, Service::INFORMATIQUE]));

       return $this->registerPersonnel($request);
    }


	/**
	 * @param $matricule
	 *
	 * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
    public function fiche($matricule)
    {

	    $this->authorize(Actions::READ, collect([Service::DG, Service::ADMINISTRATION, Service::GESTIONNAIRE_VL,
		    Service::GESTIONNAIRE_PL, Service::COMPTABILITE, Service::INFORMATIQUE]));

       return $this->fichePersonnel($matricule);
    }

	/**
	 * @param $matricule
	 *
	 * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
    public function modifier($matricule)
    {
	    $this->authorize(Actions::UPDATE, collect([Service::DG, Service::ADMINISTRATION, Service::GESTIONNAIRE_VL,
		    Service::GESTIONNAIRE_PL, Service::COMPTABILITE, Service::INFORMATIQUE]));

        try{
            $services = Service::orderBy('libelle')->get();
            $employe = Employe::with("chauffeur","service")->where("matricule",$matricule)->firstOrFail();
            return view("admin.employe.modifier", compact("employe", "services"));
        }catch (ModelNotFoundException $e){
            return back()->withErrors("Employé non trouvé");
        }
    }


    public function update(Request $request, $matricule)
    {
    	$this->authorize(Actions::UPDATE, collect([Service::DG, Service::ADMINISTRATION, Service::GESTIONNAIRE_VL,
		    Service::GESTIONNAIRE_PL, Service::COMPTABILITE, Service::INFORMATIQUE]));

    	return $this->updatePersonnel($request, $matricule);
    }
}
