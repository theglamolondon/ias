<?php

namespace App\Http\Controllers\Admin;

use App\Chauffeur;
use App\Employe;
use App\Metier\Behavior\Notifications;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;

class ChauffeurController extends Controller
{
    public function liste()
    {
        $chauffeurs = Chauffeur::with('employe')->get();

        return view('admin.chauffeur.liste',compact('chauffeurs'));
    }

    public function ajouter()
    {
        $employes = Employe::orderBy('nom','asc')
            ->whereNotIn('id',Chauffeur::select(['employe_id'])->get()->toArray())
            ->orderBy('prenoms', 'asc')
            ->get();

        return view('admin.chauffeur.ajouter',compact('employes'));
    }

    public function register(Request $request)
    {
        $this->validate($request, $this->validateRules());

        try{

            $this->create($request->except('_token'));

        }catch (\Exception $e){

            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());

            return back()->withInput()->withErrors("Une erreur s'est produite dans la création du chauffeur.");
        }

        $notifiation = new Notifications();
        $notifiation->add(Notifications::SUCCESS,Lang::get('message.admin.chauffeur.ajout'));

        return redirect()->route('admin.chauffeur.liste')->with(Notifications::NOTIFICATION_KEYS_SESSION, $notifiation);
    }

    private function create(array $data)
    {
        $raw = [
            'employe_id' => $data['employe_id'],
            'permis' => $data['permis'],
        ];

        if(array_key_exists('expiration_c',$data) && !empty($data['expiration_c']))
            $raw['expiration_c'] = Carbon::createFromFormat('d/m/Y',$data['expiration_c']);


        if(array_key_exists('expiration_d',$data) && !empty($data['expiration_d']))
            $raw['expiration_d'] = Carbon::createFromFormat('d/m/Y',$data['expiration_d']);


        if(array_key_exists('expiration_e',$data) && !empty($data['expiration_e']))
            $raw['expiration_e'] = Carbon::createFromFormat('d/m/Y',$data['expiration_e']);

        return Chauffeur::create($raw);
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