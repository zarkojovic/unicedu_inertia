<?php

namespace App\Models;

use App\Services\ImageService;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\hasMany;
use Illuminate\Database\Eloquent\Relations\hasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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

    public static function getUserNotifications() {
        $notifications = auth()
            ->user()
            ->notifications()
            ->select('message', 'url', 'created_at', 'is_read')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Format the created_at field using Carbon
        $notifications->getCollection()->transform(function($notification) {
            $notification->formatted_created_at = Carbon::parse($notification->created_at)
                ->diffForHumans();
            return $notification;
        });

        return $notifications->toArray();
    }

    public function notifications(): hasMany {
        return $this->hasMany(Notification::class, 'user_id', 'user_id');
    }

    //Relationships

    public static function getAgentStudents() {
        // check if user is agent
        if (auth()->user()->role->role_name !== 'agent') {
            return FALSE;
        }
        return User::select('first_name', 'last_name', 'user_id',
            'email', 'phone', 'profile_image')
            ->where('agent_id', auth()->user()->user_id)
            ->get()
            ->toArray();
    }

    public static function updateImage($file, $user_id = NULL) {
        $pathOriginal = "public/profile/original";
        $pathThumbnail = "public/profile/thumbnail";
        $pathTiny = "public/profile/tiny";

        $fileName = $file->getClientOriginalName();
        $fileExtension = $file->getClientOriginalExtension();

        $thumbnailSize = 150;
        $tinySize = 35;
        $uniqueString = Str::uuid()->toString();
        $currentDate = now()->format('Y-m-d');
        $newFileName = $currentDate.'_'.$uniqueString.'.'.$fileExtension;

        $moved = Storage::putFileAs($pathOriginal, $file, $newFileName);
        if (!$moved) {
            throw new Exception("Failed to move profile image to original folder.");
        }

        try {
            ImageService::resize($thumbnailSize, $file, $pathThumbnail,
                $newFileName);
            ImageService::resize($tinySize, $file, $pathTiny, $newFileName);
        }
        catch (Exception $e) {
            Log::errorLog("Failed to resize file image. Error: ".$e->getMessage(),
                Auth::user()->user_id);
            throw new Exception("An error occurred while resizing profile image.");
        }

        DB::beginTransaction();

        try {
            if ($user_id === NULL) {
                $user_id = Auth::user()->user_id;
            }
            $user = User::find($user_id);
            $oldProfileImage = $user->profile_image;

            if ($oldProfileImage !== "profile.jpg") {
                ImageService::remove($oldProfileImage);
            }

            $fieldName = "UF_CRM_1667336320092";
            ImageService::saveProfileImage($fileName, $newFileName, $fieldName);

            DB::commit();
        }
        catch (Exception $e) {
            DB::rollback();
            Log::errorLog("Failed to update profile image. Error: ".$e->getMessage(),
                Auth::user()->user_id);
            throw new Exception("An error occurred while saving profile image.");
        }

        Log::informationLog("Profile image updated.", Auth::user()->user_id);
    }

    public function role(): hasOne {
        return $this->hasOne(Role::class, 'role_id', 'role_id');
    }

    public function info(): hasMany {
        return $this->hasMany(UserInfo::class, 'user_id', 'user_id');
    }

    public function package(): hasOne {
        return $this->hasONe(Package::class, 'package_id', 'package_id');
    }

    public function agency(): hasOne {
        return $this->hasONe(Agency::class, 'agency_id', 'agency_id');
    }

}
