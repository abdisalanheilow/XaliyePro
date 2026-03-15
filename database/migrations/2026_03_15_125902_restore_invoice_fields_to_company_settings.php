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
            if (!Schema::hasColumn('company_settings', 'invoice_prefix')) {
                $table->string('invoice_prefix')->default('INV')->after('fiscal_year_start');
            }
            if (!Schema::hasColumn('company_settings', 'next_invoice_number')) {
                $table->string('next_invoice_number')->default('2024001')->after('invoice_prefix');
            }
            if (!Schema::hasColumn('company_settings', 'payment_terms_days')) {
                $table->integer('payment_terms_days')->default(30)->after('next_invoice_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            $table->dropColumn(['invoice_prefix', 'next_invoice_number', 'payment_terms_days']);
        });
    }
};
