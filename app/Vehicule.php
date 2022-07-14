<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehicule extends Model
{
	const VL = "VL";
	const PL = "PL";
    public $timestamps = false;
    protected $table = 'vehicule';
    protected $guarded = [];

    public function genre(){
      return $this->belongsTo(Genre::class);
    }

    public function chauffeur(){
      return $this->belongsTo(Chauffeur::class);
    }

    public function interventions(){
      return $this->hasMany(Intervention::class);
    }

	/**
	 * @param string|null PL, VL $mode
	 *
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
    public static function getListe(string $mode = null){
	    $vehicules = Vehicule::with("genre")
           ->where("status","=", Statut::VEHICULE_ACTIF)
           ->select("vehicule.*");

	    if($mode != null){
            $vehicules = $vehicules->join('genre', 'genre.id', '=', 'vehicule.genre_id')
                                   ->where("genre.categorie", "=" , $mode);
	    }

	    return $vehicules->orderBy("immatriculation")
	                     ->get();
    }
}
