<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Unit extends Model
{
    protected $fillable = [
        'name',
        'short_name',
        'base_unit_id',
        'operator',
        'operation_value',
        'status',
    ];

    public function baseUnit()
    {
        return $this->belongsTo(Unit::class, 'base_unit_id');
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
