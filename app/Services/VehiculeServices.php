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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageBag;

trait VehiculeServices
{

  private function getListeVehiculeActif(){
    $vehiculesBuilder = Vehicule::with('genre')->where("status", Statut::VEHICULE_ACTIF);
    return $this->getListeVehicule(Vehicule::VL, $vehiculesBuilder);
  }

  private function getListeAll(){
    return $this->triVehicule(Vehicule::with("genre"));
  }

  private function getListeVehiculesByStatus(array $statuts){
    return $this->triVehicule(
      Vehicule::with("genre")->whereIn("status", $statuts)
    );
  }

  private function getListeVehicule(string $type, Builder $builder){

    $vehicules = $builder->join("genre","genre.id","=","vehicule.genre_id")
      ->where("genre.categorie", "=", $type)->select('vehicule.*');
    return $this->triVehicule($vehicules, 15);
  }

  private function triVehicule(Builder $builder, int $page = 25){
    if(\request()->has("immatriculation") && !empty(\request()->query("immatriculation"))){
      $builder = $builder->where("immatriculation","like","%".\request()->query("immatriculation")."%");
    }

    return $builder->paginate($page);
  }

  private function getDetailsFromImmatriculation(string $immatriculation){
    return Vehicule::with("genre","chauffeur","interventions")->where("immatriculation", $immatriculation)->firstOrFail();
  }

  private function getListeGenreVehicule(){
    return Genre::all();
  }

  private function updateIntervention(int $interventionId, PieceFournisseur $piece)
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

        $this->save($request);

        if(!$request->expectsJson()) {
            $notification = new Notifications();
            $notification->add($notification::SUCCESS,Lang::get('message.vehicule.ajout',['immatriculation' => $request->input('immatriculation')]));

            return back()->with(Notifications::NOTIFICATION_KEYS_SESSION,$notification);
        }
        return null;
    }

    private function save(Request $request, Vehicule $vehicule = null)
    {
        if($vehicule === null){
            $vehicule = new Vehicule($request->except('_token'));
        }

        $vehicule->visite    = Carbon::createFromFormat('d/m/Y',$request->input("visite")   )->toDateString();
        $vehicule->assurance = Carbon::createFromFormat('d/m/Y',$request->input("assurance"))->toDateString();
        $vehicule->dateachat = Carbon::createFromFormat('d/m/Y',$request->input("dateachat"))->toDateString();

        $vehicule->save();
    }

    public function update(Request $request)
    {
        $this->validate($request, $this->validateRules(true));

        try{
            $vehicule = Vehicule::find($request->input('id'));

            $vehicule->fill($request->except("visite", "assurance", "dateachat"));

            $this->save($request, $vehicule);

            //$notification = new Notifications();
            //$notification->add($notification::SUCCESS,Lang::get('message.vehicule.modifier',['immatriculation' => $request->input('immatriculation')]));

            //return back()->with(Notifications::NOTIFICATION_KEYS_SESSION,$notification);
        }catch (ModelNotFoundException $e){
            Log::error($e->getMessage()."\r\n".$e->getTraceAsString());
            return back()->withErrors("Véhicule non trouvé");
        }
        return $vehicule;
    }

    /**
     * @return array
     */
    protected function validateRules($withID = false)
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
            'chauffeur_id' => "required|exists:chauffeur,employe_id",
        ];

        if($withID){
            $rules['id'] = 'required|numeric';
        }
        return $rules;
    }

    private function test()
    {
        //dd($request->input());
        //$this->validate($request,$this->validateRules(false));
        //return back()->withInput()->withErrors('No body');


        $n = new Notifications();
        $n->add($n::INFO,'success');

        return back()->withInput()->with(Notifications::NOTIFICATION_KEYS_SESSION,$n);
    }
}