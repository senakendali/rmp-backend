<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create parent menus
        $dashboard = Menu::firstOrCreate([
            'name' => 'Dashboard',
            'url' => '/dashboard',
            'parent_id' => null,
            'order' => 1,
            'is_active' => true,
        ]);

        $target = Menu::firstOrCreate([
            'name' => 'Target',
            'url' => '/target',
            'parent_id' => null,
            'order' => 2,
            'is_active' => true,
        ]);

        $production_request = Menu::firstOrCreate([
            'name' => 'Production Request',
            'url' => null,
            'parent_id' => null,
            'order' => 3,
            'is_active' => true,
        ]);

        Menu::firstOrCreate([
            'name' => 'Pemeliharaan',
            'url' => '/pemeliharaan',
            'parent_id' => $production_request->id,
            'order' => 1,
            'is_active' => true,
        ]);

        Menu::firstOrCreate([
            'name' => 'Sanitasi',
            'url' => '/sanitasi',
            'parent_id' => $production_request->id,
            'order' =>2,
            'is_active' => true,
        ]);

        $monitoring = Menu::firstOrCreate([
            'name' => 'Monitoring',
            'url' => null,
            'parent_id' => null,
            'order' => 4,
            'is_active' => true,
        ]);

        Menu::firstOrCreate([
            'name' => 'Suhu dan Kelembapan',
            'url' => '/suhu-dan-kelembapan',
            'parent_id' => $monitoring->id,
            'order' =>1,
            'is_active' => true,
        ]);

        Menu::firstOrCreate([
            'name' => 'Sanitasi Umum',
            'url' => '/sanitasi-umum',
            'parent_id' => $monitoring->id,
            'order' =>2,
            'is_active' => true,
        ]);

        Menu::firstOrCreate([
            'name' => 'Sanitasi Produksi',
            'url' => '/sanitasi-produksi',
            'parent_id' => $monitoring->id,
            'order' =>3,
            'is_active' => true,
        ]);

        Menu::firstOrCreate([
            'name' => 'Pembelian',
            'url' => '/suhu-dan-kelembapan',
            'parent_id' => $monitoring->id,
            'order' =>4,
            'is_active' => true,
        ]);

        $qa = Menu::firstOrCreate([
            'name' => 'QA',
            'url' => null,
            'parent_id' => null,
            'order' => 5,
            'is_active' => true,
        ]);

        Menu::firstOrCreate([
            'name' => 'Ekstraksi',
            'url' => '/ekstraksi',
            'parent_id' => $qa->id,
            'order' =>1,
            'is_active' => true,
        ]);

        Menu::firstOrCreate([
            'name' => 'Produk Jadi',
            'url' => '/produk-jadi',
            'parent_id' => $qa->id,
            'order' =>2,
            'is_active' => true,
        ]);

        Menu::firstOrCreate([
            'name' => 'Validasi',
            'url' => '/validasi',
            'parent_id' => $qa->id,
            'order' =>3,
            'is_active' => true,
        ]);

        Menu::firstOrCreate([
            'name' => 'Kualifikasi',
            'url' => '/kualifikasi',
            'parent_id' => $qa->id,
            'order' =>4,
            'is_active' => true,
        ]);

        Menu::firstOrCreate([
            'name' => 'Audit',
            'url' => '/audit',
            'parent_id' => $qa->id,
            'order' =>5,
            'is_active' => true,
        ]);

        $qc = Menu::firstOrCreate([
            'name' => 'QC',
            'url' => null,
            'parent_id' => null,
            'order' => 6,
            'is_active' => true,
        ]);

        Menu::firstOrCreate([
            'name' => 'Sampling',
            'url' => '/sampling',
            'parent_id' => $qc->id,
            'order' =>1,
            'is_active' => true,
        ]);

        Menu::firstOrCreate([
            'name' => 'QC Bahan',
            'url' => '/qc-bahan',
            'parent_id' => $qc->id,
            'order' =>2,
            'is_active' => true,
        ]);

        Menu::firstOrCreate([
            'name' => 'QC Bets',
            'url' => '/qc-bets',
            'parent_id' => $qc->id,
            'order' =>3,
            'is_active' => true,
        ]);

        Menu::firstOrCreate([
            'name' => 'Uji Sanitasi',
            'url' => '/uji-sanitasi',
            'parent_id' => $qc->id,
            'order' =>4,
            'is_active' => true,
        ]);

        $rnd = Menu::firstOrCreate([
            'name' => 'R&D',
            'url' => null,
            'parent_id' => null,
            'order' => 7,
            'is_active' => true,
        ]);

        Menu::firstOrCreate([
            'name' => 'Formulasi',
            'url' => '/formulasi',
            'parent_id' => $rnd->id,
            'order' =>1,
            'is_active' => true,
        ]);

        Menu::firstOrCreate([
            'name' => 'Prosedur',
            'url' => '/prosedur',
            'parent_id' => $rnd->id,
            'order' =>2,
            'is_active' => true,
        ]);

        Menu::firstOrCreate([
            'name' => 'Material',
            'url' => '/material',
            'parent_id' => $rnd->id,
            'order' =>3,
            'is_active' => true,
        ]);

        $pemeliharaan = Menu::firstOrCreate([
            'name' => 'Pemeliharaan',
            'url' => null,
            'parent_id' => null,
            'order' => 8,
            'is_active' => true,
        ]);

        Menu::firstOrCreate([
            'name' => 'permintaan',
            'url' => '/permintaan',
            'parent_id' => $pemeliharaan->id,
            'order' =>1,
            'is_active' => true,
        ]);

        Menu::firstOrCreate([
            'name' => 'Aktivitas',
            'url' => '/aktivitas',
            'parent_id' => $pemeliharaan->id,
            'order' =>2,
            'is_active' => true,
        ]);

        $pilotScaleBatch = Menu::firstOrCreate([
            'name' => 'Pilot Scale Batch',
            'url' => null,
            'parent_id' => null,
            'order' => 9,
            'is_active' => true,
        ]);

        Menu::firstOrCreate([
            'name' => 'Ekstraksi',
            'url' => '/ekstraksi',
            'parent_id' => $pilotScaleBatch->id,
            'order' => 1,
            'is_active' => true,
        ]);

        Menu::firstOrCreate([
            'name' => 'Pengolahan',
            'url' => '/pengolahan',
            'parent_id' => $pilotScaleBatch->id,
            'order' => 2,
            'is_active' => true,
        ]);


        Menu::firstOrCreate([
            'name' => 'Pengemasan',
            'url' => '/pengemasan',
            'parent_id' => $pilotScaleBatch->id,
            'order' => 3,
            'is_active' => true,
        ]);

        $produksi = Menu::firstOrCreate([
            'name' => 'Produksi',
            'url' => null,
            'parent_id' => null,
            'order' => 10,
            'is_active' => true,
        ]);

        Menu::firstOrCreate([
            'name' => 'Stempel',
            'url' => '/stempel',
            'parent_id' => $produksi->id,
            'order' => 1,
            'is_active' => true,
        ]);

        Menu::firstOrCreate([
            'name' => 'Penyimpangan',
            'url' => '/penyimpangan',
            'parent_id' => $produksi->id,
            'order' => 2,
            'is_active' => true,
        ]);

        $penerimaan = Menu::firstOrCreate([
            'name' => 'Penerimaan',
            'url' => '/penerimaan',
            'parent_id' => null,
            'order' => 11,
            'is_active' => true,
        ]);

        $pengeluaran = Menu::firstOrCreate([
            'name' => 'Pengeluaran',
            'url' => '/pengeluaran',
            'parent_id' => null,
            'order' => 12,
            'is_active' => true,
        ]);

        $gudang = Menu::firstOrCreate([
            'name' => 'Gudang',
            'url' => null,
            'parent_id' => null,
            'order' => 13,
            'is_active' => true,
        ]);

        Menu::firstOrCreate([
            'name' => 'Pengembalian',
            'url' => '/pengembalian',
            'parent_id' => $gudang->id,
            'order' => 1,
            'is_active' => true,
        ]);

        $pembelian = Menu::firstOrCreate([
            'name' => 'Pembelian',
            'url' => null,
            'parent_id' => null,
            'order' => 14,
            'is_active' => true,
        ]);

        Menu::firstOrCreate([
            'name' => 'Pengadaan',
            'url' => '/pengadaan',
            'parent_id' => $pembelian->id,
            'order' => 1,
            'is_active' => true,
        ]);

        Menu::firstOrCreate([
            'name' => 'Pembayaran',
            'url' => '/pembayaran',
            'parent_id' => $pembelian->id,
            'order' => 2,
            'is_active' => true,
        ]);

        Menu::firstOrCreate([
            'name' => 'Permintaan',
            'url' => '/permintaan',
            'parent_id' => $pembelian->id,
            'order' => 3,
            'is_active' => true,
        ]);

        Menu::firstOrCreate([
            'name' => 'Produk R&D',
            'url' => '/produk-r-and-d',
            'parent_id' => $pembelian->id,
            'order' => 4,
            'is_active' => true,
        ]);

        $laporan = Menu::firstOrCreate([
            'name' => 'Laporan',
            'url' => '/laporan',
            'parent_id' => null,
            'order' => 15,
            'is_active' => true,
        ]);
       
        $dokumen = Menu::firstOrCreate([
            'name' => 'Dokumen',
            'url' => '/dokumen',
            'parent_id' => null,
            'order' => 16,
            'is_active' => true,
        ]);

        $samplingAir = Menu::firstOrCreate([
            'name' => 'Sampling Air',
            'url' => '/sampling-air',
            'parent_id' => null,
            'order' => 17,
            'is_active' => true,
        ]);

        $vendor = Menu::firstOrCreate([
            'name' => 'Vendor',
            'url' => null,
            'parent_id' => null,
            'order' => 18,
            'is_active' => true,
        ]);

        Menu::firstOrCreate([
            'name' => 'Material',
            'url' => '/vendor/material',
            'parent_id' => $vendor->id,
            'order' => 1,
            'is_active' => true,
        ]);

        Menu::firstOrCreate([
            'name' => 'Non Material',
            'url' => '/vendor/non-material',
            'parent_id' => $vendor->id,
            'order' => 2,
            'is_active' => true,
        ]);

        $master = Menu::firstOrCreate([
            'name' => 'Master',
            'url' => null,
            'parent_id' => null,
            'order' => 19,
            'is_active' => true,
        ]);

        Menu::firstOrCreate([
            'name' => 'kategori-barang',
            'url' => '/kategori-barang',
            'parent_id' => $master->id,
            'order' => 1,
            'is_active' => true,
        ]);

        Menu::firstOrCreate([
            'name' => 'Barang',
            'url' => '/barang',
            'parent_id' => $master->id,
            'order' => 2,
            'is_active' => true,
        ]);
       
    }
}
