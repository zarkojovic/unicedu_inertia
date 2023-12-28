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

class UpdateUserBitrixDeals implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    /**
     * Create a new job instance.
     */
    public function __construct($user) {
        //
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void {
        // Wrap the entire process in a database transaction
        DB::beginTransaction();

        try {
            $package = DB::table('packages')
                ->where('package_id', $this->user->package_id)
                ->first();
            // Generate a deal object
            $dealObject = Deal::generateDealObject(
                $this->user->user_id,
                [],
                $this->user->contact_id,
                $package->package_bitrix_id
            );

            // Fetch Bitrix deal IDs
            $dealIds = DB::table('deals')
                ->select('bitrix_deal_id')
                ->where('user_id', $this->user->user_id)
                ->where('active', 1)
                ->whereExists(function($query) {
                    $query->select(DB::raw(1))
                        ->from('user_intake_packages')
                        ->whereColumn('user_intake_packages.user_intake_package_id',
                            'deals.user_intake_package_id')
                        ->whereExists(function($subquery) {
                            $subquery->select(DB::raw(1))
                                ->from('intakes')
                                ->whereColumn('intakes.intake_id',
                                    'user_intake_packages.intake_id')
                                ->where('active', 1);
                        });
                })
                ->pluck('bitrix_deal_id')
                ->toArray();

            $updatingContact = CRest::call("crm.contact.update", [
                'ID' => (string) $this->user->contact_id,
                'FIELDS' => $dealObject[1],
            ]);

            if ($updatingContact['result']) {
                Log::apiLog('Contact '.$this->user->contact_id.' successfully updated!');
            }

            else {
                Log::errorLog('Failed to update contact with id '.$this->user->contact_id);
                throw new Exception('Failed to save it!');
            }

            // Update the deal in Bitrix24
            foreach ($dealIds as $val) {
                // Make API call to update the deal in Bitrix24
                $res = CRest::call("crm.deal.update", [
                    'ID' => (string) $val,
                    'FIELDS' => $dealObject[0],
                ]);

                if ($res['result']) {
                    SyncDealFileIdsService::sync((string) $val);
                    Log::apiLog('Deal '.$val.' successfully updated!');
                }

                else {
                    Log::errorLog('Failed to update deal with id '.$val);
                    throw new Exception('Failed to save it!');
                }
            }

            // Commit the transaction if everything is successful
            DB::commit();
        }
        catch (Exception $e) {
            // Rollback the transaction in case of an exception
            DB::rollBack();

            // Log the exception
            Log::error('Error updating Bitrix deals: '.$e->getMessage());
        }
    }

}
