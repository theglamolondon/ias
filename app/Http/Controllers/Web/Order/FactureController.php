<?php

namespace App\Http\Controllers\Web\Order;

use App\Http\Controllers\Controller;
use App\Metier\Behavior\Notifications;
use App\Metier\Security\Actions;
use App\PieceComptable;
use App\Service;
use App\Statut;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FactureController extends Controller
{
    use Process;

	/**
	 * FactureController constructor.
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
	public function listeProforma(Request $request)
    {
	    $this->authorize(Actions::READ, collect([Service::DG, Service::ADMINISTRATION, Service::COMPTABILITE, Service::INFORMATIQUE, Service::LOGISTIQUE]));
        return $this->liste($request, PieceComptable::PRO_FORMA);
    }

	/**
	 * FactureController constructor.
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
    public function listeFacture(Request $request)
    {
	    $this->authorize(Actions::READ, collect([Service::DG, Service::ADMINISTRATION, Service::COMPTABILITE, Service::INFORMATIQUE, Service::LOGISTIQUE]));
	    $statuts = [
	     Statut::PIECE_COMPTABLE_FACTURE_ANNULEE , Statut::PIECE_COMPTABLE_FACTURE_PAYEE ,
	     Statut::PIECE_COMPTABLE_FACTURE_AVEC_BL , Statut::PIECE_COMPTABLE_FACTURE_SANS_BL
	    ];

        return $this->liste($request, PieceComptable::FACTURE, $statuts);
    }

	/**
	 * FactureController constructor.
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
    public function liste(Request $request, $type = null, $statuts = null)
    {
	    $this->authorize(Actions::READ, collect([Service::DG, Service::ADMINISTRATION, Service::COMPTABILITE, Service::INFORMATIQUE, Service::LOGISTIQUE]));
        $pieces = $this->getPiecesComptable($request, $type);
        return view("order.liste", compact("pieces", "statuts"));
    }

    /**
     * @param Request $request
     * @param null | int $type
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    private function getPiecesComptable(Request $request, $type = null)
    {
        $periode = $this->getPeriode($request);

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
     * @param Request $request
     * @return \Illuminate\Support\Collection
     */
    private function getPeriode(Request $request)
    {
        $debut = null;
        $fin = null;

        if($request->has("debut"))
            $debut = Carbon::createFromFormat("d/m/Y", $request->input("debut"));

        if($request->has("fin"))
            $fin = Carbon::createFromFormat("d/m/Y", $request->input("fin"));

        return collect(compact("debut", "fin"));
    }

	/**
	 * @param $reference
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
    public function details($reference)
    {
	    $this->authorize(Actions::READ, collect([Service::DG, Service::ADMINISTRATION, Service::COMPTABILITE, Service::INFORMATIQUE, Service::LOGISTIQUE]));
        $piece = $this->getPieceComptableFromReference($reference);
        return view("order.facture", compact("piece"));
    }

	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
    public function makeNormal(Request $request)
    {
	    $this->authorize(Actions::READ, collect([Service::DG, Service::ADMINISTRATION, Service::COMPTABILITE, Service::INFORMATIQUE, Service::LOGISTIQUE]));
        $this->validate($request, $this->valideRulesNormalPiece(), [
            "referencefacture.required" => "La référence de la facture pré-imprimée est requise."
        ]);

        $piece = $this->getPieceComptableFromReference($request->input("referenceproforma"));

        $this->switchToNormal($piece, $request->input("referencefacture"), $request->input("referencebc"));

        $notif = new Notifications();
        $notif->add(Notifications::SUCCESS,sprintf("Transformation de la pro forma %s en facture N° %s réussie", $piece->referenceproforma, $piece->referencefacture));
        return back()->with(Notifications::NOTIFICATION_KEYS_SESSION, $notif);
    }

	/**
	 * @param string $reference
	 * @param Request $request
	 *
	 * @return $this|\Illuminate\Http\RedirectResponse
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
    public function annuler(string $reference, Request $request)
    {
	    $this->authorize(Actions::READ, collect([Service::DG, Service::ADMINISTRATION, Service::COMPTABILITE, Service::INFORMATIQUE]));
        $sessionToken = $request->session()->token();
        $token = $request->query('_token');
        if (! is_string($sessionToken) || ! is_string($token) || !hash_equals($sessionToken, $token) ) {
            return back()->withErrors('Votre demande n\'a pas été validée. Veuillez recommencer SVP !');
        }

        $piece = $this->getPieceComptableFromReference($reference);
        $piece->etat = Statut::PIECE_COMPTABLE_FACTURE_ANNULEE;
        $piece->save();

        $notif = new Notifications();
        $notif->add(Notifications::SUCCESS,sprintf("Facture N° %s annulée.", $piece->referencefacture ?? $piece->referenceproforma));
        return back()->with(Notifications::NOTIFICATION_KEYS_SESSION, $notif);
    }

    private function valideRulesNormalPiece()
    {
        return [
            "referenceproforma" => "required|exists:piececomptable,referenceproforma",
            "referencefacture" => "required|numeric",
            "referencebc" => "present",
        ];
    }
}
