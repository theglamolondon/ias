<?php
/**
 * Created by PhpStorm.
 * User: Wilfried KOFFI
 * Date: 20/06/2022
 * Time: 18:35
 */

namespace App\Http\Controllers\Api;


use App\Services\Products\FamilleServices;
use App\Services\Products\ProduitServices;
use Illuminate\Http\Request;

class ProductController
{
  use ProduitServices, FamilleServices;

  public function recherche(Request $request){
    return $this->filter($this->listeProduit(), $request)->paginate(15);
  }

  public function liste(Request $request){
    return $this->filter($this->listeProduit(), $request)->paginate(25);
  }

  public function details(string $reference) {
    return $this->produitDetails($reference);
  }

  public function familles(){
    return $this->getFamilles();
  }
}