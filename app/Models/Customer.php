<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'name',
        'email',
        'phone',
        'type',
        'address',
        'city',
        'country',
        'payment_terms',
        'credit_limit',
        'balance',
        'total_sales',
        'status',
        'notes',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'balance' => 'decimal:2',
        'total_sales' => 'decimal:2',
    ];

    /**
     * Generate initials from name (up to 2 chars).
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);

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
            default => $this->payment_terms,
        };
    }

    public function invoices()
    {
        return $this->hasMany(SalesInvoice::class);
    }

    public function payments()
    {
        return $this->hasMany(CustomerPayment::class);
    }
}
