<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Account extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'type',
        'sub_type',
        'parent_id',
        'opening_balance',
        'current_balance',
        'currency',
        'is_tax_account',
        'status',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'parent_id' => 'integer',
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_tax_account' => 'boolean',
        'status' => 'string', // active/inactive
    ];

    /**
     * Get the parent account.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    /**
     * Get sub-accounts (children).
     */
    public function children(): HasMany
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    /**
     * Get journal items associated with this account.
     */
    public function journalItems(): HasMany
    {
        return $this->hasMany(JournalItem::class);
    }

    /**
     * Mathematically synchronize the current balance utilizing QuickBooks / Odoo
     * ledger logic for Debits vs Credits across all Journal activities.
     */
    public function syncBalance()
    {
        $postedItems = JournalItem::where('account_id', $this->id)
            ->whereHas('entry', function ($query) {
                $query->where('status', 'posted');
            })->get();

        $totalDebit = $postedItems->sum('debit') ?? 0;
        $totalCredit = $postedItems->sum('credit') ?? 0;

        // Assets and Expenses naturally carry debit balances. (Dr increases, Cr decreases)
        if (in_array($this->type, ['asset', 'expense'])) {
            $this->current_balance = $this->opening_balance + $totalDebit - $totalCredit;
        }
        // Liabilities, Equity, and Revenue naturally carry credit balances. (Cr increases, Dr decreases)
        else {
            $this->current_balance = $this->opening_balance + $totalCredit - $totalDebit;
        }

        $this->save();
    }

    /**
     * Compute balance as of a specific date.
     */
    public function getBalanceAsOf($date)
    {
        $postedItemsQuery = JournalItem::where('account_id', $this->id)
            ->whereHas('entry', function ($query) use ($date) {
                $query->where('status', 'posted')
                    ->whereDate('date', '<=', $date);
            });

        $totalDebit = $postedItemsQuery->sum('debit') ?? 0;
        $totalCredit = $postedItemsQuery->sum('credit') ?? 0;

        if (in_array($this->type, ['asset', 'expense'])) {
            return $this->opening_balance + $totalDebit - $totalCredit;
        } else {
            return $this->opening_balance + $totalCredit - $totalDebit;
        }
    }

    /**
     * Compute activity balance for a specific period.
     */
    public function getBalanceForPeriod($startDate, $endDate)
    {
        $postedItemsQuery = JournalItem::where('account_id', $this->id)
            ->whereHas('entry', function ($query) use ($startDate, $endDate) {
                $query->where('status', 'posted')
                    ->whereDate('date', '>=', $startDate)
                    ->whereDate('date', '<=', $endDate);
            });

        $totalDebit = $postedItemsQuery->sum('debit') ?? 0;
        $totalCredit = $postedItemsQuery->sum('credit') ?? 0;

        // For Profit & Loss accounts, we usually only care about the net movement in typical ledger logic.
        if (in_array($this->type, ['revenue', 'expense'])) {
            if ($this->type == 'expense') {
                return $totalDebit - $totalCredit;
            } else {
                return $totalCredit - $totalDebit;
            }
        }

        // For Balance Sheet accounts (if ever used in P&L context), we might need logic,
        // but traditionally P&L is for Income/Expense.
        if (in_array($this->type, ['asset', 'liability', 'equity'])) {
            if (in_array($this->type, ['asset'])) {
                return $totalDebit - $totalCredit;
            } else {
                return $totalCredit - $totalDebit;
            }
        }

        return 0;
    }
}
