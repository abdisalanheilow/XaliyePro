<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (config('database.default') === 'mysql') {
            DB::statement("ALTER TABLE stock_moves MODIFY COLUMN type ENUM('opening', 'in', 'out', 'adjustment', 'PURCHASE', 'SALE', 'TRANSFER', 'ADJUST', 'RETURN', 'CANCEL', 'CANCEL_SALE', 'CANCEL_PURCHASE') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (config('database.default') === 'mysql') {
            DB::statement("ALTER TABLE stock_moves MODIFY COLUMN type ENUM('opening', 'in', 'out', 'adjustment') NOT NULL");
        }
    }
};
