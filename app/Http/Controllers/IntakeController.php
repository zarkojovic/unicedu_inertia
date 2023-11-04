<?php

namespace App\Http\Controllers;

use App\Models\Intake;
use App\Models\Log;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class IntakeController extends Controller {

    public function showIntake() {
        try {
            $intakes = Intake::select('intake_id as id', 'intake_name',
                DB::raw('CASE WHEN active = 1 THEN "active" ELSE "inactive" END AS active'))
                ->paginate(10);

            $intakeSelect = Intake::select(
                'intake_name as label',
                DB::raw('CAST(intake_id AS CHAR) AS value'))->get()->toArray();
            return Inertia::render("Admin/Intake/Show", [
                // Pages data to be displayed
                'data' => $intakes,
                'intakeSelect' => $intakeSelect,
            ]);
        }
        catch (Exception $e) {
            // Log the error
            Log::errorLog("Error showing pages: ".$e->getMessage());

            // Redirect to the home route with an error message
            return redirect()
                ->route('home')
                ->withErrors(['error' => 'An error occurred while fetching pages.']);
        }
    }

    public function changeActiveIntake(Request $request) {
        try {
            $intakeId = (int) $request->input('intake_id');

            // Validate the incoming request data
            $request->validate([
                'intake_id' => 'required|integer',
                // Adjust the validation rules as needed
            ]);

            // Set all intakes' active column to 0 except for the provided intakeId
            Intake::where('intake_id', '!=', $intakeId)
                ->update(['active' => 0]);

            // Set the active column to 1 for the specified intake
            Intake::where('intake_id', $intakeId)->update(['active' => 1]);

            return redirect()
                ->back()
                ->with([
                    'toast' => [
                        'message' => 'Active intake updated!',
                        'type' => 'success',
                    ],
                ]);
        }
        catch (Exception $e) {
            // Handle any exceptions that may occur
            return redirect()
                ->back()
                ->with([
                    'toast' => [
                        'message' => 'An error occurred while setting the active intake.',
                        'type' => 'danger',
                    ],
                ]);
        }
    }

}
