<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldItem extends Model {

    use HasFactory;

    protected $primaryKey = 'field_item_id';

    protected $fillable = [
        'item_value',
        'item_id',
        'field_id',
        'is_active',
    ];

    public function field(): BelongsTo {
        return $this->belongsTo(Field::class, 'field_id', 'field_id');
    }

}
