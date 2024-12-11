<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateVendorsTableForVerificationFields extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            // Update verification_status field
            $table->enum('verification_status', ['verified', 'unverified', 'approved'])
                  ->default('unverified')
                  ->change();

            // Add new fields for approval and verification
            $table->unsignedBigInteger('approved_by')->nullable()->after('verification_status');
            $table->timestamp('approved_date')->nullable()->after('approved_by');
            $table->unsignedBigInteger('verified_by')->nullable()->after('approved_date');
            $table->timestamp('verified_date')->nullable()->after('verified_by');

            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            // Revert verification_status field
            $table->enum('verification_status', ['verified', 'unverified'])
                  ->default('unverified')
                  ->change();

            // Drop the newly added fields
            $table->dropColumn('approved_by');
            $table->dropColumn('approved_date');
            $table->dropColumn('verified_by');
            $table->dropColumn('verified_date');
        });
    }
}

