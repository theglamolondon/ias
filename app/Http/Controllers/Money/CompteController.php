<?php

namespace App\Http\Controllers\Money;

use App\Compte;
use App\LigneCompte;
use App\Metier\Behavior\Notifications;
use App\Metier\Security\Actions;
use App\Service;
use App\Utilisateur;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CompteController extends Controller
{
	use Tresorerie;

    public function registre()
    {
        $comptes = $this->getListCompteByUser();

        $utilisateurs = Utilisateur::with('employe')->get();

        return view('compte.registre', compact("comptes", "utilisateurs"));
    }

    public function newSortieCompte(Request $request){
    	//dd($request->query("amount"), $request->query('mission'));

	    $comptes = $this->getListCompteByUser();

	    return view("compte.new-sortie", compact("comptes"));
    }

	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
    public function addNewSousCompte(Request $request)
    {
	    $this->authorize(Actions::READ, collect([Service::DG, Service::INFORMATIQUE, Service::ADMINISTRATION, Service::COMPTABILITE]));

    	$this->validate($request, [
    		"libelle" => "required",
    		"employe_id" => "required",
    		"commentaire" => "present",
	    ]);

	    $souscompte = new Compte();
		$souscompte->datecreation = Carbon::now()->toDateTimeString();

    	if($this->checkExistUser($request->input('employe_id')))
    	{
    		$souscompte->employe_id = $request->input('employe_id');
	    }

	    if($request->has('can_appro')){
		    $souscompte->can_appro = true;
	    }

    	$souscompte->libelle = $request->input("libelle");
    	$souscompte->slug = strtolower(Str::slug($request->input("libelle"),'-'));
    	$souscompte->commentaire = $request->input("commentaire");
    	$souscompte->save();

	    $notif = new Notifications();
	    $notif->add(Notifications::SUCCESS,"Nouveau sous compte créé avec succès.");

	    return back()->with(Notifications::NOTIFICATION_KEYS_SESSION, $notif);
    }

	/**
	 * @param string $slug
	 * @param Request $request
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
    public function detailsSousCompte(string $slug, Request $request)
    {
	    $debut = null;
	    $fin = null;

        $souscompte = $this->getSousCompteFromSlug($slug);

        $last = LigneCompte::where('compte_id','=', $souscompte->id)
	        ->where("deleted_at", null)
	        ->latest('dateaction')->first();

        $solde = $last != null ? $last->balance : 0;

        $lignes = LigneCompte::with('utilisateur.employe')
	                ->where('compte_id','=', $souscompte->id)
	                ->orderBy('dateaction', 'desc');

        $lignes = $this->extractData($lignes, $request, $debut, $fin);

        $lignes = $lignes->paginate(30);

        return view('compte.souscompte', compact("lignes", "solde", "souscompte"));
    }

	/**
	 * @param string $slug
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
    public function showReset(string  $slug){

	    $this->authorize(Actions::READ, collect([Service::DG, Service::INFORMATIQUE]));

	    $souscompte = $this->getSousCompteFromSlug($slug);

	    $last = LigneCompte::where('compte_id','=', $souscompte->id)
		    ->where("deleted_at", null)
           ->latest('dateaction')->first();

	    $solde = $last != null ? $last->balance : 0;

	    return view("compte.reset", compact("souscompte", "solde"));
    }

	/**
	 * @param string $slug
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
    public function modifier(string $slug){
	    $this->authorize(Actions::READ, collect([Service::DG, Service::INFORMATIQUE, Service::ADMINISTRATION, Service::COMPTABILITE]));

	    $souscompte = $this->getSousCompteFromSlug($slug);
	    $utilisateurs = Utilisateur::with('employe')->get();
	    return view("compte.modifier", compact("souscompte", "utilisateurs"));
    }

    public function updateCompte(string $slug, Request $request){

	    $souscompte = $this->getSousCompteFromSlug($slug);

		$souscompte->libelle = $request->input("libelle");
		$souscompte->slug = strtolower(Str::slug($request->input("libelle"),'-'));
		$souscompte->commentaire = $request->input("commentaire");

	    if($this->checkExistUser($request->input('employe_id'))){
		    $souscompte->employe_id = $request->input('employe_id');
	    }

	    if($request->has('can_appro')){
		    $souscompte->can_appro = true;
	    }

	    $souscompte->update();

	    $notification = new Notifications();
	    $notification->add(Notifications::SUCCESS,"Sous-compte modifié avec succès !");
	    return redirect()->route("compte.registre")->with(Notifications::NOTIFICATION_KEYS_SESSION, $notification);
    }

	/**
	 * @param string $slug
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 * @throws \Exception
	 */
    public function reset(string $slug, Request $request)
    {
	    $this->authorize(Actions::READ, collect([Service::DG, Service::INFORMATIQUE]));
	    $souscompte = $this->getSousCompteFromSlug($slug);
	    $souscompte->lignecompte()->delete();

	    if($request->has("delete_account")){
	    	$souscompte->delete();
	    }

	    $notification = new Notifications();
	    $notification->add(Notifications::SUCCESS,"Sous-compte remis à zéro !");
	    return redirect()->route("compte.registre")->with(Notifications::NOTIFICATION_KEYS_SESSION, $notification);
    }

	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
    public function addNewLine(Request $request)
    {
        $this->validRequest($request);
        $sens = $request->input('sens');
        $montant = $request->input('montant');

	    $last = LigneCompte::where('compte_id','=', $request->input("compte_id"))
	                       ->where("deleted_at", null)
	                       ->latest('dateaction')->first();

        $request->request->remove('_token');
        $request->request->remove('sens');
        $request->request->remove('montant');
	    $request->request->all();

        $line = new LigneCompte($request->request->all());
	    $line->dateoperation = Carbon::createFromFormat('d/m/Y', $request->input('dateoperation'))->toDateString();
	    $line->dateaction = Carbon::now()->toDateTimeString();
	    $line->montant = intval($sens) * intval($montant) ;
	    $line->balance = $last != null ? $last->balance + $line->montant : $line->montant ;
	    $line->employe_id = Auth::id();

	    $line->save();

        $notification = new Notifications();
        $notification->add(Notifications::SUCCESS,"Mouvement de compte enregistré avec succès !");
        return back()->with(Notifications::NOTIFICATION_KEYS_SESSION, $notification);
    }

    public function updateLine(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:lignecompte',
            'dateoperation' => 'required|date_format:d/m/Y',
            'objet' => 'required',
        ]);
        try{
            $line = LigneCompte::findOrFail($request->input('id'));
            $line->dateoperation = Carbon::createFromFormat('d/m/Y', $request->input('dateoperation'))->toDateString();
            $line->objet = $request->input('objet');
            $line->saveOrFail();
        }catch (ModelNotFoundException | \Exception $e){
            return back()->withErrors('Ligne de sous-compte introuvable.');
        }

        $notification = new Notifications();
        $notification->add(Notifications::SUCCESS,"Ligne sous-compte modifiée.");
        return back()->with(Notifications::NOTIFICATION_KEYS_SESSION, $notification);
    }

	public function syntheseSousCompte(){

		$lignes = LigneCompte::with('utilisateur.employe',"compte")
			->join("compte", "compte.id","=","compte_id")
			->join("employe","employe.id", "=", "compte.employe_id")
			->join("service", "service.id", "=", "employe.service_id");

		if(Auth::user()->employe->service->code != Service::DG){
			$lignes = $lignes->where('service.code','<>', Service::DG);
		}

		$debut = $fin = null;

		$lignes = $this->extractData($lignes, request(), $debut, $fin);

		$lignes = $lignes->orderBy('dateaction', 'desc')->paginate(30);

		return view("compte.synthese", compact("lignes","debut", "fin"));
	}
}
