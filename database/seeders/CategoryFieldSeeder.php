<?php

namespace Database\Seeders;

use App\Models\FieldCategory;
use Illuminate\Database\Seeder;

class CategoryFieldSeeder extends Seeder {

    /**
     * Run the database seeds.
     */
    public function run(): void {
        $category_names = [
            'Personal Information',
            'Address',
            'Documents',
            'Deals',
            'Hidden',
            'Unique Deal Fields',
        ];

        foreach ($category_names as $category_name) {
            if ($category_name == 'Hidden' || $category_name == 'Deals') {
                $category = new FieldCategory();
                $category->category_name = $category_name;
                $category->is_visible = FALSE;
                $category->save();
            }
            else {
                if ($category_name == 'Unique Deal Fields') {
                    $category = new FieldCategory();
                    $category->category_name = $category_name;
                    $category->is_visible = TRUE;
                    $category->is_deal_category = TRUE;
                    $category->save();
                }
                else {
                    $category = new FieldCategory();
                    $category->category_name = $category_name;
                    $category->is_visible = TRUE;
                    $category->save();
                }
            }
        }
    }

}
