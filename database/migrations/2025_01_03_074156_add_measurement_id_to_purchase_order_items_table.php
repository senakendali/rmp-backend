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
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->dropColumn('measurement');
            $table->unsignedBigInteger('measurement_id')->nullable()->after('quantity');
            $table->foreign('measurement_id')->references('id')->on('measurement_units');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->string('measurement');
            $table->dropColumn('measurement_id');
        });
    }
};
