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
        Schema::create('purchase_bills', function (Blueprint $col) {
            $col->id();
            $col->string('bill_no')->unique();
            $col->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $col->date('bill_date');
            $col->date('due_date')->nullable();
            $col->decimal('total_amount', 15, 2)->default(0);
            $col->decimal('tax_amount', 15, 2)->default(0);
            $col->decimal('discount_amount', 15, 2)->default(0);
            $col->decimal('grand_total', 15, 2)->default(0);
            $col->decimal('paid_amount', 15, 2)->default(0);
            $col->decimal('balance_amount', 15, 2)->default(0);
            $col->enum('status', ['paid', 'unpaid', 'partially_paid', 'overdue'])->default('unpaid');
            $col->text('notes')->nullable();
            $col->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
            $col->foreignId('store_id')->nullable()->constrained('stores')->onDelete('set null');
            $col->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $col->timestamps();
        });

        Schema::create('purchase_bill_items', function (Blueprint $col) {
            $col->id();
            $col->foreignId('purchase_bill_id')->constrained('purchase_bills')->onDelete('cascade');
            $col->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $col->decimal('quantity', 15, 2);
            $col->decimal('unit_price', 15, 2);
            $col->decimal('tax_amount', 15, 2)->default(0);
            $col->decimal('amount', 15, 2);
            $col->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_bill_items');
        Schema::dropIfExists('purchase_bills');
    }
};
