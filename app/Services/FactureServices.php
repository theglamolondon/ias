<?php
/**
 * Created by PhpStorm.
 * User: SUPPORT.IT
 * Date: 20/01/2018
 * Time: 16:56
 */

namespace App\Services;

use App\Application;
use App\Interfaces\ICommercializableLine;
use App\Intervention;
use App\LignePieceComptable;
use App\Metier\Security\Actions;
use App\Partenaire;
use App\PieceComptable;
use App\PieceFournisseur;
use App\Produit;
use App\Service;
use App\Statut;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

trait FactureServices
{
  use HelperServices;

  /**
   * @param int $id
   * @return LengthAwarePaginator
   */
  private function getDetailsByClientPartner(int $id) : LengthAwarePaginator {
    $pieces = PieceComptable::with('utilisateur','moyenPaiement')
      ->where("partenaire_id", $id);

    $this->getParameters($pieces);

    return $pieces->orderBy('creationproforma')
      ->whereNotNull("referencefacture")
      ->orderBy('creationfacture')
      ->paginate(30);
  }

  /**
   * @return array
   */
	private function getStatus(): array {
		return [
			Statut::PIECE_COMPTABLE_FACTURE_PAYEE   => Statut::getStatut(Statut::PIECE_COMPTABLE_FACTURE_PAYEE),
			Statut::PIECE_COMPTABLE_FACTURE_ANNULEE => Statut::getStatut(Statut::PIECE_COMPTABLE_FACTURE_ANNULEE),
			Statut::PIECE_COMPTABLE_FACTURE_AVEC_BL => Statut::getStatut(Statut::PIECE_COMPTABLE_FACTURE_AVEC_BL),
			Statut::PIECE_COMPTABLE_FACTURE_SANS_BL => Statut::getStatut(Statut::PIECE_COMPTABLE_FACTURE_SANS_BL),
			Statut::PIECE_COMPTABLE_BON_COMMANDE    => Statut::getStatut(Statut::PIECE_COMPTABLE_BON_COMMANDE),
		];
	}

	/**
	 * @return \Illuminate\Support\Collection|ICommercializableLine
	 */
	private function getCommercializableList()
	{
		$commercializables = collect(Produit::orderBy("libelle")->get());

		collect(Intervention::with("vehicule","typeIntervention")
		               ->whereNotNull("partenaire_id")
		               ->whereNull("piecefournisseur_id")
		               ->get()
		)->each(function ($value, $key) use($commercializables){
			$commercializables->push($value);
		});

		return $commercializables;
	}

	protected function validRequestPartner(Request $request){
		$this->validate($request, [
			'datepiece' => 'required|date_format:d/m/Y',
			'objet' => 'required',
			'mode' => 'required',
			'reference' => 'required_if:mode,'.Statut::PIECE_COMPTABLE_FACTURE_AVEC_BL,
			'montanttva' => 'required|integer',
			'montantht' => 'required|integer',
			'produit_id' => 'required|array',
			'prix' => 'required|array',
			'quantite' => 'required|array',
			'modele' => 'required|array',
			'designation' => 'required|array',
			'partenaire_id' => 'required|exists:partenaire,id',
			'observation' => 'present'
		]);
	}

