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
        Schema::table('company_settings', function (Blueprint $table) {
            // Asset Mappings
            $table->foreignId('default_cash_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('default_bank_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('default_ar_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('default_inventory_account_id')->nullable()->constrained('accounts')->nullOnDelete();

            // Liability Mappings
            $table->foreignId('default_ap_account_id')->nullable()->constrained('accounts')->nullOnDelete();

            // Revenue Mappings
            $table->foreignId('default_sales_income_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('default_sales_return_account_id')->nullable()->constrained('accounts')->nullOnDelete();

            // Expense Mappings
            $table->foreignId('default_cogs_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('default_purchase_expense_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('default_stock_adjustment_account_id')->nullable()->constrained('accounts')->nullOnDelete();

            // Tax Mappings
            $table->foreignId('default_output_vat_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('default_input_vat_account_id')->nullable()->constrained('accounts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            $table->dropForeign(['default_cash_account_id']);
            $table->dropForeign(['default_bank_account_id']);
            $table->dropForeign(['default_ar_account_id']);
            $table->dropForeign(['default_inventory_account_id']);
            $table->dropForeign(['default_ap_account_id']);
            $table->dropForeign(['default_sales_income_account_id']);
            $table->dropForeign(['default_sales_return_account_id']);
            $table->dropForeign(['default_cogs_account_id']);
            $table->dropForeign(['default_purchase_expense_account_id']);
            $table->dropForeign(['default_stock_adjustment_account_id']);
            $table->dropForeign(['default_output_vat_account_id']);
            $table->dropForeign(['default_input_vat_account_id']);

            $table->dropColumn([
                'default_cash_account_id',
                'default_bank_account_id',
                'default_ar_account_id',
                'default_inventory_account_id',
                'default_ap_account_id',
                'default_sales_income_account_id',
                'default_sales_return_account_id',
                'default_cogs_account_id',
                'default_purchase_expense_account_id',
                'default_stock_adjustment_account_id',
                'default_output_vat_account_id',
                'default_input_vat_account_id',
            ]);
        });
    }
};
