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
        // Using raw SQL because Laravel's change() doesn't support ENUM modifications well without doctrine/dbal
        DB::statement("ALTER TABLE purchase_bills MODIFY COLUMN status ENUM('paid', 'unpaid', 'partially_paid', 'overdue', 'draft') DEFAULT 'unpaid'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE purchase_bills MODIFY COLUMN status ENUM('paid', 'unpaid', 'partially_paid', 'overdue') DEFAULT 'unpaid'");
    }
};
