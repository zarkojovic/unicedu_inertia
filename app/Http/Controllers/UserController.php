<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateImageRequest;
use App\Jobs\UpdateUserBitrixDeals;
use App\Models\Field;
use App\Models\FieldCategory;
use App\Models\Log;
use App\Models\Role;
use App\Models\User;
use App\Models\UserInfo;
use App\Services\ImageService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Kafka0238\Crest\Src;

class UserController extends RootController {

    /**
     * Display the specified resource.
     */
    public function show() {
        // Get the authenticated user
        $user = Auth::user();

        $categoriesWithFields = FieldCategory::getAllCategoriesWithFields('/profile');
        // If the user is admin, redirect him to the admin home page
        $adminId = Role::where('role_name', 'admin')
            ->value('role_id');

        if ($user->role_id === $adminId) {
            return redirect()->route('admin_home');
        }
        return Inertia::render("Student/Profile", [
            'categoriesWithFields' => $categoriesWithFields,
            "img" => asset("storage/profile/thumbnail/".$user->profile_image),
        ]);
    }

    // this is old function for removing user file
    public function removeUserFile(Request $request) {
        $user = Auth::user();

        $field_id = $request->field_id;

        $info = UserInfo::where('field_id', $field_id)
            ->where('user_id', $user->user_id)
            ->first();

        if ($info) {
            if ($info->delete()) {
                return response()->json(['message' => 'You removed this file!']);
            }
            else {
                http_response_code(500);
                return response()->json(['message' => 'Error occurred! File is not deleted!']);
            }
        }
        else {
            http_response_code(401);
            return response()->json(['message' => 'File not found!']);
        }
    }

