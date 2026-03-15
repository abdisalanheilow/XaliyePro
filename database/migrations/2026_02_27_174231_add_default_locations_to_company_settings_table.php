<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            // Default branch & store for Purchase Orders
            $table->unsignedBigInteger('default_purchase_branch_id')->nullable()->after('po_prefix');
            $table->unsignedBigInteger('default_purchase_store_id')->nullable()->after('default_purchase_branch_id');

            // Default branch & store for Sales / Invoices
            $table->unsignedBigInteger('default_sales_branch_id')->nullable()->after('default_purchase_store_id');
            $table->unsignedBigInteger('default_sales_store_id')->nullable()->after('default_sales_branch_id');
        });
    }

    public function down(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            $table->dropColumn([
                'default_purchase_branch_id',
                'default_purchase_store_id',
                'default_sales_branch_id',
                'default_sales_store_id',
            ]);
        });
    }
};
