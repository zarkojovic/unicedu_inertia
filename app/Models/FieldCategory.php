<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongsToMany;
use Illuminate\Support\Facades\DB;

class FieldCategory extends Model {

    use HasFactory;

    protected $primaryKey = 'field_category_id';

    protected $fillable = [
        'category_name',
    ];

    public static function getAllDealFields() {
        $fields = DB::table('fields')
            ->where('fields.field_category_id', '4')
            ->select(
                'fields.field_id',
                'fields.title',
                'fields.field_name',
                'fields.is_required',
                'fields.type',
                'fields.order',
                'fields.field_category_id'
            )
            ->get()->toArray();

        for ($i = 0; $i < count($fields); $i++) {
            if ($fields[$i]->type == 'enumeration') {
                $FieldItems = FieldItem::where('field_id',
                    $fields[$i]->field_id)
                    ->select('item_value as label', 'item_id as value')
                    ->get()
                    ->toArray();
                $fields[$i]->items = $FieldItems;
            }
        }
        return $fields;
    }

    public static function getAllCategoriesWithFields($pageName): array {
        function filterObjectsByFieldCategoryId(
            $arrayOfObjects,
            $categoryId
        ): array {
            $filteredObjects = array_filter($arrayOfObjects,
                function($object) use ($categoryId) {
                    return isset($object->field_category_id) && $object->field_category_id === $categoryId;
                });

            return array_values($filteredObjects);
        }

        $categories = DB::table('field_categories')
            ->join('field_category_page',
                'field_category_page.field_category_id', '=',
                'field_category_page.field_category_id')
            ->join('pages', 'field_category_page.page_id', '=', 'pages.page_id')
            ->where('pages.route', '=', $pageName)
            ->where('field_categories.is_visible', 1)
            ->select('field_categories.category_name',
                'field_categories.field_category_id')
            ->distinct()
            ->get()
            ->toArray();

        $catId = [];
        foreach ($categories as $object) {
            if (isset($object->field_category_id)) {
                $catId[] = $object->field_category_id;
            }
        }

        $fields = DB::table('fields')
            ->leftJoin('user_infos', function($join) {
                $join->on('fields.field_id', '=', 'user_infos.field_id')
                    ->where('user_infos.user_id', auth()->user()->user_id);
            })
            ->whereIn('fields.field_category_id', $catId)
            ->where('fields.is_active', 1)
            ->select(
                'fields.field_id',
                'fields.title',
                'fields.field_name',
                'fields.is_required',
                'fields.type',
                'fields.order',
                'fields.field_category_id',
                DB::raw('COALESCE(user_infos.value, "") as value'),
                DB::raw('COALESCE(user_infos.display_value, "") as display_value'),
                DB::raw('COALESCE(user_infos.file_name, "") as file_name'),
                DB::raw('COALESCE(user_infos.file_path, "") as file_path')
            )
            ->get()->toArray();

        for ($i = 0; $i < count($fields); $i++) {
            if ($fields[$i]->type == 'enumeration') {
                $FieldItems = FieldItem::where('field_id',
                    $fields[$i]->field_id)
                    ->select('item_value as label', 'item_id as value')
                    ->get()
                    ->toArray();
                $fields[$i]->items = $FieldItems;
            }
        }

        for ($i = 0; $i < count($categories); $i++) {
            $fieldsWithThisCategories = filterObjectsByFieldCategoryId($fields,
                $categories[$i]->field_category_id);
            $categories[$i]->fields = $fieldsWithThisCategories;
        }

        return $categories;
    }

    public function pages(): belongsToMany {
        return $this->belongsToMany(UserInfo::class, 'user_id');
    }

}
