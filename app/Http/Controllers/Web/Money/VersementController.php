<?php

namespace App\Http\Controllers\Web\Money;

use App\Http\Controllers\Mission\Process;
use App\Metier\Behavior\Notifications;
use App\Mission;
use App\MoyenReglement;
use App\Statut;
use App\Versement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class VersementController extends Controller
{
    use Process;

    public function nouveauVersement($code)
    {
        $mission = $this->missionBuilder()->where("code", $code)->firstOrFail();

        $oldVersements = Versement::with("moyenreglement")
                                    ->where("mission_id", $mission->id)->get();

        $moyenReglements = MoyenReglement::all();

        if($mission->status == Statut::MISSION_TERMINEE_SOLDEE)
            return redirect()->route("mission.liste")->withErrors("Les per diem de  cette mission ont déjà été payé et soldés.");

        return view("money.versement", compact("mission", "moyenReglements", "oldVersements"));
    }

    public function ajouter(Request $request)
    {
        $this->validate($request, [
            "mission_id" => "required|numeric|exists:mission,id",
            "dateversement" => "required|date_format:d/m/Y H:i",
            "moyenreglement_id" => "required|exists:moyenreglement,id",
            "montant" => "required|numeric|min:100",
            "commentaires" => "present"
        ]);

        $mission = Mission::find($request->input("mission_id"));

        $versement = new Versement($request->except("_token"));
        $versement->employe_id = $mission->chauffeur_id;
        $versement->operateur_id = Auth::id();
        $versement->dateversement = Carbon::createFromFormat("d/m/Y H:i", $request->input("dateversement"))->toDateTimeString();

        $versement->saveOrFail();

        $notif = new Notifications();
        $notif->add(Notifications::SUCCESS,"Nouveau versement effectué avec succès !");

        return back()->with(Notifications::NOTIFICATION_KEYS_SESSION, $notif);
    }
}
