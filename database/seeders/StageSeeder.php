<?php

namespace Database\Seeders;

use App\Models\FieldItem;
use App\Models\Stage;
use Illuminate\Database\Seeder;

class StageSeeder extends Seeder {

    /**
     * Run the database seeds.
     */
    public function run(): void {
        $fields = FieldItem::whereHas('field', function($query) {
            $query->where('field_name', 'STAGE_ID');
        })->select('item_value', 'item_id')->get()->toArray();

        foreach ($fields as $key => $item) {
            $new = new Stage();

            $new->stage_name = $item['item_value'];
            $new->bitrix_stage_id = $item['item_id'];
            $new->save();
        }
    }

}
