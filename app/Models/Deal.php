<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

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
        'stage_id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    public static function generateDealObject(
        $user_id,
        $items,
        $contact_id,
        $package_name
    ) {
        //IF IT'S SAVED IN DATABASE PROCEED TO SAVE THAT IN BITRIX
        $pathOriginalImage = "public/profile/original";
        $pathDocuments = "public/profile/documents";
        $dealFields = [
            'CONTACT_ID' => $contact_id,
        ];
        $userInfoFiles = UserInfo::where('user_id', $user_id)
            ->whereNull("value")
            ->whereNotNull("file_path")
            ->pluck("file_path", "field_id")
            ->toArray();

        $userInfoFields = UserInfo::where('user_id', $user_id)
            //            ->whereNotNull('value')
            ->orWhere(function($query) {
                $query->whereNull('value')->whereNotNull('file_path');
            })
            ->pluck('value', 'field_id')
            ->toArray();

        //EXTRACT FIELD NAMES FOR FIELDS FROM USER_INFO TABLE THAT ARE NOT FILES
        $userInfoFieldIds = array_keys($userInfoFields);

        $fieldNames = Field::whereIn('field_id', $userInfoFieldIds)
            ->pluck('field_name', 'field_id')
            ->toArray();

        $userInfoFilesNames = UserInfo::where('user_id',
            $user_id)
            ->whereNull("value")
            ->whereNotNull("file_path")
            ->pluck("file_name", "field_id")
            ->toArray();

        // Populate $dealFields with the field names and values
        foreach ($userInfoFields as $fieldId => $fieldValue) {
            $fieldName = $fieldNames[$fieldId] ?? NULL;

            if ($fieldName) {
                $dealFields[$fieldName] = $fieldValue;
            }
        }

        //EXTRACT FIELD NAMES FOR FILES
        $userInfoFileIds = array_keys($userInfoFiles);
        $fieldNames = Field::whereIn('field_id', $userInfoFileIds)
            ->pluck('field_name', 'field_id')
            ->toArray();

        //EXTRACT FIELD NAMES FOR FILES, FILE NAMES AND FILE CONTENTS
        foreach ($userInfoFiles as $fieldId => $fieldFilePath) {
            $fieldName = $fieldNames[$fieldId] ?? NULL;
            $fileName = $userInfoFilesNames[$fieldId] ?? NULL;

            if ($fieldName) {
                $profileImageField = "UF_CRM_1667336320092";
                $path = $fieldName === $profileImageField ? $pathOriginalImage : $pathDocuments;
                $fileContent = Storage::get($path.'/'.$fieldFilePath);

                $dealFields[$fieldName] = [
                    'fileData' => [
                        $fileName,
                        base64_encode($fileContent),
                    ],
                ];
            }
        }

        //EXTRACT APPLICATION FIELDS NAMES AND THEIR VALUES (FROM DROPDOWNS) AND THEIR OPTION NAMES

        $intake = Field::where('title', 'Intake')->first();
        $intakeBitrixId = Intake::where('active', '1')->first();

        $dealFields[$intake->field_name] = $intakeBitrixId->intake_bitrix_id;

        foreach ($items as $value) {
            $dealFields[$value['field_name']] = $value['value'] === NULL ? '' : $value['value'];
        }
        // PACKAGE FIELD ADDING
        $dealFields['UF_CRM_1667333858787'] = $package_name;

        $keys = array_keys($dealFields);
        $selectedFields = Field::whereIn('field_name', $keys)
            ->pluck('is_contact_field', 'field_name')
            ->toArray();
        $contactArray = [];
        $dealArray = [];
        //        dd($dealFields);
        foreach ($dealFields as $key => $value) {
            if (isset($selectedFields[$key]) && $selectedFields[$key] == 0) {
                $dealArray[$key] = $value === NULL ? '' : $value;
            }
            elseif (isset($selectedFields[$key]) && $selectedFields[$key] == 1) {
                $contactArray[$key] = $value === NULL ? '' : $value;
            }
        }

        return [$dealArray, $contactArray];
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

}
