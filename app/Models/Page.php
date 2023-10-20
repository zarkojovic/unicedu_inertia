<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        return Page::where('role_id',
            auth()->user()->role_id)
            ->select('route', 'icon', 'title')
            ->get();
    }

    public function categories(): BelongsToMany {
        return $this->belongsToMany(FieldCategory::class,
            'field_category_page');
    }

    public function role(): BelongsTo {
        return $this->BelongsTo(Role::class, 'role_id');
    }

}
