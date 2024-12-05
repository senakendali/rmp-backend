<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GoodsCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('goods_category')->insert([
            ['name' => 'ATK', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Furniture', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