    public function updateUserInfo(Request $request) {
        try {
            if (User::userDealsPastFirstStage()) {
                throw new Exception("You cannot update your information currently as it's being used in the application process.");
            }

            //GET ALL OF THE DATA FROM REQUEST
            $items = $request['formItems'];

            //GET AUTH-ED USER FOR UPDATING HIS DATA
            $user = Auth::user();

            // GETTING FIELD NAME VALUES
            $field_names = array_column($items, 'field_name');

            // GETTING ALL THE FIELDS WITH THAT FIELD NAME
            $field_id_array = DB::table('fields')
                ->select('field_name', 'title', 'field_id')
                ->whereIn('field_name',
                    $field_names)
                ->orderBy('field_id')
                ->get();

            // GETTING JUST THE IDS OF THEM
            $field_ids = array_column($field_id_array->toArray(), 'field_id');

            // GETTING THE USER INFO FROM THE FIELD IDS1
            $user_info_array = DB::table('user_infos')
                ->join('fields', 'fields.field_id', 'user_infos.field_id')
                ->where("user_infos.user_id", (int) $user->user_id)
                ->whereIn("user_infos.field_id", $field_ids)
                ->select('user_infos.*', 'fields.field_name')
                ->orderBy('fields.field_name')
                ->get();

            usort($items, function($a, $b) {
                return strcmp($a["field_name"], $b["field_name"]);
            });

            DB::beginTransaction();
            //LOOPING THROUGH EACH ELEMENT IN REQUEST
            foreach ($items as $key => $value) {
                // Use array_filter to filter the array
                $field_id = array_filter($field_id_array->toArray(),
                    function($object) use ($value) {
                        return $object->field_name === $value['field_name'];
                    });
                // Convert the filtered result back to an indexed array
                $field_id = array_values($field_id);

                $field_id = $field_id[0];
                // Get the user info from the user_info_array
                $user_info = $user_info_array[$key] ?? NULL;

                //CHECKING IF THE REQUEST IS FILE
                if ($value['value'] instanceof UploadedFile) {
                    //GETTING THE INFO FROM FILE
                    $storeFile = $value['value'];
                    // GETTING THE EXTENSION OF FILE
                    $extension = $storeFile->extension();

                    // Check if the file extension is 'pdf'
                    if ($extension !== 'pdf') {
                        if ($field_id->title !== NULL) {
                            $fieldName = $field_id->title;
                        }
                        else {
                            $fieldName = $field_id->field_name;
                        }
                        throw ValidationException::withMessages([
                            'error' => "'$fieldName' File must be pdf!",
                        ]);
                    }

                    // Create me a variable for size of mb
                    $sizeInMb = 5;
                    if ($storeFile->getSize() > $sizeInMb * 1024 * 1024) {
                        throw ValidationException::withMessages([
                            'error' => "File too big (".$sizeInMb."mb limit)!",
                        ]);
                    }

                    $fileName = $storeFile->getClientOriginalName();
                    // Store the uploaded file
                    $storedPath = $storeFile->store('profile/documents',
                        'public');
                    //                GETTING THE NEW NAME OF FILE
                    $fileNewName = basename($storedPath);
                }

                //GETTING THE FIELD
                $fieldCheck = Field::findOrFail($field_id->field_id);

                // Check if the user_info is null (if the user has not entered any data for that field)
                if (!$user_info) {
                    // Check if the value is not empty and not null and not 0 (for the dropdowns)
                    if (isset($value['is_file']) && $value['is_file']) {
                        UserInfo::create([
                            'user_id' => (int) $user->user_id,
                            'field_id' => (int) $field_id->field_id,
                            'file_name' => $fileName,
                            'file_path' => $fileNewName,
                        ]);

                        Log::informationLog("User updated ".$fieldCheck->title.' to '.$fileName.'.',
                            Auth::user()->user_id);
                    }
                    else {
                        // Check if the value is not empty and not null and not 0 (for the dropdowns)
                        if (!empty($value) && $value !== 'null' && $value != 0) {
                            // If the value is an array, it means that the user has selected a dropdown option
                            if (isset($value['label'])) {
                                // Create a new record in the UserInfo table
                                UserInfo::create([
                                    'user_id' => (int) $user->user_id,
                                    'field_id' => (int) $field_id->field_id,
                                    'value' => $value['value'],
                                    'display_value' => $value['label'],
                                ]);
                                // Log the action
                                Log::informationLog("User updated ".$fieldCheck->title.' to '.$value['label'].'.',
                                    Auth::user()->user_id);
                            }
                            else {
                                // If the value is not an array, it means that the user has entered a text value
                                if (is_string($value['value'])) {
                                    // Capitalize the first letter of the value
                                    $value = ucfirst($value['value']);
                                }
                                // Create a new record in the UserInfo table
                                UserInfo::create([
                                    'user_id' => (int) $user->user_id,
                                    'field_id' => (int) $field_id->field_id,
                                    'value' => $value,
                                ]);

                                Log::informationLog("User updated ".$fieldCheck->title ?? $field_id->field_id.' to '.$value.'.',
                                    Auth::user()->user_id);
                            }
                        }
                    }
                }
                // If the user_info is not null (if the user has entered some data for that field)
                else {
                    // If the user has uploaded a new file

                    if ($value['value'] instanceof UploadedFile) {
                        // Get the old file name
                        $oldProfileImage = $user_info->file_path;
                        // Delete the old file from the storage
                        Storage::delete([
                            "public/profile/documents/$oldProfileImage",
                        ]);

                        // Update the record in the UserInfo table with the new file name
                        $updateInfo = UserInfo::findOrFail($user_info->user_info_id);

                        $updateInfo->file_name = $fileName;
                        $updateInfo->file_path = $fileNewName;
                        $updateInfo->save();
                        Log::informationLog("User changed ".$fieldCheck->title." from ".$user_info->file_name.' to '.$fileName.'.',
                            Auth::user()->user_id);
                    }
                    else {
                        if (isset($value['is_file']) && $value['is_file']) {
                            if ($value['file_path'] === NULL && $value['file_name'] === NULL) {
                                //                            dd($value);
                                // Update the record in the UserInfo table with the new file name
                                $updateInfo = UserInfo::findOrFail($user_info->user_info_id);
                                $updateInfo->file_name = NULL;
                                $updateInfo->file_path = NULL;
                                
                                $updateInfo->save();

                                // Get the old file name
                                $oldProfileImage = $user_info->file_path;
                                // Delete the old file from the storage
                                Storage::delete([
                                    "public/profile/documents/$oldProfileImage",
                                ]);
                            }
                        }

                        else {
                            if (!empty($value)) {
                                // If the value is not empty that means the data is updated
                                $updateData = [
                                    'value' => $value['value'] ?? NULL,
                                    'display_value' => $value['label'] ?? NULL,
                                ];

                                if (is_string($value['value'])) {
                                    $updateData['value'] = ucfirst($value['value']);
                                }

                                DB::table('user_infos')
                                    ->where('user_info_id',
                                        $user_info->user_info_id)
                                    ->update($updateData);
                                Log::informationLog('User changed '.$fieldCheck->title.' from '.$user_info->display_value.' to '.isset($value['label']) ?? 'empty'.'.');
                            }
                            // If the value is empty that means the data is deleted or set to null
                            else {
                                DB::table('user_infos')
                                    ->where('user_info_id',
                                        $user_info->user_info_id)
                                    ->update([
                                        'value' => NULL,
                                        'display_value' => NULL,
                                    ]);

                                session([
                                    'toast' => [
                                        'message' => 'Field Category updated!!',
                                        'type' => 'success',
                                    ],
                                ]);
                            }
                        }
                    }
                }
                DB::commit();
            }
        }
        catch (Exception $ex) {
            // Rollback the transaction
            DB::rollback();
            // Log the error
            Log::errorLog($ex->getMessage(), Auth::user()->user_id);
            // Redirect back with an error message
            return redirect()
                ->back()
                ->with([
                    'toast' => [
                        'message' => $ex->getMessage(),
                        'type' => 'danger',
                    ],
                ]);
        }

        $deals = DB::table('deals')
            ->where('user_id', $user->user_id)
            ->where('active', 1)
            ->pluck('user_id', 'bitrix_deal_id')
            ->toArray();

        if (count($deals)) {
            // Set the unsaved_changes flag to 1 so that the user can sync the data
            $user->unsaved_changes = 1;

            $user->save();
            //            return redirect()
            //                ->back()
            //                ->with([
            //                    'toast' => [
            //                        'message' => "You already have deal! You have to synchronize the data!",
            //                        'type' => 'warning',
            //                        'duration' => '10000',
            //                    ],
            //                ]);
        }
    }

