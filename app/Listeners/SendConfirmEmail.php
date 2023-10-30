<?php

namespace App\Listeners;

use App\Models\Log;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendConfirmEmail implements shouldQueue {

    /**
     * Create the event listener.
     */
    public function __construct() {
        Log::informationLog('This as an event back jobs!');
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void {
        //
    }

}
