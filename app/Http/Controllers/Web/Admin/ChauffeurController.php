<?php

namespace App\Http\Controllers\Web\Admin;

use App\Chauffeur;
use App\Employe;
use App\Metier\Behavior\Notifications;
use App\Metier\Security\Actions;
use App\Mission;
use App\Service;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;

class ChauffeurController extends Controller
{
	/**
	 * @param $matricule
	 *
	 * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
    public function situation($matricule)
    {
    	$this->authorize(Actions::READ, collect([Service::DG, Service::INFORMATIQUE, Service::ADMINISTRATION, Service::GESTIONNAIRE_VL, Service::GESTIONNAIRE_PL]));

    	$chauffeur = Chauffeur::where("matricule",$matricule)
            ->join("employe","employe_id","employe.id")
            ->first();

        if($chauffeur == null)
        {
            return back()->withErrors("Chauffeur non trouvé.");
        }

        $missions = Mission::with("chauffeur","versements.moyenreglement","clientPartenaire","vehicule")
            ->join("employe","chauffeur_id", "=", "employe.id")
            ->where("matricule",$matricule)
            ->select("mission.*")
            ->get();

        return view("admin.chauffeur.point", compact("missions","chauffeur"));
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
        $this->validate($request, $this->validateRules());

        try{

            $this->create($request->except('_token'));

        }catch (\Exception $e){

            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());

            return back()->withInput()->withErrors("Une erreur s'est produite dans la création du chauffeur.");
        }

        $notifiation = new Notifications();
        $notifiation->add(Notifications::SUCCESS,Lang::get('message.admin.chauffeur.ajout'));

        return redirect()->route('admin.chauffeur.liste')->with(Notifications::NOTIFICATION_KEYS_SESSION, $notifiation);
    }

    private function create(array $data)
    {
        $raw = [
            'employe_id' => $data['employe_id'],
            'permis' => $data['permis'],
        ];

        if(array_key_exists('expiration_c',$data) && !empty($data['expiration_c'])){
	        $raw['expiration_c'] = Carbon::createFromFormat('d/m/Y',$data['expiration_c']);
        }

        if(array_key_exists('expiration_d',$data) && !empty($data['expiration_d'])) {
	        $raw['expiration_d'] = Carbon::createFromFormat( 'd/m/Y', $data['expiration_d'] );
        }

        if(array_key_exists('expiration_e',$data) && !empty($data['expiration_e'])) {
	        $raw['expiration_e'] = Carbon::createFromFormat( 'd/m/Y', $data['expiration_e'] );
        }

        return Chauffeur::create($raw);
    }

    private function validateRules()
    {
        return [
            'employe_id' => 'required|numeric|exists:employe,id',
            'permis' => 'required|regex:/([A-Z0-9]*)-([0-9]*)-([0-9]{8})([A-Z]*)/',
            'expiration_c' => 'nullable|date_format:d/m/Y',
            'expiration_d' => 'nullable|date_format:d/m/Y',
            'expiration_e' => 'nullable|date_format:d/m/Y',
        ];
    }
}