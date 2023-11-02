<?php

namespace App\Jobs;

use App\Models\Deal;
use App\Models\Log;
use CRest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class sendingBitrixDeal implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    public $items;

    /**
     * Create a new job instance.
     */
    public function __construct($user, $items) {
        $this->user = $user;
        $this->items = $items;
    }

    /**
     * Execute the job.
     */
    public function handle(): void {
        Log::informationLog('Items: '.count($this->items));
        $dealFields = Deal::generateDealObject($this->user->user_id,
            $this->items);

        // Make API call to create the deal in Bitrix24
        $result = CRest::call("crm.deal.add",
            ['FIELDS' => $dealFields]);
    }

    public function failed($exception) {
        // This method is called when the job fails

        // Log the exception or error message
        Log::errorLog('Job failed: '.$exception->getMessage());
        // Perform any necessary cleanup or notifications
    }

}
