<?php
/**
 * Created by PhpStorm.
 * User: Wilfried KOFFI
 * Date: 10/08/2022
 * Time: 00:01
 */

namespace App\Services;


use App\Intervention;
use App\Metier\Behavior\Notifications;
use App\Metier\Security\Actions;
use App\Service;
use App\TypeIntervention;
use App\Vehicule;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

trait ReparationServices
{

  public function getListeAll(Request $request, int $perPage = 20){
    $interventions = Intervention::with("vehicule","typeIntervention","pieceFournisseur","partenaire");
    return $this->triListe($request, $interventions)->orderBy("debut", "desc")->paginate($perPage);
  }

  private function triListe(Request $request, Builder $interventions) : Builder{
    if($request->input("debut") && $request->input("fin"))
    {
      $debut = Carbon::createFromFormat("d/m/Y", $request->input("debut"));
      $fin = Carbon::createFromFormat("d/m/Y", $request->input("fin"));

      $interventions = $interventions->whereBetween("debut",[$debut->toDateString(), $fin->toDateTimeString()]);
    }

    return $interventions;
  }

  //Ajouter une réparation
    public function DoReparation(Request $request)
    {
        $this->validate($request, $this->validateRules()[0], $this->validateRules()[1]);

        $intervention = new Intervention($request->except("_token", "vehicule"));
        $intervention->debut = Carbon::createFromFormat("d/m/Y", $request->input("debut"))->toDateTimeString();
        $intervention->fin = Carbon::createFromFormat("d/m/Y", $request->input("fin"))->toDateTimeString();

        if($request->input("partenaire_id") == -1)
        {
            $intervention->partenaire_id = null;
        }
        $intervention->saveOrFail();
        if (!$request->expectsJson()){
            $notification = new Notifications();
            $notification->add(Notifications::SUCCESS,"Nouvelle intervention enregistrée avec succès");
            return redirect()->route("reparation.liste")->with(Notifications::NOTIFICATION_KEYS_SESSION, $notification);
        }
       return null;
    }

    public function addTypeIntervention(Request $request)
    {
        $this->validate(request(), ["libelle" => "required"]);
        $type = new TypeIntervention();
        $type->libelle = request()->input("libelle");
        $type->save();
        if (!$request->expectsJson()) {
            $notification = new Notifications();
            $notification->add(Notifications::SUCCESS, "Nouveau type d'intervention ajouté avec succès");
            return redirect()->back()->with(Notifications::NOTIFICATION_KEYS_SESSION, $notification);
        }
    }
    /**
     * @param Model|Vehicule $vehicule
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    private function getVehiculeInterventions(Vehicule $vehicule)
    {
        return Intervention::with("typeIntervention")
            ->where("vehicule_id", $vehicule->id)
            ->select("intervention.*","debut as date_")
            ->get();
    }

}