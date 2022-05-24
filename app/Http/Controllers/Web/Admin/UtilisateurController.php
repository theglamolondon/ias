<?php

namespace App\Http\Controllers\Web\Admin;

use App\Employe;
use App\Metier\Behavior\Notifications;
use App\Metier\Security\Actions;
use App\Service;
use App\Statut;
use App\Utilisateur;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;

class UtilisateurController extends Controller
{
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
    public function index()
    {
	    $this->authorize(Actions::READ, collect([Service::DG, Service::ADMINISTRATION, Service::INFORMATIQUE]));
        $utilisateurs = Utilisateur::with("employe.service")->get();
        return view("admin.utilisateur.liste", compact("utilisateurs"));
    }

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
    public function ajouter()
    {
	    $this->authorize(Actions::CREATE, collect([Service::DG, Service::ADMINISTRATION, Service::INFORMATIQUE]));
        $employes = Employe::orderBy('nom','asc')
            ->whereNotIn('id',Utilisateur::select(['employe_id'])->get()->toArray())
            ->orderBy('prenoms', 'asc')
            ->get();

        return view("admin.utilisateur.ajouter", compact("employes"));
    }

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
    public function reinitialiser()
    {
	    $this->authorize(Actions::CREATE, collect([Service::DG, Service::ADMINISTRATION, Service::INFORMATIQUE]));
        $employes = Employe::orderBy('nom','asc')
            ->orderBy('prenoms', 'asc')
            ->get();

        return view("admin.utilisateur.resetpassword", compact("employes"));
    }

    public function reset(Request $request)
    {
    	$this->validate($request, [
    		"employe_id" => "required|exists:employe,id",
		    "password" => "required|confirmed"
	    ]);

    	$user = Utilisateur::find($request->input("employe_id"));
    	$user->password = bcrypt($request->input("password"));

    	$user->save();

	    $notif = new Notifications();
	    $notif->add(Notifications::SUCCESS, Lang::get("message.admin.utilisateur.preset"));
	    return redirect()->route("admin.utilisateur.liste")->with(Notifications::NOTIFICATION_KEYS_SESSION, $notif);
    }

	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 * @throws \Throwable
	 */
    public function register(Request $request)
    {
	    $this->authorize(Actions::CREATE, collect([Service::DG, Service::ADMINISTRATION, Service::INFORMATIQUE]));

	    $this->validate($request, [
            "employe_id" => "required|exists:employe,id",
            "login" => "required",
            "password" => "required|confirmed",
        ]);

        $this->addUser($request);

        $notif = new Notifications();
        $notif->add(Notifications::SUCCESS, Lang::get("message.admin.utilisateur.ajout"));
        return redirect()->route("admin.utilisateur.liste")->with(Notifications::NOTIFICATION_KEYS_SESSION, $notif);
    }

	/**
	 * @param Request $request
	 *
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 * @throws \Throwable
	 */
    private function addUser(Request $request)
    {
	    $this->authorize(Actions::CREATE, collect([Service::DG, Service::ADMINISTRATION, Service::INFORMATIQUE]));

        try{
            $user = new Utilisateur($request->except("_token", "password_confirmation"));
            $user->password = bcrypt($request->input("password"));
            $user->login.= "@ivoireautoservices.net";
            $user->satut = Statut::TYPE_UTILISATEUR.Statut::ETAT_ACTIF.Statut::AUTRE_NON_DEFINI;
            $user->employe_id = $request->input("employe_id");

            $user->saveOrFail();
        }catch (QueryException $e){
            logger($e->getMessage());
        }catch (\Exception $e){
            logger($e->getMessage());
        }
    }
}
