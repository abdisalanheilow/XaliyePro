<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalItem;
use App\Services\AccountingService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AccountingController extends Controller
{
    public function chartOfAccounts(Request $request): View
    {
        $query = Account::query();

        // Search logic
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->whereNested(function (Builder $q) use ($searchTerm) {
                $q->where('code', 'like', "%{$searchTerm}%")
                    ->orWhere('name', 'like', "%{$searchTerm}%")
                    ->orWhere('sub_type', 'like', "%{$searchTerm}%");
            });
        }

        // Type filter
        if ($request->has('type') && $request->type != 'All Account Types') {
            $typeMap = [
                'Assets' => 'asset',
                'Liabilities' => 'liability',
                'Equity' => 'equity',
                'Revenue' => 'revenue',
                'Expenses' => 'expense',
            ];
            if (isset($typeMap[$request->type])) {
                $query->where('type', $typeMap[$request->type]);
            }
        }

        // Status filter
        if ($request->has('status') && $request->status != 'All Status') {
            $query->where('status', strtolower($request->status));
        }

        $accounts = $query->get();
        $parentAccounts = Account::whereNull('parent_id')->get();

        // Stats should remain based on all accounts or current filtered set?
        // Usually, stats cards show global totals.
        $allAccounts = Account::all();
        $stats = [
            'total' => $allAccounts->count(),
            'assets' => $allAccounts->where('type', 'asset')->count(),
            'liabilities' => $allAccounts->where('type', 'liability')->count(),
            'equity' => $allAccounts->where('type', 'equity')->count(),
            'revenue' => $allAccounts->where('type', 'revenue')->count(),
            'expenses' => $allAccounts->where('type', 'expense')->count(),
        ];

        return view('admin.accounting.chart_of_accounts', compact('accounts', 'stats', 'parentAccounts'));
    }

    public function storeAccount(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|unique:accounts,code',
            'name' => 'required|string|max:255',
            'type' => 'required|in:asset,liability,equity,revenue,expense',
            'sub_type' => 'required|string',
            'parent_id' => 'nullable|exists:accounts,id',
            'opening_balance' => 'nullable|numeric',
            'currency' => 'required|string',
            'is_tax_account' => 'boolean',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        $validated['current_balance'] = $validated['opening_balance'] ?? 0;
        $validated['is_tax_account'] = $request->has('is_tax_account');

        Account::create($validated);

        return redirect()->route('accounting.accounts.index')
            ->with('message', 'Account created successfully.')
            ->with('title', 'Account Created')
            ->with('alert-type', 'success');
    }

    public function updateAccount(Request $request, $id): RedirectResponse
    {
        $account = Account::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|unique:accounts,code,'.$id,
            'name' => 'required|string|max:255',
            'type' => 'required|in:asset,liability,equity,revenue,expense',
            'sub_type' => 'required|string',
            'parent_id' => 'nullable|exists:accounts,id',
            'opening_balance' => 'nullable|numeric',
            'currency' => 'required|string',
            'is_tax_account' => 'boolean',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        $validated['is_tax_account'] = $request->has('is_tax_account');

        // Note: We might want to adjust current_balance if opening_balance changes,
        // but typically opening balance shouldn't change after transactions exist.
        // For now, let's just update the fields.
        $account->update($validated);

        return redirect()->route('accounting.accounts.index')
            ->with('message', 'Account updated successfully.')
            ->with('title', 'Account Updated')
            ->with('alert-type', 'success');
    }

    public function deleteAccount($id): RedirectResponse
    {
        $account = Account::findOrFail($id);

        // Check if account has children
        if ($account->children()->count() > 0) {
            return redirect()->back()
                ->with('message', 'Cannot delete account with sub-accounts.')
                ->with('alert-type', 'error');
        }

        // Check if it has journal items (already used in transactions)
        if ($account->journalItems()->count() > 0) {
            return redirect()->back()
                ->with('message', 'Cannot delete account with transaction history.')
                ->with('alert-type', 'error');
        }

        $account->delete();

        return redirect()->route('accounting.accounts.index')
            ->with('message', 'Account deleted successfully.')
            ->with('title', 'Account Deleted')
            ->with('alert-type', 'success');
    }

    public function generalLedger(Request $request): View
    {
        $query = JournalItem::with(['entry', 'account']);

        // Date filters
        if ($request->has('from_date') && $request->from_date != '') {
            $query->whereHas('entry', function (Builder $q) use ($request) {
                $q->where('date', '>=', $request->from_date);
            });
        }
        if ($request->has('to_date') && $request->to_date != '') {
            $query->whereHas('entry', function (Builder $q) use ($request) {
                $q->where('date', '<=', $request->to_date);
            });
        }

        // Account filter
        if ($request->has('account_id') && $request->account_id != 'All Accounts' && $request->account_id != '') {
            $query->where('account_id', $request->account_id);
        }

        $entries = $query->join('journal_entries', 'journal_items.journal_entry_id', '=', 'journal_entries.id')
            ->orderBy('journal_entries.date', 'desc')
            ->select('journal_items.*')
            ->get();

        // Stats calculation
        $stats = [
            'total_debit' => $entries->sum('debit'),
            'total_credit' => $entries->sum('credit'),
            'transaction_count' => $entries->unique('journal_entry_id')->count(),
            'active_accounts' => $entries->unique('account_id')->count(),
        ];

        $accounts = Account::orderBy('code')->get();

        return view('admin.accounting.general_ledger', compact('entries', 'stats', 'accounts'));
    }

    public function journalEntries(Request $request): View
    {
        $query = JournalEntry::with(['items.account', 'user'])->orderBy('date', 'desc');

        if ($request->search) {
            $search = $request->search;
            $query->whereNested(function (Builder $q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->status && $request->status !== 'All Status') {
            $query->where('status', strtolower($request->status));
        }

        $journalEntries = $query->paginate(15)->withQueryString();

        $allEntries = JournalEntry::all();
        $stats = [
            'total' => $allEntries->count(),
            'posted' => $allEntries->where('status', 'posted')->count(),
            'draft' => $allEntries->where('status', 'draft')->count(),
            'total_amount' => $allEntries->sum('total_amount'),
        ];

        $accounts = Account::where('status', 'active')->orderBy('code')->get();

        return view('admin.accounting.journal_entries', compact('journalEntries', 'stats', 'accounts'));
    }

    public function storeJournalEntry(Request $request): RedirectResponse
    {
        $request->validate([
            'date' => 'required|date',
            'description' => 'required|string',
            'status' => 'required|in:draft,posted',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:accounts,id',
            'lines.*.debit' => 'required|numeric|min:0',
            'lines.*.credit' => 'required|numeric|min:0',
        ]);

        try {
            $accountingService = app(AccountingService::class);
            $accountingService->createJournalEntry($request->all());

            return redirect()->route('accounting.journal.index')
                ->with('message', 'Journal entry created successfully.')
                ->with('title', 'Entry Created')
                ->with('alert-type', 'success');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function showJournalEntry($id): JsonResponse
    {
        $entry = JournalEntry::with(['items.account', 'user'])->findOrFail($id);

        return response()->json($entry);
    }

    public function updateJournalEntry(Request $request, $id): RedirectResponse
    {
        $entry = JournalEntry::findOrFail($id);

        $request->validate([
            'date' => 'required|date',
            'description' => 'required|string',
            'status' => 'required|in:draft,posted',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:accounts,id',
            'lines.*.debit' => 'required|numeric|min:0',
            'lines.*.credit' => 'required|numeric|min:0',
        ]);

        $totalDebit = collect($request->lines)->sum('debit');
        $totalCredit = collect($request->lines)->sum('credit');

        if (round($totalDebit, 2) !== round($totalCredit, 2)) {
            return back()->withErrors(['balance' => 'Debits and credits must balance.'])->withInput();
        }

        $entry->update([
            'date' => $request->date,
            'description' => $request->description,
            'status' => $request->status,
            'total_amount' => $totalDebit,
        ]);

        try {
            $accountingService = app(AccountingService::class);
            $accountingService->updateJournalEntry($entry, $request->all());

            return redirect()->route('accounting.journal.index')
                ->with('message', 'Journal entry updated successfully.')
                ->with('title', 'Entry Updated')
                ->with('alert-type', 'success');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }

    }

    public function deleteJournalEntry($id): RedirectResponse
    {
        $entry = JournalEntry::findOrFail($id);
        try {
            $accountingService = app(AccountingService::class);
            $accountingService->deleteJournalEntry($entry);

            return redirect()->route('accounting.journal.index')
                ->with('message', 'Journal entry deleted successfully.')
                ->with('title', 'Entry Deleted')
                ->with('alert-type', 'success');
        } catch (\Exception $e) {
            return redirect()->route('accounting.journal.index')
                ->with('message', 'Error deleting entry: '.$e->getMessage())
                ->with('alert-type', 'error');
        }

    }
}
