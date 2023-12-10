<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Intake extends Model {

    use HasFactory;

    protected $primaryKey = 'intake_id';

    protected $fillable = [
        'active',
        'intake_name',
    ];

    public static function insertNewIntakes() {
        try {
            // Start a database transaction
            DB::beginTransaction();

            // Fetch current intakes from the database
            $currentIntakes = Intake::pluck('intake_bitrix_id')->toArray();

            // Fetch new intake items from the 'field_items' table based on specific criteria
            $intakeItems = DB::table('field_items')
                ->join('fields', 'fields.field_id', 'field_items.field_id')
                ->where('fields.title', 'Intake')
                ->whereNotIn('field_items.item_id', $currentIntakes)
                ->get()
                ->toArray();

            // Iterate over new intake items and insert them into the 'Intake' table
            foreach ($intakeItems as $newIntake) {
                // Create a new instance of the 'Intake' model
                $insertNewIntake = new Intake();

                // Set properties for the new intake item
                $insertNewIntake->active = FALSE; // Assuming 'active' is a boolean field
                $insertNewIntake->intake_bitrix_id = (int) $newIntake->item_id;
                $insertNewIntake->intake_name = $newIntake->item_value;

                // Save the new intake item to the database
                $insertNewIntake->save();
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
