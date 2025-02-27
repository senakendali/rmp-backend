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
            $table->enum('followup_status', ['Permintaan Baru', 'Proses', 'Alihkan', 'Tolak Pengalihan'])->default('Permintaan Baru')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rnd_requests', function (Blueprint $table) {
            $table->dropColumn('followup_status');
        });
    }
};
