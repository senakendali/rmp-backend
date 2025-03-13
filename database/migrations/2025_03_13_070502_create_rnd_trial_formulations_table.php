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
        Schema::create('rnd_trial_formulations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rnd_request_id');
            $table->foreign('rnd_request_id')->references('id')->on('rnd_requests')->onDelete('cascade');
            $table->string('name');
            $table->date('trial_date');
            $table->enum('status', ['Menunggu Persetujuan', 'Direvisi', 'Disetujui', 'Ditolak'])->default('Menunggu Persetujuan');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamp('approved_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rnd_trial_formulations');
    }
};
