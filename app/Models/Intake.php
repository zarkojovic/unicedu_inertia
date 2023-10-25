<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Intake extends Model {

    use HasFactory;

    protected $primaryKey = 'intake_id';

    protected $fillable = [
        'active',
        'intake_name',
    ];

}
