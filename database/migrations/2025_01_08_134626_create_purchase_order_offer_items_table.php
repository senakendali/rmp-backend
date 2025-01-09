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
        Schema::create('purchase_order_offer_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_order_offer_id');
            $table->foreign('purchase_order_offer_id')->references('id')->on('purchase_order_offers')->onDelete('cascade');
            $table->unsignedBigInteger('po_item_id');
            $table->foreign('po_item_id')->references('id')->on('purchase_order_items')->onDelete('cascade');
            $table->double('offered_price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_offer_items');
    }
};
