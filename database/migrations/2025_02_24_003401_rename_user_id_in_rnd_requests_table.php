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
            $table->renameColumn('user_id', 'requested_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rnd_requests', function (Blueprint $table) {
            $table->renameColumn('requested_by', 'user_id');
        });
    }
};
