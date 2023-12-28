<?php

namespace App\Services;

use App\Models\Field;
use App\Models\Log;
use App\Models\UserInfo;
use CRest;
use Exception;
use Illuminate\Support\Facades\DB;

class SyncDealFileIdsService {

    public static function sync($bitrixDealId) {
        try {
            DB::beginTransaction();
            // Get deal info from Bitrix
            $dealInfoFromBitrix = CRest::call('crm.deal.get', [
                'id' => $bitrixDealId,
            ]);
            // Checking if error occurred
            if (isset($dealInfoFromBitrix['error'])) {
                throw new Exception($dealInfoFromBitrix['error_description']);
            }
            // Checking if deal was found
            if (!$dealInfoFromBitrix['result']) {
                throw new Exception('Error: Deal not found');
            }

            // Get deal info from database
            $dealInfoFromDatabase = DB::table('user_infos')
                ->join('deals', 'deals.user_id', '=', 'user_infos.user_id')
                ->join('fields', 'user_infos.field_id', '=', 'fields.field_id')
                ->where('deals.bitrix_deal_id', $bitrixDealId)
                ->where('fields.type', 'file')
                ->select('fields.field_name', 'user_infos.*')
                ->get()->toArray();

            // Get file fields from database that have a category
            $fileFields = Field::where('type', 'file')
                ->select('field_id', 'field_name')
                ->where('field_category_id', '<>', 'NULL')
                ->get()
                ->toArray();

            // Checking if deal was found
            if (!$dealInfoFromDatabase) {
                throw new Exception('Error: Deal not found');
            }

            // Extract 'field_name' items using array_column
            $fieldNamesArray = array_column($fileFields,
                'field_name');

            // Get only the actual data from the Bitrix response
            $itemsFromBitrix = $dealInfoFromBitrix['result'];

            // Filter the Bitrix response using the field names array
            $filteredArray = array_intersect_key($itemsFromBitrix,
                array_flip($fieldNamesArray));

            $transformedArray = array_filter($filteredArray, function($item) {
                if (isset($item['id'])) {
                    return $item['id'] !== NULL;
                }
            });

            foreach ($transformedArray as $key => $value) {
                // Get the field ID from the database
                $fieldId = Field::where('field_name', $key)
                    ->first()->field_id;
                // Get the user info record from the database
                $userInfo = UserInfo::where('user_id',
                    $dealInfoFromDatabase[0]->user_id)
                    ->where('field_id', $fieldId)
                    ->first();
                // Update the file ID in the user_info table
                if ($userInfo) {
                    $userInfo->file_id = (string) $value['id'];
                    $userInfo->save();
                }
                else {
                    // Create a new record in the user_info table
                    // Here we should take care of taking files from bitrix
                    UserInfo::create([
                        'user_id' => $dealInfoFromDatabase[0]->user_id,
                        'field_id' => $fieldId,
                        'file_id' => $value['id'],
                        'file_path' => 'path_to_file.pdf',
                        'file_name' => 'name_of_file.pdf',
                    ]);
                }
            }

            DB::commit();
        }
        catch (Exception $e) {
            DB::rollBack();
            Log::errorLog($e->getMessage());
            return $e->getMessage();
        }

        return 0;
    }

}
