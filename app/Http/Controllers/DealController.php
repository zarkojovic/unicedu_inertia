<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Field;
use App\Models\Intake;
use App\Models\Log;
use App\Models\UserInfo;
use CRest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use function Webmozart\Assert\Tests\StaticAnalysis\length;

class DealController extends RootController {

    public function showApplication() {
        try {
            // Fetch all deals from the 'deals' table and select 'deal_id' as 'id'
            $data = Deal::select('deal_id as id', 'intake', 'program')
                ->paginate(10);

            // Return the admin template view with necessary data
            return Inertia::render("Admin/Application/Show", [
                'data' => $data,
                // Actions data to be displayed
            ]);
        }
        catch (Exception $e) {
            // Log the error
            Log::errorLog("Error showing deals: ".$e->getMessage());

            // Redirect to the home route with an error message
            return redirect()
                ->route('home')
                ->withErrors(['error' => 'An error occurred while fetching actions.']);
        }
    }

    public function showUserDeals() {
        $user = auth()->user();
        $applications = Deal::where('user_id', $user->user_id)
            ->where('active', '1')
            ->get()
            ->toArray();

        $intakes = DB::table('user_intake_packages')
            ->where('user_id', $user->user_id)
            ->join('intakes', 'user_intake_packages.intake_id', '=',
                'intakes.intake_id')
            ->select('intakes.intake_name', 'user_intake_packages.package_id',
                'user_intake_packages.user_intake_package_id')
            ->get()->toArray();

        // Create a new array to store the result
        $result = [];

        // Iterate through the $intakePackages array
        foreach ($intakes as $intakePackage) {
            $userIntakePackageId = $intakePackage->user_intake_package_id;

            // Find the corresponding deal in the $deals array based on user_intake_package_id
            $deal = array_filter($applications,
                function($deal) use ($userIntakePackageId) {
                    return $deal['user_intake_package_id'] === $userIntakePackageId;
                });

            // If a deal is found, add it to the result with a "deals" property
            if (!empty($deal)) {
                $intakePackage->deals = $deal;
            }

            // Add the updated item to the result array
            $result[] = $intakePackage;
        }
        //        dd($result);
        return Inertia::render("Student/Applications", [
            'applications' => $result,
        ]);
    }

