<?php

namespace App\Http\Controllers\Api;
use App\Employe;
use App\Http\Controllers\Controller;
use App\Metier\Security\Actions;
use App\Service;
use App\Services\Personnel\UtilisateurServices;
use App\Utilisateur;
use Illuminate\Http\Request;


class UtilisateurController extends Controller
{
    use UtilisateurServices;


    public function getListe()
    {
       // $this->authorize(Actions::READ, collect([Service::DG, Service::ADMINISTRATION, Service::INFORMATIQUE]));
        return $this->liste();
    }

    public function reset(Request $request)
    {
        return $this->resetPassword($request);
    }

    public function register(Request $request)
    {
        //$this->authorize(Actions::CREATE, collect([Service::DG, Service::ADMINISTRATION, Service::INFORMATIQUE]));

        return $this->registerUser($request);
    }


}