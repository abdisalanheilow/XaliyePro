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
        // 1. Categories Table
        if (config('database.default') === 'mysql') {
            DB::statement("ALTER TABLE categories MODIFY COLUMN type ENUM('physical', 'product', 'service', 'digital') NOT NULL DEFAULT 'product'");
            DB::table('categories')->where('type', 'physical')->update(['type' => 'product']);
            DB::statement("ALTER TABLE categories MODIFY COLUMN type ENUM('product', 'service', 'digital') NOT NULL DEFAULT 'product'");
        } else {
            DB::table('categories')->where('type', 'physical')->update(['type' => 'product']);
        }

        // 2. Items Table
        if (config('database.default') === 'mysql') {
            DB::statement("ALTER TABLE items MODIFY COLUMN type ENUM('physical', 'product', 'service', 'digital') NOT NULL DEFAULT 'product'");
            DB::table('items')->where('type', 'physical')->update(['type' => 'product']);
            DB::statement("ALTER TABLE items MODIFY COLUMN type ENUM('product', 'service', 'digital') NOT NULL DEFAULT 'product'");
        } else {
            DB::table('items')->where('type', 'physical')->update(['type' => 'product']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('categories')->where('type', 'product')->update(['type' => 'physical']);
        if (config('database.default') === 'mysql') {
            DB::statement("ALTER TABLE categories MODIFY COLUMN type ENUM('physical', 'service', 'digital') NOT NULL DEFAULT 'physical'");
        }

        DB::table('items')->where('type', 'product')->update(['type' => 'physical']);
        if (config('database.default') === 'mysql') {
            DB::statement("ALTER TABLE items MODIFY COLUMN type ENUM('physical', 'service', 'digital') NOT NULL DEFAULT 'physical'");
        }
    }
};
