<?php

namespace App\Jobs;

use App\Models\Field;
use App\Models\FieldItem;
use App\Models\Intake;
use App\Models\Package;
use CRest;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpdateFields implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct() {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void {
        try {
            // Step 1: Retrieve field data from the CRM API
            $fields = CRest::call('crm.deal.fields');

            throw new Exception('Pozzz');
            // Extract field names from the API response
            $keys = array_keys($fields["result"]);

            // Step 2: Begin a database transaction to ensure data consistency
            DB::beginTransaction();

            // Retrieve existing field names from the database
            $dataFields = Field::pluck('field_name')->toArray();

            // Step 3: Iterate through the API response and update database fields
            foreach ($keys as $key) {
                if (!in_array($key, $dataFields)) {
                    $newItem = $fields['result'][$key];

                    // Prepare field data for database insertion
                    $fieldData = [
                        'field_name' => $key,
                        'type' => $newItem['type'],
                    ];

                    if (isset($newItem['formLabel'])) {
                        $fieldData['title'] = $newItem['formLabel'];
                    }

                    // Create a new field in the database
                    $newField = Field::create($fieldData);

                    // If the field type is 'enumeration', process its items
                    if ($newItem['type'] == 'enumeration') {
                        $items = $newItem['items'];
                        foreach ($items as $item) {
                            FieldItem::create([
                                'field_id' => $newField->field_id,
                                'item_value' => $item['VALUE'],
                                'item_id' => $item['ID'],
                            ]);

                            // Check if an identical item exists in another field and remove it
                            $checkIfExists = FieldItem::where('field_id', '<>',
                                $newField->field_id)
                                ->where('item_value', $item['VALUE'])
                                ->where('item_id', $item['ID'])
                                ->first();

                            if ($checkIfExists) {
                                $checkIfExists->delete();
                            }
                        }
                    }
                }
            }

            // Step 4: Commit the database transaction
            DB::commit();

            // Step 6: Perform additional checks and updates after the transaction
            foreach ($keys as $key) {
                $el = $fields['result'][$key];

                if ($el['type'] == 'enumeration') {
                    $enumField = Field::where('field_name', $key)->first();

                    if ($enumField) {
                        // Retrieve items from the database
                        $itemsFromDatabase = $enumField->items->pluck('item_id',
                            'item_value')->toArray();

                        // Create an array with item data for comparison
                        $resultArray = range(0, count($itemsFromDatabase) - 1);
                        $arrayItemsFromDatabase = array_map(function(
                            $key,
                            $id
                        ) use (
                            $itemsFromDatabase
                        ) {
                            $val = array_search($id, $itemsFromDatabase);
                            return ['ID' => $id, 'VALUE' => $val];
                        }, $resultArray, $itemsFromDatabase);

                        // Compare items from API with items from the database and perform updates
                        $fieldItemsFromApi = $el['items'];
                        foreach ($fieldItemsFromApi as $apiItem) {
                            $found = FALSE;
                            foreach ($arrayItemsFromDatabase as $databaseItem) {
                                if ($apiItem == $databaseItem) {
                                    $found = TRUE;
                                    break;
                                }
                            }
                            if (!$found) {
                                $new = FieldItem::create([
                                    'item_value' => $apiItem['VALUE'],
                                    'item_id' => $apiItem['ID'],
                                    'field_id' => $enumField->field_id,
                                ]);
                            }
                        }

                        // Deactivate items in the database that are not present in the API response
                        foreach ($arrayItemsFromDatabase as $databaseItem) {
                            $found = FALSE;
                            foreach ($fieldItemsFromApi as $apiItem) {
                                if ($apiItem == $databaseItem) {
                                    $found = TRUE;
                                    break;
                                }
                            }
                            if (!$found) {
                                $oldItem = FieldItem::where('item_value',
                                    $databaseItem['VALUE'])
                                    ->where('item_id', $databaseItem['ID'])
                                    ->first();
                                $oldItem->is_active = 0;
                                $oldItem->save();
                            }
                        }
                    }
                }
            }

            // Step 7: Check for fields in the database that are no longer present in the API response and deactivate them
            foreach ($dataFields as $field) {
                if (!in_array($field, $keys)) {
                    $field = Field::where('field_name', $field)->first();
                    $field->is_active = 0;
                    $field->save();
                }
            }

            Intake::insertNewIntakes();

            Package::insertNewPackages();
            // Step 8: Redirect with a success message
        }
        catch (Exception $e) {
            // Step 5: Handle exceptions and roll back the transaction in case of an error
            DB::rollback();
            // Handle the exception (e.g., log or throw a custom exception)

        }
    }

}
