<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->enum('po_status', ['Belum Diproses', 'Menunggu Persetujuan', 'Disetujui', 'Ditolak', 'Direvisi', 'PO Rilis', 'Pengiriman', 'PO Selesai'])->default('Belum Diproses')->after('needs_approval');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn('po_status');
        });
    }
};
