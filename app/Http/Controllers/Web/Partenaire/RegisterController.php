<?php

namespace App\Http\Controllers\Web\Partenaire;

use App\Metier\Behavior\Notifications;
use App\Metier\Json\Contact;
use App\Partenaire;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RegisterController extends Controller
{
    public function liste($type)
    {
        $partenaires = Partenaire::orderBy('raisonsociale','asc');

        $partenaires = $this->triPartenaire($type, $partenaires)->paginate();

        return view('partenaire.liste', compact('partenaires'));
    }

    public function nouveau()
    {
        return view("partenaire.nouveau");
    }

    public function ajouter(Request $request)
    {
        $contacts = new Collection();

        $this->validatePartner($request);

        if($request->has("titre_c"))
        {
	        for($i = 0; $i <= count($request->input("titre_c"))-1; $i++ )
	        {
		        $contact = new Contact();
		        $contact->titre_c = $request->input("titre_c")[$i];
		        $contact->type_c = $request->input("type_c")[$i];
		        $contact->valeur_c = $request->input("valeur_c")[$i];
		        $contacts->add($contact);
	        }
        }

        $raw = $request->except("_token", "valeur_c", "type_c", "titre_c");
        $raw["contact"] = $contacts->toArray();

        try{
            $this->create($raw);
        }catch (ModelNotFoundException $e){
            return back()
                ->withInput()
                ->withErrors($e->getMessage());
        }

        $notification = new Notifications();
        $notification->add(Notifications::SUCCESS,"Nouveau partenaire ajouté avec succès.");
        return back()->with(Notifications::NOTIFICATION_KEYS_SESSION, $notification);
    }

    protected function validatePartner(Request $request)
    {
        $this->validate($request, [
            "raisonsociale" => "required",
            "telephone" => "required",
            "comptecontribuable" => "present",
            "titre_c" => "array"
        ]);

        if(!$request->has("isclient") && !$request->has("isfournisseur"))
        {
            redirect()
                ->back()
                ->withInput()
                ->withErrors("Veuillez sélectionner un rôle pour le partenaire : Client ou Fourniseur ou les deux SVP !");
        }
    }

    protected function create(array $data)
    {
        $partenaire = new Partenaire($data);
        $partenaire->saveOrFail();
    }


    /**
     * @param $type
     * @param Builder $builder
     * @return Builder
     */
    private function triPartenaire($type, Builder $builder)
    {
        if($type == Partenaire::FOURNISSEUR)
        {
            $builder->where('isfournisseur',true);
        }

        if($type == Partenaire::CLIENT)
        {
            $builder->where('isclient', true);
        }

        if(\request()->has("raisonsociale") && ! empty(\request()->query("raisonsociale"))){
            $builder->where("raisonsociale","like","%".\request()->query("raisonsociale")."%");
        }

        return $builder;
    }

    public function modifier($id)
    {
        $partenaire = Partenaire::find($id);
        return view("partenaire.modifier", compact("partenaire"));
    }

    public function update(Request $request, int $id)
    {
        $partenaire = Partenaire::find($id);

        $partenaire->fill($request->except("_token"));

        if(!$request->has("isfournisseur")){
        	$partenaire->isfournisseur = false;
        }

        if(!$request->has("isclient")){
        	$partenaire->isclient = false;
        }

        $partenaire->saveOrFail();

        $notification = new Notifications();
        $notification->add(Notifications::SUCCESS,"Partenaire modifié avec succès.");
        return back()->with(Notifications::NOTIFICATION_KEYS_SESSION, $notification);
    }
}