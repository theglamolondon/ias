<?php

namespace App;

use App\Http\Controllers\Order\Commercializable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produit extends Model implements Commercializable
{
	use SoftDeletes;

	protected $dates = ['deleted_at'];

	const DISPONIBLE = "1";
	const INDISPONIBLE = "0";

    protected $table = "produit";
    public $timestamps = false;
    public $quantite = 1;
    public $modele = self::class;

    public function famille()
    {
        return $this->belongsTo(Famille::class);
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
        return $this->libelle;

        /*
        return sprintf("%s , %s",
            $this->famille->libelle,
            $this->libelle
        ); */
    }

    /**
     * @return string
     */
    public function getRealModele()
    {
        return $this->modele;
    }

    public function getReference()
    {
        return $this->reference;
    }

    public function getPrice()
    {
        return $this->prixunitaire;
    }

    public function getQuantity()
    {
        return $this->quantite;
    }

	/**
	 * @return float
	 */
	public function getRemise() {
		return $this->remise;
	}

    /**
     * @return int
     */
    public function getPeriodeQuantity()
    {
       return $this->quantite_periode;
    }

    /**
     * @return int
     */
    public function getPeriode()
    {
        return $this->periode;
    }
}
