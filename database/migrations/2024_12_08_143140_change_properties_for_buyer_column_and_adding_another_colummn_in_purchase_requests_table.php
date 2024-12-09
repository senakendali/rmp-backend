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
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->string('buyer')->nullable()->change();
            if (!Schema::hasColumn('purchase_requests', 'purchase_reason')) {
                $table->string('purchase_reason')->nullable()->after('buyer');
            }
            if (!Schema::hasColumn('purchase_requests', 'purchase_reason_detail')) {
                $table->string('purchase_reason_detail')->nullable()->after('purchase_reason');
            }
            if (!Schema::hasColumn('purchase_requests', 'department_id')) {
                $table->unsignedBigInteger('department_id')->nullable()->after('purchase_reason_detail');
            }
            if (!Schema::hasColumn('purchase_requests', 'hod')) {
                $table->string('hod')->nullable()->after('department_id');
            }
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            // Drop the foreign key first
            $table->dropForeign(['department_id']);
            
            // Drop the columns added in the up() method
            $table->dropColumn(['purchase_reason', 'purchase_reason_detail', 'department_id', 'hod']);
            
            // Restore the 'buyer' column to its original state (nullable false)
            $table->string('buyer')->nullable(false)->change();
        });
    }
};
