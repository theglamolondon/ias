<?php
/**
 * Created by PhpStorm.
 * User: SUPPORT.IT
 * Date: 17/09/2018
 * Time: 09:42
 */

namespace App\Http\Controllers\Web\RH;


use Carbon\Carbon;

trait Tools
{
	private function getYears()
	{
		$years = [];
		for ($i = 2017; $i <= Carbon::now()->year; $i++){
			$years[] = $i;
		}
		return $years;
	}

	private function getMonths()
	{
		return [
			(object)[ "id" =>"1", "libelle" => "Janvier" ],
			(object)[ "id" =>"2", "libelle" => "Février" ],
			(object)[ "id" =>"3", "libelle" => "Mars" ],
			(object)[ "id" =>"4", "libelle" => "Avril" ],
			(object)[ "id" =>"5", "libelle" => "Mai" ],
			(object)[ "id" =>"6", "libelle" => "Juin" ],
			(object)[ "id" =>"7", "libelle" => "Juillet" ],
			(object)[ "id" =>"8", "libelle" => "Août" ],
			(object)[ "id" =>"9", "libelle" => "Septembre" ],
			(object)[ "id" =>"10", "libelle" => "Octobre" ],
			(object)[ "id" =>"11", "libelle" => "Novembre" ],
			(object)[ "id" =>"12", "libelle" => "Décembre" ],
		];
	}

	public static function getMonthsById(int $id)
	{
		return [
			"Janvier" , "Février" , "Mars" , "Avril" , "Mai" , "Juin",
			"Juillet" , "Août" , "Septembre" , "Octobre", "Novembre" , "Décembre"
		][$id-1];
	}
}