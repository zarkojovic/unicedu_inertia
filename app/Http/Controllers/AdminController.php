<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\FieldCategory;
use App\Models\Log;
use App\Models\Page;
use Illuminate\Http\Request;
use Inertia\Inertia;
use function Termwind\render;

class AdminController extends RootController
{

    public function show() {
        return Inertia::render("Admin/Dashboard");
    }
    public function home()
    {
        $categories = FieldCategory::where("category_name", "<>", "Hidden")->get();
        $sortedFields = Field::whereNotIn('field_category_id', [5])->where('is_active', '1')->orderBy("order", "asc")->get()->toArray();
        $categoriesFields = [];

        foreach ($categories as $category) {
            $categoryFields = array_filter($sortedFields, function ($field) use ($category) {
                return $field['field_category_id'] === $category->field_category_id;
            });

            $categoryData = $category->toArray();
            $categoryData['fields'] = array_values($categoryFields);

            $categoriesFields[] = $categoryData;
        }

        return Inertia::render("Admin/Fields", [
//            "fields" => $sortedFields,
            "categories" => $categoriesFields
        ]);
    }

    public function fieldSelect()
    {
        $categories = FieldCategory::all();
        $fields = Field::where('is_active', '1')->where('field_category_id', '<>', NULL)->get();
        return view("category_fields", ["fields" => $fields, "categories" => $categories]);
    }

    public function search(Request $request)
    {
        if ($request->ajax()) {
            $searchQuery = $request->input('search');

            $rows = Field::whereNull("field_category_id")->where(function ($query) use ($searchQuery) {
                $query->where('title', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('field_name', 'LIKE', '%' . $searchQuery . '%');
            })->get();

            if (count($rows) > 0) {
                $output = "";
                foreach ($rows as $row) {
                    $text = $row->title ?? $row->field_name;
                    $output .= "<option value='$row->field_id'>$text</option>";
                }
                return $output;
            }

            return "<option value='0'>No results found...</option>";
        }
    }

    public function setFieldCategory(Request $request)
    {
        try {
            $fieldId = $request->input('field_id');
            $newCategoryId = $request->input('field_category_id');
            $order = $request->input('order');

            $record = Field::findOrFail($fieldId);

            $record->field_category_id = $newCategoryId;
            $record->order = $order;
            $record->save();
            $displayName = $record->title != null ? $record->title : $record->field_name;
            Log::apiLog("Added '" . $displayName . "' field to " . $record->category->category_name);
            return response()->json(['message' => 'Record updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating record'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
