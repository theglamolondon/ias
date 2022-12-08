<?php


namespace App\Services\Personnel;


use App\Employe;
use App\Metier\Behavior\Notifications;
use App\Metier\Security\Actions;
use App\Service;
use App\Statut;
use App\Utilisateur;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\MessageBag;

trait UtilisateurServices
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function liste()
    {

        $utilisateurs = Utilisateur::with("employe.service")->get();
        return view("admin.utilisateur.liste", compact("utilisateurs"));
    }



    public function resetPassword(Request $request)
    {
        $this->validate($request, [
            "employe_id" => "required|exists:employe,id",
            "password" => "required|confirmed"
        ]);

        $user = Utilisateur::find($request->input("employe_id"));
        $user->password = bcrypt($request->input("password"));

        $user->save();
        if (!$request->expectsJson()) {
            $notif = new Notifications();
            $notif->add(Notifications::SUCCESS, Lang::get("message.admin.utilisateur.preset"));
            return redirect()->route("admin.utilisateur.liste")->with(Notifications::NOTIFICATION_KEYS_SESSION, $notif);
        }
        return null;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Throwable
     */
    public function registerUser(Request $request)
    {
        $this->validate($request, [
            "employe_id" => "required|exists:employe,id",
            "login" => "required",
            "password" => "required|confirmed",
        ]);

        $this->addUser($request);

  if (!$request->expectsJson()){
      $notif = new Notifications();
      $notif->add(Notifications::SUCCESS, Lang::get("message.admin.utilisateur.ajout"));
      return redirect()->route("admin.utilisateur.liste")->with(Notifications::NOTIFICATION_KEYS_SESSION, $notif);

  }
  return null;

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