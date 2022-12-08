<?php
/**
 * Created by PhpStorm.
 * User: Wilfried KOFFI
 * Date: 07/07/2022
 * Time: 11:29
 */

namespace App\Services\Personnel;


use App\Chauffeur;
use App\Http\Controllers\Web\Admin\ChauffeurController;
use App\Metier\Behavior\Notifications;
use App\Metier\Security\Actions;
use App\Mission;
use App\Service;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageBag;

trait ChauffeurServices
{
  protected function getListe(){
    $chauffeursBuilder = Chauffeur::with('employe');
    return $this->filtreListe($chauffeursBuilder);
  }

  protected function filtreListe(Builder $builder){

    if(\request()->has("fullname") && !empty(\request()->query("fullname"))){
      $builder = $builder->join("employe","employe_id", "=", "employe.id")
                          ->where("employe.nom","like","%".\request()->query("fullname")."%")
                          ->orWhere("employe.prenoms","like","%".\request()->query("fullname")."%")
                          ->select("chauffeur.*");
    }

    return $builder->paginate();
  }

    public function registerChauffeur(Request $request)
    {
        $this->validate($request, $this->validateRules());

        try {

            $this->create($request->except('_token'));

        } catch (\Exception $e) {

            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());

            return back()->withInput()->withErrors("Une erreur s'est produite dans la création du chauffeur.");
        }
        if (!$request->expectsJson()) {
            $notifiation = new Notifications();
            $notifiation->add(Notifications::SUCCESS, Lang::get('message.admin.chauffeur.ajout'));

            return redirect()->route('admin.chauffeur.liste')->with(Notifications::NOTIFICATION_KEYS_SESSION, $notifiation);
        }
        return null;
    }


    private function create(array $data)
    {
        $raw = [
            'employe_id' => $data['employe_id'],
            'permis' => $data['permis'],
        ];

        if(array_key_exists('expiration_c',$data) && !empty($data['expiration_c'])){
            $raw['expiration_c'] = Carbon::createFromFormat('d/m/Y',$data['expiration_c']);
        }

        if(array_key_exists('expiration_d',$data) && !empty($data['expiration_d'])) {
            $raw['expiration_d'] = Carbon::createFromFormat( 'd/m/Y', $data['expiration_d'] );
        }

        if(array_key_exists('expiration_e',$data) && !empty($data['expiration_e'])) {
            $raw['expiration_e'] = Carbon::createFromFormat( 'd/m/Y', $data['expiration_e'] );
        }

        return Chauffeur::create($raw);
    }

    /**
     * @param $matricule
     *
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function situationChauffeur($matricule)
    {

        $chauffeur = Chauffeur::where("matricule",$matricule)
            ->join("employe","employe_id","employe.id")
            ->first();

        if($chauffeur == null)
        {
            return back()->withErrors("Chauffeur non trouvé.");
        }

        $missions = Mission::with("chauffeur","versements.moyenreglement","clientPartenaire","vehicule")
            ->join("employe","chauffeur_id", "=", "employe.id")
            ->where("matricule",$matricule)
            ->select("mission.*")
            ->get();

        return view("admin.chauffeur.point", compact("missions","chauffeur"));
    }

    private function validateRules()
    {
        return [
            'employe_id' => 'required|numeric|exists:employe,id',
            'permis' => 'required|regex:/([A-Z0-9]*)-([0-9]*)-([0-9]{8})([A-Z]*)/',
            'expiration_c' => 'nullable|date_format:d/m/Y',
            'expiration_d' => 'nullable|date_format:d/m/Y',
            'expiration_e' => 'nullable|date_format:d/m/Y',
        ];
    }
}