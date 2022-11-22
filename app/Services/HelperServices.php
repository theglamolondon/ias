<?php
/**
 * Created by PhpStorm.
 * User: Wilfried KOFFI
 * Date: 02/07/2022
 * Time: 17:04
 */

namespace App\Services;


use Carbon\Carbon;
use Illuminate\Http\Request;

trait HelperServices
{
  /**
   * @param Request $request
   * @return \Illuminate\Support\Collection
   */
  private function getPeriodeFromRequest(Request $request)
  {
    $debut = null;
    $fin = null;

    if($request->has("debut"))
      $debut = Carbon::createFromFormat("d/m/Y", $request->input("debut"));

    if($request->has("fin"))
      $fin = Carbon::createFromFormat("d/m/Y", $request->input("fin"));

    return collect(compact("debut", "fin"));
  }
}