<?php

namespace App\Http\Controllers\Web\Money;

use App\Metier\Behavior\Notifications;
use App\PieceComptable;
use App\PieceFournisseur;
use App\Statut;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReglementController extends Controller
{
    public function reglementClient(Request $request)
    {
        $this->validatePaiementClient($request);

        $piece = null;
        try{
            DB::beginTransaction();
                foreach ($request->input('facture') as $facture_id){
                    $piece = PieceComptable::findOrFail($facture_id);
                    $piece->etat = Statut::PIECE_COMPTABLE_FACTURE_PAYEE;
                    $piece->datereglement = Carbon::createFromFormat('d/m/Y', $request->input('datereglement'))->toDateString();
                    $piece->moyenpaiement_id = $request->input('moyenpaiement_id');
                    $piece->remarquepaiement = $request->input('remarquepaiement')."\r\n"."(Effectué par ".Auth::user()->employe->nom." ".Auth::user()->employe->prenoms.")";
                    $piece->save();
                }
            DB::commit();
        }catch (ModelNotFoundException | \Exception $e){
            DB::rollBack();
            dd($e);
        }

        $notification = new Notifications();
        $notification->add(Notifications::SUCCESS,"Le paiement des factures client a été réalisé avec succès !");
        return back()->with(Notifications::NOTIFICATION_KEYS_SESSION, $notification);
    }

    private function validatePaiementClient(Request $request){
        $this->validate($request, [
            'facture' => 'required|array',
            'facture.*' => 'integer|exists:piececomptable,id',
            'datereglement' => 'required|date_format:d/m/Y',
            'moyenpaiement_id' => 'required|exists:moyenreglement,id',
            'remarquepaiement' => 'present'
        ]);
    }

    private function validatePaiementFournisseur(Request $request){
        $this->validate($request, [
            'facture' => 'required|array',
            'facture.*' => 'integer|exists:piecefournisseur,id',
            'datereglement' => 'required|date_format:d/m/Y',
            'moyenpaiement_id' => 'required|exists:moyenreglement,id',
            'remarquepaiement' => 'present'
        ]);
    }

    public function reglementFournisseur(Request $request)
    {
        $this->validatePaiementFournisseur($request);

        $piece = null;
        try{
            DB::beginTransaction();
            foreach ($request->input('facture') as $facture_id){
                $piece = PieceFournisseur::findOrFail($facture_id);
                $piece->statut = Statut::PIECE_COMPTABLE_FACTURE_PAYEE;
                $piece->datereglement = Carbon::createFromFormat('d/m/Y', $request->input('datereglement'))->toDateString();
                $piece->moyenpaiement_id = $request->input('moyenpaiement_id');
                $piece->remarquepaiement = $request->input('remarquepaiement')."\r\n"."(Effectué par ".Auth::user()->employe->nom." ".Auth::user()->employe->prenoms.")";
                $piece->save();
            }
            DB::commit();
        }catch (ModelNotFoundException | \Exception $e){
            DB::rollBack();
            dd($e);
        }

        $notification = new Notifications();
        $notification->add(Notifications::SUCCESS,"Le paiement des factures fournisseur a été réalisé avec succès !");
        return back()->with(Notifications::NOTIFICATION_KEYS_SESSION, $notification);
    }
}