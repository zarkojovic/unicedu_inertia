<?php

namespace Database\Seeders;

use App\Models\FieldItem;
use App\Models\Package;
use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageSeeder extends Seeder {

    /**
     * Run the database seeds.
     */
    public function run(): void {
        $fieldItems = FieldItem::whereHas('field', function($query) {
            $query->where('title', 'Package');
        })->select('item_value', 'item_id')->get()->toArray();

        foreach ($fieldItems as $key => $item) {
            $new = new Package();
            $new->package_name = $item['item_value'];
            $new->package_bitrix_id = $item['item_id'];
            $new->save();
        }

        $bronze_package_pages = ['/profile', '/applications', '/documents'];

        $page_ids = Page::whereIn('route', $bronze_package_pages)
            ->pluck('page_id')->toArray();

        foreach ($page_ids as $id) {
            $data = [
                "page_id" => $id,
                "package_id" => 1,
            ];
            DB::table('student_package_pages')->insert($data);
        }
    }

}
