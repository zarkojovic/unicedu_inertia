<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class Page extends Model {

    use HasFactory;

    protected $primaryKey = 'page_id';

    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
        'updated_at' => 'datetime:Y-m-d',
        'is_editable' => 'boolean',
    ];

    protected $fillable = [
        'route',
        'title',
        'icon',
    ];

    public static function getCurrentPagesForSidebar() {
        $user = auth()->user();
        if ($user->role_id === 1) {
            $pages = Page::where('role_id', $user->role_id)
                ->select('page_id', 'route', 'icon', 'title')
                ->get();

            $activePages = DB::table('pages')
                ->where('role_id', $user->role_id)
                ->join('student_package_pages', 'student_package_pages.page_id',
                    'pages.page_id')
                ->where('student_package_pages.package_id', '1')
                ->pluck('pages.page_id')
                ->toArray();
            foreach ($pages as $page) {
                if (in_array($page->page_id, $activePages)) {
                    $page->active = 1;
                }
                else {
                    $page->active = 0;
                }
            }
            $pages = $pages->toArray();
            return $pages;
        }
        else {
            $pages = Page::select('route', 'icon', 'title',
                DB::raw('1 as active'))
                ->where('role_id', $user->role_id)
                ->get()->toArray();
            return $pages;
        }
    }

    public function categories(): BelongsToMany {
        return $this->belongsToMany(FieldCategory::class,
            'field_category_page');
    }

    public function role(): BelongsTo {
        return $this->BelongsTo(Role::class, 'role_id');
    }

}
