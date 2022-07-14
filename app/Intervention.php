<?php

namespace App;

use App\Interfaces\IAmortissement;
use App\Interfaces\ICommercializableLine;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Intervention extends Model implements IAmortissement, ICommercializableLine
{
    public $timestamps = false;
    protected $table = "intervention";
    protected $guarded = [];

    public function typeIntervention(){
        return $this->belongsTo(TypeIntervention::class,"typeintervention_id");
    }

    public function vehicule(){
        return $this->belongsTo(Vehicule::class,"vehicule_id");
    }

    public function partenaire(){
        return $this->belongsTo(Partenaire::class,"partenaire_id");
    }

    public function pieceFournisseur(){
	    return $this->belongsTo(PieceFournisseur::class,"piecefournisseur_id");
    }

    public function getDate()
    {
        return new Carbon($this->debut);
    }

    public function getDetails()
    {
        return sprintf("Intervention : %s, %s", $this->typeIntervention->libelle, $this->details);
    }

    public function getDebit()
    {
        return 0;
    }

    public function getCredit()
    {
        return $this->cout;
    }

	/**
	 * @return mixed
	 * @inheritdoc Fournit une chaine pour détailler l'objet pour un bon de commande et autre pièces comptable
	 */
	public function detailsForCommande() {
		return sprintf("Intervention (%s) sur le véhicule %s",
			$this->typeIntervention->libelle,
			$this->vehicule->immatriculation." ".$this->vehicule->marque." ".$this->vehicule->typecommercial);
	}

	/**
	 * @return mixed
	 * @inheritdoc Permet de redonner la classe d'originne de la ligne de la commande
	 */
	public function getRealModele() {
		return Intervention::class;
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
	public function getReference() {
		return "#".$this->id;
	}

	/**
	 * @return int
	 */
	public function getPrice() {
		return $this->cout;
	}

	/**
	 * @return int
	 */
	public function getQuantity() {
		return $this->quantite;
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
        return 1;
    }

    /**
     * @return int
     */
    public function getPeriode()
    {
        return "";
    }
}
