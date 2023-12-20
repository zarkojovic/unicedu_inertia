<?php

namespace App\Http\Controllers;

use App\Jobs\DeleteBitrixDeal;
use App\Jobs\sendingBitrixDeal;
use App\Models\Deal;
use App\Models\Field;
use App\Models\Intake;
use App\Models\Log;
use App\Models\Package;
use App\Models\Stage;
use App\Models\User;
use CRest;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use function Webmozart\Assert\Tests\StaticAnalysis\length;

class DealController extends RootController {

    public function showApplication(Request $request) {
        try {
            $data = DB::table('deals')
                ->join('users', 'users.user_id', 'deals.user_id')
                ->join('user_intake_packages', 'user_intake_packages.user_id',
                    'users.user_id')
                ->join('stages', 'stages.stage_id', 'deals.stage_id')
                ->select('users.profile_image as Profile Image',
                    'users.email as Email',
                    'deals.deal_id as id', 'deals.intake', 'deals.program',
                    'deals.university', 'deals.degree',
                    'stages.stage_name as Stage',
                    'user_intake_packages.package_id as Package',
                    'deals.created_at as applied at',
                    DB::raw('CASE WHEN deals.active = 1 THEN "active" ELSE "inactive" END AS active'));
            if ($request->intake) {
                $data = $data->where('deals.intake', $request->intake);
            }
            if ($request->stage) {
                $data = $data->where('deals.stage_id', $request->stage);
            }
            if ($request->package) {
                $data = $data->where('user_intake_packages.package_id',
                    $request->package);
            }
            if ($request->university) {
                $data = $data->where('deals.university', $request->university);
            }
            if ($request->degree) {
                $data = $data->where('deals.degree', $request->degree);
            }
            if ($request->begin_date) {
                $data = $data->where('deals.created_at', '>=',
                    $request->begin_date);
            }
            if ($request->end_date) {
                $data = $data->where('deals.created_at', '<=',
                    $request->end_date);
            }
            if ($request->userInfo) {
                $userInfo = strtolower(trim($request->userInfo));
                $data->whereRaw('LOWER(users.first_name) like ?',
                    ['%'.$userInfo.'%'])
                    ->orWhereRaw('LOWER(users.last_name) like ?',
                        ['%'.$userInfo.'%'])
                    ->orWhereRaw('LOWER(users.email) like ?',
                        ['%'.$userInfo.'%'])
                    ->orWhereRaw('LOWER(users.phone) like ?',
                        ['%'.$userInfo.'%']);
            }
            if ($request->program) {
                $program = strtolower(trim($request->program));
                $data->whereRaw('LOWER(deals.program) like ?',
                    ['%'.$program.'%']);
            }
            if ($request->has('active')) {
                $isActive = filter_var($request->active,
                    FILTER_VALIDATE_BOOLEAN);
                $data = $data->where('deals.active', $isActive);
            }
            $data = $data->paginate(10);

            // Return the admin template view with necessary data
            return Inertia::render("Admin/Application/Show", [
                'data' => $data,
                'dealIntakes' => fn() => Intake::select('intake_name as label',
                    'intake_name as value')->get()->toArray(),
                'dealStages' => fn() => Stage::select('stage_name as label',
                    DB::raw('CAST(stage_id AS CHAR) AS value'))
                    ->get()
                    ->toArray(),
                'dealPackages' => fn(
                ) => Package::select('package_name as label',
                    DB::raw('CAST(package_id AS CHAR) AS value'))
                    ->get()
                    ->toArray(),
                'dealUniversities' => fn() => DB::table('field_items')
                    ->join('fields', 'fields.field_id',
                        'field_items.field_id')
                    ->where('fields.title', 'University name')
                    ->select('field_items.item_value as label',
                        'field_items.item_value as value')
                    ->get()->toArray(),
                'dealDegrees' => fn() => DB::table('field_items')
                    ->join('fields', 'fields.field_id',
                        'field_items.field_id')
                    ->where('fields.title', 'Degree')
                    ->select('field_items.item_value as label',
                        'field_items.item_value as value')
                    ->get()->toArray(),
                'activeFields' => fn() => [
                    [
                        'label' => 'Active',
                        'value' => '1',
                    ],
                    [
                        'label' => 'Inactive',
                        'value' => '0',
                    ],
                ],
                'intake' => fn() => $request->intake,
                'stage' => fn() => $request->stage,
                'package' => fn() => $request->package,
                'university' => fn() => $request->university,
                'degree' => fn() => $request->degree,
                'userInfo' => fn() => $request->userInfo,
                'program' => fn() => $request->program,
                'begin_date' => fn() => $request->begin_date,
                'end_date' => fn() => $request->end_date,
                'active' => fn() => $request->active,
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

    public function showUserDeals(Request $request) {
        $user = auth()->user();

        $applications = DB::table('deals')
            ->join('stages', 'stages.stage_id', 'deals.stage_id')
            ->where('deals.user_id', $user->user_id)
            ->where('deals.active', '1')
            ->select('deals.*', 'stages.stage_name')
            ->get()
            ->toArray();

        $applications = json_decode(json_encode($applications), TRUE);

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
            'isModalOpen' => $request->isModalOpen === '1',
        ]);
    }

    public function apply(Request $request) {
        $user = Auth::user();

        try {
            // Check if the user's profile image needs to be updated
            if ($user->profile_image === 'profile.jpg') {
                throw new Exception('You have to update your image!');
            }

            $missing = Field::checkRequiredFields($user);

            if (!empty($missing)) {
                Log::errorLog('Required fields not filled in.', $user->user_id);
                throw new Exception('You must fill in all required fields before applying to universities.');
            }

            // Create a new university application deal if there are items in the request
            if (count($request->items)) {
                $deal = new Deal();

                $deal->active = TRUE;
                $deal->bitrix_deal_id = rand(1, 1000);
                $deal->user_id = $user->user_id;

                // Process request items
                foreach ($request->items as $item) {
                    switch ($item['field_name']) {
                        case 'UF_CRM_1667335624051':
                            $deal->university = isset($item['label']) ? $item['label'] : $item['value'];
                            break;
                        case 'UF_CRM_1667335695035':
                            $deal->degree = isset($item['label']) ? $item['label'] : $item['value'];
                            break;
                        case 'UF_CRM_1667335742921':
                            $deal->program = isset($item['label']) ? $item['label'] : $item['value'];
                            break;
                    }
                }

                $active_intake = Intake::where('active', '1')->first();
                $deal->intake = $active_intake->intake_name;

                // Check for an existing intake package
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
                        throw new Exception('You have reached the limit of possible applications.');
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
                    sendingBitrixDeal::dispatch($user, $request->items,
                        $deal->deal_id);
                    //IF DEAL SUCCESSFULLY ADDED IN BITRIX
                    //                    if (isset($result['result']) && $result['result'] > 0) {

                    return redirect()
                        ->route('applications')
                        ->with([
                            'toast' => [
                                'message' => 'Your application to the university has been successfully created.',
                                'type' => 'success',
                            ],
                        ]);
                }
                else {
                    throw new Exception('An error occurred while saving the application.');
                }
            }
        }
        catch (Exception $e) {
            return redirect()
                ->route('applications')
                ->with([
                    'toast' => [
                        'message' => $e->getMessage(),
                        'type' => 'danger',
                    ],
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

        if ($deal->stage_id !== 1) {
            return redirect()->back()->with([
                'toast' =>
                    [
                        'message' => 'Application past first stage can\'t be deleted!',
                        'type' => 'danger',
                    ],
            ]);
        }

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
        // Retrieve the Bitrix deal ID associated with the deal
        $bitrix_deal_id = $deal->bitrix_deal_id;

        DeleteBitrixDeal::dispatch($bitrix_deal_id, $user->user_id);

        // Update the 'active' column to indicate that the deal is inactive (false)
        $deal->active = FALSE;

        if (User::userActiveDeals()) {
            $user->unsaved_changes = 0;
            $user->save();
        }

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

        Log::informationLog('Deal #'.$deal->deal_id.' set as inactive(deleted) in the database.',
            $user->user_id);

        return redirect()->back()->with([
            'toast' =>
                [
                    'message' => 'Application removed successfully!',
                    'type' => 'success',
                ],
        ]);
    }

    public function editApplication(string $id) {
        $dealInfo = DB::table('deals')
            ->join('users', 'deals.user_id', 'users.user_id')
            ->join('stages', 'stages.stage_id', 'deals.stage_id')
            ->where('deals.deal_id', $id)
            ->select('deals.deal_id as id', 'deals.program', 'deals.degree',
                'deals.intake', 'deals.university', 'deals.stage_id',
                'deals.user_id', 'deals.created_at', 'deals.active',
                'users.first_name',
                'users.last_name', 'stages.stage_name')
            ->first();

        $stages = Stage::select('stage_name as label',
            DB::raw('CAST(stage_id AS CHAR) AS value'))->get()->toArray();

        return Inertia::render("Admin/Application/Edit",
            [
                'dealInfo' => $dealInfo,
                'stages' => $stages,
            ]);
    }

    public function changeDealStage(Request $request) {
        try {
            // Find the deal to update or throw an exception if not found
            $dealToUpdate = Deal::where('deal_id', $request->deal_id)
                ->where('active', 1)
                ->firstOrFail();

            // Begin a database transaction using transaction scope
            DB::transaction(function() use ($dealToUpdate, $request) {
                // Update deal information
                $dealToUpdate->stage_id = $request->stage_id;

                // Save the changes
                if ($dealToUpdate->save()) {
                    // Retrieve the stage key from the Stage model
                    $stageKey = Stage::findOrFail($request->stage_id);

                    // Make API call to update the deal in Bitrix24
                    CRest::call("crm.deal.update", [
                        'id' => (string) $dealToUpdate->bitrix_deal_id,
                        'fields' => [
                            'STAGE_ID' => $stageKey->bitrix_stage_id,
                        ],
                    ]);

                    // Commit the transaction if successful
                    DB::commit();
                }
                else {
                    // Rollback the transaction if saving fails
                    DB::rollBack();

                    return redirect()->back()->with([
                        'toast' => [
                            'type' => 'danger',
                            'message' => 'Error has occurred while updating!',
                        ],
                    ]);
                }
            });

            return redirect()->back()->with([
                'toast' => [
                    'type' => 'success',
                    'message' => 'You successfully changed the stage of the deal!',
                ],
            ]);
        }
        catch (ModelNotFoundException $exception) {
            // Handle the case when the deal is not found or not active
            return redirect()->back()->with([
                'toast' => [
                    'type' => 'danger',
                    'message' => 'Deal not found or not editable!',
                ],
            ]);
        }
        catch (Exception $exception) {
            // Handle other exceptions
            return redirect()->back()->with([
                'toast' => [
                    'type' => 'danger',
                    'message' => 'An unexpected error occurred!',
                ],
            ]);
        }
    }

}
