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
            $this->makeColumnsNullable($table, true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $this->makeColumnsNullable($table, false);
        });
    }

    /**
     * Change columns' nullable property.
     *
     * @param Blueprint $table
     * @param bool $nullable
     */
    private function makeColumnsNullable(Blueprint $table, bool $nullable): void
    {
        $columns = [
            ['name' => 'po_date', 'type' => 'date'],
            ['name' => 'department_id', 'type' => 'unsignedBigInteger'],
            ['name' => 'vendor_id', 'type' => 'unsignedBigInteger'],
        ];

        foreach ($columns as $column) {
            $method = $column['type'];
            $table->$method($column['name'])->nullable($nullable)->change();
        }
    }
};
