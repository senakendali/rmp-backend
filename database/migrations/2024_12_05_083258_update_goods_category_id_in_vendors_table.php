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
        Schema::table('vendors', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['goods_category_id']);

            // Alter goods_category_id to store tags (e.g., JSON format)
            $table->json('goods_category_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->unsignedBigInteger('goods_category_id')->change();
            $table->foreign('goods_category_id')->references('id')->on('goods_category')->onDelete('cascade');
        });
    }
};
