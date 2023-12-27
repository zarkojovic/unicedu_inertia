<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\hasMany;
use Illuminate\Database\Eloquent\Relations\hasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

//use Illuminate\Auth\Passwords\CanResetPassword;

class User extends Authenticatable implements MustVerifyEmail {

    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $primaryKey = 'user_id';

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'profile_image',
        'contact_id',
        'role_id',
        'agent_id',
        'package_id',
        'name',
        'email',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */

    public static function userActiveDeals() {
        $userActiveDeals = Deal::where('user_id', auth()->user()->user_id)
            ->where('active', 1)
            ->get();
        return $userActiveDeals->count();
    }

    public static function userDealsPastFirstStage() {
        return Deal::where('user_id', auth()->user()->user_id)
            ->where('stage_id', '!=', 1)
            ->get()->count();
    }

    //Relationships
    public function role(): hasOne {
        return $this->hasOne(Role::class, 'role_id');
    }

    public function info(): hasMany {
        return $this->hasMany(UserInfo::class, 'user_id');
    }

    public function package(): hasOne {
        return $this->hasONe(Package::class, 'package_id', 'package_id');
    }

}
