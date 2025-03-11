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
        Schema::table('rnd_trial_packaging_material_details', function (Blueprint $table) {
            $table->unsignedBigInteger('rnd_trial_pm_id')->after('id');
            $table->foreign('rnd_trial_pm_id')->references('id')->on('rnd_trial_packaging_materials')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rnd_trial_packaging_material_details', function (Blueprint $table) {
            $table->dropForeign(['rnd_trial_packaging_material_id']);
        });
    }
};
