<?php

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;

class IasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
        DB::table("application")->insert([
            [
                "version" => "1",
                "sendmail" => "Y",
                "mailcopy" => "commercial@ivoireautoservices.net",
                "numeroproforma" => 1,
                "numerobl" => 1,
                "numerofacture" => 1,
                "prefix" => ""
            ],
        ]);

        DB::table("service")->insert([
            [ "code" => "INFO", "libelle" => "Informatique" ],
            [ "code" => "COMPT","libelle" => "Comptabilité" ],
            [ "code" => "LOGIS","libelle" => "Logistique" ],
        ]);

        DB::table("typeintervention")->insert([
            [ "libelle" => "Vidange" ],
            [ "libelle" => "Révision" ],
            [ "libelle" => "Panne" ],
        ]);
        */

        /*
        DB::table("employe")->insert([
            [
                "matricule" => "E000",
                "nom" => "Administrateur",
                "prenoms" => "",
                "datenaissance" => \Carbon\Carbon::now()->toDateString(),
                "pieceidentite" => "C XXX XXXX XXXX",
                "dateembauche" => \Carbon\Carbon::now()->toDateString(),
                "basesalaire" => 0,
                "service_id" => 1,
            ],
        ]);

        DB::table("utilisateur")->insert([
            [
                "login" => "admin@ivoireautoservices.net" ,
                "password" => bcrypt("azerty"),
                "employe_id" => 1
            ],
        ]);
        */

        /*
        DB::table('chauffeur')->insert([
            [
                'employe_id' => '3',
                'permis' => 'PM544 054545 544',
                'expiration_c' => null,
                'expiration_d' => null,
                'expiration_e' => null,
            ],
            [
                'employe_id' => '4',
                'permis' => 'PM987 018780 545',
                'expiration_c' => '2019-05-26',
                'expiration_d' => '2019-05-26',
                'expiration_e' => '2019-05-26',
            ],
            [
                'employe_id' => '5',
                'permis' => 'PM987 87802521 545',
                'expiration_c' => '2019-05-26',
                'expiration_d' => '2019-05-26',
                'expiration_e' => '2019-05-26',
            ],
        ]); */

        DB::table('genre')->insert([
            ['libelle' => '4x4 Pick Up'],
            ['libelle' => '4x4 SUV'],
            ['libelle' => 'Berline'],
            ['libelle' => 'Camionnette'],
            ['libelle' => 'Cyclomoteur'],
            ['libelle' => 'Auto-bus'],
            ['libelle' => 'Tracteur routier'],
            ['libelle' => 'Véhicule particulier'],
            ['libelle' => 'Véhicule utilitaire'],
        ]);

        /*
        DB::table('partenaire')->insert([
            [
                'raisonsociale' => 'Glamo Group',
                'comptecontribuable' => '54780-98988598-A',
                'telephone' => '+22589966602',
                'isclient' => true,
                'contact' => '[{"titre_c":"Will Koffi","type_c":"MOB","valeur_c":"5464545"},{"titre_c":"Will Koffi","type_c":"EMA","valeur_c":"w.koffi@pont-hkb.com"}]',
            ],
            [
                'raisonsociale' => 'MOHAM',
                'comptecontribuable' => '87026-66556055-K',
                'telephone' => '+2259562014',
                'isclient' => true,
                'contact' => '[{"titre_c":"Simon Kouakou","type_c":"MOB","valeur_c":"50466545"},{"titre_c":"Simon Kouakou","type_c":"EMA","valeur_c":"glamolondon@gmail.com"}]',
            ],
            [
                'raisonsociale' => 'MARCY',
                'comptecontribuable' => '65890-12120201-P',
                'telephone' => '+22589966602',
                'isclient' => true,
                'contact' => '[{"titre_c":"Touré Amadou","type_c":"MOB","valeur_c":"78996302"},{"titre_c":"Touré Amadou","type_c":"EMA","valeur_c":"glamolondon@live.fr"}]',
            ]
        ]); */

        /*
        DB::table('famille')->insert([
            ['libelle' => 'Non définie']
        ]);
        */

        DB::table('moyenreglement')->insert([
            ['libelle' => 'Espèce'],
            ['libelle' => 'Chèque'],
            ['libelle' => 'Orange Money'],
            ['libelle' => 'MTN Mobil Money'],
            ['libelle' => 'Moov Money (Flooz)'],
        ]);


        DB::table('vehicule')->insert([
            [
                'cartegrise' => '00001',
                'coutachat' => 25000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '6597HN01',
                'marque' => 'TOYOTA',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'FORTUNER',
                'couleur' => 'Noire',
                'energie' => 'ESSENCE',
                'nbreplace' => 7,
                'puissancefiscale' => 15,
                'genre_id' => 1
            ],
            [
                'cartegrise' => '00002',
                'coutachat' => 30000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '771HC01',
                'marque' => 'TOYOTA',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'FORTUNER',
                'couleur' => 'Noire',
                'energie' => 'ESSENCE',
                'nbreplace' => 7,
                'puissancefiscale' => 15,
                'genre_id' => 1
            ],
            [
                'cartegrise' => '00003',
                'coutachat' => 11000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '7987FY01',
                'marque' => 'HYUNDAI',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'SANTAFE',
                'couleur' => 'Noire',
                'energie' => 'ESSENCE',
                'nbreplace' => 7,
                'puissancefiscale' => 14,
                'genre_id' => 1
            ],
            [
                'cartegrise' => '00004',
                'coutachat' => 11000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '2427GN01',
                'marque' => 'HYUNDAI',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'SANTAFE',
                'couleur' => 'Noire',
                'energie' => 'ESSENCE',
                'nbreplace' => 5,
                'puissancefiscale' => 11,
                'genre_id' => 1
            ],
            [
                'cartegrise' => '00005',
                'coutachat' => 15000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '2937GJ01',
                'marque' => 'TOYOTA',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'LAND CRUISER PRADO',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 7,
                'puissancefiscale' => 12,
                'genre_id' => 1
            ],
            [
                'cartegrise' => '00006',
                'coutachat' => 17000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '4014HH01',
                'marque' => 'TOYOTA',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'LAND CRUISER PRADO',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 7,
                'puissancefiscale' => 18,
                'genre_id' => 1
            ],
            [
                'cartegrise' => '00007',
                'coutachat' => 11000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '4740HC01',
                'marque' => 'TOYOTA',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'LAND CRUISER PRADO',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 7,
                'puissancefiscale' => 12,
                'genre_id' => 1
            ],
            [
                'cartegrise' => '00008',
                'coutachat' => 15000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '6131GS01',
                'marque' => 'TOYOTA',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'LAND CRUISER PRADO',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 12,
                'genre_id' => 1
            ],
            [
                'cartegrise' => '00009',
                'coutachat' => 15000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '6113GS01',
                'marque' => 'TOYOTA',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'HIACE',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 15,
                'puissancefiscale' => 10,
                'genre_id' => 4
            ],
            [
                'cartegrise' => '00010',
                'coutachat' => 8000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '3437HN01',
                'marque' => 'FOURGON',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => '',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 3,
                'puissancefiscale' => 12,
                'genre_id' => 4
            ],
            [
                'cartegrise' => '00011',
                'coutachat' => 8000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '6017HN01',
                'marque' => 'FOURGON',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => '',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 2,
                'puissancefiscale' => 10,
                'genre_id' => 4
            ],
            [
                'cartegrise' => '00012',
                'coutachat' => 8000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '8519GJ01',
                'marque' => 'FORD',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'RANGER',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 10,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00013',
                'coutachat' => 10000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '2105FU01',
                'marque' => 'FORD',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'RANGER',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 10,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00014',
                'coutachat' => 10000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '2801HF01',
                'marque' => 'FORD',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'RANGER',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 10,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00015',
                'coutachat' => 15000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '2276FV01',
                'marque' => 'FORD',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'RANGER',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 10,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00016',
                'coutachat' => 9000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '705FU01',
                'marque' => 'FORD',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'RANGER',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 10,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00017',
                'coutachat' => 8000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '3623FY01',
                'marque' => 'FORD',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'RANGER',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 10,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00018',
                'coutachat' => 6000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '1147GF01',
                'marque' => 'FORD',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'RANGER',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 10,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00019',
                'coutachat' => 15000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '6210FU01',
                'marque' => 'FORD',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'RANGER',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 10,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00020',
                'coutachat' => 7000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '6488GT01',
                'marque' => 'FORD',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'RANGER',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 10,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00021',
                'coutachat' => 9000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '7190GX01',
                'marque' => 'TOYOTA',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'HILUX',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 12,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00022',
                'coutachat' => 13000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '6650HH01',
                'marque' => 'TOYOTA',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'HILUX',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 12,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00023',
                'coutachat' => 15000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '3544GV01',
                'marque' => 'TOYOTA',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'HILUX',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 12,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00024',
                'coutachat' => 15000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '3543GV01',
                'marque' => 'TOYOTA',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'HILUX',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 12,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00025',
                'coutachat' => 0,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '8521HR01',
                'marque' => 'TOYOTA',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'HILUX',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 12,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00026',
                'coutachat' => 0,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '8520HR01',
                'marque' => 'TOYOTA',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'HILUX',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 12,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00027',
                'coutachat' => 15000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '3553GV01',
                'marque' => 'TOYOTA',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'HILUX',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 12,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00028',
                'coutachat' => 10000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '8996HA01',
                'marque' => 'TOYOTA',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'HILUX',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 11,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00029',
                'coutachat' => 15000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '3547GV01',
                'marque' => 'TOYOTA',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'HILUX',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 12,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00030',
                'coutachat' => 15000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '5749GA01',
                'marque' => 'TOYOTA',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'HILUX',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 12,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00031',
                'coutachat' => 15000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '9092GY01',
                'marque' => 'MADZA',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'BT-50',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 10,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00032',
                'coutachat' => 10000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '7025GF01',
                'marque' => 'MADZA',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'BT-50',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 10,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00033',
                'coutachat' => 15000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '760GV01',
                'marque' => 'MADZA',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'BT-50',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 10,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00034',
                'coutachat' => 15000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '762GV01',
                'marque' => 'MADZA',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'BT-50',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 10,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00035',
                'coutachat' => 16000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '6410HG01',
                'marque' => 'MADZA',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'BT-50',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 12,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00036',
                'coutachat' => 15000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '4505HA09',
                'marque' => 'MADZA',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'BT-50',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 10,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00037',
                'coutachat' => 8000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '616GA01',
                'marque' => 'MADZA',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'BT-50',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 10,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00038',
                'coutachat' => 12000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '616GA01',
                'marque' => 'MADZA',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'BT-50',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 10,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00039',
                'coutachat' => 8000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '6221GG01',
                'marque' => 'MADZA',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'BT-50',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 10,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00040',
                'coutachat' => 10000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '2660GT01',
                'marque' => 'MADZA',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'BT-50',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 10,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00041',
                'coutachat' => 15000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '3985GV01',
                'marque' => '',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'GRAND TIGER',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 13,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00042',
                'coutachat' => 15000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '3982GV01',
                'marque' => '',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'GRAND TIGER',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 13,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00043',
                'coutachat' => 10000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '8860FV01',
                'marque' => 'MITSUBISHI',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'L200',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 10,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00044',
                'coutachat' => 8000000,
                "dateachat" => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '8082FG01',
                'marque' => 'MITSUBISHI',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'L200',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 6,
                'puissancefiscale' => 10,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00045',
                'coutachat' => 8000000,
                'dateachat' => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '1525EZ01',
                'marque' => 'MITSUBISHI',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'L200',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 10,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00046',
                'coutachat' => 7000000,
                'dateachat' => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '942EQ01',
                'marque' => 'MITSUBISHI',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'L200',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 10,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00047',
                'coutachat' => 8000000,
                'dateachat' => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '6997HH01',
                'marque' => 'MITSUBISHI',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'L200',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 10,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00048',
                'coutachat' => 15000000,
                'dateachat' => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '3038HH01',
                'marque' => 'NISSAN',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'NAVARA',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 10,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00049',
                'coutachat' => 10000000,
                'dateachat' => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '6446EU01',
                'marque' => 'NISSAN',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'HARDBODY',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 13,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00050',
                'coutachat' => 9000000,
                'dateachat' => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '1625HS01',
                'marque' => 'HYUNDAY',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'TUCSON',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 15,
                'genre_id' => 3
            ],
            [
                'cartegrise' => '00051',
                'coutachat' => 9000000,
                'dateachat' => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '4029HT01',
                'marque' => 'HYUNDAY',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'TUCSON',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 11,
                'genre_id' => 3
            ],
            [
                'cartegrise' => '00052',
                'coutachat' => 8000000,
                'dateachat' => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '7641HF01',
                'marque' => 'TOYOTA',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'AVENSIS',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 10,
                'genre_id' => 3
            ],
            [
                'cartegrise' => '00053',
                'coutachat' => 8000000,
                'dateachat' => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '2850GX01',
                'marque' => 'TOYOTA',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'AVENSIS',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 7,
                'genre_id' => 3
            ],
            [
                'cartegrise' => '00054',
                'coutachat' => 8000000,
                'dateachat' => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '1654EK01',
                'marque' => 'MITSUBISHI',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'L200',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 10,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00055',
                'coutachat' => 10000000,
                'dateachat' => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '985EX01',
                'marque' => 'TOYOTA',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'HILUX',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 12,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00056',
                'coutachat' => 10000000,
                'dateachat' => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '2242FY01',
                'marque' => 'FORD',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'RANGER',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 10,
                'genre_id' => 2
            ],
            [
                'cartegrise' => '00057',
                'coutachat' => 6000000,
                'dateachat' => \Carbon\Carbon::now()->toDateString(),
                'immatriculation' => '750HU01',
                'marque' => 'CITROËN',
                'visite' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'assurance' => \Carbon\Carbon::now()->addDays(180)->toDateString(),
                'typecommercial' => 'XSARA',
                'couleur' => 'Noire',
                'energie' => 'GASOIL',
                'nbreplace' => 5,
                'puissancefiscale' => 8,
                'genre_id' => 3
            ]
        ]);

        /*
        DB::table('produit')->insert([
            [
                "reference" => "000004",
                "libelle" => "ROTULE SUPERIEUR TROOPER",
                "prixunitaire" => 17450,
                "famille_id" => 1
            ],
            [
                "reference" => "000005",
                "libelle" => "JEUX DE PLAQUETTE DE FREINS TROOPER AV",
                "prixunitaire" => 33465,
                "famille_id" => 1
            ],
            [
                "reference" => "000006",
                "libelle" => "BIELLETTE DE DIRECTION TROOPER",
                "prixunitaire" => 12450,
                "famille_id" => 1
            ],
            [
                "reference" => "00001",
                "libelle" => "Amortisseur av ISUZU Trooper",
                "prixunitaire" => 17650,
                "famille_id" => 1
            ],
            [
                "reference" => "00002",
                "libelle" => "ROTULE DE INFERIEUR",
                "prixunitaire" => 21450,
                "famille_id" => 1
            ],
            [
                "reference" => "00007",
                "libelle" => "BIELLETTE DE SUSPENTION TROOPER",
                "prixunitaire" => 180430,
                "famille_id" => 1
            ],
            [
                "reference" => "00003",
                "libelle" => "Parallelisme des roues",
                "prixunitaire" => 3000,
                "famille_id" => 1
            ],
            [
                "reference" => "00008",
                "libelle" => "BIELLETTE DE SUSP AR TROOPER",
                "prixunitaire" => 130210,
                "famille_id" => 1
            ],
            [
                "reference" => "00009",
                "libelle" => "SEILENTBLOC DE BRAS SUP TROOPER",
                "prixunitaire" => 3455,
                "famille_id" => 1
            ],
            [
                "reference" => "0001",
                "libelle" => "DISAUE D''EMBRAYAGE TRACTEUR",
                "prixunitaire" => 475819,
                "famille_id" => 1
            ],
            [
                "reference" => "00010",
                "libelle" => "SOUFLET DE CARDANT TROOPER",
                "prixunitaire" => 6750,
                "famille_id" => 1
            ],
            [
                "reference" => "00010-10025716",
                "libelle" => "Pneus à carcasse radiale",
                "prixunitaire" => 125000,
                "famille_id" => 1
            ],
            [
                "reference" => "00011",
                "libelle" => "SEILENTBLOC DE BARRE STABILI TROOPER",
                "prixunitaire" => 6755,
                "famille_id" => 1
            ],
            [
                "reference" => "00012",
                "libelle" => "CROISILLON DE CARDAN TRANFER TROOPER",
                "prixunitaire" => 8760,
                "famille_id" => 1
            ],
            [
                "reference" => "00013",
                "libelle" => "TAMPONT DE CHASSIE AR TROOPER",
                "prixunitaire" => 60450,
                "famille_id" => 1
            ],
            [
                "reference" => "00014",
                "libelle" => "BOITE DE GRAISSE TROOPER",
                "prixunitaire" => 7000,
                "famille_id" => 1
            ],
            [
                "reference" => "00015",
                "libelle" => "Boite d''huile de direction",
                "prixunitaire" => 7000,
                "famille_id" => 1
            ],
            [
                "reference" => "00016",
                "libelle" => "Boite d''huile teracan",
                "prixunitaire" => 2000,
                "famille_id" => 1
            ],
            [
                "reference" => "00017",
                "libelle" => "Bidon d'huile moteur",
                "prixunitaire" => 9000,
                "famille_id" => 1
            ],
            [
                "reference" => "00019",
                "libelle" => "Reglage de pharre",
                "prixunitaire" => 3000,
                "famille_id" => 1
            ],
            [
                "reference" => "0002",
                "libelle" => "PALATEUX D''EMBRAYAGE TRACTEUR",
                "prixunitaire" => 778419,
                "famille_id" => 1
            ],
            [
                "reference" => "00020",
                "libelle" => "Pneus falken 245/70/16",
                "prixunitaire" => 147210,
                "famille_id" => 1
            ],
        ]);

        */
    }
}
/*
('00020-10004950', 'Jante K7561-1913-0 KUBOTA', 48, 185321),
('00021', 'Litre carburan pour la visite', 35, 615),
('00022', 'Certifica de visite ', 3, 17000),
('00023', 'Vignette 2013', 4, 40000),
('00024', 'Maint d''oeuvre mecani', 4, 22000),
('00025', 'Klaxon ', 4, 3000),
('00026', 'Filtre a huile', 49, 9000),
('00027', 'Reglage de pharres', -1, 3000),
('00030-10024797', 'Triangle de sécurité de vehicule', 47, 25000),
('00040-10000318', 'Extincteur', 47, 85000),
('00050-10025871', 'Kit de remplacement de pneus FACOM', 47, 235000),
('001', ' AMORTISEUR AV L200', 0, 7500),
('0011', 'Croisillon de cardant tranfer', 0, 6000),
('0013', 'Tampont de chassie ar ', 0, 83510),
('0014', 'Boite de graisse ', 0, 2500),
('0015', 'BOITE D''HUILE DE FREINS', 50, 2000),
('00155', 'POMPE A INJECTION', 9, 3119475),
('0016', 'BOITE D''HUILE DE DIRECTION TROOPER', 50, 14000),
('0017', 'FILTRE A HUILE TROOPER', 50, 7000),
('0018', 'BIDON D''HUILE DE MOTEUR', 50, 9000),
('0019', 'PARALLESME DES ROUE', 5, 3000),
*/