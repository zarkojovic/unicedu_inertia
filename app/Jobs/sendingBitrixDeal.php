<?php

namespace App\Jobs;

use App\Models\Deal;
use App\Models\Log;
use App\Services\SyncDealFileIdsService;
use CRest;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

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
        $package = DB::table('packages')
            ->where('package_id', $this->user->package_id)
            ->first();
        $dealFields = Deal::generateDealObject($this->user->user_id,
            $this->items, $this->user->contact_id, $package->package_bitrix_id);

        // Make API call to create the deal in Bitrix24
        $result = CRest::call("crm.deal.add",
            ['FIELDS' => $dealFields[0]]);
        $fillTheContact = CRest::call("crm.contact.update",
            [
                'id' => (string) $this->user->contact_id,
                'fields' => $dealFields[1],
            ]

        );

        $deal = Deal::findOrFail($this->deal_id);

        $deal->bitrix_deal_id = $result['result'];

        if (!$fillTheContact['result']) {
            throw new Exception('Contact failed to update in Bitrix24.');
        }
        if (!$deal->save()) {
            throw new Exception('Saving has failed');
        }
        else {
            SyncDealFileIdsService::sync($result['result']);
        }
    }

    public function failed($exception) {
        // This method is called when the job fails

        // Log the exception or error message
        Log::errorLog('Job failed: '.$exception->getMessage());
        // Perform any necessary cleanup or notifications
    }

}
