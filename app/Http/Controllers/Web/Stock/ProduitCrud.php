<?php
/**
 * Created by PhpStorm.
 * User: BW.KOFFI
 * Date: 02/12/2017
 * Time: 11:07
 */

namespace App\Http\Controllers\Web\Stock;


use App\Famille;
use App\Produit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

trait ProduitCrud
{
	/**
	 * @param array $data
	 * @param Produit|null $produit
	 *
	 * @return Produit
	 * @throws \Throwable
	 */
    protected function persitProduct(array $data, Produit $produit = null)
    {
        if(!$produit){
            $produit = new Produit();
        }

        $produit->reference = $data["reference"];
        $produit->libelle = $data["libelle"];
        $produit->prixunitaire = $data["prixunitaire"];
        $produit->isdisponible = isset($data["isdisponible"]);
        $produit->famille()->associate(Famille::find($data["famille_id"]));

        if(key_exists("stock", $data)){
	        $produit->stock = intval($data["stock"]);
        }

        $produit->saveOrFail();

        return $produit;
    }

    protected function getVente(Produit $produit){

    	$annee = date('Y');

    	//creationfacture

    	$sql = <<<EOD
SELECT t2.id, month(p.creationproforma) as mois, sum(t1.quantite) as total
FROM lignepiece t1 JOIN produit t2 ON t1.modele_id = t2.id JOIN piececomptable p ON p.id = t1.piececomptable_id
WHERE year(p.creationproforma) = {$annee} AND t2.id = {$produit->id}
GROUP BY 1,2;
EOD;

		return DB::select($sql);

    }

    /**
     * @param $reference
     * @return bool
     */
    protected function checkReferenceExist($reference){
        if(Produit::where("reference",$reference)->count() > 0){
            return true;
        }else{
            return false;
        }
    }

	/**
	 * @param int $id
	 *
	 * @return bool|null
	 * @throws \Exception
	 */
    protected function delete (int $id){

        $produit = Produit::find($id);
        return $produit->delete();
    }

	/**
	 * @return \Illuminate\Support\Collection
	 */
    private function getPeriode()
    {
	    $du = request()->has("debut") ? Carbon::createFromFormat("d/m/Y",request()->query("debut")) : null;
	    $au = request()->has("fin") ? Carbon::createFromFormat("d/m/Y",request()->query("fin")) : null;

	    if($du == null){$du = Carbon::now()->firstOfMonth(); }
	    if($au == null){$au = Carbon::now(); }

	    return collect(["du" => $du, "au" => $au]);
    }

    private function getDataToCharts()
    {
	    $periode = $this->getPeriode();
		$produit = Produit::class;

	    $du = $periode->get("du");
	    $au = $periode->get("au");

    	$sql = <<<EOF
SELECT p.raisonsociale, sum(l.quantite) as nbre FROM piececomptable f JOIN lignepiece l ON f.id = l.piececomptable_id
JOIN partenaire p ON f.partenaire_id = p.id WHERE f.referencebl is NOT null AND f.creationbl BETWEEN '{$du->format('Y-m-d')}' AND '{$au->format('Y-m-d')}' AND l.modele = '$produit' GROUP BY 1
EOF;
;
	    return DB::select($sql);
    }

    private function getProduitRatio()
    {
    	$sql = "SELECT t2.reference, t2.stock, t2.libelle, f.libelle as famille, t2.isdisponible, t2.prixunitaire, sum(t1.quantite) as total FROM lignepiece t1 JOIN produit t2 on t1.modele_id = t2.id JOIN famille f ON t2.famille_id = f.id";

    	$complement = "";

    	$periode = $this->getPeriode();
	    $du = $periode->get("du");
	    $au = $periode->get("au");

    	if($du && $au){
		    $complement = " JOIN piececomptable p ON t1.piececomptable_id = p.id WHERE p.creationfacture BETWEEN '{$du->format('Ymd')}' AND '{$au->format('Ymd')}' ";
	    }

	    if(!empty(request()->query('famille')) && request()->query('famille') != "all")
	    {
		    if(empty($complement)){
			    $complement .= " WHERE f.id = ".request()->query('famille');
		    }else{
			    $complement .= " AND f.id = ".request()->query('famille');
		    }
	    }

	    if(request()->has("available") && request()->query("available") != 0)
	    {
    		if(empty($complement)){
    			$complement .= " WHERE t2.isdisponible = ".(intval(request()->query("available"))-1);
		    }else{
			    $complement .= " AND t2.isdisponible = ".(intval(request()->query("available"))-1);
		    }
	    }

	    $produit = Produit::class;
	    if(empty($complement)){
		    $complement .= " WHERE (t1.modele = '".str_replace("\\","\\\\",Produit::class)."' OR t1.modele = '".str_replace("\\","",Produit::class)."') ";
	    }else{
		    $complement .= " AND (t1.modele = '".str_replace("\\","\\\\",Produit::class)."' OR t1.modele = '".str_replace("\\","",Produit::class)."') ";
	    }

	    $display = request()->query('display') ?? 10;


    	return DB::select($sql.$complement." GROUP BY 1,2,3,4,5,6 ORDER BY 7 DESC LIMIT $display;");

    }


}