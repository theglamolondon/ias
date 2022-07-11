<?php
/**
 * Created by PhpStorm.
 * User: Wilfried KOFFI
 * Date: 20/06/2022
 * Time: 18:32
 */

namespace App\Services\Products;


use App\Produit;
use App\Services\Products\ProduitCrud;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait ProduitServices
{

  use ProduitCrud;

  private function filter(Builder $builder, Request $request) : Builder
  {
    if(!empty($request->query('famille')) && $request->query('famille') != "all")
    {
      $builder->where("famille_id", $request->query("famille"));
    }

    if(!empty($request->query('keyword')))
    {
      $keyword = $request->query('keyword');
      $builder->whereRaw("( reference like '%{$keyword}%' OR libelle like '%{$keyword}%')");
    }
    return $builder;
  }

  private function listeProduit() : Builder{
    $produits = Produit::with("famille")
      ->orderBy("reference", 'asc');
    return $produits;
  }

  private function produitDetails(string $reference){
    return Produit::with("famille")->where("reference", $reference)->firstOrFail();
  }

  private function updateStock(int $produit, int $qte)
  {
    $produit = Produit::find($produit);
    if($produit)
    {
      $produit->stock += $qte;
      $produit->save();
    }
  }
}