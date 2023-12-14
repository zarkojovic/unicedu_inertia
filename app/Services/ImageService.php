<?php

namespace App\Services;

use App\Models\Field;
use App\Models\UserInfo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageService
{
    //fields
    private static string $pathOriginal = "public/profile/original";
    private static string $pathThumbnail = "public/profile/thumbnail";
    private static string $pathTiny = "public/profile/tiny";


    //methods
    public static function resize($size, $file, $path, $newFileName)
    {
        $image = Image::make($file)->fit($size, $size, NULL, "top");
        if ($image) {
            Storage::put($path.'/'.$newFileName, (string) $image->encode());
        } else {
            throw new \Exception('Failed to create the image.');
        }
    }

    public static function remove($image)
    {
        $paths = [
            self::$pathOriginal.'/'.$image,
            self::$pathThumbnail.'/'.$image,
            self::$pathTiny.'/'.$image,
        ];

        if (!Storage::delete($paths)) {
            throw new \Exception("Failed to delete image files from folders.");
        }
    }

    public static function saveProfileImage($fileName, $newFileName, $fieldName)
    {
        $user = Auth::user();

        //save profile image in users
        $user->profile_image = $newFileName;
        if (!$user->save()){
            throw new \Exception("Profile image updating not saved.");
        }

        //find the profile image field
        $fieldId = Field::where('field_name', $fieldName)
            ->value('field_id');
        if (!$fieldId){
            throw new \Exception("Field id for field '".$fieldName ."' not found.");
        }

        // Update or insert a row into the UserInfo table
        $userInfoImage = UserInfo::where('user_id', $user->user_id)
            ->where('field_id', $fieldId)
            ->first();

        if ($userInfoImage) {
            // Update the existing record
            $userInfoImage->file_name = $fileName;
            $userInfoImage->file_path = $newFileName;
            if (!$userInfoImage->save()){
                throw new \Exception("Saving profile image in UserInfo table failed.");
            }
        }
        else {
            // Insert a new record
            if (!UserInfo::create([
                'user_id' => $user->user_id,
                'field_id' => $fieldId,
                'file_name' => $fileName,
                'file_path' => $newFileName,
            ])) {
                throw new \Exception("Creating a new profile image record in UserInfo table failed.");
            }
        }
    }
}
