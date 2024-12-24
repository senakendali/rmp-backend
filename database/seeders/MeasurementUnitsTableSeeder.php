<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MeasurementUnitsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $units = [
            ['name' => 'Kilogram', 'abbreviation' => 'kg', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Gram', 'abbreviation' => 'g', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ton', 'abbreviation' => 't', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Meter', 'abbreviation' => 'm', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Centimeter', 'abbreviation' => 'cm', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Millimeter', 'abbreviation' => 'mm', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kilometer', 'abbreviation' => 'km', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Liter', 'abbreviation' => 'L', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Milliliter', 'abbreviation' => 'mL', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Hektar', 'abbreviation' => 'ha', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Meter Persegi', 'abbreviation' => 'm²', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kubik Meter', 'abbreviation' => 'm³', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pack', 'abbreviation' => 'pak', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Box', 'abbreviation' => 'box', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Unit', 'abbreviation' => 'unit', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lembar', 'abbreviation' => 'lembar', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Rim', 'abbreviation' => 'rim', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Batang', 'abbreviation' => 'batang', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Butir', 'abbreviation' => 'butir', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dus', 'abbreviation' => 'dus', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('measurement_units')->insert($units);
    }
}
