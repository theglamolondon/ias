<?php
/**
 * Created by PhpStorm.
 * User: Wilfried KOFFI
 * Date: 20/06/2022
 * Time: 18:35
 */

namespace App\Http\Controllers\Api;


use App\Produit;
use App\Services\ProduitServices;
use Illuminate\Http\Request;

class ProductController
{
  use ProduitServices;

  public function recherche(Request $request){
    $produits = Produit::with("famille")
      ->orderBy("reference", 'asc');

    $this->filter($produits, $request);

    return $produits->paginate(25);
  }
}