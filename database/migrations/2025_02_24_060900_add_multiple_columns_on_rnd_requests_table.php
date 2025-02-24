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
        Schema::table('rnd_requests', function (Blueprint $table) {
            // Pastikan kolom 'status' sudah ada sebelum menambahkan 'progress'
            if (!Schema::hasColumn('rnd_requests', 'status')) {
                $table->string('status')->nullable();
            }

            $table->enum('progress', ['Menunggu Persetujuan', 'Direvisi', 'Disetujui', 'Ditolak', 'Proses RND', 'Selesai'])
                ->default('Menunggu Persetujuan')
                ->after('status');

            $table->unsignedBigInteger('approved_by')->nullable()->after('progress');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');

            $table->timestamp('approved_date')->nullable()->after('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rnd_requests', function (Blueprint $table) {
            if (Schema::hasColumn('rnd_requests', 'approved_by')) {
                $table->dropForeign(['approved_by']);
                $table->dropColumn('approved_by');
            }

            if (Schema::hasColumn('rnd_requests', 'approved_date')) {
                $table->dropColumn('approved_date');
            }

            if (Schema::hasColumn('rnd_requests', 'progress')) {
                $table->dropColumn('progress');
            }
        });
    }
};
