<?php

namespace App\Services;

use App\Models\Deal; // Adjust the model as needed
use App\Models\Contact; // Adjust the model as needed
use App\Models\Stage;
use App\Models\User;
use CRest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Http;
use App\Models\Log;

class OutboundService
{
    public static function handleUpdate($type, $bitrixId)
    {
        // Check if deal/contact exists in our database
        try {
            $localData = self::fetchData("database", $type, $bitrixId);
        }
        catch (ModelNotFoundException $e) {
            Log::errorLog("No deals or contacts found in the database for Bitrix ID: ".$bitrixId);
            $localData = null;
        }
        catch (\Exception $e) {
            Log::errorLog($e->getMessage());
            $localData = null;
        }

        if ($localData) {
//            Log::informationLog("Deal ID in database: ".$localData->deal_id);
//            var_dump($localData->toArray());
            // retrieve fresh data from bitrix
            $bitrixData = self::fetchData('bitrix', $type, $bitrixId);

            $localDataArray = array_change_key_case($localData->toArray(), CASE_LOWER);
            $bitrixDataArray = array_change_key_case($bitrixData["result"], CASE_LOWER);

            var_dump($localDataArray["stage_id"]);
            var_dump($bitrixDataArray["stage_id"]);

            $correctStageId = Stage::where('bitrix_stage_id', $bitrixDataArray["stage_id"])->value('stage_id');
            var_dump($correctStageId);
//            var_dump($correctStageId);
            // Update the stage_id in $localData
            try {
//                $localData->stage_id = $correctStageId;
                $deal = Deal::where('bitrix_deal_id', $bitrixId)->firstOrFail();
                $deal->stage_id = $correctStageId;
                $deal->save();
                var_dump($deal->stage_id);
//                if($localData->save()){
//                    var_dump("uspeh");
//                };
//                var_dump($localData->stage_id);
            }
            catch (\Exception $e){
                var_dump("neuspeh ".$e->getMessage());
            }

//            var_dump($bitrixData["result"]["STAGE_ID"]);

            // Check if there are any differences between our data and received from Bitrix
//            $differences = self::compareData($localData, $bitrixData);
//            var_dump($differences);
            // If there are differences, update the appropriate fields in our database
//            if ($differences) {
//                self::updateDatabase('database', $type, $bitrixId, $differences);
//            }
        }
//        else {
//            Log::informationLog("No matching record in our database with received Outbound webhook.");
//        }
    }

    private static function fetchData($location, $type, $bitrixId)
    {
        // Fetch data based on the specified location (database or Bitrix24)
        switch ($location) {
            case 'database':
                if ($type === "deal"){
                    return Deal::where('deals.bitrix_deal_id', $bitrixId)->
                                 join('stages', 'deals.stage_id', '=', 'stages.stage_id')->
                                 select("deals.bitrix_deal_id as id", "deals.university", "deals.degree", "deals.program as uf_crm_1667335742921",
                                        "deals.intake", "stages.bitrix_stage_id as stage_id")->
                                 firstOrFail();
                }
//                elseif ($type === "contact"){
//                    //CONTACT STILL NOT FINISHED, CONTINUE TOMORROW
//                    return User::where('contact_id', $bitrixId)->firstOrFail();
//                }
                else throw new \Exception("Unsupported type of record provided for outbound webhook data retrieval from database.");

            case 'bitrix':
                if ($type === "deal"){// || $type === "contact"
                    $method = 'crm.'.$type.'.get';
                    return CRest::call($method, [
                        'ID' => $bitrixId,
                    ]);
                }
                else throw new \Exception("Unsupported type of record provided for outbound webhook data retrieval from bitrix.");

            default:
                // Unsupported location
                return null;
        }
    }

//    private static function compareData($localData, $bitrixData)
//    {
//        // Compare the data and return the differences
//        //CONTINUE WORKING HERE
//        $localDataArray = array_change_key_case($localData->toArray(), CASE_LOWER);
//        $bitrixDataArray = array_change_key_case($bitrixData["result"], CASE_LOWER);
//        var_dump($bitrixData);
////        var_dump($bitrixDataArray);
//
//        return array_diff_assoc($localDataArray, $bitrixDataArray);
//    }

//    private static function updateDatabase($location, $type, $bitrixId, $differences)
//    {
//        // Update the appropriate fields in our database
//                $storage = ($type === 'deal') ? new Deal() : new Contact();
//                $storage->where('bitrix_id', $bitrixId)->update($differences);
//
//    }
}
