<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\CompanySetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountingModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $accounts = [
                // Assets
                ['code' => '1000', 'name' => 'Cash on Hand', 'type' => 'asset', 'sub_type' => 'Bank and Cash', 'currency' => 'USD', 'status' => 'active'],
                ['code' => '1010', 'name' => 'Main Bank Account', 'type' => 'asset', 'sub_type' => 'Bank and Cash', 'currency' => 'USD', 'status' => 'active'],
                ['code' => '1200', 'name' => 'Accounts Receivable', 'type' => 'asset', 'sub_type' => 'Accounts Receivable', 'currency' => 'USD', 'status' => 'active'],
                ['code' => '1300', 'name' => 'Inventory Asset', 'type' => 'asset', 'sub_type' => 'Inventory', 'currency' => 'USD', 'status' => 'active'],
                ['code' => '1400', 'name' => 'Prepaid Expenses', 'type' => 'asset', 'sub_type' => 'Prepayments', 'currency' => 'USD', 'status' => 'active'],

                // Liabilities
                ['code' => '2000', 'name' => 'Accounts Payable', 'type' => 'liability', 'sub_type' => 'Accounts Payable', 'currency' => 'USD', 'status' => 'active'],
                ['code' => '2200', 'name' => 'Sales Tax Payable', 'type' => 'liability', 'sub_type' => 'Tax Payable', 'currency' => 'USD', 'status' => 'active', 'is_tax_account' => true],

                // Equity
                ['code' => '3000', 'name' => 'Retained Earnings', 'type' => 'equity', 'sub_type' => 'Retained Earnings', 'currency' => 'USD', 'status' => 'active'],
                ['code' => '3100', 'name' => 'Owner Capital', 'type' => 'equity', 'sub_type' => 'Equity', 'currency' => 'USD', 'status' => 'active'],

                // Revenue
                ['code' => '4000', 'name' => 'Sales Income', 'type' => 'revenue', 'sub_type' => 'Operating Revenue', 'currency' => 'USD', 'status' => 'active'],
                ['code' => '4100', 'name' => 'Services Revenue', 'type' => 'revenue', 'sub_type' => 'Service Income', 'currency' => 'USD', 'status' => 'active'],
                ['code' => '4200', 'name' => 'Sales Returns', 'type' => 'revenue', 'sub_type' => 'Operating Revenue', 'currency' => 'USD', 'status' => 'active'],

                // Expenses
                ['code' => '5000', 'name' => 'Cost of Goods Sold', 'type' => 'expense', 'sub_type' => 'Cost of Goods Sold (COGS)', 'currency' => 'USD', 'status' => 'active'],
                ['code' => '5100', 'name' => 'Purchase Expense', 'type' => 'expense', 'sub_type' => 'Operating Expense', 'currency' => 'USD', 'status' => 'active'],
                ['code' => '5200', 'name' => 'Stock Adjustments', 'type' => 'expense', 'sub_type' => 'Operating Expense', 'currency' => 'USD', 'status' => 'active'],
                ['code' => '5300', 'name' => 'Bank Charges', 'type' => 'expense', 'sub_type' => 'Operating Expense', 'currency' => 'USD', 'status' => 'active'],
                ['code' => '5400', 'name' => 'Rent Expense', 'type' => 'expense', 'sub_type' => 'Operating Expense', 'currency' => 'USD', 'status' => 'active'],
                ['code' => '5500', 'name' => 'Payroll Expense', 'type' => 'expense', 'sub_type' => 'Payroll Expense', 'currency' => 'USD', 'status' => 'active'],
            ];

            $accountModels = [];
            /** @var \App\Models\Account[] $accountModels */
            foreach ($accounts as $a) {
                $accountModels[$a['code']] = Account::updateOrCreate(['code' => $a['code']], $a);
            }

            // Update Company Settings with Mappings
            /** @var \App\Models\CompanySetting $settings */
            $settings = CompanySetting::first();
            if (! $settings) {
                $settings = CompanySetting::create([
                    'company_name' => 'Dreams ERP Solutions Ltd',
                    'default_currency' => 'USD',
                ]);
            }

            $settings->update([
                'default_cash_account_id' => $accountModels['1000']->id,
                'default_bank_account_id' => $accountModels['1010']->id,
                'default_ar_account_id' => $accountModels['1200']->id,
                'default_inventory_account_id' => $accountModels['1300']->id,
                'default_ap_account_id' => $accountModels['2000']->id,
                'default_sales_income_account_id' => $accountModels['4000']->id,
                'default_sales_return_account_id' => $accountModels['4200']->id,
                'default_cogs_account_id' => $accountModels['5000']->id,
                'default_purchase_expense_account_id' => $accountModels['5100']->id,
                'default_stock_adjustment_account_id' => $accountModels['5200']->id,
                'default_output_vat_account_id' => $accountModels['2200']->id,
                'default_input_vat_account_id' => $accountModels['2200']->id, // Using same for simplicity in this demo
            ]);
        });
    }
}
