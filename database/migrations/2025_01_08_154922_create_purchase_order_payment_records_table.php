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
        Schema::create('purchase_order_payment_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_order_payment_id');
            $table->foreign('purchase_order_payment_id')->references('id')->on('purchase_order_payments')->onDelete('cascade');
            $table->decimal('amount_paid', 10, 2); 
            $table->text('remarks')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_payment_records');
    }
};
