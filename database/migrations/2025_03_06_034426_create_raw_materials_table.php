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
        Schema::create('raw_materials', function (Blueprint $table) {
            $table->id();
            $table->string('raw_material_code');
            $table->string('raw_material_name');
            $table->string('raw_material_unit')->nullable();
            $table->integer('stock')->nullable()->default(0);
            $table->enum('category', ['Bahan Baku Ekstrak', 'Bahan Baku Awal (Aktif)', 'Bahan Baku Awal (Non-Aktif)', 'Bahan Baku Mentah', 'Bahan Kemas (Primer)', 'Bahan Pelarut']);
            $table->enum('material_category', ['Non Ekstraksi', 'Bahan Ekstraksi']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_materials');
    }
};
