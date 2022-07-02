<?php
/**
 * Created by PhpStorm.
 * User: Wilfried KOFFI
 * Date: 02/07/2022
 * Time: 12:57
 */

namespace App\Services;

use App\Intervention;
use App\PieceFournisseur;

trait VehiculeServices
{


  private function updateIntervention(int $interventionId, PieceFournisseur $piece)
  {
    $intervention = Intervention::find($interventionId);
    if($intervention)
    {
      $intervention->piecefournisseur_id = $piece->id;
      $intervention->save();
    }
  }
}