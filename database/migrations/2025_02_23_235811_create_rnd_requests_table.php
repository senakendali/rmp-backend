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
        Schema::create('rnd_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('title');
            $table->enum('development_type', ['Produk Baru', 'Produk Lama']);
            $table->date('launching_date');
            $table->text('description');
            $table->enum('category', ['Obat Bahan Alam', 'Suplemen Kesehatan', 'Kosmetik']);
            $table->enum('priority', ['Rendah', 'Sedang', 'Tinggi']);
            $table->enum('status', ['draft', 'submit'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rnd_requests');
    }
};
