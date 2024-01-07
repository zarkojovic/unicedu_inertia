<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\FieldItem;
use Illuminate\Database\Seeder;

class AgencySeeder extends Seeder {

    /**
     * Run the database seeds.
     */
    public function run(): void {
        $fieldItems = FieldItem::whereHas('field', function($query) {
            $query->where('title', 'Agency');
        })->select('item_value', 'item_id')->get()->toArray();

        foreach ($fieldItems as $key => $item) {
            $new = new Agency();
            $new->agency_name = $item['item_value'];
            $new->bitrix_agency_id = $item['item_id'];
            $new->save();
        }
    }

}
