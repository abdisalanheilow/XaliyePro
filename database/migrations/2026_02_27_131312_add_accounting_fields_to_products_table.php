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
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('track_inventory')->default(true)->after('unit_id');
            $table->decimal('reorder_quantity', 15, 2)->default(0)->after('reorder_level');
            $table->foreignId('sales_account_id')->nullable()->constrained('accounts')->onDelete('set null');
            $table->foreignId('purchase_account_id')->nullable()->constrained('accounts')->onDelete('set null');
            $table->foreignId('inventory_asset_account_id')->nullable()->constrained('accounts')->onDelete('set null');
            $table->foreignId('cogs_account_id')->nullable()->constrained('accounts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['sales_account_id']);
            $table->dropForeign(['purchase_account_id']);
            $table->dropForeign(['inventory_asset_account_id']);
            $table->dropForeign(['cogs_account_id']);
            $table->dropColumn(['track_inventory', 'reorder_quantity', 'sales_account_id', 'purchase_account_id', 'inventory_asset_account_id', 'cogs_account_id']);
        });
    }
};
