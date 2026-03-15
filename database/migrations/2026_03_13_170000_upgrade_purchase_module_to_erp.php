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
        // 1. Purchase Requests (Internal)
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_no')->unique();
            $table->foreignId('requester_id')->constrained('users');
            $table->foreignId('department_id')->nullable()->constrained('departments'); // Assuming departments table exists or will be added
            $table->date('request_date');
            $table->date('needed_by')->nullable();
            $table->text('reason')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'converted', 'cancelled'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('branch_id')->nullable()->constrained('branches');
            $table->timestamps();
        });

        Schema::create('purchase_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_id')->constrained('purchase_requests')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('items');
            $table->decimal('quantity', 15, 2);
            $table->text('specifications')->nullable();
            $table->timestamps();
        });

        // 2. Goods Receipts (GRN)
        Schema::create('goods_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_no')->unique();
            $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->onDelete('set null');
            $table->foreignId('vendor_id')->constrained('vendors');
            $table->date('received_date');
            $table->string('delivery_challan_no')->nullable();
            $table->foreignId('received_by')->constrained('users');
            $table->foreignId('branch_id')->nullable()->constrained('branches');
            $table->foreignId('store_id')->nullable()->constrained('stores');
            $table->enum('status', ['draft', 'received', 'returned', 'cancelled'])->default('received');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('goods_receipt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goods_receipt_id')->constrained('goods_receipts')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('items');
            $table->decimal('ordered_qty', 15, 2)->default(0);
            $table->decimal('received_qty', 15, 2);
            $table->decimal('rejected_qty', 15, 2)->default(0);
            $table->string('quality_status')->nullable();
            $table->timestamps();
        });

        // 3. Vendor Price Lists
        Schema::create('vendor_price_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->decimal('unit_price', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->integer('lead_time_days')->default(0);
            $table->decimal('min_order_qty', 15, 2)->default(0);
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Update Purchase Orders for ERP Workflow
        // Since SQLite doesn't support modifying enums directly well, we might need a workaround if not for MySQL.
        // Assuming MySQL/Postgres for ERP scale, but let's be safe.
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->foreignId('purchase_request_id')->nullable()->after('id')->constrained('purchase_requests')->onDelete('set null');
            $table->string('payment_status')->default('unpaid')->after('status');
        });

        // 5. Update Purchase Bills to Link to PO
        Schema::table('purchase_bills', function (Blueprint $table) {
            $table->foreignId('purchase_order_id')->nullable()->after('id')->constrained('purchase_orders')->onDelete('set null');
            $table->foreignId('goods_receipt_id')->nullable()->after('purchase_order_id')->constrained('goods_receipts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_bills', function (Blueprint $table) {
            $table->dropForeign(['purchase_order_id']);
            $table->dropColumn('purchase_order_id');
            $table->dropForeign(['goods_receipt_id']);
            $table->dropColumn('goods_receipt_id');
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['purchase_request_id']);
            $table->dropColumn('purchase_request_id');
            $table->dropColumn('payment_status');
        });

        Schema::dropIfExists('vendor_price_lists');
        Schema::dropIfExists('goods_receipt_items');
        Schema::dropIfExists('goods_receipts');
        Schema::dropIfExists('purchase_request_items');
        Schema::dropIfExists('purchase_requests');
    }
};
