<?php
/**
 * Created by PhpStorm.
 * User: Wilfried KOFFI
 * Date: 02/07/2022
 * Time: 12:57
 */

namespace App\Services;

use App\Genre;
use App\Intervention;
use App\Metier\Behavior\Notifications;
use App\PieceFournisseur;
use App\Service;
use App\Statut;
use App\Vehicule;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageBag;

trait VehiculeServices
{

  protected function getListeVehiculeActif(){
    $vehiculesBuilder = Vehicule::with('genre')->whereIn("status", [Statut::VEHICULE_ACTIF, Statut::VEHICULE_EN_MISSION, Statut::VEHICULE_AU_GARAGE]);
    return $this->getListeVehicule(Vehicule::VL, $vehiculesBuilder);
  }
  protected function getListeVehiculeInactif(){
    $vehiculesBuilder = Vehicule::with('genre')->whereIn("status", [Statut::VEHICULE_ENDOMAGE, Statut::VEHICULE_RESERVE, Statut::VEHICULE_VENDU]);
    return $this->getListeVehicule(Vehicule::VL, $vehiculesBuilder);
  }

  protected function getListeAll(){
    return $this->triVehicule(Vehicule::with("genre"));
  }

  protected function getListeVehiculesByStatus(array $statuts){
    return $this->triVehicule(
      Vehicule::with("genre")->whereIn("status", $statuts)
    );
  }

  protected function getListeVehicule(string $type, Builder $builder){

    $vehicules = $builder->join("genre","genre.id","=","vehicule.genre_id")
      ->where("genre.categorie", "=", $type)->select('vehicule.*');
    return $this->triVehicule($vehicules, 15);
  }

  protected function triVehicule(Builder $builder, int $page = 25){
    if(\request()->has("search") && !empty(\request()->query("search"))){
      $builder = $builder->where("immatriculation","like","%".\request()->query("search")."%");
    }
    if(\request()->has("search") && !empty(\request()->query("search"))){
      $builder = $builder->orWhere("marque","like","%".\request()->query("search")."%");
    }
    if(\request()->has("search") && !empty(\request()->query("search"))){
      $builder = $builder->orWhere("typecommercial","like","%".\request()->query("search")."%");
    }

    return $builder->paginate($page);
  }

  protected function getDetailsFromImmatriculation(string $immatriculation){
    return Vehicule::with("genre","chauffeur","interventions")->where("immatriculation", $immatriculation)->firstOrFail();
  }

  protected function getListeGenreVehicule() : Collection{
    return Genre::all();
  }

  protected function updateIntervention(int $interventionId, PieceFournisseur $piece)
  {
    $intervention = Intervention::find($interventionId);
    if($intervention)
    {
      $intervention->piecefournisseur_id = $piece->id;
      $intervention->save();
    }
  }

  public function ajouter(Request $request)
    {
        $this->validate($request,$this->validateRules(false));

        $vehicule = $this->save($request);

        if(!$request->expectsJson()) {
            $notification = new Notifications();
            $notification->add($notification::SUCCESS,Lang::get('message.vehicule.ajout',['immatriculation' => $request->input('immatriculation')]));

            return back()->with(Notifications::NOTIFICATION_KEYS_SESSION,$notification);
        }
        return $vehicule;
    }

    protected function save(Request $request, Vehicule $vehicule = null) : Vehicule
    {
        if($vehicule === null){
            $vehicule = new Vehicule($request->except('_token'));
        }

        $vehicule->visite    = Carbon::createFromFormat('d/m/Y',$request->input("visite")   )->toDateString();
        $vehicule->assurance = Carbon::createFromFormat('d/m/Y',$request->input("assurance"))->toDateString();
        $vehicule->dateachat = Carbon::createFromFormat('d/m/Y',$request->input("dateachat"))->toDateString();

        $vehicule->save();

        return $vehicule;
    }

    public function update(Request $request){

        $this->validate($request, $this->validateRules(true));

        try{
            $vehicule = Vehicule::find($request->input('id'));

            $vehicule->fill($request->except("visite", "assurance", "dateachat"));

            $vehicule->visite    = Carbon::createFromFormat('d/m/Y',$request->input("visite")   )->toDateString();
            $vehicule->assurance = Carbon::createFromFormat('d/m/Y',$request->input("assurance"))->toDateString();
            $vehicule->dateachat = Carbon::createFromFormat('d/m/Y',$request->input("dateachat"))->toDateString();


          if(!$request->expectsJson()) {
            $this->save($request, $vehicule);

            $notification = new Notifications();
            $notification->add($notification::SUCCESS, Lang::get('message.vehicule.modifier', ['immatriculation' => $request->input('immatriculation')]));

            return back()->with(Notifications::NOTIFICATION_KEYS_SESSION, $notification);
          }
        }catch (ModelNotFoundException $e){
            Log::error($e->getMessage()."\r\n".$e->getTraceAsString());
            return back()->withErrors("Véhicule non trouvé");
        }
        return $vehicule;
    }

    /**
     * @return array
     */
    protected function validateRules($withID = false, $withChauffeur = false)
    {
        $rules = [
            'immatriculation' =>'required|regex:/([0-9]{1,4})([A-Z]{2})([0-2]{2})/',
            'genre_id' => 'required|numeric|exists:genre,id',
            'cartegrise' => 'required',
            'marque' => 'required',
            'typecommercial' => 'present',
            'couleur' => 'required',
            'energie' => 'required',
            'nbreplace' => 'required|numeric',
            'puissancefiscale' => 'present',
            'dateachat' => "required|date_format:d/m/Y",
            'coutachat' => "required|numeric",
        ];

        if($withChauffeur){
          $rules['chauffeur_id'] = "required|exists:chauffeur,employe_id";
        }

        if($withID){
            $rules['id'] = 'required|numeric';
        }

        return $rules;
    }
}