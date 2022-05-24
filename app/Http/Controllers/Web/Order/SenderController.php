<?php

namespace App\Http\Controllers\Web\Order;

use App\PieceComptable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SenderController extends Controller
{
    public function choice(Request $request, $reference)
    {
        $piece = PieceComptable::with('partenaire')->where("referenceproforma",$reference)->first();
        $partenaire = $piece->partenaire;

        return view("order.senderchoice", compact("partenaire", "piece"));
    }
}
