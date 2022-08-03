<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 07/05/2017
 * Time: 15:52
 */

namespace App;


class Statut
{
    const TYPE_UTILISATEUR = 1;

    const ETAT_ACTIF = 1;
    const ETAT_INACTIF = 0;
    const AUTRE_NON_DEFINI = 0;

    const MISSION_COMMANDEE = 100;
    const MISSION_EN_COURS = 101;
    const MISSION_TERMINEE = 102;
    const MISSION_ANNULEE = 103;
    const MISSION_TERMINEE_SOLDEE = 104;

    const PIECE_COMPTABLE_PRO_FORMA = 200;
    const PIECE_COMPTABLE_FACTURE_SANS_BL = 201;
    const PIECE_COMPTABLE_FACTURE_AVEC_BL = 202;
    const PIECE_COMPTABLE_FACTURE_PAYEE = 203;
    const PIECE_COMPTABLE_FACTURE_ANNULEE = 204;
    const PIECE_COMPTABLE_BON_COMMANDE = 205;
    const PIECE_COMPTABLE_BON_COMMANDE_VALIDE = 206;
    const PIECE_COMPTABLE_FACTURE_PARTIEL_PAYEE = 207;

    const VEHICULE_ACTIF = 300;
    const VEHICULE_VENDU = 301;
    const VEHICULE_ENDOMAGE = 302;
    const VEHICULE_RESERVE = 303;
    const VEHICULE_EN_MISSION = 304;
    const VEHICULE_AU_GARAGE = 305;

    const PERSONNEL_ACTIF = 400;
    const PERSONNEL_TEMPORAIRE = 401;
    const PERSONNEL_DEMISSIONNE = 402;

    /**
     * @param int $statut
     * @return null|string
     */
    public static function getStatut($statut)
    {
        $string = null;

        switch ($statut){
          case self::PIECE_COMPTABLE_PRO_FORMA : $string = "Facture pro forma"; break;
          case self::PIECE_COMPTABLE_FACTURE_AVEC_BL : $string = "Facture (Impayé)"; break;
          case self::PIECE_COMPTABLE_FACTURE_SANS_BL : $string = "Facture non livrée (Impayé)"; break;
          case self::PIECE_COMPTABLE_FACTURE_PAYEE : $string = "Facture (Payé)"; break;
          case self::PIECE_COMPTABLE_FACTURE_PARTIEL_PAYEE : $string = "Facture avancée (Paiement partiel)"; break;
          case self::PIECE_COMPTABLE_FACTURE_ANNULEE : $string = "Facture Annulée"; break;
          case self::PIECE_COMPTABLE_BON_COMMANDE : $string = "Bon de commande (Non Signé)"; break;
          case self::PIECE_COMPTABLE_BON_COMMANDE_VALIDE : $string = "Bon de commande (Signé)"; break;

          case self::MISSION_COMMANDEE : $string = "Mission commandée"; break;
          case self::MISSION_EN_COURS : $string = "Mission en cours"; break;
          case self::MISSION_TERMINEE : $string = "Mission terminée"; break;
          case self::MISSION_ANNULEE : $string = "Mission annulée"; break;

	        case self::VEHICULE_ACTIF : $string = "Véhicule actif"; break;
	        case self::VEHICULE_VENDU : $string = "Véhicule vendu"; break;
	        case self::VEHICULE_ENDOMAGE : $string = "Véhicule hors d'usage"; break;
	        case self::VEHICULE_RESERVE : $string = "Véhicule réservé"; break;
	        case self::VEHICULE_EN_MISSION : $string = "Véhicule en mission"; break;
	        case self::VEHICULE_AU_GARAGE : $string = "Véhicule au garage"; break;
        }

        return $string;
    }
}