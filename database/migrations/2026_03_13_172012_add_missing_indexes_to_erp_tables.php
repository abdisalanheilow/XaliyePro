<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $addIndex = function ($table, $column) {
            if (! Schema::hasTable($table)) {
                return;
            }

            $indexName = "{$table}_{$column}_index";

            // For Laravel 11, try adding index inside a try-catch as hasIndex needs an array or full logic.
            // Using DB::select to check index existence is safest across versions without DBAL.
            $indexes = DB::select("SHOW INDEXES FROM {$table} WHERE Key_name = ?", [$indexName]);

            if (count($indexes) === 0) {
                Schema::table($table, function (Blueprint $t) use ($column) {
                    $t->index($column);
                });
            }
        };

        $addIndex('purchase_bills', 'status');
        $addIndex('purchase_bills', 'bill_date');
        $addIndex('vendor_payments', 'payment_date');
        $addIndex('sales_invoices', 'status');
        $addIndex('sales_invoices', 'invoice_date');
        $addIndex('stock_moves', 'item_id');
        $addIndex('stock_moves', 'type');
        $addIndex('items', 'status');
        $addIndex('items', 'type');
        $addIndex('items', 'track_inventory');
    }

    public function down(): void
    {
        $dropIndex = function ($table, $column) {
            if (! Schema::hasTable($table)) {
                return;
            }

            $indexName = "{$table}_{$column}_index";

            $indexes = DB::select("SHOW INDEXES FROM {$table} WHERE Key_name = ?", [$indexName]);

            if (count($indexes) > 0) {
                Schema::table($table, function (Blueprint $t) use ($column) {
                    $t->dropIndex([$column]);
                });
            }
        };

        $dropIndex('purchase_bills', 'status');
        $dropIndex('purchase_bills', 'bill_date');
        $dropIndex('vendor_payments', 'payment_date');
        $dropIndex('sales_invoices', 'status');
        $dropIndex('sales_invoices', 'invoice_date');
        $dropIndex('stock_moves', 'item_id');
        $dropIndex('stock_moves', 'type');
        $dropIndex('items', 'status');
        $dropIndex('items', 'type');
        $dropIndex('items', 'track_inventory');
    }
};
