<?php

namespace App\Http\Controllers\Web\Mail;

use App\Application;
use App\Http\Controllers\Order\Process;
use App\Mail\FactureProforma;
use App\Metier\Behavior\Notifications;
use App\PieceComptable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    use Process;

    public function envoyerProforma($reference, Request $request)
    {
        $piece = $this->getPieceComptableFromReference($reference);

        $collection = new Collection();

        foreach ($request->input("email") as  $email)
        {
            $collection->push(["email" => $email]);
        }

        try{
            Mail::to($collection->toArray())
                ->cc(Application::getMailCopie())
                ->send(new FactureProforma($piece, PieceComptable::PRO_FORMA ));
        }catch (\Exception $e){
            Log::error($e->getMessage()."\r\n".$e->getTraceAsString());
        }

        $notification = new Notifications();
        $notification->add(Notifications::SUCCESS,"Votre proforma n° $piece->referenceproforma a été envoyée.");
        return redirect()->route("facturation.liste.all")->with(Notifications::NOTIFICATION_KEYS_SESSION, $notification);
    }
}