<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageSeeder extends Seeder {

    /**
     * Run the database seeds.
     */
    public function run(): void {
        $package_names = ['bronze', 'silver', 'gold', 'platinum'];

        foreach ($package_names as $package_name) {
            $package = Package::create([
                'package_name' => $package_name,
            ]);
        }

        $bronze_package_pages = ['/profile', '/applications'];

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
