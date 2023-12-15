<?php

namespace App\Http\Controllers;

use App\Jobs\UpdateFields;
use App\Models\Deal;
use App\Models\Field;
use App\Models\FieldCategory;
use App\Models\Log;
use CRest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FieldController extends Controller {

    public function getAvailableFields(Request $request) {
        //        OLD WAY WITH THE FIELDS JSON

        //        $c_vals = $request->input('id');
        //
        //        $categories = \App\Models\FieldCategory::whereIn('field_category_id', $c_vals)->get();
        //        $fields = Field::where('is_active', '1')->where('field_category_id', '<>', NULL)->get();
        //
        //        // Path to the public/js directory
        //        $jsPath = resource_path('js');
        //        //Gets content from json file
        //        $json = file_get_contents($jsPath . "/fields.json");
        //        //Make it in php array
        //        $json_fields = json_decode($json, true);
        //
        //        $data = [$categories, $fields, $json_fields];
        //
        //        return response()->json($data);

        $c_vals = $request->input('id');

        $categories = FieldCategory::whereIn('field_category_id',
            $c_vals)->get();
        $fields = Field::where('is_active', '1')
            ->where('field_category_id', '<>', NULL)
            ->whereIn('field_category_id', $c_vals)
            ->orderBy('order', 'asc')
            ->get();
        $items = [];
        $user = Auth::user();
        if (in_array(4, $c_vals)) {
            $info = count(Deal::where('user_id', $user->user_id)
                ->pluck('deal_id')
                ->toArray());
        }
        else {
            $info = Db::table("user_infos")
                ->selectRaw("`field_id`, `value`, `display_value`, `file_name`,`file_path`")
                ->where("user_id", $user->user_id)
                ->groupBy("field_id", "value", "display_value", "file_name",
                    'file_path')
                ->get();
        }

        foreach ($fields as $field) {
            if ($field->type == "enumeration") {
                $field['items'] = $field->items;
            }
        }

        $data = [$categories, $fields, $info];

        return response()->json($data);
    }

    public function setFieldCategory(Request $request) {
        //Validate incoming request
        $validator = Validator::make($request->all(), [
            'fieldsOrders' => 'required|array',
            // Ensure 'fieldsOrders' is present and an array
            'fieldsOrders.*.field_id' => 'required|integer',
            // Validate 'field_id' within each array element
            'fieldsOrders.*.field_name' => 'required|string',
            // Validate 'field_name' within each array element
            'fieldsOrders.*.order' => 'integer|nullable',
            // Validate 'order' within each array element
            'fieldsOrders.*.is_required' => 'required|boolean',
            // Validate 'is_required' within each array element
            'fieldsOrders.*.field_category_id' => 'integer|nullable',
        ]);

        if ($validator->fails()) {
            // If validation fails, redirect back with errors and input data
            Log::errorLog('Validation failed. One or more fields are invalid.',
                Auth::user()->user_id);
            return redirect()
                ->route("admin_home")
                ->with([
                    'toast' => [
                        'message' => "Validation failed. One or more fields are invalid.",
                        'type' => 'danger',
                    ],
                ]);
        }

        // If validated, check if all received fields exist in the database
        $validatedData = $validator->validated(); // Get the validated data

        $fieldIds = collect($validatedData['fieldsOrders'])
            ->pluck('field_id')
            ->toArray();
        $fieldNames = collect($validatedData['fieldsOrders'])
            ->pluck('field_name')
            ->toArray();

        // Check if all submitted fields exist in the database
        $fieldsExist = Field::whereIn('field_id', $fieldIds)
                ->whereIn('field_name', $fieldNames)
                ->count() === count($fieldIds);

        if (!$fieldsExist) {
            // If not, log the error and redirect back with an error message
            Log::errorLog('Validation failed. One or more fields do not exist.',
                Auth::user()->user_id);
            return redirect()
                ->route("admin_home")
                ->with([
                    'toast' => [
                        'message' => "Validation failed",
                        'type' => 'danger',
                    ],
                ]);
        }

        try {
            $fieldsOrders = $validatedData["fieldsOrders"];

            //UPDATE ORDERS, FIELD_CATEGORY_IDS, IS_REQUIRED SETTINGS IN DATABASE
            Field::upsert(
                $fieldsOrders, //insert or update this
                ["field_id", "field_name"], //determine by this
                [
                    "order",
                    "is_required",
                    "field_category_id",
                ]); //if exists update this

            Log::apiLog('Fields updated in admin panel!',
                Auth::user()->user_id);
            return redirect()
                ->route("admin_home")
                ->with([
                    'toast' => [
                        'message' => "Fields updated successfully!",
                        'type' => 'success',
                    ],
                ]);
        }
        catch (Exception $e) {
            Log::errorLog($e->getMessage(), Auth::user()->user_id);
            return redirect()
                ->route("admin_home")
                ->with([
                    'toast' => [
                        'message' => "An error occurred on the server.",
                        'type' => 'danger',
                    ],
                ]);
        }
    }

    public function updateCustomTitle(Request $request) {
        //Validate incoming request
        $validator = Validator::make($request->all(), [
            'field_id' => 'required|integer',
            // how to allow null values for customField?
            'customTitle' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            // If validation fails, redirect back with errors and input data
            Log::errorLog('Validation failed. One or more fields are invalid.',
                Auth::user()->user_id);
            return redirect()
                ->route("admin_home")
                ->with([
                    'toast' => [
                        'message' => "Validation failed. One or more fields are invalid.",
                        'type' => 'danger',
                    ],
                ]);
        }

        // If validated, check if all received fields exist in the database
        $validatedData = $validator->validated(); // Get the validated data

        $fieldId = $validatedData['field_id'];
        $customTitle = $validatedData['customTitle'];

        // Check if the submitted field exists in the database

        $fieldExists = Field::where('field_id', $fieldId)->count() === 1;

        if (!$fieldExists) {
            // If not, log the error and redirect back with an error message
            Log::errorLog('Validation failed. One or more fields do not exist.',
                Auth::user()->user_id);
            return redirect()
                ->route("admin_home")
                ->with([
                    'toast' => [
                        'message' => "Validation failed",
                        'type' => 'danger',
                    ],
                ]);
        }

        try {
            $field = Field::where('field_id', $fieldId)->first();

            if ($customTitle === '') {
                $customTitle = NULL;
            }
            $field->custom_title = $customTitle;
            $field->save();

            Log::apiLog('Custom title updated in admin panel!',
                Auth::user()->user_id);
            return redirect()
                ->route("admin_home")
                ->with([
                    'toast' => [
                        'message' => "Custom title ".$field->title." updated successfully to ".$customTitle ?? 'null'."!",
                        'type' => 'success',
                    ],
                ]);
        }
        catch (Exception $e) {
            Log::errorLog($e->getMessage(), Auth::user()->user_id);
            return redirect()
                ->route("admin_home")
                ->with([
                    'toast' => [
                        'message' => "An error occurred on the server.",
                        'type' => 'danger',
                    ],
                ]);
        }
    }

    //OLD FUNCTION WITH JSON
    public function updateFieldss() {
        // Path to the public/js directory
        $jsPath = resource_path('js');
        //Gets content from json file
        $json = file_get_contents($jsPath."/fields.json");
        //Make it in php array
        $jsonData = json_decode($json, TRUE);

        //getting all fields from API
        $fields = CRest::call('crm.deal.fields');
        //simulating new input
        //    $fields['result']['new_field'] = ['type' => 'string', 'field_name' => "new_field", 'formLabel' => 'Novo polje'];
        //get the names of all fileds in response
        $keys = array_keys($fields["result"]);

        //getting all keys from api
        $jsonKeys = array_map(function($el) {
            return $el['field_name'];
        }, $jsonData);

        //passing through all api keys
        foreach ($keys as $key) {
            $newItem = $fields['result'][$key];

            // checking if the type is dropdown list
            if ($newItem['type'] == 'enumeration') {
                // gets the dropdown item from json, to check it's fields
                $jsonItem = array_filter($jsonData, function($item) use ($key) {
                    return $item['field_name'] == $key;
                });
                // make the index goes from zero
                $jsonItem = array_values($jsonItem);
                // going through items in dropdown menu
                foreach ($newItem['items'] as $item) {
                    //geting the id from dropdown item
                    $i_id = $item["ID"];
                    // getting dropdown items from json array
                    $elemItems = $jsonItem[0]['items'];

                    // checking if the items exists in json dropdown items
                    $checkItem = array_filter($elemItems,
                        function($el) use ($i_id) {
                            return $el["ID"] == $i_id;
                        });
                    // if it doesn't exist add to json
                    if ($checkItem == NULL) {
                        // get the index of array element with that field name
                        $id = array_filter($jsonData, function($el) use ($key) {
                            return $el['field_name'] == $key;
                        });
                        //                    get id and convert to int
                        $id = array_keys($id);

                        $id = (int) $id[0];

                        // add it to existing json file
                        $jsonData[$id]['items'][] = $item;
                    }
                }
            }

            //checking if key from json is in array
            if (!in_array($key, $jsonKeys)) {
                //            var_dump($fields['result'][$key]);
                $newItem['field_name'] = $newItem['title'];
                //if not then add it to php array which goes to json
                $jsonData[] = $newItem;

                //            adding it to the table in database
                if (isset($newItem['formLabel'])) {
                    Field::create([
                        'field_name' => $newItem['field_name'],
                        'type' => $newItem['type'],
                        'title' => $newItem['formLabel'],
                    ]);
                }
                else {
                    Field::create([
                        'field_name' => $newItem['field_name'],
                        'type' => $newItem['type'],
                    ]);
                }
            }
        }

        //check if json has some element that is not in api anymore
        foreach ($jsonKeys as $jsonKey) {
            if (!in_array($jsonKey, $keys)) {
                $field = Field::where('field_name', $jsonKey)->first();
                $field->is_active = 0;
                $field->save();
            }
        }
        // updating the json file back
        $json = json_encode($jsonData, JSON_PRETTY_PRINT);

        // Path to the public/js directory
        $jsPath = resource_path('js');

        file_put_contents($jsPath."/fields.json", $json);

        return redirect()
            ->back()
            ->with(['fieldMessage' => 'Fields are updated!']);
    }

    public function updateFields() {
        UpdateFields::dispatch();
        return redirect()
            ->back()
            ->with([
                'toast' => [
                    'message' => 'Fields updated',
                    'type' => 'success',
                ],
            ]);

        // this is old way of updating fields before the job

        //        try {
        //            // Step 1: Retrieve field data from the CRM API
        //            $fields = CRest::call('crm.deal.fields');
        //
        //            // Extract field names from the API response
        //            $keys = array_keys($fields["result"]);
        //
        //            // Step 2: Begin a database transaction to ensure data consistency
        //            DB::beginTransaction();
        //
        //            // Retrieve existing field names from the database
        //            $dataFields = Field::pluck('field_name')->toArray();
        //
        //            // Step 3: Iterate through the API response and update database fields
        //            foreach ($keys as $key) {
        //                if (!in_array($key, $dataFields)) {
        //                    $newItem = $fields['result'][$key];
        //
        //                    // Prepare field data for database insertion
        //                    $fieldData = [
        //                        'field_name' => $key,
        //                        'type' => $newItem['type'],
        //                    ];
        //
        //                    if (isset($newItem['formLabel'])) {
        //                        $fieldData['title'] = $newItem['formLabel'];
        //                    }
        //
        //                    // Create a new field in the database
        //                    $newField = Field::create($fieldData);
        //
        //                    // If the field type is 'enumeration', process its items
        //                    if ($newItem['type'] == 'enumeration') {
        //                        $items = $newItem['items'];
        //                        foreach ($items as $item) {
        //                            FieldItem::create([
        //                                'field_id' => $newField->field_id,
        //                                'item_value' => $item['VALUE'],
        //                                'item_id' => $item['ID'],
        //                            ]);
        //
        //                            // Check if an identical item exists in another field and remove it
        //                            $checkIfExists = FieldItem::where('field_id', '<>',
        //                                $newField->field_id)
        //                                ->where('item_value', $item['VALUE'])
        //                                ->where('item_id', $item['ID'])
        //                                ->first();
        //
        //                            if ($checkIfExists) {
        //                                $checkIfExists->delete();
        //                            }
        //                        }
        //                    }
        //                }
        //            }
        //
        //            // Step 4: Commit the database transaction
        //            DB::commit();
        //
        //            // Step 6: Perform additional checks and updates after the transaction
        //            foreach ($keys as $key) {
        //                $el = $fields['result'][$key];
        //
        //                if ($el['type'] == 'enumeration') {
        //                    $enumField = Field::where('field_name', $key)->first();
        //
        //                    if ($enumField) {
        //                        // Retrieve items from the database
        //                        $itemsFromDatabase = $enumField->items->pluck('item_id',
        //                            'item_value')->toArray();
        //
        //                        // Create an array with item data for comparison
        //                        $resultArray = range(0, count($itemsFromDatabase) - 1);
        //                        $arrayItemsFromDatabase = array_map(function(
        //                            $key,
        //                            $id
        //                        ) use (
        //                            $itemsFromDatabase
        //                        ) {
        //                            $val = array_search($id, $itemsFromDatabase);
        //                            return ['ID' => $id, 'VALUE' => $val];
        //                        }, $resultArray, $itemsFromDatabase);
        //
        //                        // Compare items from API with items from the database and perform updates
        //                        $fieldItemsFromApi = $el['items'];
        //                        foreach ($fieldItemsFromApi as $apiItem) {
        //                            $found = FALSE;
        //                            foreach ($arrayItemsFromDatabase as $databaseItem) {
        //                                if ($apiItem == $databaseItem) {
        //                                    $found = TRUE;
        //                                    break;
        //                                }
        //                            }
        //                            if (!$found) {
        //                                $new = FieldItem::create([
        //                                    'item_value' => $apiItem['VALUE'],
        //                                    'item_id' => $apiItem['ID'],
        //                                    'field_id' => $enumField->field_id,
        //                                ]);
        //                            }
        //                        }
        //
        //                        // Deactivate items in the database that are not present in the API response
        //                        foreach ($arrayItemsFromDatabase as $databaseItem) {
        //                            $found = FALSE;
        //                            foreach ($fieldItemsFromApi as $apiItem) {
        //                                if ($apiItem == $databaseItem) {
        //                                    $found = TRUE;
        //                                    break;
        //                                }
        //                            }
        //                            if (!$found) {
        //                                $oldItem = FieldItem::where('item_value',
        //                                    $databaseItem['VALUE'])
        //                                    ->where('item_id', $databaseItem['ID'])
        //                                    ->first();
        //                                $oldItem->is_active = 0;
        //                                $oldItem->save();
        //                            }
        //                        }
        //                    }
        //                }
        //            }
        //
        //            // Step 7: Check for fields in the database that are no longer present in the API response and deactivate them
        //            foreach ($dataFields as $field) {
        //                if (!in_array($field, $keys)) {
        //                    $field = Field::where('field_name', $field)->first();
        //                    $field->is_active = 0;
        //                    $field->save();
        //                }
        //            }
        //
        //            Intake::insertNewIntakes();
        //
        //            Package::insertNewPackages();
        //            // Step 8: Redirect with a success message
        //            return redirect()
        //                ->back()
        //                ->with([
        //                    'toast' => [
        //                        'message' => 'Fields are updated!',
        //                        'type' => 'success',
        //                    ],
        //                ]);
        //        }
        //        catch (Exception $e) {
        //            // Step 5: Handle exceptions and roll back the transaction in case of an error
        //            DB::rollback();
        //            // Handle the exception (e.g., log or throw a custom exception)
        //            return redirect()
        //                ->back()
        //                ->with([
        //                    'toast' => [
        //                        'message' => $e->getMessage(),
        //                        'type' => 'danger',
        //                    ],
        //                ]);
        //        }
    }

}
