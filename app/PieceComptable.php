<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PieceComptable extends Model
{
    const PRO_FORMA = 1;
    const FACTURE = 2;
    const BON_LIVRAISON = 3;

    const TYPE_FACTURE_PIECE = 1;
    const TYPE_FACTURE_MISSION = 2;

    const TVA = 0.18;

    public $timestamps = false;
    protected $table = 'piececomptable';

    public function partenaire(){
        return $this->belongsTo(Partenaire::class);
    }

    public function lignes(){
        return $this->hasMany(LignePieceComptable::class, "piececomptable_id");
    }

    public function utilisateur(){
        return $this->belongsTo(Utilisateur::class,'utilisateur_id');
    }

    public function moyenPaiement(){
        return $this->belongsTo(MoyenReglement::class,'moyenpaiement_id');
    }

    public function getReference()
    {
        return $this->referencefacture ? $this->referencefacture : $this->referenceproforma;
    }

    public function printing()
    {
        switch ($this->etat){
            case  Statut::PIECE_COMPTABLE_PRO_FORMA :
                return route("print.piececomptable",["reference"=> $this->referenceproforma, "state" => PieceComptable::PRO_FORMA]);
                break;
            case  Statut::PIECE_COMPTABLE_FACTURE_SANS_BL || Statut::PIECE_COMPTABLE_FACTURE_AVEC_BL || Statut::PIECE_COMPTABLE_FACTURE_PAYEE:
                return route("print.piececomptable",["reference"=> $this->referencefacture ?? '0000', "state" => PieceComptable::FACTURE]);
                break;

            default : return null; break;
        }
    }
}
