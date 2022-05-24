<?php

namespace App\Http\Controllers\Web\Stock;

use App\Famille;
use App\Http\Controllers\Controller;
use App\Metier\Behavior\Notifications;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class FamilleController extends Controller
{
    public function liste()
    {
        $familles = Famille::all();
        return view('produit.famille.liste', compact("familles"));
    }

    public function addFamille(Request $request)
    {
        $this->validate($request, ["libelle" => "required"]);

        $famille = new Famille();
        $famille->libelle = $request->input("libelle");
        try{
            $famille->saveOrFail();
        }catch (ModelNotFoundException $e){
            return back()->withErrors($e->getMessage());
        }

        $notification = new Notifications();
        $notification->add(Notifications::SUCCESS, "Nouvelle famille créée avec succès");
        return back()->with(Notifications::NOTIFICATION_KEYS_SESSION, $notification);
    }

    public function modifier($id)
    {
        $famille = Famille::find($id);
        return view("produit.famille.modifier", compact("famille"));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            "libelle" => "required",
            "id" => "required|exists:famille,id"
        ]);

        $famille = Famille::find($request->input("id"));
        try{
            $famille->libelle = $request->input("libelle");
            $famille->saveOrFail();
        }catch (ModelNotFoundException $e){
            return back()->withErrors($e->getMessage());
        }

        $request->session()->flash("close","yes");

        $notification = new Notifications();
        $notification->add(Notifications::SUCCESS, "Famille modifiée avec succès");
        return back()->with(Notifications::NOTIFICATION_KEYS_SESSION, $notification);
    }
}