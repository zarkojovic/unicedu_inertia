<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model {

    use HasFactory;

    protected $primaryKey = 'notification_id';

    protected $fillable = [
        'user_id',
        'message',
        'is_read',
        'url',
    ];

    public static function createNotification($user_id, $message, $url = NULL) {
        $notification = new Notification();
        $notification->user_id = $user_id;
        $notification->message = $message;
        $notification->url = $url != NULL ? env('APP_URL').$url : $url;
        return $notification->save();
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

}
