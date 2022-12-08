<?php
/**
 * Created by PhpStorm.
 * User: Wilfried KOFFI
 * Date: 14/07/2022
 * Time: 00:04
 */

namespace App\Services;


use App\Application;
use App\Chauffeur;
use App\Http\Controllers\Web\Mission\MissionController;
use App\Http\Controllers\Web\Mission\Process;
use App\Metier\Behavior\Notifications;
use App\Metier\Security\Actions;
use App\Mission;
use App\MissionPL;
use App\Partenaire;
use App\Service;
use App\Statut;

use App\Vehicule;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

trait MissionServices
{

    public function getMissionsListe($withVehicule = false, $withPartenaire = false)
    {
        $builder = Mission::with("chauffeur")->orderBy("debuteffectif", "desc");

        if ($withVehicule) {
            $builder->with("vehicule");
        }
        if ($withPartenaire) {
            $builder->with("clientPartenaire");
        }
        return $builder->paginate(25);
    }

    /**
     * @param $reference
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function detailsMission($reference)
    {

        $mission = $this->missionBuilder()
            ->where("code",$reference)
            ->firstOrFail();

        return view("mission.vl.details",compact("mission"));
    }




    public function ajouterMission(Request $request)
    {

        $this->validate($request, $this->validateMission());

        $mission = $this->createMission($request);
        if (!$request->expectsJson()) {
            $notification = new Notifications();
            $notification->add(Notifications::SUCCESS, "Votre mission a été prise en compte. Voulez aller définir le bon de commande");

            $request->session()->put(Notifications::MISSION_OBJECT, $mission);

            return redirect()->route("mission.liste")->with(Notifications::NOTIFICATION_KEYS_SESSION, $notification);

        }
        return null;
    }

    private function createMission(Request $request)
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



    /**
     * @param $reference
     * @param Request $request
     *
     * @return $this|\Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function updateMission($reference, Request $request)
    {

        $this->validate($request, $this->validateMissionMaj());

        try {
            $mission = $this->missionBuilder()
                ->where("code", $reference)
                ->firstOrFail();

            if (!empty($mission->piececomptable_id) || $mission->status != Statut::MISSION_COMMANDEE) {
                return back()->withErrors("Impossible de modifier la mission #{$reference}. Celle-ci a déjà fait l'objet de facture ou son statut à changé.");
            }

            $this->maj($mission, $request);

        } catch (ModelNotFoundException $e) {
            return back()->withErrors("La mission référencée est introuvable, vous l'avaez peut-être supprimée");
        }
        if (!$request->expectsJson()) {
            $notification = new Notifications();
            $notification->add(Notifications::SUCCESS, "Mission modifiée avec succès !");

            return redirect()->route("mission.liste")->with(Notifications::NOTIFICATION_KEYS_SESSION, $notification);
        }
        return null;
    }

    /**
     * @param Mission $mission
     * @param Request $request
     */
    private function maj(Mission $mission, Request $request)
    {

        $data = $request->except("_token");

        //Check si mission est sous traitée
        if (key_exists("soustraite", $data) && $data['soustraite']) {
            $data['chauffeur_id'] = null;
            $data['vehicule_id'] = null;

            unset($data["soustraite"]);
        }

        $data["debuteffectif"] = Carbon::createFromFormat("d/m/Y", $request->input("debuteffectif"))->toDateString();
        $data["fineffective"] = Carbon::createFromFormat("d/m/Y", $request->input("fineffective"))->toDateString();

        $mission->update($data);
    }

    /**
     * @param Request $request
     * @param string $reference
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */

    public function updateAfterStartMission(Request $request, string $reference)
    {


        $this->validate($request, [
            "observation" => "present",
            "debuteffectif" => "required",
            "fineffective" => "required|date_format:d/m/Y",
        ]);

        try {
            $mission = $this->missionBuilder()
                ->where("code", $reference)
                ->firstOrFail();

            $mission->debuteffectif = Carbon::createFromFormat("d/m/Y", $request->input("debuteffectif"))->toDateString();
            $mission->fineffective = Carbon::createFromFormat("d/m/Y", $request->input("fineffective"))->toDateString();
            $mission->observation = $request->input("observation");

            $mission->saveOrFail();

        } catch (ModelNotFoundException $e) {

        }
        if (!$request->expectsJson()) {
            $notification = new Notifications();
            $notification->add(Notifications::SUCCESS, "Mission modifiée avec succès !");
            return redirect()->back()->with(Notifications::NOTIFICATION_KEYS_SESSION, $notification);
        }
        return null;
    }

