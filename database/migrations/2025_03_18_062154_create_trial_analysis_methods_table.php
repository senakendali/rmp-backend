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
        Schema::create('trial_analysis_methods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rnd_request_id');
            $table->foreign('rnd_request_id')->references('id')->on('rnd_requests')->onDelete('cascade');
            $table->unsignedBigInteger('rnd_trial_formulation_id');
            $table->foreign('rnd_trial_formulation_id')->references('id')->on('rnd_trial_formulations')->onDelete('cascade');
            $table->string('name');
            $table->date('trial_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trial_analysis_methods');
    }
};
