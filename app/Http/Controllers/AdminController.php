<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\FieldCategory;
use App\Models\Log;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AdminController extends RootController {

    public function show() {
        return Inertia::render("Admin/Dashboard");
    }

    public function home() {
        $categories = FieldCategory::where("category_name", "<>", "Hidden")
            ->select("field_category_id", "category_name")
            ->get();
        $sortedFields = Field::whereNotIn('field_category_id', [5])
            ->where('is_active', '1')
            ->select("field_id", "field_name", "title", "is_required", "order",
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

    public function fieldSelect() {
        $categories = FieldCategory::all();
        $fields = Field::where('is_active', '1')
            ->where('field_category_id', '<>', NULL)
            ->get();
        return view("category_fields",
            ["fields" => $fields, "categories" => $categories]);
    }

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
            $fieldId = $request->input('field_id');
            $newCategoryId = $request->input('field_category_id');
            $order = $request->input('order');

            $record = Field::findOrFail($fieldId);

            $record->field_category_id = $newCategoryId;
            $record->order = $order;
            $record->save();
            $displayName = $record->title != NULL ? $record->title : $record->field_name;
            Log::apiLog("Added '".$displayName."' field to ".$record->category->category_name);
            return redirect()
                ->back()
                ->with([
                    'toast' => [
                        'message' => "Successfully added field to category!",
                        'type' => 'success',
                    ],
                ]);
        }
        catch (Exception $ex) {
            http_response_code(500);
            Log::errorLog($ex->getMessage(), Auth::user()->user_id);
            return redirect()
                ->back()
                ->with([
                    'toast' => [
                        'message' => "An error occured on the server.",
                        'type' => 'danger',
                    ],
                ]);
        }
    }

}
