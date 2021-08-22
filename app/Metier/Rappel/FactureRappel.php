<?php
/**
 * Created by PhpStorm.
 * User: Wilfried KOFFI
 * Date: 22/08/2021
 * Time: 14:43
 */

namespace App\Metier\Rappel;


use App\PieceComptable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;

class FactureRappel extends RappelBase
{

    /**
     * @param int $id
     * @return Model | mixed
     */
    function getData(int $id) : Model
    {
        return PieceComptable::with("lignes")->findOrFail($id);
    }

    /**
     * @param Model $arg
     */
    function handler(Model $arg) : void
    {
        // TODO: Implement handler() method.
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function getMailView() : View
    {
        return view("");
    }
}