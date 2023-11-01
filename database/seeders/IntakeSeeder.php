<?php

namespace Database\Seeders;

use App\Models\FieldItem;
use App\Models\Intake;
use Illuminate\Database\Seeder;

class IntakeSeeder extends Seeder {

    /**
     * Run the database seeds.
     */
    public function run(): void {
        $fieldItems = FieldItem::whereHas('field', function($query) {
            $query->where('title', 'Intake');
        })->select('item_value', 'item_id')->get()->toArray();

        foreach ($fieldItems as $key => $item) {
            $new = new Intake();
            if ($key == 3) {
                $new->active = TRUE;
            }
            else {
                $new->active = FALSE;
            }
            $new->intake_name = $item['item_value'];
            $new->intake_bitrix_id = $item['item_id'];
            $new->save();
        }
    }

}
