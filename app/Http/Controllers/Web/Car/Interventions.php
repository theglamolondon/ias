<?php

namespace App\Http\Controllers\Web\Car;


trait Interventions
{
	private function validateRules()
	{
		return [
			[
				"debut" => "required|date_format:d/m/Y",
				"fin" => "required|date_format:d/m/Y",
				"vehicule_id" => "required|exists:vehicule,id",
				"typeintervention_id" => "required|exists:typeintervention,id",
				"cout" => "required|integer|min:1",
				"details" => "required",
				"partenaire_id" => "required"
			],
			[
				"cout.min" => "Le coût de la réparation ne peut pas être égale à 0"
			]
		];
	}
}
