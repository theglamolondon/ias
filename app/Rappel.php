<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rappel extends Model
{
    public $timestamps = false;
    protected $table = "rappel";

    protected $fillable = ["*"];
}
