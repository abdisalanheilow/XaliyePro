<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class PurchaseOrder extends Model
{
    protected $fillable = [
        'order_no',
        'reference_no',
        'vendor_id',
        'order_date',
        'expected_date',
        'payment_terms',
        'total_amount',
        'tax_amount',
        'discount_amount',
        'grand_total',
        'status',
        'notes',
        'branch_id',
        'store_id',
        'payment_status',
        'created_by',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_date' => 'date',
        'total_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function receipts()
    {
        return $this->hasMany(GoodsReceipt::class);
    }

    public function bills()
    {
        return $this->hasMany(PurchaseBill::class);
    }
}
