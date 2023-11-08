<?php

namespace App\Http\Controllers;

use App\Jobs\DeleteBitrixDeal;
use App\Jobs\sendingBitrixDeal;
use App\Models\Deal;
use App\Models\Field;
use App\Models\Intake;
use App\Models\Log;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use function Webmozart\Assert\Tests\StaticAnalysis\length;

class DealController extends RootController {

    public function showApplication() {
        try {
            $data = DB::table('deals')
                ->join('users', 'users.user_id', 'deals.user_id')
                ->join('user_intake_packages', 'user_intake_packages.user_id',
                    'users.user_id')
                ->select('users.profile_image as Profile Image',
                    'users.first_name as Name',
                    'deals.deal_id as id', 'deals.intake', 'deals.program',
                    'deals.university', 'deals.degree',
                    'user_intake_packages.package_id as Package',
                    'deals.created_at as applied at',
                    DB::raw('CASE WHEN deals.active = 1 THEN "active" ELSE "inactive" END AS active'))
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
            if (count($request->items) > 0) {
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
                    //                    $dealFields = Deal::generateDealObject($user,
                    //                        []);
                    //                    dd($dealFields);

                    sendingBitrixDeal::dispatch($user, $request->items,
                        $deal->deal_id);
                    //                    dd('pozz');
                    //                    // Make API call to create the deal in Bitrix24
                    //                    $result = CRest::call("crm.deal.add",
                    //                        ['FIELDS' => $dealFields]);

                    //IF DEAL SUCCESSFULLY ADDED IN BITRIX
                    //                    if (isset($result['result']) && $result['result'] > 0) {
                    if (1) {
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
                        throw new Exception('There was an error while saving the application!');
                    }
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

        DeleteBitrixDeal::dispatch($bitrix_deal_id, $user->user_id);

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
