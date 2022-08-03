<?php
/**
 * Created by PhpStorm.
 * User: Wilfried KOFFI
 * Date: 11/07/2022
 * Time: 13:12
 */

namespace App\Services\Personnel;


use App\Employe;
use Illuminate\Database\Eloquent\Builder;

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
}