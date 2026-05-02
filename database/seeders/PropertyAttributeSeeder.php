<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PropertyAttributeSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        // ── 1. GRUPO PARA PROPIEDADES (PRODUCTOS) ──
        $propertyGroupId = DB::table('attribute_groups')->insertGetId([
            'name' => 'Detalles de la Propiedad',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // ── 2. GRUPO PARA PERFIL INMOBILIARIO (LEADS) ──
        $leadGroupId = DB::table('attribute_groups')->insertGetId([
            'name' => 'Perfil Inmobiliario AI',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // ── 3. ATRIBUTOS DE PROPIEDADES ──
        $this->createAttributes('products', $propertyGroupId, [
            ['code' => 'property_type', 'name' => 'Tipo de Propiedad', 'type' => 'select', 'options' => ['Casa', 'Departamento', 'Oficina', 'Terreno']],
            ['code' => 'area_m2', 'name' => 'Área (m²)', 'type' => 'text'],
            ['code' => 'rooms', 'name' => 'Habitaciones', 'type' => 'text'],
            ['code' => 'location_city', 'name' => 'Ciudad', 'type' => 'text'],
        ]);

        // ── 4. ATRIBUTOS DE LEADS (GESTIÓN DE VENTA) ──
        $this->createAttributes('leads', $leadGroupId, [
            ['code' => 'project_interest', 'name' => 'Proyecto de Interés', 'type' => 'select', 'options' => ['Residencial Primavera', 'Torre Ejecutiva', 'Hacienda Real']],
            ['code' => 'budget', 'name' => 'Presupuesto', 'type' => 'text'],
            ['code' => 'currency', 'name' => 'Moneda', 'type' => 'select', 'options' => ['USD', 'PEN']],
            ['code' => 'source_channel', 'name' => 'Canal de Origen', 'type' => 'select', 'options' => ['WhatsApp AI', 'Facebook Ads', 'Instagram', 'Web', 'Referido']],
            ['code' => 'interest_details', 'name' => 'Detalles de Interés', 'type' => 'textarea'],
        ]);
    }

    private function createAttributes($entity, $groupId, $attributes)
    {
        foreach ($attributes as $attr) {
            $options = $attr['options'] ?? null;
            unset($attr['options']);

            $attr['entity_type'] = $entity;
            $attr['is_required'] = 0;
            $attr['is_unique'] = 0;
            $attr['created_at'] = Carbon::now();
            $attr['updated_at'] = Carbon::now();

            $attributeId = DB::table('attributes')->insertGetId($attr);

            DB::table('attribute_group_mappings')->insert([
                'attribute_id' => $attributeId,
                'attribute_group_id' => $groupId,
                'sort_order' => 1,
            ]);

            if ($options) {
                foreach ($options as $index => $label) {
                    DB::table('attribute_options')->insert([
                        'attribute_id' => $attributeId,
                        'name' => $label,
                        'sort_order' => $index + 1,
                    ]);
                }
            }
        }
    }
}
