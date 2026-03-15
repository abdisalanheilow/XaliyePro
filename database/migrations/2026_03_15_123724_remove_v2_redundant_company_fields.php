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
            $table->dropColumn([
                'support_email',
                'support_phone',
                'allow_negative_stock',
                'show_qr_code',
                'enable_shipping_charge'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            $table->string('support_email')->nullable();
            $table->string('support_phone')->nullable();
            $table->boolean('allow_negative_stock')->default(false);
            $table->boolean('show_qr_code')->default(false);
            $table->boolean('enable_shipping_charge')->default(true);
        });
    }
};
