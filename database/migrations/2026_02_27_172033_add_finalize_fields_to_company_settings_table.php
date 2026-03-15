<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {

            // --- Legal & Identity ---
            $table->string('company_legal_name')->nullable()->after('company_name');
            $table->string('industry', 100)->nullable()->after('company_type');
            $table->string('support_email')->nullable()->after('email');
            $table->string('support_phone')->nullable()->after('phone_whatsapp');

            // --- Social Media ---
            $table->string('facebook_url')->nullable()->after('website');
            $table->string('instagram_url')->nullable()->after('facebook_url');
            $table->string('linkedin_url')->nullable()->after('instagram_url');
            $table->string('twitter_url')->nullable()->after('linkedin_url');

            // --- Address: ZIP and Shipping ---
            // zip_code is already in table from initial migration
            $table->string('shipping_address')->nullable()->after('zip_code');
            $table->string('shipping_city', 100)->nullable()->after('shipping_address');
            $table->string('shipping_country', 100)->nullable()->after('shipping_city');

            // --- Financial / Accounting ---
            $table->enum('accounting_method', ['cash', 'accrual'])->default('accrual')->after('fiscal_year_start');
            $table->boolean('tax_inclusive')->default(false)->after('tax_name');
            $table->enum('rounding_method', ['per_line', 'per_total'])->default('per_total')->after('tax_inclusive');
            $table->integer('vendor_payment_terms')->default(30)->after('payment_terms_days');
            $table->date('books_lock_date')->nullable()->after('fiscal_year_start');

            // --- Invoice Extras ---
            $table->text('invoice_footer_note')->nullable()->after('terms_and_conditions');
            $table->boolean('enable_discount')->default(false)->after('show_qr_code');
            $table->boolean('enable_shipping_charge')->default(false)->after('enable_discount');

            // --- Purchasing ---
            $table->string('po_prefix', 20)->default('PO')->after('invoice_prefix');
            $table->string('next_po_number', 50)->default('2024001')->after('next_invoice_number');
        });
    }

    public function down(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            $table->dropColumn([
                'company_legal_name',
                'industry',
                'support_email',
                'support_phone',
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
                'enable_shipping_charge',
                'po_prefix',
                'next_po_number',
            ]);
        });
    }
};
