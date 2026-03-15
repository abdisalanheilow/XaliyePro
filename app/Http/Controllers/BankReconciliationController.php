<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\BankStatement;
use App\Models\BankStatementLine;
use App\Models\JournalItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BankReconciliationController extends Controller
{
    public function index(): View
    {
        $statements = BankStatement::with(['account'])->latest()->paginate(10);

        return view('admin.accounting.reconciliation.index', compact('statements'));
    }

    public function create(): View
    {
        $bankAccounts = Account::where('sub_type', 'Bank and Cash')->get();

        return view('admin.accounting.reconciliation.create', compact('bankAccounts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'statement_no' => 'required|unique:bank_statements,statement_no',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'opening_balance' => 'required|numeric',
            'closing_balance' => 'required|numeric',
        ]);

        /** @var \App\Models\BankStatement $statement */
        $statement = BankStatement::create($request->all() + ['created_by' => Auth::id()]);

        return redirect()->route('accounting.reconciliation.show', $statement->id);
    }

    public function show($id): View
    {
        /** @var \App\Models\BankStatement $statement */
        $statement = BankStatement::with(['account', 'lines.journalItem.entry'])->findOrFail($id);

        // Fetch unreconciled journal items for this account
        $unreconciledItems = JournalItem::with(['entry'])
            ->where('account_id', $statement->account_id)
            ->where('is_reconciled', false)
            ->whereHas('entry', function ($q) use ($statement) {
                $q->where('status', 'posted')
                    ->whereDate('date', '<=', $statement->end_date);
            })
            ->get();

        return view('admin.accounting.reconciliation.show', compact('statement', 'unreconciledItems'));
    }

    public function reconcile(Request $request, $id): RedirectResponse
    {
        $statement = BankStatement::findOrFail($id);
        $journalItemId = $request->journal_item_id;
        $lineId = $request->line_id;

        DB::transaction(function () use ($statement, $journalItemId, $lineId) {
            $journalItem = JournalItem::findOrFail($journalItemId);
            $journalItem->update([
                'is_reconciled' => true,
                'reconciled_at' => now(),
                'bank_statement_ref' => $statement->statement_no,
            ]);

            if ($lineId) {
                BankStatementLine::where('id', $lineId)->update([
                    'is_reconciled' => true,
                    'journal_item_id' => $journalItemId,
                ]);
            }
        });

        return back()->with('success', 'Transaction reconciled successfully.');
    }
}
