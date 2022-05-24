<?php
/**
 * Created by PhpStorm.
 * User: BW.KOFFI
 * Date: 18/08/2017
 * Time: 21:13
 */

namespace App\Http\Controllers\Web\Mission;


use App\Application;
use App\Metier\Behavior\Notifications;
use App\Mission;
use App\MissionPL;
use App\Statut;
use App\Vehicule;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

trait Process
{
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
        if (! is_string($sessionToken) || ! is_string($token) || !hash_equals($sessionToken, $token) ) {
            return back()->withErrors('La page a expiré, veuillez recommencer SVP !');
        }

        try {
            $mission = $this->missionBuilder()
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