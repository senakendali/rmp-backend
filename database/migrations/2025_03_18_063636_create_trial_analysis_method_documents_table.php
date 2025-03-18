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
        Schema::create('trial_analysis_method_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trial_analysis_method_id');
            $table->foreign('trial_analysis_method_id')->references('id')->on('trial_analysis_methods')->onDelete('cascade');
            $table->string('literature_document');
            $table->string('report_document');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trial_analysis_method_documents');
    }
};