    public function apply(Request $request) {
        $user = Auth::user();

        try {
            if (count($request->items) > 0) {
                $deal = new Deal();

                $deal->active = TRUE;
                $deal->bitrix_deal_id = rand(1, 1000);
                $deal->user_id = $user->user_id;
                foreach ($request->items as $item) {
                    switch ($item['field_name']) {
                        case 'UF_CRM_1667335624051':
                            if (isset($item['label'])) {
                                $deal->university = $item['label'];
                            }
                            else {
                                $deal->university = $item['value'];
                            }
                            break;
                        case 'UF_CRM_1667335695035':

                            if (isset($item['label'])) {
                                $deal->degree = $item['label'];
                            }
                            else {
                                $deal->degree = $item['value'];
                            }
                            break;
                        case 'UF_CRM_1667335742921':
                            if (isset($item['label'])) {
                                $deal->program = $item['label'];
                            }
                            else {
                                $deal->program = $item['value'];
                            }
                            break;
                    }
                }
                $active_intake = Intake::where('active', '1')->first();
                $deal->intake = $active_intake->intake_name;
                $checkIntakePackage = DB::table('user_intake_packages')
                    ->where('user_id', $user->user_id)
                    ->where('intake_id', $active_intake->intake_id)
                    ->first();

                if ($checkIntakePackage) {
                    $numberOfDealsInIntake = Deal::where('user_intake_package_id',
                        $checkIntakePackage->user_intake_package_id)
                        ->where('active', '1')
                        ->count();

                    if ($numberOfDealsInIntake >= 5) {
                        throw new Exception('You reach the limit of the possible applications!');
                    }
                    $deal->user_intake_package_id = $checkIntakePackage->user_intake_package_id;
                }
                else {
                    $newUserIntake = DB::table('user_intake_packages')
                        ->insertGetId([
                            'user_id' => $user->user_id,
                            'intake_id' => $active_intake->intake_id,
                            'package_id' => $user->package_id,
                        ]);
                    $deal->user_intake_package_id = $newUserIntake;
                }
                if ($deal->save()) {
                    return redirect()
                        ->route('applications')
                        ->with([
                            "toast" => [
                                'message' => "Your application to university has been successfully created.",
                                'type' => 'success',
                            ],

                        ]);
                }
                else {
                    return redirect()
                        ->route('applications')
                        ->with([
                            "toast" => [
                                'message' => "Error occured while saving the application.",
                                'type' => 'danger',
                            ],

                        ]);
                }
            }
        }
        catch (Exception $e) {
            return redirect()
                ->route('applications')
                ->with([
                    "toast" => [
                        'message' => $e->getMessage(),
                        'type' => 'danger',
                    ],

                ]);
        }

        //OLD WAY AND BITRIX

        if (!$user || !$user->email_verified_at) {
            Log::errorLog('Unauthenticated or unverified user tried to apply to university.',
                $user->user_id);
            return redirect()->route("fallback");
        }

        $dealCount = Deal::where('user_id', $user->user_id)
            ->where('active', 1)
            ->count();

        if ($dealCount === 4) {//5 deals
            Log::errorLog('Max number of deals already reached.',
                $user->user_id);
            return redirect()
                ->back()
                ->with(["errors" => ["You have already reached the maximum number applications."]]);
        }

        try {
            $title = "University Application From Platform";
            $contactId = $user->contact_id;
            $pathOriginalImage = "public/profile/original";
            $pathDocuments = "public/profile/documents";

            //GET FROM UNIVERSITY APPLICATION FORM SUBMIT
            $applicationFields = $request->items();

            if (empty($applicationFields)) {
                return redirect()
                    ->route("home")
                    ->with(["errors" => ["You must fill in your information before applying to universities."]]);
            }

            foreach ($applicationFields as $key => $value) {
                if (!$value) {
                    return redirect()
                        ->route("home")
                        ->with(["errors" => ["You must fill in the required information in your application before applying to universities."]]);
                }
            }

            $userInfoFields = UserInfo::where('user_id', $user->user_id)
                ->whereNotNull("value")
                ->pluck("value", "field_id")
                ->toArray(); #ASOCIJATIVNI NIZ

            $userInfoFiles = UserInfo::where('user_id', $user->user_id)
                ->whereNull("value")
                ->whereNotNull("file_path")
                ->pluck("file_path", "field_id")
                ->toArray();

            $userInfoFilesNames = UserInfo::where('user_id', $user->user_id)
                ->whereNull("value")
                ->whereNotNull("file_path")
                ->pluck("file_name", "field_id")
                ->toArray();

            $allUserInfoFieldIds = array_merge(array_keys($userInfoFields),
                array_keys($userInfoFiles));

            //GET REQUIRED FIELDS
            $requiredFields = Field::where("is_required", 1)
                ->where("field_category_id", "!=", 4)
                ->pluck("field_id")
                ->toArray();

            //CHECK REQUIRED FIELDS
            $missing = array_diff($requiredFields, $allUserInfoFieldIds);
            if (!empty($missing)) {
                Log::errorLog('Required fields not filled in.', $user->user_id);
                return redirect()
                    ->route("home")
                    ->with(["errors" => ["You must fill in all required fields before applying to universities."]]);
            }

            $dealFields = [
                'TITLE' => $title,
                'CONTACT_ID' => $contactId,
            ];

            //EXTRACT FIELD NAMES FOR FIELDS FROM USER_INFO TABLE THAT ARE NOT FILES
            $userInfoFieldIds = array_keys($userInfoFields);
            $fieldNames = Field::whereIn('field_id', $userInfoFieldIds)
                ->pluck('field_name', 'field_id')
                ->toArray();

            // Populate $dealFields with the field names and values
            foreach ($userInfoFields as $fieldId => $fieldValue) {
                $fieldName = $fieldNames[$fieldId] ?? NULL;

                if ($fieldName) {
                    $dealFields[$fieldName] = $fieldValue;
                }
            }

            //EXTRACT FIELD NAMES FOR FILES
            $userInfoFileIds = array_keys($userInfoFiles);
            $fieldNames = Field::whereIn('field_id', $userInfoFileIds)
                ->pluck('field_name', 'field_id')
                ->toArray();

            //EXTRACT FIELD NAMES FOR FILES, FILE NAMES AND FILE CONTENTS
            foreach ($userInfoFiles as $fieldId => $fieldFilePath) {
                $fieldName = $fieldNames[$fieldId] ?? NULL;
                $fileName = $userInfoFilesNames[$fieldId] ?? NULL;

                if ($fieldName) {
                    $path = $fieldName === "UF_CRM_1667336320092" ? $pathOriginalImage : $pathDocuments;
                    $fileContent = Storage::get($path.'/'.$fieldFilePath);

                    $dealFields[$fieldName] = [
                        'fileData' => [
                            $fileName,
                            base64_encode($fileContent),
                        ],
                    ];
                }
            }

            //EXTRACT APPLICATION FIELDS NAMES AND THEIR VALUES (FROM DROPDOWNS) AND THEIR OPTION NAMES
            unset($applicationFields['_token']);
            $applicationFieldsValues = [];
            $applicationFieldsOptions = [];

            foreach ($applicationFields as $key => $value) {
                $array = explode("__", $value);
                if (is_array($array) && count($array) > 1) {
                    $applicationFieldsValues[$key] = $array[0];
                    $applicationFieldsOptions[$key] = $array[1];
                }
                else {
                    $applicationFieldsOptions[$key] = $value;
                }
            }

            //MERGE WITH APPLICATION FIELDS
            $dealFields = array_merge($dealFields, $applicationFieldsValues);

            // Make API call to create the deal in Bitrix24
            $result = CRest::call("crm.deal.add", ['FIELDS' => $dealFields]);

            //IF DEAL SUCCESSFULLY ADDED IN BITRIX
            //            if (isset($result['result']) && $result['result'] > 0) {
            if (isset($result['result']) && $result['result'] > 0) {
                Log::apiLog('Deal successfully created in Bitrix24.',
                    $user->user_id);

                // Insert a record into the 'deal' table in your database
                $deal = new Deal();
                $deal->active = TRUE;
                $deal->bitrix_deal_id = $result['result'];
                $deal->user_id = $user->user_id;

                foreach ($applicationFieldsOptions as $fieldName => $fieldValue) {//$dealFields array previously
                    switch ($fieldName) {
                        case 'UF_CRM_1667335624051':
                            $deal->university = $fieldValue;
                            break;
                        case 'UF_CRM_1667335695035':
                            $deal->degree = $fieldValue;
                            break;
                        case 'UF_CRM_1667335742921':
                            $deal->program = $fieldValue;
                            break;
                        case 'UF_CRM_1668001651504':
                            $deal->intake = $fieldValue;
                            break;
                    }
                }

                $deal->save();

                Log::informationLog('Deal inserted into Deals table.',
                    $user->user_id);
                return redirect()
                    ->route('applications')
                    ->with([
                        "success" => "Your application to university has been successfully created.",
                        "showModal" => "false",
                    ]);
                //                return redirect()->back()->with(["success" => "Your application to university has been successfully created.", "showModal" => "false"]);
            }

            // Deal creation failed
            Log::errorLog('Failed to create deal in Bitrix24.', $user->user_id);

            return redirect()
                ->route('applications')
                ->with([
                    "errors" => ["Application to university failed. Please try again later."],
                    "showModal" => "false",
                ]);
        }
        catch (Exception $e) {
            Log::errorLog('Error during application creation: '.$e->getMessage(),
                $user->user_id);
            return redirect()
                ->route('applications')
                ->with([
                    "errors" => ["Application to university failed. Please try again later."],
                    "showModal" => "false",
                ]);
        }
    }

