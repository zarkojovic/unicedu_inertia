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
            $pages = Page::select([
                'pages.page_id',
                'pages.route',
                'pages.icon',
                'pages.title',
                DB::raw('IF(student_package_pages.page_id IS NOT NULL, 1, 0) as active'),
            ])
                ->where('pages.role_id', $user->role_id)
                ->leftJoin('student_package_pages',
                    function($join) use ($user) {
                        $join->on('student_package_pages.page_id', '=',
                            'pages.page_id')
                            ->where('student_package_pages.package_id', 1);
                    })
                ->get()
                ->toArray();

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
