<?php

namespace Database\Seeders;

use App\Models\RndProcessDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RndProcessDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Melaksanakan Studi Praformulasi (Qbd) QTTP', 'rnd_process_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Melaksanakan Studi Praformulasi (Qbd) CQA', 'rnd_process_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Melaksanakan Studi Praformulasi (Qbd) CMA', 'rnd_process_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Melaksanakan Studi Praformulasi (Qbd) CPP', 'rnd_process_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Melaksanakan Studi Praformulasi (Qbd) Technical Feasibility', 'rnd_process_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Riset Bahan dan Vendor', 'rnd_process_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Trial Bahan Kemas', 'rnd_process_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Penetapan Bahan Kemas', 'rnd_process_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Trial Formula', 'rnd_process_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Formula Produk', 'rnd_process_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Trial Metode Analisis', 'rnd_process_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Metode Pemeriksaan', 'rnd_process_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Penentuan Nama Produk', 'rnd_process_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ceklist Cetak Desain Kemas', 'rnd_process_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Prosedur Produksi & Batch Record', 'rnd_process_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'COGM', 'rnd_process_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pilot Scale Batch Product', 'rnd_process_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Validasi Proses', 'rnd_process_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Registrasi BPOM', 'rnd_process_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Registrasi BPJH', 'rnd_process_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Permintaan Desain Kemas', 'rnd_process_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pengembangan Desain Kemas', 'rnd_process_id' => 5, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('rnd_process_details')->insert($data);
    }
}

