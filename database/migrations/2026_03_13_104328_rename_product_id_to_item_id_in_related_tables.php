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
        if (Schema::hasTable('stock_moves')) {
            Schema::table('stock_moves', function (Blueprint $table) {
                $table->renameColumn('product_id', 'item_id');
            });
        }

        if (Schema::hasTable('purchase_order_items')) {
            Schema::table('purchase_order_items', function (Blueprint $table) {
                $table->renameColumn('product_id', 'item_id');
            });
        }

        if (Schema::hasTable('purchase_bill_items')) {
            Schema::table('purchase_bill_items', function (Blueprint $table) {
                $table->renameColumn('product_id', 'item_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('stock_moves')) {
            Schema::table('stock_moves', function (Blueprint $table) {
                $table->renameColumn('item_id', 'product_id');
            });
        }

        if (Schema::hasTable('purchase_order_items')) {
            Schema::table('purchase_order_items', function (Blueprint $table) {
                $table->renameColumn('item_id', 'product_id');
            });
        }

        if (Schema::hasTable('purchase_bill_items')) {
            Schema::table('purchase_bill_items', function (Blueprint $table) {
                $table->renameColumn('item_id', 'product_id');
            });
        }
    }
};
