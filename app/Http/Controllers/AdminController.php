<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\Field;
use App\Models\FieldCategory;
use App\Models\Log;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AdminController extends RootController {

    public function showLogsPage(Request $request) {
        // Initialize the main query
        $query = DB::table('logs')
            ->leftJoin('actions', 'actions.action_id', 'logs.action_id')
            ->leftJoin('users', 'users.user_id', 'logs.user_id')
            ->select(
                DB::raw('COALESCE(users.email, "no user") as user_email'),
                'actions.action_name',
                'logs.ip_address',
                'logs.description',
                'logs.created_at'
            );

        // Apply filters based on request parameters
        if ($request->action) {
            $query->where('actions.action_name', $request->action);
        }

        if ($request->user_email) {
            // Case-insensitive search for user email
            $request->user_email = strtolower(trim($request->user_email));
            $query->whereRaw('LOWER(users.email) LIKE ?',
                ["%{$request->user_email}%"]);
        }

        if ($request->begin_date) {
            // Filter logs created after the specified date
            $query->where('logs.created_at', '>=', $request->begin_date);
        }

        if ($request->end_date) {
            // Filter logs created before the specified date
            $query->where('logs.created_at', '<=', $request->end_date);
        }

        // Paginate the query results
        $data = $query->paginate(10);

        // Render the Inertia view with data and filters
        return Inertia::render("Admin/Dashboard", [
            'data' => $data,
            'actions' => Action::select('action_name as label',
                'action_name as value')->get()->toArray(),
            'action' => fn() => $request->action,
            'user_email' => fn() => $request->user_email,
            'begin_date' => fn() => $request->begin_date,
            'end_date' => fn() => $request->end_date,
        ]);
    }

    public function home() {
        $categories = FieldCategory::where("category_name", "<>", "Hidden")
            ->select("field_category_id", "category_name")
            ->get();
        $sortedFields = Field::whereNotIn('field_category_id', [5])
            ->where('is_active', '1')
            ->select("field_id", "field_name", "title", "custom_title",
                "is_required", "order",
                "field_category_id")
            ->orderBy("order", "asc")
            ->get()
            ->toArray();
        $categoriesFields = [];

        foreach ($categories as $category) {
            $categoryFields = array_filter($sortedFields,
                function($field) use ($category) {
                    return $field['field_category_id'] === $category->field_category_id;
                });

            $categoryData = $category->toArray();
            $categoryData['fields'] = array_values($categoryFields);

            $categoriesFields[] = $categoryData;
        }

        return Inertia::render("Admin/Fields", [
            "categories" => $categoriesFields,
        ]);
    }

    // This was from olf project
    public function fieldSelect() {
        $categories = FieldCategory::all();
        $fields = Field::where('is_active', '1')
            ->where('field_category_id', '<>', NULL)
            ->get();
        return view("category_fields",
            ["fields" => $fields, "categories" => $categories]);
    }

    // Fetching all fields that are not in any category
    public function fetchFields(Request $request) {
        $fields = Field::whereNull("field_category_id")
            ->select("field_id", "field_name", "title")
            ->get();
        if (count($fields) > 0) {
            $output = [];
            foreach ($fields as $field) {
                $output[] = [
                    'field_id' => $field->field_id,
                    'title' => $field->title ?? $field->field_name,
                ];
            }

            return $output;
        }

        return ['field_id' => 0, 'title' => "No uncategorized fields found..."];
    }

    public function setFieldCategory(Request $request) {
        try {
            // Get the field ID and new category ID
            $fieldId = $request->input('field_id');
            // If the field ID is 0, then we are adding a new field
            $newCategoryId = $request->input('field_category_id');
            // Get the order of the field
            $order = $request->input('order');

            $record = Field::findOrFail($fieldId);

            $record->field_category_id = $newCategoryId;
            $record->order = $order;
            $record->save();

            $displayName = $record->title != NULL ? $record->title : $record->field_name;
            Log::apiLog("Added '".$displayName."' field to ".$record->category->category_name);
            return redirect()
                ->route("admin_home")
                ->with([
                    'toast' => [
                        'message' => "Successfully added field to category!",
                        'type' => 'success',
                    ],
                ]);
        }
        catch (Exception $ex) {
            // Something went wrong, rollback the transaction
            DB::rollBack();
            Log::errorLog($ex->getMessage(), Auth::user()->user_id);
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

}
