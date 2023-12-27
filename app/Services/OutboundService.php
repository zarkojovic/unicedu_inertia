<?php

namespace App\Services;

use App\Models\Deal;
use App\Models\Field;
use App\Models\FieldItem;
use App\Models\UserInfo;
use CRest;
use Illuminate\Support\Facades\Http;
use App\Models\Log;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class OutboundService
{
    // 1. check if deal/contact exists in our database
    // 2. use rest api method to retrieve fresh values from bitrix for all fields for that deal/contact
    // 3. check if there are any differences between our data and received from bitrix
    // 4. if there are differences, update the appropriate fields in our database
    public static function handleUpdate($type, $bitrixId)
    {
        // Check if deal exists in our database and get column values
        $record = Deal::where('bitrix_deal_id', $bitrixId)
            ->select('user_id', 'university', 'degree', 'program', 'intake')
            ->first();

        if (!$record) {
            Log::errorLog("Outbound webhook error: Couldn't find a matching record in the database.");
            return null;
        }

        // Extract values as separate array members to be able to get their option values later
        $fieldValues = [
            $record->university,
            $record->degree,
            $record->intake,
        ];

        // Retrieve field_ids for correct field_names from deal table (university, degree, intake)
        $fieldNames = ["UF_CRM_1667335624051", "UF_CRM_1667335695035", "UF_CRM_1668001651504"];
        $fieldIds = Field::whereIn('field_name', $fieldNames)
                            ->pluck('field_id')
                            ->toArray();

        if (!$fieldIds) {
            Log::errorLog("Outbound webhook error: Couldn't find matching fieldId records in the Deals table for fieldNames array (maybe field names have changed).");
            return null;
        }


        // Find corresponding item_id values from Field_items table
        $itemIds = FieldItem::whereIn('item_value', $fieldValues)
            ->whereIn('field_id', $fieldIds)
            ->pluck('item_id', 'item_value')
            ->toArray();

        if (!$itemIds) {
            Log::errorLog("Outbound webhook error: Couldn't find matching itemId records in the database for fieldIds and fieldValues arrays.");
            return null;
        }


        // Map field_names with corresponding item_ids
        $fieldItemMap = array_combine($fieldNames, array_map(function ($fieldValue) use ($itemIds) {
            return $itemIds[$fieldValue] ?? null;
        }, $fieldValues));

        $fieldItemMap["UF_CRM_1667335742921"] = $record->program; // add program to array


        // retrieve data from DEALS and USER_INFOS tables
        $userId = $record["user_id"];
        if (!$userId) {
            Log::errorLog("Outbound webhook error: Couldn't find user_id in Deals table.");
            return null;
        }

        // here the minimum number of records should be the required fields! Otherwise, no deal should exist
        $userInfoFromDatabase = UserInfo::join('fields', 'user_infos.field_id', '=', 'fields.field_id')
                                        ->where('user_infos.user_id', $userId)
                                        ->whereNotNull('user_infos.value') // Only include rows which are not files
                                        ->pluck('user_infos.value', 'fields.field_name')
                                        ->toArray();

        if (!$userInfoFromDatabase) {
            Log::errorLog("Outbound webhook error: Couldn't find records in User_infos table (missing required fields).");
            return null;
        }

        $dataFromDatabase = array_merge($fieldItemMap, $userInfoFromDatabase);

        // retrieve fresh data from bitrix
        $dataFromBitrix = [];
        try {
            $dataFromBitrix = self::fetchDataFromBitrix($type, $bitrixId);
        }
        catch (\Exception $e){
            Log::errorLog("Outbound webhook error: ".$e->getMessage());
        }

//        var_dump($dataFromBitrix); die;
        // Convert timestamps to formatted strings without timezone information
        // THIS PART ASSUMES THERE'S DATE OF BIRTH FIELD VALUE! IT SHOULD BE OKAY SINCE IT'S A REQUIRED FIELD...
        $format = 'Y-m-d';

        $bitrixTimestamp = date($format, strtotime($dataFromBitrix["result"]["UF_CRM_1680032383794"]));
        $dataFromBitrix["result"]["UF_CRM_1680032383794"] = $bitrixTimestamp;


        // COMPARE DATABASE AND BITRIX DATA (REGULAR AND DROPDOWN FIELDS, NO FILES) AND CHECK FOR DIFFERENCES
        $differences = [];
        foreach ($dataFromDatabase as $key => $value) {
            if (isset($dataFromBitrix["result"][$key]) && $value !== $dataFromBitrix["result"][$key]) {
                $differences[$key] = $dataFromBitrix["result"][$key];
            }
        }

        if ($differences){
            // here check if the fields found in differences belong to DEALS or USER_INFOS tables and update the values accordingly
            $fieldNames[] = "UF_CRM_1667335742921";
            $dealFields = array_intersect(array_keys($differences), $fieldNames);

            if ($dealFields){
                // changes in deal fields found. Proceed to update DEALS table
//                var_dump($dealFields);
//                die;
            }
            else {
                // no changes found for DEALS table. Proceed to update USER_INFOS table
                $fieldData = Field::whereIn('field_name', array_keys($differences))
                    ->select('field_id', 'type')
                    ->get()
                    ->keyBy('field_id');

                $fileFieldIds = $fieldData->filter(function ($field) {
                    return $field->type === 'file';
                })->pluck('field_id')->toArray();

                if ($fileFieldIds){
                    // update files
                    var_dump("there are file fields to change");
                }
                else {
                    // no file fields changed for USER_INFO table
                    $nonFileFieldIds = $fieldData->filter(function ($field) {
                        return $field->type !== 'file';
                    })->pluck('field_id')->toArray();

                    $fieldIdsAndNames = Field::whereIn('field_id', $nonFileFieldIds)
                                            ->pluck("field_name", "field_id")
                                            ->toArray();

                    $fieldIdValueMapping = [];

                    // Populate the field_id => value mapping
                    foreach ($nonFileFieldIds as $fieldId) {
                        $field_name = $fieldIdsAndNames[$fieldId];
                        $fieldIdValueMapping[$fieldId] = $differences[$field_name];
                    }

                    // retrieve dropdown fields and their values if they exist
                    $dropdownFieldIdsAndValues = FieldItem::whereIn('field_id', $nonFileFieldIds)
                        ->whereIn("item_id", $fieldIdValueMapping)
                        ->pluck("item_value", "field_id")
                        ->toArray();


                    $valuesForUpdating = [];
                    foreach ($fieldIdValueMapping as $fieldId=>$value) {
                        $displayValue = $dropdownFieldIdsAndValues[$fieldId] ?? null;

                        $valuesForUpdating[] = [
                            'user_id' => $userId,
                            'field_id' => $fieldId,
                            'value' => $value,
                            'display_value' => $displayValue
                        ];
                    }

                    var_dump("differences");
                    die;

//                    try {
//                        UserInfo::upsert($valuesForUpdating,
//                                        ['user_id', 'field_id'],
//                                        ['value', 'display_value']);
//
//                        Log::informationLog("Outbound webhook message: Successfully updated values in our database!");
//                    } catch (\Exception $e) {
//                        Log::errorLog("Outbound webhook error: " . $e->getMessage());
//                    }
                }
            }




        }
        else { // ELSE THE VALUE OF A FIELD THAT THE USER HASN'T FILLED IN OUR APP HAS CHANGED
            // 1. if the field already exists in our database, insert the new value into USER_INFOS table (special case for DEALS table)
            // 2. if the field doesn't exist in our database, refresh fields and then insert the new value --||--


            // 1st case
//            var_dump($dataFromBitrix); die;
            $receivedCategoryFieldNames = Field::where("field_category_id", 6)
                                            ->pluck("field_id", "field_name")
                                            ->toArray();

            var_dump($receivedCategoryFieldNames); die;
        }




        // retrieve field_ids and field_names for file fields in USER_INFOS table
//        $fileFieldIdsAndNames = UserInfo::join('fields', 'user_infos.field_id', '=', 'fields.field_id')
//                                        ->where('user_infos.user_id', $userId)
//                                        ->whereNotNull('user_infos.file_name') // Only include rows which are files
//                                        ->whereNotNull('user_infos.file_path')
//                                        ->pluck('user_infos.file_name', 'fields.field_name')
//                                        ->toArray();

//        var_dump($fileFieldIdsAndNames); die;


        // download files from bitrix



//        $pathOriginal = "public/profile/original";
//        $pathThumbnail = "public/profile/thumbnail";
//        $pathTiny = "public/profile/tiny";
//
//        $downloadUrl = $dataFromBitrix["result"]["UF_CRM_1667336320092"]["downloadUrl"];
//        $fileContents = Http::get("https://polandstudy.bitrix24.pl".$downloadUrl)->body();
//
//        //formatting
//        $thumbnailSize = 150;
//        $tinySize = 35;
//
//        $fileName = "profile-image-from-bitrix.png";
////        $moved = Storage::putFileAs($pathOriginal, $fileContents, $fileName);
//        $moved = file_put_contents(storage_path("app/".$pathOriginal.'/'.$fileName), $fileContents);
//        if ($moved) {
//            ImageService::resize($thumbnailSize, $file, $pathThumbnail, $newFileName);
//            ImageService::resize($tinySize, $file, $pathTiny, $newFileName);
//        }


//            $correctStageId = Stage::where('bitrix_stage_id', $dataFromBitrixArray["stage_id"])->value('stage_id');
//            var_dump($correctStageId);
//            var_dump($correctStageId);
            // Update the stage_id in $dataFromDatabase
//            try {
////                $dataFromDatabase->stage_id = $correctStageId;
//                $deal = Deal::where('bitrix_deal_id', $bitrixId)->firstOrFail();
////                $deal->stage_id = $correctStageId;
////                $deal->save();
//                var_dump($deal->stage_id);
////                if($dataFromDatabase->save()){
////                    var_dump("uspeh");
////                };
////                var_dump($dataFromDatabase->stage_id);
//            }
//            catch (\Exception $e){
//                var_dump("neuspeh ".$e->getMessage());
//            }

//            var_dump($dataFromBitrix["result"]["STAGE_ID"]);

            // Check if there are any differences between our data and received from Bitrix
//            $differences = self::compareData($dataFromDatabase, $dataFromBitrix);
//            var_dump($differences);
            // If there are differences, update the appropriate fields in our database
//            if ($differences) {
//                self::updateDatabase('database', $type, $bitrixId, $differences);
//            }
//        }
//        else {
//            Log::informationLog("No matching record in our database with received Outbound webhook.");
//        }
    }

//    private static function fetchDataFromDatabase($type, $bitrixId)
//    {
//        //STAGE_ID NIJE SELEKTOVAN! DA BI MOGAO DA SE POREDI SA BITRIXOM TREBA DA SE JOINUJE SA STAGES TABELOM
//        //USER_INTAKE_PACKAGE_ID NIJE SELEKTOVAN! DA BI MOGAO DA SE POREDI SA BITRIXOM TREBA DA SE JOINUJE SA USER_INTAKE_PACKAGES I PACKAGES
//        if ($type === "deal") {
//            return Deal::where('deals.bitrix_deal_id', $bitrixId)
//                        ->select(
//                            "bitrix_deal_id as ID",
//                            "university as UF_CRM_1667335624051",
//                            "user_id",
//                            "degree as UF_CRM_1667335695035",
//                            "program as UF_CRM_1667335742921",// as uf_crm_1667335742921
//                            "intake as UF_CRM_1668001651504",
//                            //"stage_id",
//                            //"user_intake_package_id"
//                        )->first();
////            return Deal::where('deals.bitrix_deal_id', $bitrixId)
////                ->join('stages', 'deals.stage_id', '=', 'stages.stage_id')
////                ->select(
////                    "deals.bitrix_deal_id as id",
////                    "deals.university",
////                    "deals.degree",
////                    "deals.program as uf_crm_1667335742921",
////                    "deals.intake",
////                    "stages.bitrix_stage_id as stage_id"
////                )
////                ->firstOrFail(); //returns ModelNotFoundException if fail
//        }
//        // elseif ($type === "contact"){
//        //     //CONTACT STILL NOT FINISHED, CONTINUE TOMORROW
//        //     return User::where('contact_id', $bitrixId)->firstOrFail();
//        // }
//        else {
//            throw new \Exception("Unsupported type of record provided for outbound webhook data retrieval from database.");
//        }
//    }

    private static function fetchDataFromBitrix($type, $bitrixId)
    {
        if ($type === "deal") {// || $type === "contact"
            $method = 'crm.' . $type . '.get';
            return CRest::call($method, [
                'ID' => $bitrixId,
            ]);
        }
        else {
            throw new \Exception("Unsupported type of record provided for outbound webhook data retrieval from Bitrix.");
        }
    }
//    private static function compareData($dataFromDatabase, $dataFromBitrix)
//    {
//        // Compare the data and return the differences
//        //CONTINUE WORKING HERE
//        $dataFromDatabaseArray = array_change_key_case($dataFromDatabase->toArray(), CASE_LOWER);
//        $dataFromBitrixArray = array_change_key_case($dataFromBitrix["result"], CASE_LOWER);
//        var_dump($dataFromBitrix);
////        var_dump($dataFromBitrixArray);
//
//        return array_diff_assoc($dataFromDatabaseArray, $dataFromBitrixArray);
//    }

//    private static function updateDatabase($location, $type, $bitrixId, $differences)
//    {
//        // Update the appropriate fields in our database
//                $storage = ($type === 'deal') ? new Deal() : new Contact();
//                $storage->where('bitrix_id', $bitrixId)->update($differences);
//
//    }
}
