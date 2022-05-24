<?php
/**
 * Created by PhpStorm.
 * User: BW.KOFFI
 * Date: 17/08/2017
 * Time: 23:12
 */

namespace App\Interfaces;


interface ICommercializableLine
{
    /**
     * @return int
     */
    public function getId();
    /**
     * @return string
     */
    public function detailsForCommande();

    /**
     * @return string
     */
    public function getRealModele();

    /**
     * @return string
     */
    public function getReference();

    /**
     * @return int
     */
    public function getPrice();

    /**
     * @return int
     */
    public function getQuantity();

    /**
     * @return int
     */
    public function getPeriodeQuantity();

    /**
     * @return int
     */
    public function getPeriode();

	/**
	 * @return float
	 */
    public function getRemise();
}