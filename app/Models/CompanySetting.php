<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class CompanySetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'logo',
        'company_name',
        'email',
        'phone',
        'website',
        'street_address',
        'city',
        'state',
        'zip_code',
        'country',
        'default_currency',
        'fiscal_year_start',
        'invoice_prefix',
        'next_invoice_number',
        'payment_terms_days',
        'invoice_template',
        'multi_currency_enabled',
        'inventory_tracking_enabled',
        'auto_invoice_reminders',
        // New East-Africa fields
        'company_type',
        'phone_whatsapp',
        'secondary_currency',
        'costing_method',
        'default_branch_id',
        'low_stock_threshold',
        'due_reminder_days',
        'payment_bank_details',
        'terms_and_conditions',
        'language',
        'timezone',
        'date_format',
        'decimal_precision',
        // Finalized fields (QB / Odoo parity)
        'company_legal_name',
        'industry',
        'facebook_url',
        'instagram_url',
        'linkedin_url',
        'twitter_url',
        'shipping_address',
        'shipping_city',
        'shipping_country',
        'accounting_method',
        'tax_inclusive',
        'rounding_method',
        'vendor_payment_terms',
        'books_lock_date',
        'invoice_footer_note',
        'enable_discount',
        'po_prefix',
        'next_po_number',
        // Default Locations
        'default_purchase_branch_id',
        'default_purchase_store_id',
        'default_sales_branch_id',
        'default_sales_store_id',
        // Accounting Mappings
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
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'multi_currency_enabled' => 'boolean',
        'inventory_tracking_enabled' => 'boolean',
        'auto_invoice_reminders' => 'boolean',
        'payment_terms_days' => 'integer',
        'enable_discount' => 'boolean',
        'low_stock_threshold' => 'integer',
        'due_reminder_days' => 'integer',
        'decimal_precision' => 'integer',
        'vendor_payment_terms' => 'integer',
        'books_lock_date' => 'date',
        'default_purchase_branch_id' => 'integer',
        'default_purchase_store_id' => 'integer',
        'default_sales_branch_id' => 'integer',
        'default_sales_store_id' => 'integer',
        // Accounting Mappings
        'default_cash_account_id' => 'integer',
        'default_bank_account_id' => 'integer',
        'default_ar_account_id' => 'integer',
        'default_inventory_account_id' => 'integer',
        'default_ap_account_id' => 'integer',
        'default_sales_income_account_id' => 'integer',
        'default_sales_return_account_id' => 'integer',
        'default_cogs_account_id' => 'integer',
        'default_purchase_expense_account_id' => 'integer',
        'default_stock_adjustment_account_id' => 'integer',
        'default_output_vat_account_id' => 'integer',
        'default_input_vat_account_id' => 'integer',
    ];

    public function defaultPurchaseBranch()
    {
        return $this->belongsTo(Branch::class, 'default_purchase_branch_id');
    }

    public function defaultPurchaseStore()
    {
        return $this->belongsTo(Store::class, 'default_purchase_store_id');
    }

    public function defaultSalesBranch()
    {
        return $this->belongsTo(Branch::class, 'default_sales_branch_id');
    }

    public function defaultSalesStore()
    {
        return $this->belongsTo(Store::class, 'default_sales_store_id');
    }
}
