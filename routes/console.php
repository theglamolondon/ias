<?php

use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

Artisan::command('update-db', function () {
	\Illuminate\Support\Facades\Schema::table('lignepiece', function (\Illuminate\Database\Schema\Blueprint $table){
		$table->float('remise',7,4)
		      ->default(0.0)
		      ->after('prixunitaire');
	});
})->describe('Update DB');

Artisan::command('mission:reminder', function () {
	$reminder = new \App\Http\Controllers\Mission\MissionController();
	$reminder->reminder();
})->describe('Send mail reminder to user for mission');

Artisan::command('ias:reminder', function (\App\Console\Commands\ReminderCheck $checker) {
    $checker->handle();
})->describe('Send mail reminder to user for mission');

