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
        Schema::table('purchase_order_payment_records', function (Blueprint $table) {
            $table->string('payment_struk')->nullable()->after('remarks');
            $table->string('delivery_order_document')->nullable()->after('payment_struk');
            $table->string('sales_order_document')->nullable()->after('delivery_order_document');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_order_payment_records', function (Blueprint $table) {
            $table->dropColumn('payment_struk');
            $table->dropColumn('delivery_order_document');
            $table->dropColumn('sales_order_document');
        });
    }
};
