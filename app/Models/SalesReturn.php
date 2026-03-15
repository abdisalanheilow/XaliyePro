<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class SalesReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_no', 'customer_id', 'sales_invoice_id', 'return_date',
        'reference_no', 'total_amount', 'tax_amount', 'grand_total',
        'notes', 'branch_id', 'store_id', 'created_by',
    ];

    protected $casts = [
        'return_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function invoice()
    {
        return $this->belongsTo(SalesInvoice::class, 'sales_invoice_id');
    }

    public function items()
    {
        return $this->hasMany(SalesReturnItem::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
