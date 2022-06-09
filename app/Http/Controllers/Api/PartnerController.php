<?php
/**
 * Created by PhpStorm.
 * User: Wilfried KOFFI
 * Date: 27/05/2022
 * Time: 17:59
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Partenaire;
use App\Services\FactureServices;
use App\Services\PartnerServices;

class PartnerController extends Controller
{
  use PartnerServices, FactureServices;

  public function listeClient(){
    return $this->getListePartners(Partenaire::CLIENT);
  }

  public function listeFournisseur(){
    return $this->getListePartners(Partenaire::FOURNISSEUR);
  }

  public function getPartnerDetails(int $id){
    $partenaire = Partenaire::find($id);
    return [
      "partner" => $partenaire,
      "pieces"  => $this->getDetailsByClientPartner($partenaire->id)];
  }

}