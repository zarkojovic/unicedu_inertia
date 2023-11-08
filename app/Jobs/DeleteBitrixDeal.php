<?php

namespace App\Jobs;

use App\Models\Log;
use CRest;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteBitrixDeal implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $deal_id;

    public $user_id;

    /**
     * Create a new job instance.
     */
    public function __construct($deal_id, $user_id) {
        $this->deal_id = $deal_id;
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void {
        // Make an API call to delete the deal in Bitrix24
        $result = CRest::call("crm.deal.delete",
            ['ID' => (string) $this->deal_id]);

        // Check if the deal was successfully removed from Bitrix24
        if (isset($result['error_description']) && $result['error_description'] === 'Not found') {
            throw new Exception('Deal failed to delete from Bitrix24.',
                $this->user_id);
        }
    }

    public function failed($e) {
        Log::errorLog($e->getMessage);
    }

}
