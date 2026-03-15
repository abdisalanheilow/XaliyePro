<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $accounts = [
            // Assets
            ['code' => '1000', 'name' => 'Cash', 'type' => 'asset', 'sub_type' => 'Bank and Cash', 'currency' => 'USD', 'status' => 'active'],
            ['code' => '1010', 'name' => 'Bank Account', 'type' => 'asset', 'sub_type' => 'Bank and Cash', 'currency' => 'USD', 'status' => 'active'],
            ['code' => '1200', 'name' => 'Accounts Receivable', 'type' => 'asset', 'sub_type' => 'Accounts Receivable', 'currency' => 'USD', 'status' => 'active'],
            ['code' => '1300', 'name' => 'Inventory Asset', 'type' => 'asset', 'sub_type' => 'Inventory', 'currency' => 'USD', 'status' => 'active'],
            ['code' => '1400', 'name' => 'Prepaid Expenses', 'type' => 'asset', 'sub_type' => 'Prepayments', 'currency' => 'USD', 'status' => 'active'],
            ['code' => '1500', 'name' => 'Furniture and Equipment', 'type' => 'asset', 'sub_type' => 'Fixed Asset', 'currency' => 'USD', 'status' => 'active'],
            ['code' => '1510', 'name' => 'Accumulated Depreciation', 'type' => 'asset', 'sub_type' => 'Fixed Asset', 'currency' => 'USD', 'status' => 'active'],

            // Liabilities
            ['code' => '2000', 'name' => 'Accounts Payable', 'type' => 'liability', 'sub_type' => 'Accounts Payable', 'currency' => 'USD', 'status' => 'active'],
            ['code' => '2100', 'name' => 'Credit Card', 'type' => 'liability', 'sub_type' => 'Credit Card', 'currency' => 'USD', 'status' => 'active'],
            ['code' => '2200', 'name' => 'Sales Tax Payable', 'type' => 'liability', 'sub_type' => 'Tax Payable', 'currency' => 'USD', 'status' => 'active', 'is_tax_account' => true],
            ['code' => '2300', 'name' => 'Payroll Liabilities', 'type' => 'liability', 'sub_type' => 'Payroll Payable', 'currency' => 'USD', 'status' => 'active'],
            ['code' => '2400', 'name' => 'Short Term Loan', 'type' => 'liability', 'sub_type' => 'Current Liability', 'currency' => 'USD', 'status' => 'active'],
            ['code' => '2800', 'name' => 'Long Term Debt', 'type' => 'liability', 'sub_type' => 'Non-current Liability', 'currency' => 'USD', 'status' => 'active'],

            // Equity
            ['code' => '3000', 'name' => 'Owner\'s Equity', 'type' => 'equity', 'sub_type' => 'Equity', 'currency' => 'USD', 'status' => 'active'],
            ['code' => '3100', 'name' => 'Owner\'s Draw', 'type' => 'equity', 'sub_type' => 'Owner Draw', 'currency' => 'USD', 'status' => 'active'],
            ['code' => '3200', 'name' => 'Owner\'s Investment', 'type' => 'equity', 'sub_type' => 'Owner Contribution', 'currency' => 'USD', 'status' => 'active'],
            ['code' => '3900', 'name' => 'Retained Earnings', 'type' => 'equity', 'sub_type' => 'Retained Earnings', 'currency' => 'USD', 'status' => 'active'],

            // Revenue
            ['code' => '4000', 'name' => 'Sales Income', 'type' => 'revenue', 'sub_type' => 'Operating Revenue', 'currency' => 'USD', 'status' => 'active'],
            ['code' => '4100', 'name' => 'Services Income', 'type' => 'revenue', 'sub_type' => 'Service Income', 'currency' => 'USD', 'status' => 'active'],
            ['code' => '4200', 'name' => 'Product Income', 'type' => 'revenue', 'sub_type' => 'Product Income', 'currency' => 'USD', 'status' => 'active'],
            ['code' => '4300', 'name' => 'Discounts Given', 'type' => 'revenue', 'sub_type' => 'Discounts Given', 'currency' => 'USD', 'status' => 'active'],
            ['code' => '4900', 'name' => 'Other Income', 'type' => 'revenue', 'sub_type' => 'Other Income', 'currency' => 'USD', 'status' => 'active'],

            // Expenses
            ['code' => '5000', 'name' => 'Cost of Goods Sold', 'type' => 'expense', 'sub_type' => 'Cost of Goods Sold (COGS)', 'currency' => 'USD', 'status' => 'active'],
            ['code' => '5100', 'name' => 'Advertising Expense', 'type' => 'expense', 'sub_type' => 'Operating Expense', 'currency' => 'USD', 'status' => 'active'],
            ['code' => '5200', 'name' => 'Bank Charges', 'type' => 'expense', 'sub_type' => 'Operating Expense', 'currency' => 'USD', 'status' => 'active'],
            ['code' => '5300', 'name' => 'Depreciation Expense', 'type' => 'expense', 'sub_type' => 'Depreciation', 'currency' => 'USD', 'status' => 'active'],
            ['code' => '5400', 'name' => 'Insurance Expense', 'type' => 'expense', 'sub_type' => 'Operating Expense', 'currency' => 'USD', 'status' => 'active'],
            ['code' => '5500', 'name' => 'Payroll Expense', 'type' => 'expense', 'sub_type' => 'Payroll Expense', 'currency' => 'USD', 'status' => 'active'],
            ['code' => '5600', 'name' => 'Rent Expense', 'type' => 'expense', 'sub_type' => 'Rent or Lease', 'currency' => 'USD', 'status' => 'active'],
            ['code' => '5700', 'name' => 'Office Supplies', 'type' => 'expense', 'sub_type' => 'Operating Expense', 'currency' => 'USD', 'status' => 'active'],
            ['code' => '5800', 'name' => 'Taxes & Licenses', 'type' => 'expense', 'sub_type' => 'Taxes and Licenses', 'currency' => 'USD', 'status' => 'active'],
            ['code' => '5900', 'name' => 'Utilities', 'type' => 'expense', 'sub_type' => 'Operating Expense', 'currency' => 'USD', 'status' => 'active'],
        ];

        foreach ($accounts as $accountData) {
            Account::firstOrCreate(
                ['code' => $accountData['code']],
                $accountData
            );
        }
    }
}
