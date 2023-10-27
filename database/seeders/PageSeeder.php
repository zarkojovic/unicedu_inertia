<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PageSeeder extends Seeder {

    /**
     * Run the database seeds.
     */
    public function run(): void {
        $pages = [
            [
                'route' => '/admin/dashboard',
                'title' => 'Dashboard',
                'icon' => 'dashboard',
                'role_id' => '3',
                'is_editable' => FALSE,
            ],
            [
                'route' => '/profile',
                'title' => 'My Information',
                'icon' => 'user',
                'role_id' => '1',
                'is_editable' => FALSE,
            ],
            [
                'route' => '/admin/pages',
                'title' => 'Pages',
                'icon' => 'wallpaper',
                'role_id' => '3',
                'is_editable' => FALSE,
            ],
            [
                'route' => '/admin/categories',
                'title' => 'Categories',
                'icon' => 'box-multiple',
                'role_id' => '3',
                'is_editable' => FALSE,
            ],
            [
                'route' => '/admin/fields',
                'title' => 'Fields',
                'icon' => 'row-insert-top',
                'role_id' => '3',
                'is_editable' => FALSE,
            ],
            [
                'route' => '/admin/users',
                'title' => 'Users',
                'icon' => 'users',
                'role_id' => '3',
                'is_editable' => FALSE,
            ],
            [
                'route' => '/applications',
                'title' => 'Applications',
                'icon' => 'school',
                'role_id' => '1',
                'is_editable' => FALSE,
            ],
            [
                'route' => '/admin/applications',
                'title' => 'Applications',
                'icon' => 'api-app',
                'role_id' => '3',
                'is_editable' => FALSE,
            ],
        ];

        foreach ($pages as $page) {
            $new = new Page();
            $new->route = $page['route'];
            $new->title = $page['title'];
            $new->icon = $page['icon'];
            $new->is_editable = $page['is_editable'];
            $new->role_id = $page['role_id'];

            $new->save();

            if ($page['route'] == '/profile') {
                $ids = ['1', '2', '3'];
                foreach ($ids as $id) {
                    DB::table('field_category_page')->insert([
                        'field_category_id' => $id,
                        'page_id' => $new->page_id,
                    ]);
                }
            }
        }
    }

}
