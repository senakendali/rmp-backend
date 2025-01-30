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
        Schema::create('procurement_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_request_id')->nullable();
            $table->foreign('purchase_request_id')->references('id')->on('purchase_requests')->onDelete('set null');
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('set null');
            $table->unsignedBigInteger('purchase_order_item_id')->nullable();
            $table->foreign('purchase_order_item_id')->references('id')->on('purchase_order_items')->onDelete('set null');
            $table->unsignedBigInteger('purchase_order_offer_id')->nullable();
            $table->foreign('purchase_order_offer_id')->references('id')->on('purchase_order_offers')->onDelete('set null');
            $table->unsignedBigInteger('purchase_order_participant_id')->nullable();
            $table->foreign('purchase_order_participant_id')->references('id')->on('purchase_order_participants')->onDelete('set null');
            $table->string('log_name');
            $table->string('log_description')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procurement_logs');
    }
};