    public function deleteDeal(Request $request) {
        $deal_id = $request->deal_id;

        // Get the authenticated user
        $user = Auth::user();

        // Find the deal with the given ID
        $deal = Deal::where('user_id', $user->user_id)
            ->find($deal_id);

        if (!$deal) {
            Log::errorLog('Tried to remove a deal that doesn\'t exist.',
                $user->user_id);
            return redirect()->back()->with([
                'toast' =>
                    [
                        'message' => 'An error occurred while deleting an application.',
                        'type' => 'danger',
                    ],
            ]);
        }
        //OVDE MOZDA TRY CATCH
        // Retrieve the Bitrix deal ID associated with the deal
        $bitrix_deal_id = $deal->bitrix_deal_id;

        // Make an API call to delete the deal in Bitrix24
        //        $result = CRest::call("crm.deal.delete",
        //            ['ID' => (string) $bitrix_deal_id]);

        // Check if the deal was successfully removed from Bitrix24
        //        if (isset($result['error_description']) && $result['error_description'] === 'Not found') {
        //            Log::apiLog('Deal failed to delete from Bitrix24.', $user->user_id);
        //            return redirect('/applications')->with('error',
        //                'An error occurred while deleting an application.');
        //        }

        // Update the 'active' column to indicate that the deal is inactive (false)
        $deal->active = FALSE;

        if (!$deal->save()) {
            Log::errorLog('Couldn\'t remove the deal from the database.',
                $user->user_id);
            return redirect()->back()->with([
                'toast' =>
                    [
                        'message' => 'An error occurred while deleting an application.',
                        'type' => 'danger',
                    ],
            ]);
        }

        Log::informationLog('Deal set as inactive (active = 0) in the database.',
            $user->user_id);

        return redirect()->back()->with([
            'toast' =>
                [
                    'message' => 'Application removed successfully!',
                    'type' => 'success',
                ],
        ]);
    }

}
