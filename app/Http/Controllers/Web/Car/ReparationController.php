<?php

namespace App\Http\Controllers\Web\Car;

use App\Intervention;
use App\Metier\Behavior\Notifications;
use App\Metier\Security\Actions;
use App\Partenaire;
use App\Service;
use App\TypeIntervention;
use App\Vehicule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ReparationController extends Controller
{
	use Interventions;
	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
    public function index(Request $request)
    {
	    $this->authorize(Actions::READ, collect([Service::DG, Service::ADMINISTRATION, Service::INFORMATIQUE,
		    Service::GESTIONNAIRE_PL, Service::GESTIONNAIRE_VL]));

        $debut = Carbon::now()->firstOfMonth();
        $fin = Carbon::now();

	    $interventions = Intervention::with("vehicule","typeIntervention","pieceFournisseur","partenaire");

        if($request->input("debut") && $request->input("fin"))
        {
            $debut = Carbon::createFromFormat("d/m/Y", $request->input("debut"));
            $fin = Carbon::createFromFormat("d/m/Y", $request->input("fin"));

	        $interventions = $interventions->whereBetween("debut",[$debut->toDateString(), $fin->toDateTimeString()]);
        }

	    $interventions = $interventions->orderBy("debut", "desc");

        if($request->input("vehicule") && $request->input("vehicule") != "#") {
            $interventions->where("vehicule_id", $request->input("vehicule"));
        }
        if($request->input("type") && $request->input("type") != "#") {
            $interventions->where("typeintervention_id", $request->input("type"));
        }

	    if(Auth::user()->employe->service->code == Service::GESTIONNAIRE_PL){
		    $interventions = $interventions
			    ->join("vehicule","vehicule.id", "=", "intervention.vehicule_id")
			    ->join("genre","genre.id","=","vehicule.genre_id")
			    ->where("genre.categorie", "=", "PL");
	    }elseif(Auth::user()->employe->service->code == Service::GESTIONNAIRE_VL){
		    $interventions = $interventions
			    ->join("vehicule","vehicule.id", "=", "intervention.vehicule_id")
			    ->join("genre","genre.id","=","vehicule.genre_id")
			    ->where("genre.categorie", "=", "VL");
		}

        $interventions = $interventions->paginate(30);

	    $vehicules = null;

	    if(Auth::user()->employe->service->code == Service::GESTIONNAIRE_PL){
		    $vehicules = Vehicule::getListe("PL");
	    }elseif (Auth::user()->employe->service->code == Service::GESTIONNAIRE_VL){
		    $vehicules = Vehicule::getListe("VL");
	    }else{
		    $vehicules = Vehicule::getListe();
	    }

        $types = TypeIntervention::all();

        return view('car.intervention.reparations', compact("debut", "fin", "interventions", "vehicules", "types"));
    }

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
    public function nouvelle()
    {
	    $this->authorize(Actions::CREATE, collect([Service::DG, Service::ADMINISTRATION, Service::INFORMATIQUE, Service::GESTIONNAIRE_VL, Service::GESTIONNAIRE_PL]));

        $vehicules = null;

	    if(Auth::user()->employe->service->code == Service::GESTIONNAIRE_PL){
		    $vehicules = Vehicule::getListe("PL");
	    }elseif (Auth::user()->employe->service->code == Service::GESTIONNAIRE_VL){
		    $vehicules = Vehicule::getListe("VL");
	    }else{
		    $vehicules = Vehicule::getListe();
	    }

        $types = TypeIntervention::all();
        $fournisseurs = Partenaire::where('isfournisseur','=',true)->orderBy('raisonsociale')->get();
        return view("car.intervention.nouveau", compact("vehicules", "types", "fournisseurs"));
    }

	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 * @throws \Throwable
	 */
    public function ajouter(Request $request)
    {
    	$this->authorize(Actions::CREATE, collect([Service::DG, Service::ADMINISTRATION, Service::INFORMATIQUE,
		    Service::GESTIONNAIRE_VL, Service::GESTIONNAIRE_PL]));

	    $this->validate($request, $this->validateRules()[0], $this->validateRules()[1]);

        $intervention = new Intervention($request->except("_token", "vehicule"));
        $intervention->debut = Carbon::createFromFormat("d/m/Y", $request->input("debut"))->toDateTimeString();
        $intervention->fin = Carbon::createFromFormat("d/m/Y", $request->input("fin"))->toDateTimeString();

        if($request->input("partenaire_id") == -1)
        {
        	$intervention->partenaire_id = null;
        }

        $intervention->saveOrFail();

        $notification = new Notifications();
        $notification->add(Notifications::SUCCESS,"Nouvelle intervention enregistrée avec succès");

        return redirect()->route("reparation.liste")->with(Notifications::NOTIFICATION_KEYS_SESSION, $notification);
    }


    public function details(int $id){

    	$intervention = Intervention::with("typeIntervention","vehicule", "partenaire", "pieceFournisseur")->find($id);

    	if($intervention == null){
    		return redirect()->back()->withErrors("Intervention introuvable");
	    }

    	return view("car.intervention.details", compact("intervention"));
    }

	public function addType(){

		$this->validate(request(), ["libelle" => "required"]);

		$type = new TypeIntervention();
		$type->libelle = request()->input("libelle");
		$type->save();

		$notification = new Notifications();
		$notification->add(Notifications::SUCCESS,"Nouveau type d'intervention ajouté avec succès");
		return redirect()->back()->with(Notifications::NOTIFICATION_KEYS_SESSION, $notification);
	}
}