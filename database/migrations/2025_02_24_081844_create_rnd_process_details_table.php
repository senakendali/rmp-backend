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
        Schema::create('rnd_process_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rnd_process_id');
            $table->foreign('rnd_process_id')->references('id')->on('rnd_processes')->onDelete('cascade');
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rnd_process_details');
    }
};
