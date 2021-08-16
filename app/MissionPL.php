<?php

namespace App;

use App\Http\Controllers\Order\Commercializable;
use App\Interfaces\IAmortissement;
use App\Interfaces\IMission;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class MissionPL extends Model implements Commercializable, IAmortissement, IMission
{
    protected $table = "mission_pl";
    public $timestamps = false;
	protected $guarded = [];

	public function chauffeur(){
		return $this->belongsTo(Chauffeur::class,"chauffeur_id");
	}

	public function vehicule(){
		return $this->belongsTo(Vehicule::class);
	}

	public function client(){
		return $this->belongsTo(Partenaire::class,"partenaire_id");
	}

	public function pieceComptable(){
		return $this->belongsTo(PieceComptable::class,"piececomptable_id");
	}

	public function getDate() {
		return new Carbon($this->datedebut);
	}

	public function getDetails() {
		return sprintf("Mission %s Destination(s): %s", $this->code, $this->destination);
	}

	public function getDebit() {
		return $this->carburant;
	}

	public function getCredit() {
		return 0;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function detailsForCommande() {
		$txt = sprintf("Location de véhicule de type %s <br/> immatriculé %s <br/> à partir du %s.<br/> Destination(s) : %s",
			$this->vehicule->genre->libelle . " " . $this->vehicule->marque . " " . $this->vehicule->typecommercial,
			$this->vehicule->immatriculation, (new Carbon($this->datedebut))->format("d/m/Y"),
			$this->destination );
		return $txt;
	}

	/**
	 * @return string
	 */
	public function getRealModele() {
		return MissionPL::class;
	}

	/**
	 * @return string
	 */
	public function getReference() {
		return empty($this->code) ? '#' : $this->code ;
	}

	/**
	 * @return int
	 */
	public function getPrice() {
		return $this->carburant ?? 0;
	}

	/**
	 * @return int
	 */
	public function getQuantity() {
		return 1;
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
	public function getDuree(){
		return (new Carbon($this->datefin ?? Carbon::now()))->diffInDays(new Carbon($this->datedebut)) + 1;
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