    /**
     * Synchronize User Fields
     *
     * This function is responsible for synchronizing user fields with Bitrix
     * CRM. It updates unsaved changes count, dispatches a job to update Bitrix
     * deals, and then redirects back with a success message or an error
     * message if the sync fails.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function syncFields() {
        try {
            // Get the authenticated user
            $user = Auth::user();

            // Reset unsaved changes count
            $user->unsaved_changes = 0;

            // Dispatch a job to update Bitrix deals for the user
            UpdateUserBitrixDeals::dispatch($user);

            // Save the user and check if the operation was successful
            if ($user->save()) {
                // Redirect back with a success message
                return redirect()->back()->with([
                    'toast' => [
                        'message' => 'Synced changes!',
                        'type' => 'success',
                    ],
                ]);
            }
            else {
                // Throw an exception if the save operation fails
                throw new Exception('Save failed');
            }
        }
        catch (Exception $e) {
            // Handle exceptions by redirecting back with an error message
            return redirect()->back()->with([
                'toast' => [
                    'message' => 'Sync failed: '.$e->getMessage(),
                    'type' => 'danger',
                ],
            ]);
        }
    }

    public function updateImage(UpdateImageRequest $request): RedirectResponse {
        //paths
        $pathOriginal = "public/profile/original";
        $pathThumbnail = "public/profile/thumbnail";
        $pathTiny = "public/profile/tiny";

        //inputs
        $file = $request->file('profileImage');
        $fileName = $file->getClientOriginalName();
        $fileExtension = $file->getClientOriginalExtension();

        //formatting
        $thumbnailSize = 150;
        $tinySize = 35;
        $uniqueString = Str::uuid()->toString();
        $currentDate = now()->format('Y-m-d');
        $newFileName = $currentDate.'_'.$uniqueString.'.'.$fileExtension;

        // if the file is not moved to the original folder
        $moved = Storage::putFileAs($pathOriginal, $file, $newFileName);
        if (!$moved) {
            throw new Exception("Failed to move profile image to original folder.");
        }

        //make small images
        try {
            ImageService::resize($thumbnailSize, $file, $pathThumbnail,
                $newFileName);
            ImageService::resize($tinySize, $file, $pathTiny, $newFileName);
        }
        catch (Exception $e) {
            Log::errorLog("Failed to resize file image. Error: ".$e->getMessage(),
                Auth::user()->user_id);
            return to_route('home')->with([
                'toast' => [
                    'message' => 'An error occurred while saving profile image.',
                    'type' => 'danger',
                ],
            ]);
        }

        //save in the database
        try {
            DB::beginTransaction();

            $user = Auth::user();

            //remove old image from folders
            $oldProfileImage = $user->profile_image;
            if ($oldProfileImage !== "profile.jpg") {
                ImageService::remove($oldProfileImage);
            }

            //save profile image
            $fieldName = "UF_CRM_1667336320092";
            ImageService::saveProfileImage($fileName, $newFileName, $fieldName);

            DB::commit();
        }
        catch (Exception $e) {
            DB::rollback();
            Log::errorLog("Failed to update profile image. Error: ".$e->getMessage(),
                Auth::user()->user_id);
            return to_route('home')->with([
                'toast' => [
                    'message' => 'An error occurred while saving profile image.',
                    'type' => 'danger',
                ],
            ]);
        }

        Log::informationLog("Profile image updated.",
            Auth::user()->user_id);
        return to_route('home')->with([
            'toast' => [
                'message' => 'Profile image updated successfully!',
                'type' => 'success',
            ],
        ]);
    }

}

