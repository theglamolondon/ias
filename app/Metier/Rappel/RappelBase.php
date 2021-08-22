<?php
/**
 * Created by PhpStorm.
 * User: Wilfried KOFFI
 * Date: 19/08/2021
 * Time: 17:12
 */

namespace App\Metier\Rappel;


use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;

abstract class RappelBase
{

    /**
     * @var $target
     */
    private $target;

    function __construct()
    {
    }

    /**
     * @param int $id
     * @return Model | mixed
     */
    abstract function getData(int $id) : Model;

    /**
     * @param Model $arg
     * @return void
     */
    abstract function handler(Model $arg) : void;

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    abstract function getMailView() : View;
}