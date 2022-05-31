<?php
/**
 * Created by PhpStorm.
 * User: Wilfried KOFFI
 * Date: 27/05/2022
 * Time: 17:43
 */

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use App\Partenaire;

trait PartnerServices
{

  private function getListePartners(String $partnerType) : LengthAwarePaginator{
    $partenaires = Partenaire::orderBy('raisonsociale','asc');
    return $this->triPartenaire($partnerType, $partenaires)->paginate(25);
  }
  /**
   * @param String $type
   * @param Builder $builder
   * @return Builder
   */
  private function triPartenaire($type, Builder $builder)
  {
    if($type == Partenaire::FOURNISSEUR)
    {
      $builder->where('isfournisseur',true);
    }

    if($type == Partenaire::CLIENT)
    {
      $builder->where('isclient', true);
    }

    if(\request()->has("raisonsociale") && ! empty(\request()->query("raisonsociale"))){
      $builder->where("raisonsociale","like","%".\request()->query("raisonsociale")."%");
    }

    return $builder;
  }

}