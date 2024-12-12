<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Menu item name
            $table->string('url')->nullable(); // URL for the menu item
            $table->unsignedBigInteger('parent_id')->nullable(); // Self-referencing parent ID
            $table->integer('order')->default(0); // Order for display
            $table->boolean('is_active')->default(true); // To control visibility
            $table->timestamps();

            // Foreign key to self
            $table->foreign('parent_id')->references('id')->on('menus')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menus');
    }
}

