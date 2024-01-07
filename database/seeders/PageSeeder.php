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
                'route' => '/documents',
                'title' => 'Documents',
                'icon' => 'file-type-doc',
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
            [
                'route' => '/admin/intakes',
                'title' => 'Intakes',
                'icon' => 'timeline-event-text',
                'role_id' => '3',
                'is_editable' => FALSE,
            ],
            [
                'route' => '/admin/packages',
                'title' => 'Packages',
                'icon' => 'military-rank',
                'role_id' => '3',
                'is_editable' => FALSE,
            ],
            [
                'route' => '/visa',
                'title' => 'Visa',
                'icon' => 'brand-visa',
                'role_id' => '1',
                'is_editable' => FALSE,
            ],
            [
                'route' => '/accommodation',
                'title' => 'Accommodation',
                'icon' => 'home-check',
                'role_id' => '1',
                'is_editable' => FALSE,
            ],
            [
                'route' => '/agent/profile',
                'title' => 'Agent Profile',
                'icon' => 'user-pentagon',
                'role_id' => '2',
                'is_editable' => FALSE,
            ],
            [
                'route' => '/agent/students',
                'title' => 'My Students',
                'icon' => 'list-details',
                'role_id' => '2',
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
                $ids = ['1', '2'];
                foreach ($ids as $id) {
                    DB::table('field_category_page')->insert([
                        'field_category_id' => $id,
                        'page_id' => $new->page_id,
                    ]);
                }
            }
            if ($page['route'] === '/documents') {
                DB::table('field_category_page')->insert([
                    'field_category_id' => '3',
                    'page_id' => $new->page_id,
                ]);
            }
        }
    }

}
