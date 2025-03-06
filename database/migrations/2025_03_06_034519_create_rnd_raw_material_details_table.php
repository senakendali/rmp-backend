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
        Schema::create('rnd_raw_material_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rnd_raw_material_id');
            $table->foreign('rnd_raw_material_id')->references('id')->on('rnd_raw_materials')->onDelete('cascade');
            $table->unsignedBigInteger('raw_material_id');
            $table->foreign('raw_material_id')->references('id')->on('raw_materials')->onDelete('cascade');
            $table->enum('material_status', ['Baru', 'Tersedia'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rnd_raw_material_details');
    }
};
