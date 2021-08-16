<?php

namespace App;

use App\Http\Controllers\Order\Commercializable;
use App\Interfaces\IAmortissement;
use App\Interfaces\IMission;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Mission extends Model implements Commercializable, IAmortissement, IMission
{
    protected $table = 'mission';
    protected $guarded = [];
    public $timestamps = false;

    public function chauffeur(){
        return $this->belongsTo(Chauffeur::class,"chauffeur_id");
    }

    public function vehicule(){
        return $this->belongsTo(Vehicule::class);
    }

    public function clientPartenaire(){
        return $this->belongsTo(Partenaire::class,"client");
    }

    public function soustraitantPartenaire(){
        return $this->belongsTo(Partenaire::class,"soustraitant");
    }

    public function pieceComptable(){
        return $this->belongsTo(PieceComptable::class,"piececomptable_id");
    }

    public function versements(){
        return $this->hasMany(Versement::class, "mission_id", "id");
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function detailsForCommande()
    {
        if($this->soustraite)
        {
            return sprintf("Location de véhicule %s <br/> Du %s au %s.<br/> Destination(s) : %s",
                $this->immat_soustraitance ,
                (new Carbon($this->debuteffectif))->format("d/m/Y"),
                (new Carbon($this->fineffective))->format("d/m/Y"),
                //(new Carbon($this->debutprogramme))->diffInDays(new Carbon($this->finprogramme))+1,
                $this->destination
            );
        }else{
            return sprintf("Location de véhicule de type %s <br/> immatriculé %s <br/> Du %s au %s.<br/> Destination(s) : %s",
                $this->vehicule->genre->libelle . " " . $this->vehicule->marque . " " . $this->vehicule->typecommercial,
                $this->vehicule->immatriculation,
                (new Carbon($this->debuteffectif))->format("d/m/Y"),
                (new Carbon($this->fineffective))->format("d/m/Y"),
                //(new Carbon($this->debutprogramme))->diffInDays(new Carbon($this->finprogramme))+1,
                $this->destination
            );
        }
    }

	/**
	 * @return int
	 */
    public function getDuree(){
	    return (new Carbon($this->fineffective))->diffInDays(new Carbon($this->debuteffectif)) + 1;
    }

    /**
     * @return string
     */
    public function getRealModele()
    {
        return self::class;
    }

    public function getReference()
    {
        return empty($this->code) ? '#' : $this->code ;
    }

    public function getPrice()
    {
        return $this->montantjour ? $this->montantjour : 0 ;
    }

    public function getQuantity()
    {
        return 1;
    }

    public function getDate()
    {
        return new Carbon($this->debuteffectif);
    }

    public function getDetails()
    {
        return sprintf("Mission %s Destination(s): %s", $this->code, $this->destination);
    }

    public function getDebit()
    {
        $nbreJours = (new Carbon($this->debuteffectif))->diffInDays(new Carbon($this->fineffective));
        return ($this->montantjour * $nbreJours) - ($this->perdiem * $nbreJours);
    }

    public function getCredit()
    {
        return 0;
    }

	/**
	 * @return float
	 */
	public function getRemise() {
		return 0;
	}

    /**
     * @return int
     */
    public function getPeriodeQuantity()
    {
        return 0;
    }

    /**
     * @return int
     */
    public function getPeriode()
    {
        return "jours";
    }
}