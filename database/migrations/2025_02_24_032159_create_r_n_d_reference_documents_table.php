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
        Schema::create('rnd_reference_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rnd_request_id');
            $table->foreign('rnd_request_id')->references('id')->on('rnd_requests')->onDelete('cascade');
            $table->string('name');
            $table->string('file_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rnd_reference_documents');
    }
};
