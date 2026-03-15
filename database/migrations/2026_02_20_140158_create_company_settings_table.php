<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();

            // Logo
            $table->string('logo')->nullable();

            // Company Information
            $table->string('company_name');
            $table->string('email');
            $table->string('phone');
            $table->string('website')->nullable();
            $table->string('tax_id')->nullable();

            // Address
            $table->string('street_address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('country')->nullable();

            // Financial Settings
            $table->string('default_currency')->default('USD');
            $table->decimal('default_tax_rate', 5, 2)->default(0);
            $table->string('fiscal_year_start')->default('January');

            // Invoice Settings
            $table->string('invoice_prefix')->default('INV');
            $table->string('next_invoice_number')->default('2024001');
            $table->integer('payment_terms_days')->default(30);
            $table->string('invoice_template')->default('Modern');

            // Additional Settings (toggles)
            $table->boolean('multi_currency_enabled')->default(false);
            $table->boolean('inventory_tracking_enabled')->default(true);
            $table->boolean('auto_invoice_reminders')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