  private function validateProformaRequest(Request $request){
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
   * @param Request $request
   *
   * @return \Illuminate\Http\JsonResponse
   * @throws \Throwable
   */
  public function creerNouvelleProforma(Request $request) {
    $this->validateProformaRequest($request);

    try{

      $partenaire = new Partenaire(['id' => $request->input("partenaire_id")]);

      $piececomptable = $this->createPieceComptableProforma($partenaire, collect($request->only(["montantht", "isexonere", "conditions", "validite", "delailivraison", "objet", "type_piece"])));

      $this->addLineToPieceComptable($piececomptable, $request->input("lines"));

      return response()->json(["reference" => urlencode($piececomptable->referenceproforma)],200, [],JSON_UNESCAPED_UNICODE);
    }catch(\Exception $e){
      return response()->json(["code" => 0, "message" => $e->getMessage() ],400);
    }
  }

  /**
   * @param Partenaire $partenaire
   * @param Collection $data
   * @param null $id
   * @return PieceComptable
   * @throws \Throwable
   */
  private function createPieceComptableProforma(Partenaire $partenaire, Collection $data, $id=null)
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

    if($id == null || $id == 0)
    {
      $piececomptable->referenceproforma = Application::getNumeroProforma(true);
      $piececomptable->creationproforma = Carbon::now()->toDateTimeString();
      $piececomptable->etat = Statut::PIECE_COMPTABLE_PRO_FORMA;
    }

    $piececomptable->saveOrFail();

    return $piececomptable;
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
    }

    return true;
  }

  /**
   * @param Request $request
   * @param null | int $type
   * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
   */
  private function getPiecesComptable(Request $request, $type = null) {
    $periode = $this->getPeriodeFromRequest($request);

    $raw = PieceComptable::with("partenaire","lignes");

    if($periode->get("debut") && $periode->get("fin")){
      $raw = $raw->whereBetween("creationproforma", [
          $periode->get("debut")->toDateString().' 00:00:00',
          $periode->get("fin")->toDateTimeString().' 23:59:59']
      );
    }

    if(!empty($request->query('reference'))){
      $raw->whereRaw("( referencebc like '%{$request->query('reference')}%' OR referenceproforma like '%{$request->query('reference')}%' 
            OR referencebl like '%{$request->query('reference')}%' OR referencefacture like '%{$request->query('reference')}%' ) ");
    }

    if($type){
      switch ($type){
        case PieceComptable::PRO_FORMA : $raw->where("etat", "=", Statut::PIECE_COMPTABLE_PRO_FORMA); break;
        case PieceComptable::FACTURE : $raw->whereIn("etat", [Statut::PIECE_COMPTABLE_FACTURE_AVEC_BL, Statut::PIECE_COMPTABLE_FACTURE_SANS_BL]); break;

        default : null;
      };
    }

    if($request->has("status") && $request->input("status") != "all"){
      $raw = $raw->where("etat","=", $request->input("status"));
    }

    $raw->orderBy("creationproforma","desc");

    return $raw->paginate(30);
  }
	/**
	 * @param Builder $builder
	 */
	private function getParameters(Builder &$builder)
	{
		$du = request()->has("debut") ? Carbon::createFromFormat("d/m/Y", request()->query("debut")) : null;
		$au = request()->has("fin") ? Carbon::createFromFormat("d/m/Y", request()->query("fin")) : null;

		if($du && $au)
		{
			if($builder->getModel() instanceof PieceComptable){
				$builder->whereBetween("creationproforma", [$du->toDateString(), $au->toDateString()]);
			}
			if($builder->getModel() instanceof PieceFournisseur){
				$builder->whereBetween("datepiece", [$du->toDateString(), $au->toDateString()]);
			}
		}

		if(request()->has("status") && request()->query("status") != "*"){
			$builder->where("etat", "=", request()->query("status"));
		}
	}

	private function getSommeTotale(Partenaire $partenaire){
		$du = request()->has("debut") ? Carbon::createFromFormat("d/m/Y", request()->query("debut")) : null;
		$au = request()->has("fin") ? Carbon::createFromFormat("d/m/Y", request()->query("fin")) : null;

		$statut = implode("','",[Statut::PIECE_COMPTABLE_BON_COMMANDE_VALIDE, Statut::PIECE_COMPTABLE_BON_COMMANDE]);
		$sql = "select sum(montantht+montanttva) as somme from piecefournisseur where partenaire_id = {$partenaire->id} and statut not in ('$statut')";

		if($du && $au){
			$sql .= " and creationproforma between {$du->toDateString()} and {$au->toDateString()} ";
		}
		return DB::select($sql)[0];
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
}