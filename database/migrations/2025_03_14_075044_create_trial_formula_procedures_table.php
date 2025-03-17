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
        Schema::create('trial_formula_procedures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rnd_trial_formulation_id');
            $table->foreign('rnd_trial_formulation_id')->references('id')->on('rnd_trial_formulations')->onDelete('cascade');
            $table->string('procedure');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trial_formula_procedures');
    }
};
