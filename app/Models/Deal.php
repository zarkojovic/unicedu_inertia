<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deal extends Model {

    use HasFactory;

    protected $primaryKey = 'deal_id';

    protected $fillable = [
        'bitrix_deal_id',
        'university',
        'user_id',
        'degree',
        'program',
        'intake',
        'date',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

}
