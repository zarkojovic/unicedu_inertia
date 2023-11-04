<?php

namespace App\Listeners;

use App\Events\DealCreation;
use App\Models\Log;
use Illuminate\Contracts\Queue\ShouldQueue;

class MakeBitrixDeal implements shouldQueue {

    /**
     * Create the event listener.
     */
    public function __construct(DealCreation $event) {
        $dealFields = $event->dealFields;
        Log::informationLog('Salje se bitrix deallll!'.count($dealFields));
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void {
        //
    }

}
