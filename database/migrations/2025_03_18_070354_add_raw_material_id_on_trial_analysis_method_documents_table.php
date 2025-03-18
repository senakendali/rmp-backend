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
        Schema::table('trial_analysis_method_documents', function (Blueprint $table) {
            $table->unsignedBigInteger('raw_material_id')->after('trial_analysis_method_id');
            $table->foreign('raw_material_id')->references('id')->on('raw_materials')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trial_analysis_method_documents', function (Blueprint $table) {
            $table->dropForeign(['raw_material_id']);
            $table->dropColumn('raw_material_id');
        });
    }
};
