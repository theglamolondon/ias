<?php
/**
 * Created by PhpStorm.
 * User: Wilfried KOFFI
 * Date: 14/07/2022
 * Time: 00:18
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Services\MissionServices;
use Illuminate\Http\Request;

class MissionController extends Controller
{
  use MissionServices;

  public function liste(Request $request){
    return $this->getMissionsListe(true, true);
  }
}