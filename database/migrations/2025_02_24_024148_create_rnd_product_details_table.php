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
        Schema::create('rnd_product_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rnd_request_id')->constrained('rnd_requests')->onDelete('cascade');
            $table->json('name');
            $table->string('manufacturer');
            $table->string('registrant');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rnd_product_details');
    }
};
