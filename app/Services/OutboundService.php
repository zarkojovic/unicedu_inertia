<?php

namespace App\Services;

use App\Models\Deal;
use App\Models\Field;
use App\Models\FieldItem;
use App\Models\Stage;
use App\Models\UserInfo;
use CRest;
use Illuminate\Support\Facades\Http;
use App\Models\Log;
use Illuminate\Support\Str;

class OutboundService
{
    // 1. check if deal/contact exists in our database
    // 2. use rest api method to retrieve fresh values from bitrix for all fields for that deal/contact
    // 3. check if there are any differences between our data and received from bitrix
    // 4. if there are differences, update the appropriate fields in our database
    // 5. if there are no differences, insert new values
    public static function handleUpdate($type, $bitrixId)
    {
        // Check if deal exists in our database and get column values
        $record = Deal::join('stages','deals.stage_id','=','stages.stage_id')
//            ->join('user_intake_packages', 'deals.user_intake_package_id', '=', 'user_intake_packages.user_intake_package_id')
//            ->join('packages', 'user_intake_package.package_id')
            ->where('deals.bitrix_deal_id', $bitrixId)
            ->select('deals.deal_id', 'deals.user_id', 'deals.university', 'deals.degree', 'deals.program',
                     'deals.intake', 'stages.bitrix_stage_id as STAGE_ID')
            ->first();

        if (!$record) {
            Log::errorLog("Outbound webhook error: Couldn't find a matching record in the database.");
            return null;
        }

        // retrieve user_id and deal_id
        $userId = $record["user_id"];
        $dealId = $record["deal_id"];

        if (!$userId) {
            Log::errorLog("Outbound webhook error: Couldn't find user_id in Deals table.");
            return null;
        }
        if (!$dealId) {
            Log::errorLog("Outbound webhook error: Couldn't find deal_id in Deals table.");
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

//        $fieldItemMap["UF_CRM_1667335742921"] = $record->program; // add program to array
        $fieldItemMap["STAGE_ID"] = $record['STAGE_ID']; // add stage id to array

        // here the minimum number of records should be the required fields! Otherwise, no deal should exist
        $userInfoFromDatabase = UserInfo::join('fields', 'user_infos.field_id', '=', 'fields.field_id')
                                        ->where('user_infos.user_id', $userId)
                                        ->whereNotNull('user_infos.value') // Only include rows which are not files
                                        ->pluck('user_infos.value', 'fields.field_name')
                                        ->toArray();

        // get all file fields with values
        $userInfoFileFields = UserInfo::leftJoin('fields', function ($join) use ($userId, $dealId) {
                                            $join->on('user_infos.field_id', '=', 'fields.field_id')
                                                ->where(function ($query) use ($userId, $dealId) {
                                                    $query->where('user_infos.user_id', '=', $userId)
                                                        ->orWhere('user_infos.deal_id', '=', $dealId);
                                                });
                                        })
                                        ->where('fields.field_name', '!=', 'UF_CRM_1667336320092') // no profile image
                                        ->whereNull('user_infos.value') // Only include rows which ARE files
                                        ->pluck('user_infos.file_id','fields.field_name')
                                        ->toArray();

        if (!$userInfoFromDatabase) {
            Log::errorLog("Outbound webhook error: Couldn't find records in User_infos table (missing required fields).");
            return null;
        }
//        if (!$userInfoFileFields) {
//            Log::errorLog("Outbound webhook error: Couldn't find records in User_infos table (missing file fields).");
//            return null;
//        }


        $dataFromDatabase = array_merge($fieldItemMap, $userInfoFromDatabase);

        // retrieve fresh data from bitrix
        $dataFromBitrix = [];
        try {
            $dataFromBitrix = self::fetchDataFromBitrix($type, $bitrixId);
        }
        catch (\Exception $e){
            Log::errorLog("Outbound webhook error: ".$e->getMessage());
        }

//        var_dump($dataFromBitrix["result"]); die;
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
        // COMPARE DATABASE AND BITRIX DATA FOR FILE FIELDS
        foreach ($userInfoFileFields as $fieldName => $fileId) {
            if (isset($dataFromBitrix["result"][$fieldName]) && $fileId != $dataFromBitrix["result"][$fieldName]["id"]) {
                $differences[$fieldName] = $dataFromBitrix["result"][$fieldName];
            }
        }

//        var_dump($differences); die;
        if ($differences){
            // here check if the fields found in differences belong to DEALS or USER_INFOS tables and update the values accordingly
//            $fieldNames[] = "UF_CRM_1667335742921"; // add program for checking for deal fields
            $fieldNames[] = "STAGE_ID"; // add stage id for checking for deal fields
            $dealFields = array_intersect_key($differences, array_flip($fieldNames));

            if ($dealFields) {
                // changes in deal fields found. Proceed to update DEALS table
                // currently there's support only for STAGE_ID, and not for other deal fields

                $currentStageIdValue = $record["STAGE_ID"];
                $bitrixStageIdValue = $differences["STAGE_ID"];

                if ($bitrixStageIdValue !== $currentStageIdValue) {
                    try {
                        $newStageId = Stage::where("bitrix_stage_id", $bitrixStageIdValue)
                            ->value("stage_id");

                        if (!$newStageId) {
                            throw new \Exception("Could not retrieve stage_id for bitrix_stage_id: " . $bitrixStageIdValue);
                        }

                        $deal = Deal::where('bitrix_deal_id', $bitrixId)
                            ->firstOrFail();
                        $deal->stage_id = $newStageId;
                        if (!$deal->save()) {
                            throw new \Exception("Could not save stage_id for deal.");
                        }

                        // After updating the deal, unset "STAGE_ID" from $differences to prevent inserting in user_infos table
                        unset($differences["STAGE_ID"]);

                        Log::informationLog("Outbound webhook message: Successfully updated deal stage!");
                    } catch (\Exception $e) {
                        Log::errorLog("Outbound webhook error: " . $e->getMessage());
                    }
                }
            }

            if ($differences !== $dealFields){
                // proceed to update USER_INFOS table
                self::updateOrInsertIntoUserInfoTable($dealId, $userId, $differences);
            }
        }

        // HERE CHECK IF THE VALUE OF A FIELD THAT THE USER HASN'T FILLED IN OUR APP HAS CHANGED
        // 1. if the field already exists in our database, insert the new value into USER_INFOS table (special case for DEALS table)
        // 2. if the field doesn't exist in our database, refresh fields and then insert the new value --||--


        // 1st case
        // get all categorized fields which are not filled in (optional fields, as well as unfillable fields
        // which come from bitrix like payment confirmation field)
        $categorizedUnusedFieldNames = Field::leftJoin('user_infos', function ($join) use ($userId, $dealId) {
                                                $join->on('fields.field_id', '=', 'user_infos.field_id')
                                                    ->where(function ($query) use ($userId, $dealId) {
                                                        $query->where('user_infos.user_id', '=', $userId)
                                                            ->orWhere('user_infos.deal_id', '=', $dealId);
                                                    });
                                            })
                                            ->whereNull('user_infos.field_id')
                                            ->whereNotIn('fields.field_category_id', [4, 5])
                                            ->pluck('fields.field_name')
                                            ->toArray();
//        var_dump($notFilledAndUniqueDealFieldNames); die;

        // format the array for inserting into USER_INFOS table
        $filledFieldNamesAndValues = [];
        foreach ($categorizedUnusedFieldNames as $fieldName) {
            if (isset($dataFromBitrix["result"][$fieldName]) && $dataFromBitrix["result"][$fieldName]) {
                $filledFieldNamesAndValues[$fieldName] = $dataFromBitrix["result"][$fieldName];
            }
        }

        // insert into USER_INFOS table
        if ($filledFieldNamesAndValues) {
            self::updateOrInsertIntoUserInfoTable($dealId, $userId, $filledFieldNamesAndValues);
        }
    }

    private static function updateOrInsertIntoUserInfoTable($dealId, $userId, $differences)
    {
        $fieldData = Field::whereIn('field_name', array_keys($differences))
            ->select(
                'fields.field_id',
                'fields.field_name',
                'fields.type',
                'fields.field_category_id',
                'field_categories.is_deal_category'
            )
            ->join('field_categories', 'fields.field_category_id', '=', 'field_categories.field_category_id')
            ->get()
            ->keyBy('field_id');

        $fileFieldIds = $fieldData->filter(function ($field) {
            return $field->type === 'file';
        })->pluck('field_id')->toArray();

        // update or insert files
        if ($fileFieldIds){
            self::updateOrInsertFileFields($fileFieldIds, $fieldData, $differences, $dealId, $userId);
        }

        // update or insert other non-file fields in USER_INFO table
        $nonFileFieldIds = $fieldData->filter(function ($field) {
            return $field->type !== 'file';
        })->pluck('field_id')->toArray();

        // maybe check here if there are any non file fields before proceeding


        // get the right format for mapping
        $fieldIdsAndNames = $fieldData->pluck('field_name', 'field_id')->toArray();

        // Populate the field_id => value mapping
        $fieldIdValueMapping = [];
        foreach ($nonFileFieldIds as $fieldId) {
            $field_name = $fieldIdsAndNames[$fieldId];
            $fieldIdValueMapping[$fieldId] = $differences[$field_name];
        }

        $hasEnumerationFields = $fieldData->contains('type', 'enumeration');

        // Retrieve dropdown fields and their values if they exist
        if ($hasEnumerationFields) {
            $dropdownFieldIdsAndValues = FieldItem::whereIn('field_id', $nonFileFieldIds)
                ->whereIn("item_id", $fieldIdValueMapping)
                ->pluck("item_value", "field_id")
                ->toArray();
        }


        $valuesForUpdating = [];
        foreach ($fieldIdValueMapping as $fieldId=>$value) {
            $displayValue = $dropdownFieldIdsAndValues[$fieldId] ?? null;

            // determine if it's a deal unique field, or contact field
            $isDealCategory = $fieldData->where("field_id", $fieldId)->value('is_deal_category');

            // insert or update in database
            $valuesForUpdating[] = [
                'user_id' => $isDealCategory ? null : $userId,
                'deal_id' => $isDealCategory ? $dealId : null,
                'field_id' => $fieldId,
                'value' => $value,
                'display_value' => $displayValue
            ];
        }

//            var_dump($valuesForUpdating); die;
        try {
            UserInfo::upsert($valuesForUpdating,
                ['deal_id', 'user_id', 'field_id'],
                ['value', 'display_value']);

            Log::informationLog("Outbound webhook message: Successfully updated USER_INFOS field values in our database!");
        } catch (\Exception $e) {
            Log::errorLog("Outbound webhook error: " . $e->getMessage());
        }
    }

    private static function updateOrInsertFileFields($fileFieldIds, $fieldData, $differences, $dealId, $userId){
        $domain = "https://polandstudy.bitrix24.pl";
        $pathDocuments = "public/profile/documents";
        $valuesForUpdating = [];
//        $fileIds = UserInfo::whereIn('field_id', array_keys($fieldData->toArray()))
//                            ->pluck('file_id', 'field_id')
//                            ->toArray();

        foreach ($fileFieldIds as $fileFieldId) {
            $fieldName = $fieldData->where("field_id", $fileFieldId)->value('field_name');
            $downloadUrl = $differences[$fieldName]["downloadUrl"];
            $bitrixFileId = $differences[$fieldName]['id'];

            // determine if it's a deal unique file field, or contact file field
            $isDealCategory = $fieldData->where("field_id", $fileFieldId)->value('is_deal_category');

            // download file
            $response = Http::get($domain.$downloadUrl); //returns Laravel Response object
            $originalFileName = '';
            $newFileName = Str::random(45);

            if ($response->hasHeader('Content-Disposition')) {
                // Extract the filename from the Content-Disposition header
                $contentDisposition = $response->header('Content-Disposition');

                if (preg_match('/filename="(.+)"/', $contentDisposition, $matches)) {
                    $originalFileName = $matches[1];
                    $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
                    $newFileName = $newFileName . '.' . $fileExtension;

                    try {
                        file_put_contents(storage_path("app/" . $pathDocuments . '/' . $newFileName), $response->body());
                        Log::informationLog("Outbound webhook message: Successfully moved file to documents folder!");

                        self::deletePreviousFile($pathDocuments, $fileFieldId, $isDealCategory, $userId, $dealId);
                    } catch (\Exception $e) {
                        Log::errorLog("Outbound webhook error: " . $e->getMessage());
                    }
                }
                else {
                    Log::errorLog("Outbound webhook error: Filename doesn't match regular expression.");
                }
            }
            else {
                // default file name
                try {
                    $newFileName = Str::random(45);
                    $fileExtension = ".pdf";
                    file_put_contents(storage_path("app/" . $pathDocuments . '/' . $newFileName . $fileExtension), $response->body());
                    Log::informationLog("Outbound webhook message: Successfully moved file to documents folder!");

                    self::deletePreviousFile($pathDocuments, $fileFieldId, $isDealCategory, $userId, $dealId);
                } catch (\Exception $e) {
                    Log::errorLog("Outbound webhook error: " . $e->getMessage());
                }
            }

            // insert or update in database
            $valuesForUpdating[] = [
                'user_id' => $isDealCategory ? null : $userId,
                'deal_id' => $isDealCategory ? $dealId : null,
                'field_id' => $fileFieldId,
                'file_name' => $originalFileName,
                'file_path' => $newFileName,
                'file_id' => $bitrixFileId
            ];
        }

//            var_dump($valuesForUpdating); die;
        try {
            UserInfo::upsert($valuesForUpdating,
                ['user_id', 'deal_id', 'field_id'],
                ['file_name', 'file_path', 'file_id']);

            Log::informationLog("Outbound webhook message: Successfully updated USER_INFOS field values in our database!");
        } catch (\Exception $e) {
            Log::errorLog("Outbound webhook error: " . $e->getMessage());
        }
    }

    private static function fetchDataFromBitrix($type, $bitrixId)
    {
        if ($type === "deal") {// || $type === "contact"
            $method = 'crm.' . $type . '.get';
            return CRest::call($method, [
                'ID' => $bitrixId,
            ]);
        }
        else {
//            throw new \Exception("Unsupported type of record provided for outbound webhook data retrieval from Bitrix.");
            Log::errorLog("Unsupported type of record provided for outbound webhook data retrieval from Bitrix.");
        }
    }

    private static function deletePreviousFile($path, $fileFieldId, $isDealCategory, $userId, $dealId) {
        // Find the previous file_path in the database
        $previousFilePath = UserInfo::where('field_id', $fileFieldId)
            ->when($isDealCategory, function ($query) use ($dealId) {
                return $query->where('deal_id', $dealId);
            }, function ($query) use ($userId) {
                return $query->where('user_id', $userId);
            })
            ->value('file_path');

        // Check if a previous file exists and delete it
        if ($previousFilePath) {
            $previousFilePath = storage_path("app/" . $path . '/' . $previousFilePath);
            if (file_exists($previousFilePath)) {
                unlink($previousFilePath);
            }
        }
    }
}
