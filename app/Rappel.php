<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rappel extends Model
{
    public $timestamps = false;
    protected $table = "rappel";

    protected $fillable = ["*"];
    protected $guarded = [];

    protected $dates = [
        'dt_rappel', 'dt_echeance'
    ];
}
