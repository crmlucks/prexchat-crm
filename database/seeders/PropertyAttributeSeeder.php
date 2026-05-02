<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PropertyAttributeSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        // 1. Create Attribute Group for Properties (if not exists)
        $groupId = DB::table('attribute_groups')->insertGetId([
            'name' => 'Detalles de la Propiedad',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 2. Define Real Estate Attributes
        $attributes = [
            [
                'code' => 'property_type',
                'name' => 'Tipo de Propiedad',
                'type' => 'select',
                'entity_type' => 'products',
                'is_required' => 1,
                'is_unique' => 0,
                'options' => ['Casa', 'Departamento', 'Oficina', 'Terreno', 'Local Comercial']
            ],
            [
                'code' => 'area_m2',
                'name' => 'Área (m²)',
                'type' => 'text',
                'entity_type' => 'products',
                'is_required' => 0,
                'is_unique' => 0,
            ],
            [
                'code' => 'rooms',
                'name' => 'Habitaciones',
                'type' => 'text',
                'entity_type' => 'products',
                'is_required' => 0,
                'is_unique' => 0,
            ],
            [
                'code' => 'bathrooms',
                'name' => 'Baños',
                'type' => 'text',
                'entity_type' => 'products',
                'is_required' => 0,
                'is_unique' => 0,
            ],
            [
                'code' => 'location_city',
                'name' => 'Ciudad',
                'type' => 'text',
                'entity_type' => 'products',
                'is_required' => 1,
                'is_unique' => 0,
            ],
        ];

        foreach ($attributes as $attr) {
            $options = $attr['options'] ?? null;
            unset($attr['options']);
            
            $attr['created_at'] = $now;
            $attr['updated_at'] = $now;

            $attributeId = DB::table('attributes')->insertGetId($attr);

            // Link to Group
            DB::table('attribute_group_mappings')->insert([
                'attribute_id' => $attributeId,
                'attribute_group_id' => $groupId,
                'sort_order' => 1
            ]);

            // Add Options if select type
            if ($options) {
                foreach ($options as $index => $label) {
                    DB::table('attribute_options')->insert([
                        'attribute_id' => $attributeId,
                        'name' => $label,
                        'sort_order' => $index + 1
                    ]);
                }
            }
        }
    }
}
