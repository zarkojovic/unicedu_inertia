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

class UpdateUserDealPackage implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $deal_ids;

    public $package_id;

    /**
     * Create a new job instance.
     */
    public function __construct($deal_ids, $package_id) {
        $this->deal_ids = $deal_ids;
        $this->package_id = $package_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void {
        try {
            foreach ($this->deal_ids as $deal_id) {
                $res = CRest::call("crm.deal.update", [
                    'ID' => (string) $deal_id,
                    'FIELDS' => [
                        ['UF_CRM_1667333858787' => $this->package_id],
                    ],
                ]);

                if ($res['result']) {
                    Log::apiLog('Deal '.$deal_id.' successfully updated!');
                }
                else {
                    Log::errorLog('Failed to update deal with id '.$deal_id);
                    throw new Exception('Failed to save it!');
                }
            }
        }
        catch (Exception $e) {
            // Handle the exception here, you can log or perform other actions
            Log::errorLog('Exception: '.$e->getMessage());
        }
    }

}
