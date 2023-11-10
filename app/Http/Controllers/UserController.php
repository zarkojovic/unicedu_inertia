<?php

namespace App\Http\Controllers;

use App\Jobs\UpdateUserBitrixDeals;
use App\Models\Field;
use App\Models\FieldCategory;
use App\Models\Log;
use App\Models\UserInfo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Intervention\Image\Facades\Image;
use Kafka0238\Crest\Src;

class UserController extends RootController {

    /**
     * Display the specified resource.
     */
    public function show() {
        $user = Auth::user();

        $categoriesWithFields = FieldCategory::getAllCategoriesWithFields('/profile');
        if ($user->role_id === 3) {
            return redirect()->route('admin_home');
        }
        return Inertia::render("Student/Profile", [
            'categoriesWithFields' => $categoriesWithFields,
            "img" => asset("storage/profile/thumbnail/".$user->profile_image),
        ]);
    }

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
        //GET ALL OF THE DATA FROM REQUEST
        $items = $request['formItems'];

        //        GET AUTH-ED USER FOR UPDATING HIS DATA
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

        try {
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

                $user_info = $user_info_array[$key] ?? NULL;
                //                dd($user_info);
                //CHECKING IF THE REQUEST IS FILE
                if ($value['value'] instanceof UploadedFile) {
                    //                GETTING THE INFO FROM FILE
                    $storeFile = $value['value'];

                    $extension = $storeFile->extension();

                    // Check if the file extension is 'pdf'
                    if ($extension !== 'pdf') {
                        if ($field_id->title !== NULL) {
                            $fieldName = $field_id->title;
                        }
                        else {
                            $fieldName = $field_id->field_name;
                        }
                        session([
                            'toast' => [
                                'message' => "'$fieldName' File must be pdf!",
                                'type' => 'danger',
                            ],
                        ]);
                        throw ValidationException::withMessages([
                            'error' => "'$fieldName' File must be pdf!",
                        ]);
                    }

                    if ($storeFile->getSize() > 5 * 1024 * 1024) {
                        session([
                            'toast' => [
                                'message' => "File too big (5mb limit)!",
                                'type' => 'danger',
                            ],
                        ]);
                        // The file is over 8MB (8 * 1024 * 1024 bytes)
                        throw ValidationException::withMessages([
                            'error' => "File too big (5mb limit)!",
                        ]);
                        // Handle the validation error or other actions here
                    }

                    $fileName = $storeFile->getClientOriginalName();
                    // Store the uploaded file
                    $storedPath = $storeFile->store('profile/documents',
                        'public');
                    //                GETTING THE NEW NAME OF FILE
                    $fileNewName = basename($storedPath);
                }
                //IF INFO DOESNT EXIST
                $fieldCheck = Field::findOrFail($field_id->field_id);

                if (!$user_info) {
                    //                IF IT IS A FILE
                    if (isset($value['is_file']) && $value['is_file']) {
                        UserInfo::create([
                            'user_id' => (int) $user->user_id,
                            'field_id' => (int) $field_id->field_id,
                            'file_name' => $fileName,
                            'file_path' => $fileNewName,
                        ]);

                        Log::informationLog("User updated ".$field_id->field_name.'.',
                            Auth::user()->user_id);
                    }
                    else {
                        //                    IF IT'S NOT FILE
                        if (!empty($value) && $value !== 'null' && $value != 0) {
                            if (isset($value['label'])) {
                                UserInfo::create([
                                    'user_id' => (int) $user->user_id,
                                    'field_id' => (int) $field_id->field_id,
                                    'value' => $value['value'],
                                    'display_value' => $value['label'],
                                ]);
                            }
                            else {
                                if (is_string($value['value'])) {
                                    $value = ucfirst($value['value']);
                                }
                                UserInfo::create([
                                    'user_id' => (int) $user->user_id,
                                    'field_id' => (int) $field_id->field_id,
                                    'value' => $value,
                                ]);
                            }
                            Log::informationLog("User updated ".$field_id->field_name.'.',
                                Auth::user()->user_id);
                        }
                    }
                }
                else {
                    // IF ITS AN UPDATING
                    if ($value['value'] instanceof UploadedFile) {
                        #REMOVE OLD IMAGE FROM FOLDERS
                        $oldProfileImage = $user_info->file_path;

                        Storage::delete([
                            "public/profile/documents/$oldProfileImage",
                        ]);

                        // UPDATE INFO
                        $user_info->file_name = $fileName;
                        $user_info->file_path = $fileNewName;
                        $user_info->save();
                    }

                    else {
                        if (!empty($value)) {
                            if ($value['value'] !== NULL) {
                                if (isset($value['label'])) {
                                    DB::table('user_infos')
                                        ->where('user_info_id', '=',
                                            $user_info->user_info_id)
                                        ->update([
                                            'value' => $value['value'],
                                            'display_value' => $value['label'],
                                        ]);
                                }
                                else {
                                    if (is_string($value['value'])) {
                                        $value = ucfirst($value['value']);
                                        DB::table('user_infos')
                                            ->where('user_info_id', '=',
                                                $user_info->user_info_id)
                                            ->update([
                                                'value' => $value,
                                            ]);
                                    }
                                    else {
                                        DB::table('user_infos')
                                            ->where('user_info_id', '=',
                                                $user_info->user_info_id)
                                            ->update([
                                                'value' => $value['value'],
                                            ]);
                                    }
                                }
                            }
                            else {
                                DB::table('user_infos')
                                    ->where('user_info_id', '=',
                                        $user_info->user_info_id)
                                    ->update([
                                        'value' => NULL,
                                        'display_value' => NULL,
                                    ]);
                            }
                        }
                        else {
                            DB::table('user_infos')
                                ->where('user_info_id', '=',
                                    $user_info->user_info_id)
                                ->update([
                                    'value' => NULL,
                                    'display_value' => NULL,
                                ]);
                            session([
                                'toast' => [
                                    'message' => "Field Category updated!!",
                                    'type' => 'success',
                                ],
                            ]);
                        }
                    }
                }
                DB::commit();
            }
        }
        catch (Exception $ex) {
            http_response_code(500);
            Log::errorLog($ex->getMessage(), Auth::user()->user_id);
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

        //        dd($deals);

        //        $deals = Deal::where('user_id', $user->user_id)
        //            ->pluck('user_id', 'bitrix_deal_id')
        //            ->toArray();
        //
        if (count($deals)) {
            $user->unsaved_changes = 1;

            $user->save();
            return redirect()
                ->back()
                ->with([
                    'toast' => [
                        'message' => "You already have deal! You have to synchronize the data!",
                        'type' => 'warning',
                        'duration' => '10000',
                    ],
                ]);

            //            $fields = User::getAllUserFieldsValue();
            //
            //            foreach ($deals as $key => $val) {
            //                // Make API call t*3o create the deal in Bitrix24
            //                $res = CRest::call("crm.deal.update", [
            //                    'ID' => (string) $key,
            //                    'FIELDS' => $fields,
            //                ]);
            //
            //                if ($res['result']) {
            //                    Log::apiLog('Deal '.$key.' successfully updated!');
            //                }
            //                else {
            //                    Log::errorLog('Failed to update deal '.$key);
            //                }
            //            }
        }
    }

    public function syncFields() {
        try {
            $user = Auth::user();

            $user->unsaved_changes = 0;

            UpdateUserBitrixDeals::dispatch($user);

            if ($user->save()) {
                return redirect()->back()->with([
                    'toast' => [
                        'message' => 'Synced changes!',
                        'type' => 'success',
                    ],
                ]);
            }
            else {
                throw new Exception('Save failed');
            }
        }
        catch (Exception $e) {
            return redirect()->back()->with([
                'toast' => [
                    'message' => 'Sync failed: '.$e->getMessage(),
                    'type' => 'danger',
                ],
            ]);
        }
    }

    public function updateImage(Request $request) {
        #INPUTS
        if (!$request->hasFile('profileImage')) {
            return to_route('home')->with([
                'toast' => [
                    'message' => 'No file uploaded.',
                    'type' => 'danger',
                ],
            ]);
        }
        //            return Inertia::render("404");

        $pathOriginal = "public/profile/original";
        $pathThumbnail = "public/profile/thumbnail";
        $pathTiny = "public/profile/tiny";
        $allowedMimeTypes = ['image/jpg', 'image/jpeg', 'image/png'];
        $numberOfMegabytes = 8;
        $kilobyte = 1024; // 2MB in kilobytes
        $errors = [];

        $file = $request->file('profileImage');

        $fileName = $file->getClientOriginalName();
        $tmpName = $file->getPathname(); // tmp_name
        $fileSize = $file->getSize();
        $fileType = $file->getClientMimeType();
        $fileExtension = $file->getClientOriginalExtension();

        #VALIDATE INPUTS
        if (!in_array($fileType, $allowedMimeTypes)) {
            $errors[] = "Allowed file types are jpg, jpeg and png.";
        }

        if ($fileSize > $numberOfMegabytes * pow($kilobyte, 2)) {
            $errors[] = "File size should not exceed 8MB.";
        }

        if (!empty($errors)) {
            $errorMessages = implode('\n',
                $errors); // Concatenate error messages
            Log::errorLog("Bad file for profile image.", Auth::user()->user_id);
            return to_route("home")->with([
                'toast' => [
                    'message' => $errorMessages,
                    'type' => 'danger',
                ],
            ]);
        }

        #QUESTION: DA LI SU OVDE PRISTUPACNE SLIKE? DA LI MOGU DA SE PRIKAZU IZ STORAGEA? MOZDA MORA SOFTLINK...
        #ODGOVOR: MORAO JE SOFTLINK...

        $uniqueString = Str::uuid()->toString();
        $currentDate = now()->format('Y-m-d');
        $newFileName = $currentDate.'_'.$uniqueString.'.'.$fileExtension;

        if (!Storage::exists($pathOriginal)) {
            Log::errorLog("Original folder path not found.",
                Auth::user()->user_id);
            return to_route('home')->with([
                'toast' => [
                    'message' => 'Saving image on the server failed.',
                    'type' => 'danger',
                ],
            ]);
        }
        $moved = Storage::putFileAs($pathOriginal, $file, $newFileName);
        if (!$moved) {
            Log::errorLog("Failed to move profile image to original folder.",
                Auth::user()->user_id);
            return to_route('home')->with([
                'toast' => [
                    'message' => 'Saving image on the server failed.',
                    'type' => 'danger',
                ],
            ]);
        }

        #MAKE SMALL IMAGES
        try {
            #THUMBNAIL
            $size = 150;
            $thumbnail = Image::make($file)->fit($size, $size, NULL, "top");
            Storage::put($pathThumbnail.'/'.$newFileName,
                (string) $thumbnail->encode());

            #TINY
            $size = 35;
            $tinyImage = Image::make($file)->fit($size, $size, NULL, "top");
            Storage::put($pathTiny.'/'.$newFileName,
                (string) $tinyImage->encode());
        }
        catch (Exception $e) {
            report($e);
            Log::errorLog("Failed to resize file image.",
                Auth::user()->user_id);
            return to_route('home')->with([
                'toast' => [
                    'message' => 'An error occurred while saving profile image.',
                    'type' => 'danger',
                ],
            ]);
        }

        #INSERT INTO DATABASE
        try {
            DB::beginTransaction();

            $user = Auth::user();

            #REMOVE OLD IMAGE FROM FOLDERS
            $oldProfileImage = $user->profile_image;
            if ($oldProfileImage !== "profile.jpg") {
                Storage::delete([
                    "{$pathOriginal}/{$oldProfileImage}",
                    "{$pathThumbnail}/{$oldProfileImage}",
                    "{$pathTiny}/{$oldProfileImage}",
                ]);
            }
            $user->profile_image = $newFileName;
            if (!$user->save()) {
                DB::rollback();
                Log::errorLog("Profile image updating not saved.",
                    Auth::user()->user_id);
                return to_route('home')->with([
                    'toast' => [
                        'message' => 'An error occurred while saving profile image.',
                        'type' => 'danger',
                    ],
                ]);
            }

            $fieldId = Field::where('field_name', 'UF_CRM_1667336320092')
                ->value('field_id');
            //                $imageContent = Storage::get($pathOriginal.'/'.$newFileName);

            if (!$fieldId) {
                DB::rollback();
                Log::errorLog("Field id for 'UF_CRM_1667336320092' not found.",
                    Auth::user()->user_id);
                return to_route('home')->with([
                    'toast' => [
                        'message' => 'An error occurred while saving profile image.',
                        'type' => 'danger',
                    ],
                ]);
            }

            // Insert a row into the UserInfo table
            $userInfoImage = UserInfo::where('user_id', $user->user_id)
                ->where('field_id', $fieldId)
                ->first();

            if ($userInfoImage) {
                // Update the existing record
                $userInfoImage->file_name = $fileName;
                $userInfoImage->file_path = $newFileName;
                $userInfoImage->save();
            } // Insert a new record
            else {
                UserInfo::create([
                    'user_id' => $user->user_id,
                    'field_id' => $fieldId,
                    'file_name' => $fileName,
                    'file_path' => $newFileName,
                ]);
            }

            //                DB::rollback();
            //                Log::errorLog("Photo in User Info table not updated.", Auth::user()->user_id);
            //                return redirect()->route('profile')->with(["errors" => ['An error occurred while saving profile image.']]);

            Log::informationLog("Profile image updated.",
                Auth::user()->user_id);
            DB::commit();
        }
        catch (Exception $e) {
            DB::rollback();
            Log::errorLog("Failed to update profile image.",
                Auth::user()->user_id);
            return to_route('home')->with([
                'toast' => [
                    'message' => 'An error occurred while saving profile image.',
                    'type' => 'danger',
                ],
            ]);
        }

        #UF_CRM_1667336320092 - polje za sliku
        #6533 - DEAL ID

        #IF UPDATED IN DATABASE, UPDATE IN BITRIX24
        //        try {
        //            $imageContent = Storage::get($pathOriginal.'/'.$newFileName);
        //
        //            CRest::call("crm.deal.update", [
        //                'id' => '6533',//test deal
        //                'fields' => [
        //                    'UF_CRM_1667336320092' => [
        //                        'fileData' => [
        //                            $newFileName,
        //                            base64_encode($imageContent)
        //                        ]
        //                    ]
        //                ]
        //            ]);
        //        } catch (\Exception $e) {
        //            return "Error: " . $e->getMessage();
        //        }

        return to_route('home')->with([
            'toast' => [
                'message' => 'Profile image updated successfully!',
                'type' => 'success',
            ],
        ]);
    }

}

