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
            $table->unsignedBigInteger('user_created')->nullable()->after('measurement_id');
            $table->unsignedBigInteger('user_updated')->nullable()->after('user_created');
            $table->foreign('user_created')->references('id')->on('users')->onDelete('set null');
            $table->foreign('user_updated')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->dropColumn('user_created');
            $table->dropColumn('user_updated');
        });
    }
};
