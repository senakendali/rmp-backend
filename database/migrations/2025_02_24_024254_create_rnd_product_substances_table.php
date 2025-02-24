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
        Schema::create('rnd_product_substances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rnd_product_details_id');
            $table->foreign('rnd_product_details_id')->references('id')->on('rnd_product_details')->onDelete('cascade');
            $table->string('acttive_substance');
            $table->string('strength');
            $table->string('dose');
            $table->string('form');
            $table->string('packaging');
            $table->string('brand');
            $table->float('hna_target');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rnd_product_substances');
    }
};
