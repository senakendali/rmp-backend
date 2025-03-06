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
        Schema::create('rnd_raw_materials', function (Blueprint $table) {
            $table->id();
            $table->enum('raw_material_type', ['Bahan Aktif', 'Bahan Tambahan', 'Bahan Penolong']);
            $table->string('raw_material_name');
            $table->string('raw_material_unit')->nullable();
            $table->integer('stock')->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rnd_raw_materials');
    }
};
