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
            $indexName = "{$table}_{$column}_index";
            // Check if table exists before trying to check indexes (safeguard)
            if (! Schema::hasTable($table)) {
                return;
            }

            $indexes = DB::select("SHOW INDEXES FROM {$table} WHERE Key_name = ?", [$indexName]);

            if (count($indexes) === 0) {
                Schema::table($table, function (Blueprint $t) use ($column) {
                    $t->index($column);
                });
            }
        };

        // Sales Module
        $addIndex('sales_orders', 'status');
        $addIndex('sales_orders', 'order_date');
        $addIndex('sales_orders', 'customer_id');
        $addIndex('sales_orders', 'branch_id');
        $addIndex('sales_invoices', 'customer_id');
        $addIndex('sales_invoices', 'branch_id');
        $addIndex('delivery_notes', 'status');
        $addIndex('delivery_notes', 'delivery_date');
        $addIndex('delivery_notes', 'customer_id');
        $addIndex('delivery_notes', 'store_id');
        $addIndex('customer_payments', 'customer_id');
        $addIndex('customer_payments', 'payment_date');
        $addIndex('customer_payments', 'account_id');

        // Accounting Module
        $addIndex('journal_entries', 'date');
        $addIndex('journal_entries', 'status');
        $addIndex('journal_entry_items', 'account_id');
        $addIndex('journal_entry_items', 'journal_entry_id');

        // Purchase Module
        $addIndex('purchase_orders', 'status');
        $addIndex('purchase_orders', 'order_date');
        $addIndex('purchase_orders', 'vendor_id');
        $addIndex('purchase_bills', 'vendor_id');
        $addIndex('purchase_bills', 'branch_id');
        $addIndex('goods_receipts', 'status');
        $addIndex('goods_receipts', 'received_date');
        $addIndex('goods_receipts', 'vendor_id');
        $addIndex('goods_receipts', 'store_id');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $dropIndex = function ($table, $column) {
            $indexName = "{$table}_{$column}_index";
            if (! Schema::hasTable($table)) {
                return;
            }

            $indexes = DB::select("SHOW INDEXES FROM {$table} WHERE Key_name = ?", [$indexName]);

            if (count($indexes) > 0) {
                Schema::table($table, function (Blueprint $t) use ($column) {
                    $t->dropIndex([$column]);
                });
            }
        };

        $dropIndex('sales_orders', 'status');
        $dropIndex('sales_orders', 'order_date');
        $dropIndex('sales_orders', 'customer_id');
        $dropIndex('sales_orders', 'branch_id');
        $dropIndex('sales_invoices', 'customer_id');
        $dropIndex('sales_invoices', 'branch_id');
        $dropIndex('delivery_notes', 'status');
        $dropIndex('delivery_notes', 'delivery_date');
        $dropIndex('delivery_notes', 'customer_id');
        $dropIndex('delivery_notes', 'store_id');
        $dropIndex('customer_payments', 'customer_id');
        $dropIndex('customer_payments', 'payment_date');
        $dropIndex('customer_payments', 'account_id');
        $dropIndex('journal_entries', 'date');
        $dropIndex('journal_entries', 'status');
        $dropIndex('journal_entry_items', 'account_id');
        $dropIndex('journal_entry_items', 'journal_entry_id');
        $dropIndex('purchase_orders', 'status');
        $dropIndex('purchase_orders', 'order_date');
        $dropIndex('purchase_orders', 'vendor_id');
        $dropIndex('purchase_bills', 'vendor_id');
        $dropIndex('purchase_bills', 'branch_id');
        $dropIndex('goods_receipts', 'status');
        $dropIndex('goods_receipts', 'received_date');
        $dropIndex('goods_receipts', 'vendor_id');
        $dropIndex('goods_receipts', 'store_id');
    }
};
