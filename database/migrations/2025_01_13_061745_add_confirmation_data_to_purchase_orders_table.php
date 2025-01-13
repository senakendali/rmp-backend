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
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('user_confirmed')->nullable()->after('user_updated');
            $table->foreign('user_confirmed')->references('id')->on('users')->onDelete('set null');
            $table->timestamp('confirmed_at')->nullable()->after('user_confirmed');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['user_confirmed']);
            $table->dropColumn(['user_confirmed', 'confirmed_at']); 
        });
    }
};
