<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Field extends Model {

    use HasFactory;

    protected $primaryKey = 'field_id';

    protected $fillable = [
        'field_name',
        'type',
        'title',
        'status',
        'is_active',
        'is_required',
        'field',
    ];

    public static function checkRequiredFields($user) {
        // Retrieve user information fields and required fields
        $userInfoFields = UserInfo::where('user_id', $user->user_id)
            ->whereNotNull('value')
            ->orWhere(function($query) {
                $query->whereNull('value')->whereNotNull('file_path');
            })
            ->pluck('value', 'field_id')
            ->toArray();

        $requiredFields = Field::where('is_required', 1)
            ->where('field_category_id', '!=', 4)
            ->pluck('field_id')
            ->toArray();

        // Check for missing required fields
        return array_diff($requiredFields, array_keys($userInfoFields));
    }

    public function category(): BelongsTo {
        return $this->belongsTo(FieldCategory::class, 'field_category_id');
    }

    public function items(): HasMany {
        return $this->hasMany(FieldItem::class, 'field_id', 'field_id');
    }

}
