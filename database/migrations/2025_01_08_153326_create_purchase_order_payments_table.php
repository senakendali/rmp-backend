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
        Schema::create('purchase_order_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_order_offer_id');
            $table->foreign('purchase_order_offer_id')->references('id')->on('purchase_order_offers')->onDelete('cascade');
            $table->enum('payment_method', ['pay_in_full', 'pay_in_part']); 
            $table->decimal('amount', 10, 2); 
            $table->decimal('down_payment_amount', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_payments');
    }
};
