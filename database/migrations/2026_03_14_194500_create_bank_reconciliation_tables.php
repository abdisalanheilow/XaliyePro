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
        // Add reconciliation fields to journal items
        Schema::table('journal_items', function (Blueprint $table) {
            $table->boolean('is_reconciled')->default(false)->after('description');
            $table->timestamp('reconciled_at')->nullable()->after('is_reconciled');
            $table->string('bank_statement_ref')->nullable()->after('reconciled_at');
        });

        // Bank Statements (For Reconciliation)
        Schema::create('bank_statements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('accounts'); // Usually a Bank account
            $table->string('statement_no')->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('opening_balance', 15, 2);
            $table->decimal('closing_balance', 15, 2);
            $table->enum('status', ['draft', 'reconciled', 'partial'])->default('draft');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        // Bank Statement Lines
        Schema::create('bank_statement_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_statement_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->string('reference')->nullable();
            $table->string('description')->nullable();
            $table->decimal('amount', 15, 2); // Positive for deposit, negative for withdrawal
            $table->foreignId('journal_item_id')->nullable()->constrained('journal_items')->nullOnDelete();
            $table->boolean('is_reconciled')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_statement_lines');
        Schema::dropIfExists('bank_statements');

        Schema::table('journal_items', function (Blueprint $table) {
            $table->dropColumn(['is_reconciled', 'reconciled_at', 'bank_statement_ref']);
        });
    }
};
