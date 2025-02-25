<?php

namespace Database\Seeders;

use App\Models\RndProcess;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class RndProcessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Pengembangan Produk', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Trial Formulasi', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Proses Persiapan Master Data', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pilot Scale Batch', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Proses Registrasi Dokumen', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('rnd_processes')->insert($data);
    }
}
