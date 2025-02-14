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
            $table->unsignedBigInteger('po_release_by')->nullable()->after('confirmed_at');
            $table->foreign('po_release_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamp('po_release_date')->nullable()->after('po_release_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['po_release_by', 'po_release_date']);
        });
    }
};
