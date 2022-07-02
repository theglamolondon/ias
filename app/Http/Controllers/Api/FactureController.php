<?php
/**
 * Created by PhpStorm.
 * User: Wilfried KOFFI
 * Date: 02/07/2022
 * Time: 11:12
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Metier\Security\Actions;
use App\Service;
use App\Services\FactureServices;
use App\Statut;
use Illuminate\Http\Request;

class FactureController extends Controller
{
  use FactureServices;

  public function ajouterNouvelleFacture(Request $request){
    $this->authorize(Actions::CREATE, collect([Service::DG, Service::ADMINISTRATION, Service::COMPTABILITE, Service::INFORMATIQUE, Service::LOGISTIQUE]));
    $this->creerNouvelleProforma($request);
  }


  public function getFactures(Request $request){
    return $this->getPiecesComptable($request, null);
  }

  public function getDetailsFacture(string $reference) {
    //$this->authorize(Actions::READ, collect([Service::DG, Service::ADMINISTRATION, Service::COMPTABILITE, Service::INFORMATIQUE, Service::LOGISTIQUE]));
    return $this->getPieceComptableFromReference($reference);
  }
}