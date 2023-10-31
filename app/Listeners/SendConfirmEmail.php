<?php

namespace App\Listeners;

use App\Models\Log;

class SendConfirmEmail {

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
