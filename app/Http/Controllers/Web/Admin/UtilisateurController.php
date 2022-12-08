<?php

namespace App\Http\Controllers\Web\Admin;

use App\Employe;
use App\Metier\Behavior\Notifications;
use App\Metier\Security\Actions;
use App\Service;
use App\Services\Personnel\UtilisateurServices;
use App\Statut;
use App\Utilisateur;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;

class UtilisateurController extends Controller
{
    use UtilisateurServices;


    public function index()
    {
        $this->authorize(Actions::READ, collect([Service::DG, Service::ADMINISTRATION, Service::INFORMATIQUE]));
        return $this->liste();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function ajouter()
    {
        $this->authorize(Actions::CREATE, collect([Service::DG, Service::ADMINISTRATION, Service::INFORMATIQUE]));
        $employes = Employe::orderBy('nom', 'asc')
            ->whereNotIn('id', Utilisateur::select(['employe_id'])->get()->toArray())
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
        $employes = Employe::orderBy('nom', 'asc')
            ->orderBy('prenoms', 'asc')
            ->get();

        return view("admin.utilisateur.resetpassword", compact("employes"));
    }

    public function reset(Request $request)
    {
        return $this->resetPassword($request);
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

        return $this->registerUser($request);
    }

}
