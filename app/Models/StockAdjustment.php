<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class StockAdjustment extends Model
{
    protected $fillable = [
        'adjustment_no',
        'adjustment_date',
        'store_id',
        'reason',
        'notes',
        'status',
        'created_by',
    ];

    protected $casts = [
        'adjustment_date' => 'date',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(StockAdjustmentItem::class);
    }
}
