<?php

namespace App\Jobs;

use App\Models\Deal;
use App\Models\Log;
use CRest;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class sendingBitrixDeal implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    public $items;

    public $deal_id;

    /**
     * Create a new job instance.
     */
    public function __construct($user, $items, $deal_id) {
        $this->user = $user;
        $this->items = $items;
        $this->deal_id = $deal_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void {
        $dealFields = Deal::generateDealObject($this->user->user_id,
            $this->items, $this->user->contact_id);

        // Make API call to create the deal in Bitrix24
        $result = CRest::call("crm.deal.add",
            ['FIELDS' => $dealFields]);

        $deal = Deal::findOrFail($this->deal_id);

        $deal->bitrix_deal_id = $result['result'];

        if (!$deal->save()) {
            throw new Exception('Saving has failed');
        }
    }

    public function failed($exception) {
        // This method is called when the job fails

        // Log the exception or error message
        Log::errorLog('Job failed: '.$exception->getMessage());
        // Perform any necessary cleanup or notifications
    }

}
