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
        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedBigInteger('sales_account_id')->nullable()->after('status');
            $table->unsignedBigInteger('purchase_account_id')->nullable()->after('sales_account_id');
            $table->unsignedBigInteger('inventory_asset_account_id')->nullable()->after('purchase_account_id');
            $table->unsignedBigInteger('cogs_account_id')->nullable()->after('inventory_asset_account_id');

            $table->foreign('sales_account_id')->references('id')->on('accounts')->onDelete('set null');
            $table->foreign('purchase_account_id')->references('id')->on('accounts')->onDelete('set null');
            $table->foreign('inventory_asset_account_id')->references('id')->on('accounts')->onDelete('set null');
            $table->foreign('cogs_account_id')->references('id')->on('accounts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['sales_account_id']);
            $table->dropForeign(['purchase_account_id']);
            $table->dropForeign(['inventory_asset_account_id']);
            $table->dropForeign(['cogs_account_id']);

            $table->dropColumn(['sales_account_id', 'purchase_account_id', 'inventory_asset_account_id', 'cogs_account_id']);
        });
    }
};
