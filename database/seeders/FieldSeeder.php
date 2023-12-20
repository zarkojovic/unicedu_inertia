<?php

namespace Database\Seeders;

use App\Models\Field;
use CRest;
use Illuminate\Database\Seeder;

class FieldSeeder extends Seeder {

    /**
     * Run the database seeds.
     */
    public function run(): void {
        // Initialize $i and $hiddenFields as global variables
        global $i, $hiddenFields;
        $i = 0;
        $hiddenFields = [];

        // Function to process and store CRM fields in the database
        function processAndStoreFields($res, $isContactField = FALSE): void {
            // Initialize an array to store processed objects.
            $arrayOfObjects = [];

            // Extract values and keys from the API response.
            $values = $res["result"];
            $keys = array_keys($res["result"]);

            // Transform the associative array into an array of objects, each containing 'field_name'.
            foreach ($keys as $i => $key) {
                $values[$key]["field_name"] = $key;
                $arrayOfObjects[] = $values[$key];
            }

            // Initialize a counter for hidden fields.
            global $i;  // Access the global variable $i

            // Iterate through the array of objects to create Field records in the database.
            foreach ($arrayOfObjects as $object) {
                // Check if the 'formLabel' key is set, indicating a visible field.
                if (isset($object['formLabel'])) {
                    // Create a Field record for visible fields.
                    Field::create([
                        'field_name' => $object['field_name'],
                        'type' => $object['type'],
                        'title' => $object['formLabel'],
                        'is_contact_field' => $isContactField,
                    ]);
                }
                else {
                    // Increment the counter for hidden fields.
                    $i++;

                    // Create an array with details of hidden fields.
                    global $hiddenFields;  // Access the global variable $hiddenFields
                    $hiddenFields[] = [
                        "field_name" => $object['field_name'],
                        "is_required" => '0',
                        'order' => $i,
                        "field_category_id" => '5',
                    ];

                    // Create a Field record for hidden fields.
                    Field::create([
                        'field_name' => $object['field_name'],
                        'type' => $object['type'],
                        'is_contact_field' => $isContactField,
                    ]);
                }
            }
        }

        // Example usage for CRM deal fields
        $resDeal = CRest::call('crm.deal.fields');
        $resContact = CRest::call('crm.contact.fields');
        processAndStoreFields($resDeal);
        processAndStoreFields($resContact, TRUE);

        // Array for declaring the order of fields in the 'Personal' category
        $personalCategory = [
            [
                "field_name" => 'UF_CRM_1697975635',
                "is_required" => '1',
                "order" => '3',
                "field_category_id" => '1',
            ],
            [
                "field_name" => 'UF_CRM_1680032383794',
                "is_required" => '1',
                "order" => '1',
                "field_category_id" => '1',
            ],
            [
                "field_name" => 'UF_CRM_1680298400987',
                "is_required" => '1',
                "order" => '2',
                "field_category_id" => '1',
            ],
            [
                "field_name" => 'UF_CRM_1683816608395',
                "is_required" => '0',
                "order" => '4',
                "field_category_id" => '1',
            ],
            [
                "field_name" => 'UF_CRM_1683816650459',
                "is_required" => '0',
                "order" => '5',
                "field_category_id" => '1',
            ],
            [
                "field_name" => 'UF_CRM_1683816660119',
                "is_required" => '0',
                "order" => '6',
                "field_category_id" => '1',
            ],
            [
                "field_name" => 'UF_CRM_1683816693121',
                "is_required" => '0',
                "order" => '7',
                "field_category_id" => '1',
            ],

        ];

        // Array for declaring the order of fields in the 'Address' category
        $addressCategory = [
            [
                "field_name" => 'UF_CRM_1680032015767',
                "is_required" => '0',
                "order" => '1',
                "field_category_id" => '2',
            ],
            [
                "field_name" => 'UF_CRM_1680032052359',
                "is_required" => '0',
                "order" => '2',
                "field_category_id" => '2',
            ],
            [
                "field_name" => 'UF_CRM_1680032063562',
                "is_required" => '0',
                "order" => '3',
                "field_category_id" => '2',
            ],
            [
                "field_name" => 'UF_CRM_1680032097700',
                "is_required" => '0',
                "order" => '4',
                "field_category_id" => '2',
            ],
            [
                "field_name" => 'UF_CRM_1680032106247',
                "is_required" => '0',
                "order" => '5',
                "field_category_id" => '2',
            ],
            [
                "field_name" => 'UF_CRM_1680032120828',
                "is_required" => '0',
                "order" => '6',
                "field_category_id" => '2',
            ],
        ];
        // Array for declaring the order of fields in the 'Documents' category
        $documentsCategory = [
            [
                "field_name" => 'UF_CRM_1668771731749',
                "is_required" => '0',
                "order" => '1',
                "field_category_id" => '3',
            ],
            [
                "field_name" => 'UF_CRM_1668771824662',
                "is_required" => '0',
                "order" => '2',
                "field_category_id" => '3',
            ],
            [
                "field_name" => 'UF_CRM_1668771835309',
                "is_required" => '0',
                "order" => '3',
                "field_category_id" => '3',
            ],
            [
                "field_name" => 'UF_CRM_1668771849100',
                "is_required" => '0',
                "order" => '4',
                "field_category_id" => '3',
            ],
            [
                "field_name" => 'UF_CRM_1668771875508',
                "is_required" => '0',
                "order" => '5',
                "field_category_id" => '3',
            ],
        ];
        // Array for declaring the order of fields in the 'Deals' category
        $dealsCategory = [
            [
                "field_name" => 'UF_CRM_1667335624051',
                "is_required" => '0',
                "order" => '3',
                "field_category_id" => '4',
            ],
            [
                "field_name" => 'UF_CRM_1667335695035',
                "is_required" => '0',
                "order" => '4',
                "field_category_id" => '4',
            ],
            [
                "field_name" => 'UF_CRM_1667335742921',
                "is_required" => '0',
                "order" => '1',
                "field_category_id" => '4',
            ],
        ];

        $i++;
        // Array for declaring the order of fields in the 'Hidden' category
        $hiddenCategory = [
            [
                "field_name" => 'UF_CRM_1667336320092',
                "is_required" => '1',
                'order' => $i,
                "field_category_id" => '5',
            ],
        ];
        $hiddenCategory = array_merge($hiddenCategory, $hiddenFields);

        $allCategories = [
            $personalCategory,
            $addressCategory,
            $documentsCategory,
            $dealsCategory,
            $hiddenCategory,
        ];

        foreach ($allCategories as $category) {
            foreach ($category as $item) {
                $row = Field::where('field_name', $item["field_name"])->first();
                $row->field_category_id = $item["field_category_id"];
                $row->is_required = $item["is_required"];
                if (isset($item["order"])) {
                    $row->order = $item["order"];
                }
                $row->save();
            }
        }
    }

}
