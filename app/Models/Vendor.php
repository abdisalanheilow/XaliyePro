<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'name',
        'email',
        'phone',
        'tax_id',
        'type',
        'address',
        'city',
        'country',
        'payment_terms',
        'credit_limit',
        'balance',
        'total_purchases',
        'status',
        'notes',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'balance' => 'decimal:2',
        'total_purchases' => 'decimal:2',
    ];

    /**
     * Generate initials from name (up to 2 chars).
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name ?? 'V');

        return strtoupper(
            collect($words)->take(2)->map(fn ($w) => $w[0] ?? '')->implode('')
        );
    }

    /**
     * Human-readable payment terms label.
     */
    public function getPaymentTermsLabelAttribute(): string
    {
        return match ($this->payment_terms) {
            'net_30' => 'Net 30',
            'net_15' => 'Net 15',
            'due_on_receipt' => 'Due on Receipt',
            'net_60' => 'Net 60',
            default => $this->payment_terms ?? 'Net 30',
        };
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'vendor_id');
    }

    public function purchaseBills()
    {
        return $this->hasMany(PurchaseBill::class, 'vendor_id');
    }

    public function vendorPayments()
    {
        return $this->hasMany(VendorPayment::class, 'vendor_id');
    }
}
