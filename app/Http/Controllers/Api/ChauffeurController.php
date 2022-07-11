<?php
/**
 * Created by PhpStorm.
 * User: Wilfried KOFFI
 * Date: 07/07/2022
 * Time: 11:28
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Services\Personnel\ChauffeurServices;
use Illuminate\Http\Request;

class ChauffeurController extends Controller
{
  use ChauffeurServices;

  public function liste(Request $request){
    return $this->getListe();
  }
}