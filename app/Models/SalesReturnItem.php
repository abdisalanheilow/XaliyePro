<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class SalesReturnItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_return_id', 'item_id', 'quantity', 'unit_price',
        'tax_amount', 'amount',
    ];

    public function salesReturn()
    {
        return $this->belongsTo(SalesReturn::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
