<?php

namespace App\Http\Controllers\Web\Partenaire;

use App\Application;
use App\Events\BCcreated;
use App\Http\Controllers\Controller;
use App\Intervention;
use App\LignePieceFournisseur;
use App\Metier\Behavior\Notifications;
use App\Metier\Security\Actions;
use App\Partenaire;
use App\PieceFournisseur;
use App\Produit;
use App\Service;
use App\Services\FactureServices;
use App\Statut;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FournisseurController extends Controller
{
    use FactureServices;

    public function newOrder()
    {
	    $commercializables = $this->getCommercializableList();

        $partenaires = Partenaire::where('isfournisseur','=',true)->get();

        return view('partenaire.order.nouvelle', compact('partenaires',"commercializables"));
    }

    public function addOrder(Request $request)
    {
        $this->validRequestPartner($request);

        try{
            $pieceFournisseur = new PieceFournisseur($request->except('_token','produits','price','prix','quantity',
	            'produit_id','quantite','modele', 'designation', 'complement', 'mode', 'from', "reference"));
            $pieceFournisseur->datepiece = Carbon::createFromFormat('d/m/Y', $request->input('datepiece'));
            $pieceFournisseur->employe_id = Auth::id();

            $pieceFournisseur->statut = $request->input("mode");

            if($pieceFournisseur->statut == Statut::PIECE_COMPTABLE_BON_COMMANDE){
            	$pieceFournisseur->numerobc = Application::getNumeroBC(true);
            }

            $pieceFournisseur->save();

            $this->saveLines($request, $pieceFournisseur);

	        if($pieceFournisseur->statut == Statut::PIECE_COMPTABLE_BON_COMMANDE){
		        event(new BCcreated($pieceFournisseur));
	        }

        }catch (ModelNotFoundException $e){
            return back()->with('Impossible d\'ajouter cette facture. La référence est déjà utilisée. <br>'.$e->getMessage());
        }

        $notification = new Notifications();
        $notification->add(Notifications::SUCCESS,"Facture fournisseur enregistrée avec succès !");
        return back()->with(Notifications::NOTIFICATION_KEYS_SESSION, $notification);
    }

    private function saveLines(Request $request, PieceFournisseur $piece)
    {
    	for($i=0; $i < count($request->input("produit_id")); $i++)
	    {
	    	$line = new LignePieceFournisseur();
	    	$line->produit_id = $request->input("produit_id")[$i];
	    	$line->prix = $request->input("prix")[$i];
	    	$line->quantite = $request->input("quantite")[$i];
	    	$line->piecefournisseur_id = $piece->id;
	    	$line->modele = $request->input("modele")[$i];
	    	$line->designation = $request->input("designation")[$i];
	    	$line->reference = $request->input("reference")[$i];
	    	$line->save();

	    	if($line->modele == Produit::class)
		    {
			    $this->updateStock($line->produit_id, $line->quantite);
		    }
		    if($line->modele == Intervention::class)
		    {
		    	$this->updateIntervention($line->produit_id, $piece);
		    }

	    }
    }

	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
    public function liste(Request $request)
    {
	    $pieces = $this->getPiecesFournisseur($request);
	    $statuts = [
		    Statut::PIECE_COMPTABLE_FACTURE_ANNULEE , Statut::PIECE_COMPTABLE_FACTURE_PAYEE ,
		    Statut::PIECE_COMPTABLE_FACTURE_AVEC_BL , Statut::PIECE_COMPTABLE_FACTURE_SANS_BL
	    ];
	    return view("partenaire.order.liste", compact("pieces", "statuts"));
    }

	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
	 */
    private function getPiecesFournisseur(Request $request)
    {
	    $raw = PieceFournisseur::with("partenaire","lignes")->orderBy("datepiece", "desc")->paginate(30);

	    return $raw;
    }

    public function switchPiece(){

    	try{
		    $bc = PieceFournisseur::find(request()->input("id"));
		    $bc->reference = request()->input("reference");
		    $bc->statut = Statut::PIECE_COMPTABLE_FACTURE_AVEC_BL;
		    $bc->save();
	    }catch (ModelNotFoundException $e){
    		return redirect()->back()->withErrors("Pièce introuvable");
	    }

	    $notification = new Notifications();
	    $notification->add(Notifications::SUCCESS,"Bon de commande transformé en facture !");
	    return back()->with(Notifications::NOTIFICATION_KEYS_SESSION, $notification);
    }

    public function validePiece(int $id){
    	$this->authorize(Actions::UPDATE, collect([ Service::INFORMATIQUE, Service::DG]));

    	try{
    		$piece = PieceFournisseur::find($id);
    		$piece->statut = Statut::PIECE_COMPTABLE_BON_COMMANDE_VALIDE;
    		$piece->save();

		    $notification = new Notifications();
		    $notification->add(Notifications::SUCCESS,"Bon de commande validé !");
		    return back()->with(Notifications::NOTIFICATION_KEYS_SESSION, $notification);
	    }catch (\Exception $e){
    		return back()->withErrors($e->getMessage());
	    }
    }

    public function details(int $id)
    {
    	$piece = PieceFournisseur::with("lignes","partenaire")->find($id);
    	if($piece){
		    return view("partenaire.order.details", compact("piece"));
	    }else{
		    return back()->withErrors("Impossible de trouver la commande.");
	    }

    }
}
