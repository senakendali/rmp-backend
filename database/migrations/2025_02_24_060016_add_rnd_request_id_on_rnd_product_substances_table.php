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
        Schema::table('rnd_product_substances', function (Blueprint $table) {
            $table->dropForeign(['rnd_product_details_id']);
            $table->dropColumn('rnd_product_details_id');
            $table->unsignedBigInteger('rnd_request_id')->nullable()->after('id');
            $table->foreign('rnd_request_id')->references('id')->on('rnd_requests')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rnd_product_substances', function (Blueprint $table) {
            $table->dropForeign(['rnd_request_id']);
            $table->dropColumn('rnd_request_id');
        });
    }
};
