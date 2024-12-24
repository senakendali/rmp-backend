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
        Schema::table('purchase_request_items', function (Blueprint $table) {
             // Drop the old 'measurement' field
             $table->dropColumn('measurement');

             // Add the new 'measurement_id' field
             $table->unsignedBigInteger('measurement_id')->nullable()->after('quantity');
 
             // Define the foreign key relationship
             $table->foreign('measurement_id')
                   ->references('id')
                   ->on('measurement_units')
                   ->onDelete('set null'); // Set to null if measurement unit is deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_request_items', function (Blueprint $table) {
            // Drop the 'measurement_id' foreign key and column
            $table->dropForeign(['measurement_id']);
            $table->dropColumn('measurement_id');

            // Add the old 'measurement' field back
            $table->string('measurement')->after('quantity');
        });
    }
};
