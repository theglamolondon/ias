<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bulletin extends Model
{
    public $timestamps = false;
    protected $table = "bulletin";
    protected $primaryKey = "mois";

	public function employe(){
		return $this->belongsTo(Employe::class,"employe_id");
	}
}