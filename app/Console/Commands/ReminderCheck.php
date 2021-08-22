<?php

namespace App\Console\Commands;

use App\Metier\Rappel\FactureRappel;
use App\Rappel;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ReminderCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ias:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Relance de checking des rappels de l\'application IAS';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $console = new \Symfony\Component\Console\Output\ConsoleOutput();

        $console->writeln("Début de checking rappel IAS");

        $r1 = new Rappel(["id" => 1, "titre" => "Test 1", "dt_rappel" => null, "dt_echeance" => "2021-08-26 00:00:00", "modele_id" => "1984", "handler" => FactureRappel::class]);
        $r2 = new Rappel(["id" => 2, "titre" => "Test 2", "dt_rappel" => "2021-08-22 14:45:00", "dt_echeance" => "2021-08-26 00:00:00", "modele_id" => "1980", "handler" => FactureRappel::class]);

        $console->writeln("Récupétration des rappels de la journée");
        //$reminders = Rappel::whereBetween('dt_echeance', [Carbon::now()->startOfDay()->toString(), Carbon::now()->endOfDay()->toString()])->get();
        $reminders = collect([$r1, $r2]);

        foreach ($reminders as $reminder){
            $date = Carbon::now();

            if(is_null($reminder->dt_rappel)){ //Aucun rappel marqué pour cette tache
                $console->writeln("");
            }else{ //Notifier uniquement de manière générale
                env("APP_MISSION_REMINDER", 5);
            }
        }

        return 0;
    }
}
