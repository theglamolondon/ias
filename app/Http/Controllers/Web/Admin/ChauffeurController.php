<?php

namespace App\Http\Controllers\Web\Admin;

use App\Chauffeur;
use App\Employe;
use App\Metier\Behavior\Notifications;
use App\Metier\Security\Actions;
use App\Mission;
use App\Service;
use App\Services\Personnel\ChauffeurServices;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;

class ChauffeurController extends Controller
{
    use ChauffeurServices;

	/**
	 * @param $matricule
	 *
	 * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
    public function situation( Request $request,$matricule)
    {
    	$this->authorize(Actions::READ, collect([Service::DG, Service::INFORMATIQUE, Service::ADMINISTRATION, Service::GESTIONNAIRE_VL, Service::GESTIONNAIRE_PL]));

    	return $this->situationChauffeur($matricule);
    }

    public function liste()
    {
        $chauffeurs = Chauffeur::with('employe')->get();

        return view('admin.chauffeur.liste',compact('chauffeurs'));
    }

    public function ajouter()
    {
        $employes = Employe::orderBy('nom','asc')
            ->whereNotIn('id',Chauffeur::select(['employe_id'])->get()->toArray())
            ->orderBy('prenoms', 'asc')
            ->get();

        return view('admin.chauffeur.ajouter',compact('employes'));
    }

    public function register(Request $request)
    {
        return $this->registerChauffeur($request);
    }





}