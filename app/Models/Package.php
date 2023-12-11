<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Package extends Model {

    use HasFactory;

    protected $primaryKey = 'package_id';

    protected $fillable = [
        'package_name',
    ];

    public static function insertNewPackages() {
        try {
            // Start a database transaction
            DB::beginTransaction();

            // Fetch current intakes from the database
            $currentPackages = Package::pluck('package_bitrix_id')->toArray();

            // Fetch new intake items from the 'field_items' table based on specific criteria
            $packageItems = DB::table('field_items')
                ->join('fields', 'fields.field_id', 'field_items.field_id')
                ->where('fields.title', 'Package')
                ->whereNotIn('field_items.item_id', $currentPackages)
                ->get()
                ->toArray();

            // Iterate over new intake items and insert them into the 'Intake' table
            foreach ($packageItems as $newPackage) {
                // Create a new instance of the 'Intake' model
                $insertNewPackage = new Package();

                // Set properties for the new intake item
                $insertNewPackage->package_bitrix_id = (int) $newPackage->item_id;
                $insertNewPackage->package_name = $newPackage->item_value;

                // Save the new intake item to the database
                $insertNewPackage->save();
            }

            // Commit the database transaction
            DB::commit();
            // Additional code or transitions can be added here if needed

        }
        catch (Exception $e) {
            // Rollback the database transaction in case of an error
            DB::rollback();

            Log::errorLog($e->getMessage());
            throw $e;
        }
    }

}
