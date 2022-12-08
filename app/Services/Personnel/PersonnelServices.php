<?php
/**
 * Created by PhpStorm.
 * User: Wilfried KOFFI
 * Date: 11/07/2022
 * Time: 13:12
 */

namespace App\Services\Personnel;


use App\Bulletin;
use App\Employe;
use App\Http\Controllers\Web\Admin\EmployeController;
use App\Metier\Behavior\Notifications;
use App\Metier\Security\Actions;
use App\Mission;
use App\Service;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\MessageBag;

trait PersonnelServices
{

  public function listePersonnel($limit = 15) {
    $builder = Employe::with('service')
      ->orderBy('nom')->orderBy('prenoms');

    $builder = $this->triPersonnel($builder);

    return $builder->paginate($limit);
  }

  private function triPersonnel(Builder $builder) : Builder{
    if(request()->has("fullname")){
      $builder = $builder->whereRaw("CONCAT(nom, prenoms)");
    }
    return $builder;
  }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Throwable
     */
    public function registerPersonnel(Request $request)
    {
        $this->authorize(Actions::CREATE, collect([Service::DG, Service::ADMINISTRATION, Service::GESTIONNAIRE_VL,
            Service::GESTIONNAIRE_PL, Service::COMPTABILITE, Service::INFORMATIQUE]));

        $this->validate($request, [
            "service_id" => "required|exists:service,id",
            "datesortie" => "present",
            "dateembauche" => "required|date_format:d/m/Y",
            "datenaissance" => "required|date_format:d/m/Y",
            "contact" => "required",
            "prenoms" => "required",
            "nom" => "required",
            "pieceidentite" => "required",
            "matricule" => "required",
            "basesalaire" => "required",
            "rib" => "present",
            "cnps" => "present",
        ]);

        $this->addEmploye($request);
        if (!$request->expectsJson()) {
            $notif = new Notifications();
            $notif->add(Notifications::SUCCESS, Lang::get("message.admin.employe.ajout"));
            return redirect()->route("admin.employe.liste")->with(Notifications::NOTIFICATION_KEYS_SESSION, $notif);
        }
        return null;
    }

    /**
     * @param Request $request
     *
     * @throws \Throwable
     */
    private function addEmploye(Request $request)
    {
        $employe = new Employe($request->except("_token"));
        $employe->datenaissance = Carbon::createFromFormat("d/m/Y", $request->input("datenaissance"))->toDateString();
        $employe->dateembauche = Carbon::createFromFormat("d/m/Y", $request->input("dateembauche"))->toDateString();

        if(!empty($request->input("datesortie"))){
            $employe->datesortie = Carbon::createFromFormat("d/m/Y", $request->input("datesortie"))->toDateString();
        }

        $employe->saveOrFail();
    }

    /**
     * @param $matricule
     *
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function fichePersonnel($matricule)
    {
        $missions = null;

        try{
            $employe = Employe::with("chauffeur","service")->where("matricule",$matricule)->firstOrFail();
            if($employe->chauffeur){
                $missions = Mission::with("vehicule")->where("chauffeur_id","=",$employe->id)
                    ->orderBy("debuteffectif", "desc")
                    ->limit(10)
                    ->get();
            }

            $bulletins = Bulletin::with(["salaire","employe"])
                ->where("employe_id","=",$employe->id)
                ->orderBy("annee", "desc")
                ->orderBy("mois", "desc")
                ->limit(12)
                ->get();

            return view("admin.employe.fiche",compact("employe", "missions", "bulletins"));
        }catch (ModelNotFoundException $e){
            return back()->withErrors("Employé non trouvé");
        }
    }

    /**
     * @param Request $request
     * @param $matricule
     *
     * @return $this|\Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function updatePersonnel(Request $request, $matricule)
    {

        try {
            $employe = Employe::with("chauffeur","service")->where("matricule",$matricule)->firstOrFail();
            $update = $request->except("_token");

            $update["datenaissance"] = Carbon::createFromFormat("d/m/Y", $request->input("datenaissance"))->toDateString();
            $update["dateembauche"] = Carbon::createFromFormat("d/m/Y", $request->input("dateembauche"))->toDateString();

            if($request->input("datesortie") != null){
                $update["datesortie"] = Carbon::createFromFormat("d/m/Y", $request->input("datesortie"))->toDateString();
            }

        }catch (ModelNotFoundException $e){
            return back()->withErrors("Employé non trouvé");
        }

        $employe->update($update);

      if (!$request ->expectsJson()){
        $notif = new Notifications();
        $notif->add(Notifications::SUCCESS,Lang::get("message.admin.employe.modifier", ['nom' => sprintf("%s %s",$employe->nom, $employe->prenoms)]));
        return redirect()->route("admin.employe.liste")->with(Notifications::NOTIFICATION_KEYS_SESSION, $notif);
    }
      return null;
    }
}