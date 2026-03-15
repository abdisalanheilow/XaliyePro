<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class JournalEntry extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'date',
        'reference',
        'description',
        'status',
        'total_amount',
        'user_id',
        'branch_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'total_amount' => 'decimal:2',
        'user_id' => 'integer',
        'branch_id' => 'integer',
        'status' => 'string',
    ];

    /**
     * Get the line items associated with the journal entry.
     */
    public function items()
    {
        return $this->hasMany(JournalItem::class);
    }

    /**
     * Get the user who created the entry.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the branch associated with the entry.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
