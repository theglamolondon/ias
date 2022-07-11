<?php
/**
 * Created by PhpStorm.
 * User: Wilfried KOFFI
 * Date: 11/07/2022
 * Time: 13:12
 */

namespace App\Services\Personnel;


use App\Employe;

trait PersonnelServices
{

  public function listePersonnel() {
    return Employe::with('service')
      ->orderBy('nom')->orderBy('prenoms')
      ->paginate(15);
  }
}