    private function getPeriode(Request $request)
    {
        $debut = null;
        $fin = null;

        if($request->has(["debut","fin"]))
        {
            $debut = Carbon::createFromFormat("d/m/Y", $request->input("debut"));
            $fin = Carbon::createFromFormat("d/m/Y", $request->input("fin"));
        }

        $this->debut_periode = $debut;
        $this->fin_periode = $fin;
    }

    /**
     * @param array $data
     */
    private function generateCodeMission(array &$data){
        if(! array_key_exists("code",$data) || $data["code"] == null || empty($data["code"]))
        {
            $data["code"] = Application::getNumeroMission(true);
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    private function missionBuilder()
    {
        return Mission::with(["chauffeur.employe", "vehicule", "clientPartenaire", "pieceComptable"]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    private function missionPLBuilder()
    {
        return MissionPL::with(["chauffeur.employe", "vehicule", "client", "pieceComptable"]);
    }

    /**
     * @return array
     */
    public function validateMission()
    {
        return [
            "code" => "present|unique:mission",
            "destination" => "required",
            "debutprogramme" => "required",
            "finprogramme" => "required|date_format:d/m/Y",
            "perdiem" => "numeric",
            "chauffeur_id" => "required|numeric",
            "vehicule_id" => "required|numeric",
            "client" => "required|numeric",
        ];
    }

    /**
     * @return array
     */
    public function validateMissionPL()
    {
        return [
            "code" => "present|unique:mission",
            "destination" => "required",
            "datedebut" => "required|date_format:d/m/Y",
            "carburant" => "numeric",
            "kilometrage" => "numeric",
            "chauffeur_id" => "required|numeric",
            "vehicule_id" => "required|numeric",
            "partenaire_id" => "required|numeric"
        ];
    }

    /**
     * @return array
     */
    public function validateMissionMaj()
    {
        return [
            "code" => "required",
            "destination" => "required",
            "debuteffectif" => "required",
            "fineffective" => "required|date_format:d/m/Y",
            "perdiem" => "numeric",
            "chauffeur_id" => "required|numeric",
            "vehicule_id" => "required|numeric",
            "client" => "required|numeric"
        ];
    }/**
 * @return array
 */
    public function validateMissionPLMaj()
    {
        return [
            "code" => "present|unique:mission",
            "destination" => "required",
            "datedebut" => "required|date_format:d/m/Y",
            "carburant" => "numeric",
            "kilometrage" => "numeric",
            "chauffeur_id" => "required|numeric",
            "vehicule_id" => "required|numeric",
            "partenaire_id" => "required|numeric"
        ];
    }

    private function listeVehiculeSelection(Request $request, string $mode = Vehicule::VL)
    {
        if($request->has('vehicule'))
        {
            return Vehicule::with("genre")
                ->where('immatriculation',$request->input('vehicule'))
                ->get();
        }else{
            return Vehicule::with("genre")
                ->join('genre', 'genre.id', '=', 'vehicule.genre_id')
                ->where("genre.categorie", "=" , $mode)
                ->where("status","=", Statut::VEHICULE_ACTIF)
                ->select("vehicule.*")
                ->get();
        }
    }

    public function changeStatus(string $reference, int $statut, Request $request)
    {
        $sessionToken = $request->session()->token();
        $token = $request->input('_token');
        if (!is_string($sessionToken) || !is_string($token) || !hash_equals($sessionToken, $token)) {
            return back()->withErrors('La page a expiré, veuillez recommencer SVP !');
        }

        try {
            $mission = $this->missionBuilder()
                ->where("code", $reference)
                ->firstOrFail();

            $mission->status = $statut;
            $mission->save();

        } catch (ModelNotFoundException $e) {
            return back()->withErrors('La mission est introuvable !');
        }
        if (!$request->expectsJson()) {
            $notification = new Notifications();
            $notification->add(Notifications::SUCCESS, "Statut de la mission modifiée avec succès !");
            return back()->with(Notifications::NOTIFICATION_KEYS_SESSION, $notification);
        }
        return null;
    }

    public function changeStatusPL(string $reference, int $statut, Request $request)
    {
        $sessionToken = $request->session()->token();
        $token = $request->input('_token');
        if (! is_string($sessionToken) || ! is_string($token) || !hash_equals($sessionToken, $token) ) {
            return back()->withErrors('La page a expiré, veuillez recommencer SVP !');
        }

        try {
            $mission = $this->missionPLBuilder()
                ->where("code", $reference)
                ->firstOrFail();

            $mission->status = $statut;
            $mission->save();

        }catch(ModelNotFoundException $e){
            return back()->withErrors('La mission est introuvable !');
        }

        $notification = new Notifications();
        $notification->add(Notifications::SUCCESS,"Statut de la mission modifiée avec succès !");
        return back()->with(Notifications::NOTIFICATION_KEYS_SESSION, $notification);
    }
}