<?php

namespace App\Listeners;

use App\Events\BCcreated;
use App\Mail\BCAlert;
use App\Service;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class BCcreatedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  BCcreated  $event
     * @return void
     */
    public function handle(BCcreated $event)
    {
    	$dg =  Service::with("employes.utilisateur")
		    ->where("code","=", Service::DG)
	        ->first();

    	$to = [];

    	foreach ($dg->employes as $p){
    	    if($p->utilisateur){
                $to[] = $p->utilisateur->login;
            }
	    }

    	try{
		    Mail::to($to)
		        ->send(new BCAlert($event->BC));

	    }catch (\Exception $e){
    		logger($e->getMessage());
	    }
    }
}
