<?php
/**
 * Created by PhpStorm.
 * User: Wilfried KOFFI
 * Date: 11/07/2022
 * Time: 20:47
 */

namespace App\Services\Products;


use App\Famille;

trait FamilleServices
{
  private function getFamilles(){
    return Famille::orderBy("libelle")->get();
  }
}