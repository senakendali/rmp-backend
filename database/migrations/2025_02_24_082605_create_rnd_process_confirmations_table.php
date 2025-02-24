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
        Schema::create('rnd_process_confirmations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rnd_request_id');
            $table->foreign('rnd_request_id')->references('id')->on('rnd_requests')->onDelete('cascade');
            $table->unsignedBigInteger('rnd_process_detail_id');
            $table->foreign('rnd_process_detail_id')->references('id')->on('rnd_process_details')->onDelete('cascade');
            $table->enum('confirmation', ['Alihkan', 'Tolak Permintaan Pengalihan', 'Konfirmasi Proses'])->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rnd_process_confirmations');
    }
};
