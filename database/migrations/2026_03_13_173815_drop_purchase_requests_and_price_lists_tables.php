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
        // 1. Drop child table first
        Schema::dropIfExists('purchase_request_items');

        // 2. Drop foreign key in another child table
        if (Schema::hasTable('purchase_orders') && Schema::hasColumn('purchase_orders', 'purchase_request_id')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->dropForeign(['purchase_request_id']);
                $table->dropColumn('purchase_request_id');
            });
        }

        // 3. Drop parent table
        Schema::dropIfExists('purchase_requests');

        // 4. Drop unrelated table
        Schema::dropIfExists('vendor_price_lists');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No safe down migration, data is lost on dropping these tables.
    }
};
