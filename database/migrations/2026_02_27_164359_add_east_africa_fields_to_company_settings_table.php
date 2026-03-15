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
            // Company Profile additions
            $table->string('registration_number')->nullable()->after('company_name');
            $table->string('company_type')->nullable()->after('tax_id');
            $table->string('phone_whatsapp')->nullable()->after('phone');

            // Financial Settings additions
            $table->string('secondary_currency', 3)->nullable()->after('default_currency');
            $table->string('tax_name', 50)->nullable()->after('default_tax_rate');

            // Inventory Settings additions
            $table->enum('costing_method', ['FIFO', 'Average', 'LIFO'])->default('FIFO')->after('fiscal_year_start');
            $table->unsignedBigInteger('default_branch_id')->nullable()->after('costing_method');
            $table->boolean('allow_negative_stock')->default(false)->after('default_branch_id');
            $table->integer('low_stock_threshold')->default(5)->after('allow_negative_stock');

            // Invoice Settings additions
            $table->integer('due_reminder_days')->nullable()->default(3)->after('payment_terms_days');
            $table->text('payment_bank_details')->nullable()->after('invoice_template');
            $table->text('terms_and_conditions')->nullable()->after('payment_bank_details');
            $table->boolean('show_qr_code')->default(false)->after('terms_and_conditions');

            // System Preferences additions
            $table->string('language', 10)->default('en')->after('country');
            $table->string('timezone', 100)->default('Africa/Mogadishu')->after('language');
            $table->string('date_format', 20)->default('d/m/Y')->after('timezone');
            $table->integer('decimal_precision')->default(2)->after('date_format');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            $table->dropColumn([
                'registration_number',
                'company_type',
                'phone_whatsapp',
                'secondary_currency',
                'tax_name',
                'costing_method',
                'default_branch_id',
                'allow_negative_stock',
                'low_stock_threshold',
                'due_reminder_days',
                'payment_bank_details',
                'terms_and_conditions',
                'show_qr_code',
                'language',
                'timezone',
                'date_format',
                'decimal_precision',
            ]);
        });
    }
};
