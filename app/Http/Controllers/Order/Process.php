<?php
/**
 * Created by PhpStorm.
 * User: BW.KOFFI
 * Date: 24/08/2017
 * Time: 08:33
 */

namespace App\Http\Controllers\Order;


use App\Application;
use App\LignePieceComptable;
use App\Metier\Finance\InvoiceFrom;
use App\Mission;
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

    private function getCommercializableList(Request $request)
    {
        $commercializables = new Collection();

        if($request->has('from') && $request->input('from') == InvoiceFrom::mission())
        {

        }else{
            $commercializables = collect(Produit::orderBy("libelle")->get());
        }

        collect(Mission::with("vehicule")
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
            "lignes.*.id" => "required|numeric",
            "lignes.*.designation" => "required",
            "lignes.*.quantite" => "required|numeric|min:1",
            "lignes.*.prixunitaire" => "required|numeric|min:50",
            "lignes.*.modele" => "required",
            "lignes.*.modele_id" => "required|numeric|min:1",
            "partenaire_id" => "required|exists:partenaire,id",
            "montantht" => "required|numeric",
            "isexonere" => "required|boolean",
            "conditions" => "required",
            "validite" => "required",
            "objet" => "required",
            "delailivraison" => "required",
        ]);
    }

    /**
     * @param PieceComptable $pieceComptable
     * @param array $data
     * @return bool
     */
    private function addLineToPieceComptable(PieceComptable $pieceComptable, array $data)
    {
        foreach ($data as $ligne)
        {
            $lignepiece = new LignePieceComptable($ligne);
            $lignepiece->piececomptable()->associate($pieceComptable);
            $lignepiece->saveOrFail();

            if($lignepiece->modele == Mission::class)
            {
                $mission = Mission::find($lignepiece->modele_id);
                $mission->piececomptable_id = $pieceComptable->id;
                $mission->saveOrFail();
            }
        }

        return true;
    }

    /**
     * @param int $type
     * @param Partenaire $partenaire
     * @param Collection $data
     * @param integer|null $id
     * @return PieceComptable
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

        $piececomptable->utilisateur_id = Auth::id();
        $piececomptable->tva = PieceComptable::TVA;

        $piececomptable->partenaire()->associate($partenaire);

        if( ($id == null || $id == 0) && $type === PieceComptable::PRO_FORMA)
        {
            $piececomptable->referenceproforma = Application::getInitial().Application::getNumeroProforma(true);
            $piececomptable->creationproforma = Carbon::now()->toDateTimeString();
            $piececomptable->etat = Statut::PIECE_COMPTABLE_PRO_FORMA;
        }

        $piececomptable->saveOrFail();

        return $piececomptable;
    }

    /**
     * @param $reference
     * @return \Illuminate\Database\Eloquent\Model|null|PieceComptable
     */
    private function getPieceComptableForReference($reference)
    {
        return PieceComptable::with('partenaire','lignes','utilisateur')
            ->where("referenceproforma",$reference)
            ->orWhere("referencefacture",$reference)
            ->firstOrFail();
    }
}