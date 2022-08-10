<?php
/**
 * Created by PhpStorm.
 * User: Wilfried KOFFI
 * Date: 10/08/2022
 * Time: 00:01
 */

namespace App\Services;


use App\Intervention;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

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
}