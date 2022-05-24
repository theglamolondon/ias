<?php
/**
 * Created by PhpStorm.
 * User: SUPPORT.IT
 * Date: 20/01/2018
 * Time: 16:56
 */

namespace App\Http\Controllers\Web\Partenaire;


use App\Http\Controllers\Order\Commercializable;
use App\Intervention;
use App\Partenaire;
use App\PieceComptable;
use App\PieceFournisseur;
use App\Produit;
use App\Statut;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait Factures
{
	private function getStatus(): array {
		return [
			Statut::PIECE_COMPTABLE_FACTURE_PAYEE => Statut::getStatut(Statut::PIECE_COMPTABLE_FACTURE_PAYEE),
			Statut::PIECE_COMPTABLE_FACTURE_ANNULEE => Statut::getStatut(Statut::PIECE_COMPTABLE_FACTURE_ANNULEE),
			Statut::PIECE_COMPTABLE_FACTURE_AVEC_BL => Statut::getStatut(Statut::PIECE_COMPTABLE_FACTURE_AVEC_BL),
			Statut::PIECE_COMPTABLE_FACTURE_SANS_BL => Statut::getStatut(Statut::PIECE_COMPTABLE_FACTURE_SANS_BL),
			Statut::PIECE_COMPTABLE_BON_COMMANDE => Statut::getStatut(Statut::PIECE_COMPTABLE_BON_COMMANDE),
		];
	}

	/**
	 * @return \Illuminate\Support\Collection|Commercializable
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

	private function updateIntervention(int $interventionId, PieceFournisseur $piece)
	{
		$intervention = Intervention::find($interventionId);
		if($intervention)
		{
			$intervention->piecefournisseur_id = $piece->id;
			$intervention->save();
		}
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
}