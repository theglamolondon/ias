<?php
/**
 * Created by PhpStorm.
 * User: BW.KOFFI
 * Date: 24/08/2017
 * Time: 08:33
 */

namespace App\Http\Controllers\Web\Order;


use App\Application;
use App\LignePieceComptable;
use App\Metier\Finance\InvoiceFrom;
use App\Mission;
use App\MissionPL;
use App\Partenaire;
use App\PieceComptable;
use App\Produit;
use App\Statut;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

trait Process
{
    private function getPartenaireList(Request $request)
    {
        return Partenaire::orderBy("raisonsociale")->get();
    }

    /**
     * @param Request $request
     * @return Collection
     */
    private function getCommercializableList(Request $request)
    {
        $commercializables = new Collection();

        if($request->has('from') && $request->input('from') == InvoiceFrom::mission())
        {

        }else{
            $commercializables = collect(Produit::orderBy("libelle")->get());
        }

        //Pour les missions VL
        collect(Mission::with("vehicule")
            ->where("status",Statut::MISSION_COMMANDEE)
            ->whereNull("piececomptable_id")
            ->get()
        )->each(function ($value, $key) use($commercializables){
            $commercializables->push($value);
        });

        //Pour les missions PL
        collect(MissionPL::with("vehicule")
            ->where("status",Statut::MISSION_COMMANDEE)
            ->whereNull("piececomptable_id")
            ->get()
        )->each(function ($value, $key) use($commercializables){
            $commercializables->push($value);
        });

        return $commercializables;
    }

    private function validateProformaRequest(Request $request)
    {
        return Validator::make($request->input(),[
            "lines.*.id" => "required|numeric",
            "lines.*.designation" => "required",
            "lines.*.quantite" => "required|numeric|min:1",
            "lines.*.prixunitaire" => "required|numeric|min:5",
            "lines.*.modele" => "required",
            "lines.*.modele_id" => "required|numeric",
            "lines.*.remise" => "present",
            "partenaire_id" => "required|exists:partenaire,id",
            "montantht" => "required|numeric",
            "isexonere" => "required|boolean",
            "conditions" => "required",
            "validite" => "required",
            "objet" => "required",
            "delailivraison" => "required",
        ],[
	        "conditions.required" => "Veuillez saisir les conditions SVP.",
	        "validite.required" => "Veuillez saisir la validité de l'offre.",
	        "objet.required" => "Veuillez saisir l'objet de l'offre.",
	        "delailivraison.required" => "Veuillez saisir le délai de livraison.",
        ])->validate();
    }

    /**
     * @param PieceComptable $pieceComptable
     * @param array $data
     * @return bool
     * @throws \Throwable
     */
    private function addLineToPieceComptable(PieceComptable $pieceComptable, array $data)
    {
        foreach ($data as $ligne)
        {
            $lignepiece = new LignePieceComptable($ligne);
            $lignepiece->piececomptable()->associate($pieceComptable);
            $lignepiece->saveOrFail();

	        $lignepiece->modele = str_replace("\\", "\\\\", $lignepiece->modele);

            if($lignepiece->modele == Mission::class)
            {
                $mission = Mission::find($lignepiece->modele_id);
                $mission->piececomptable_id = $pieceComptable->id;
                $mission->saveOrFail();
            }elseif ($lignepiece->modele == MissionPL::class){
	            $mission = MissionPL::find($lignepiece->modele_id);
	            $mission->piececomptable_id = $pieceComptable->id;
	            $mission->saveOrFail();
            }
        }

        return true;
    }

	/**
	 * @param PieceComptable $pieceComptable
	 *
	 * @return bool|null
	 * @throws \Exception
	 */
    private function deleteAllLines(PieceComptable $pieceComptable)
    {
    	return LignePieceComptable::where("piececomptable_id","=", $pieceComptable->id)
		    ->delete();
    }

    /**
     * @param $type
     * @param Partenaire $partenaire
     * @param Collection $data
     * @param null $id
     * @return PieceComptable
     * @throws \Throwable
     */
    private function createPieceComptable($type, Partenaire $partenaire, Collection $data, $id=null)
    {
        $piececomptable = new PieceComptable();

        $piececomptable->montantht = $data->get("montantht");
        $piececomptable->isexonere = $data->get("isexonere");
        $piececomptable->conditions = $data->get("conditions");
        $piececomptable->validite = $data->get("validite");
        $piececomptable->objet = $data->get("objet");
        $piececomptable->delailivraison = $data->get("delailivraison");
        $piececomptable->type_piece = $data->get("type_piece");
        $piececomptable->utilisateur_id = Auth::id();
        $piececomptable->tva = PieceComptable::TVA;

        $piececomptable->partenaire()->associate($partenaire);

        if( ($id == null || $id == 0) && $type === PieceComptable::PRO_FORMA)
        {
            $piececomptable->referenceproforma = Application::getNumeroProforma(true);
            $piececomptable->creationproforma = Carbon::now()->toDateTimeString();
            $piececomptable->etat = Statut::PIECE_COMPTABLE_PRO_FORMA;
        }

        $piececomptable->saveOrFail();

        return $piececomptable;
    }

	/**
	 * @param PieceComptable $piececomptable
	 * @param Collection $data
	 *
	 * @return bool
	 * @throws \Throwable
	 */
    private function updatePieceComptable(PieceComptable $piececomptable, Collection $data)
    {
	    $piececomptable->montantht = $data->get("montantht");
	    $piececomptable->isexonere = $data->get("isexonere");
	    $piececomptable->conditions = $data->get("conditions");
	    $piececomptable->validite = $data->get("validite");
	    $piececomptable->objet = $data->get("objet");
	    $piececomptable->delailivraison = $data->get("delailivraison");
	    $piececomptable->partenaire_id = $data->get("partenaire_id");

	    $piececomptable->utilisateur_id = Auth::id();
	    $piececomptable->tva = PieceComptable::TVA;

	    $piececomptable->saveOrFail();

	    return true;
    }

    /**
     * @param $reference
     * @return \Illuminate\Database\Eloquent\Model|null|PieceComptable
     */
    private function getPieceComptableFromReference($reference)
    {
        return PieceComptable::with('partenaire','lignes','utilisateur')
            ->where("referenceproforma",$reference)
            ->orWhere("referencefacture",$reference)
            ->firstOrFail();
    }

	/**
	 * @param $id
	 *
	 * @return \Illuminate\Database\Eloquent\Model|static|PieceComptable
	 */
    private function getPieceComptableFromId($id)
    {
	    return PieceComptable::with('partenaire','lignes','utilisateur')
	                         ->where("id",'=',$id)
	                         ->firstOrFail();
    }

    private function switchToNormal(PieceComptable &$pieceComptable, $numeroPreImprime, $referenceBonCommande = null)
    {
        $pieceComptable->referencebc = $referenceBonCommande;
        $pieceComptable->referencefacture = $numeroPreImprime;
        $pieceComptable->creationfacture = Carbon::now()->toDateTimeString();
        $pieceComptable->etat = Statut::PIECE_COMPTABLE_FACTURE_SANS_BL;
        $pieceComptable->save();
    }
}