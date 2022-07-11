<?php
/**
 * Created by PhpStorm.
 * User: Wilfried KOFFI
 * Date: 07/07/2022
 * Time: 11:29
 */

namespace App\Services\Personnel;


use App\Chauffeur;
use Illuminate\Database\Eloquent\Builder;

trait ChauffeurServices
{
  protected function getListe(){
    $chauffeursBuilder = Chauffeur::with('employe');
    return $this->filtreListe($chauffeursBuilder);
  }

  protected function filtreListe(Builder $builder){

    if(\request()->has("fullname") && !empty(\request()->query("fullname"))){
      $builder = $builder->join("employe","employe_id", "=", "employe.id")
                          ->where("employe.nom","like","%".\request()->query("fullname")."%")
                          ->orWhere("employe.prenoms","like","%".\request()->query("fullname")."%")
                          ->select("chauffeur.*");
    }

    return $builder->paginate();
  }
}