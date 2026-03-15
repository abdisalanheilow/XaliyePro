@extends('admin.admin_master')
@php use Illuminate\Support\Str; @endphp
@section('admin')
<div class="px-8 py-6">
    <!-- Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Process Reconciliation</h1>
            <p class="text-gray-500 mt-1">Match bank statement lines with system ledger entries</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-3 py-1 bg-white border border-gray-200 rounded-lg text-sm font-bold text-gray-700">
                STMT: {{ $statement->statement_no }}
            </span>
            <a href="{{ route('accounting.reconciliation.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all">
               <i data-lucide="arrow-left" class="w-4 h-4"></i>
               Back
            </a>
        </div>
    </div>

    <!-- Statement Info Bar -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-center">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">
                    <i data-lucide="landmark" class="w-6 h-6"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Bank Account</p>
                    <p class="text-sm font-bold text-gray-900">{{ $statement->account->name }}</p>
                </div>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Statement Period</p>
                <p class="text-sm font-bold text-gray-900">{{ $statement->start_date->format('d M Y') }} - {{ $statement->end_date->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Balances</p>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-400">Op:</span>
                    <span class="text-sm font-bold text-gray-700">{{ number_format($statement->opening_balance, 2) }}</span>
                    <span class="text-xs text-gray-400">Cl:</span>
                    <span class="text-sm font-bold text-[#28A375]">{{ number_format($statement->closing_balance, 2) }}</span>
                </div>
            </div>
            <div class="text-right">
                <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-tighter {{ $statement->status == 'reconciled' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                    {{ $statement->status }}
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left: Bank Lines -->
        <div class="lg:col-span-6 bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                    <i data-lucide="list" class="w-4 h-4 text-[#28A375]"></i>
                    Statement Lines
                </h3>
                <span class="text-xs font-bold text-gray-500">{{ $statement->lines->where('is_reconciled', false)->count() }} to match</span>
            </div>
            <div class="overflow-y-auto" style="max-height: 500px;">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100 sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 uppercase">Description</th>
                            <th class="px-6 py-3 text-right text-[10px] font-bold text-gray-500 uppercase">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($statement->lines as $line)
                        <tr class="{{ $line->is_reconciled ? 'bg-gray-50/50 opacity-60' : 'cursor-pointer hover:bg-[#28A375]/5 group' }}"
                            @if (!$line->is_reconciled) onclick="selectBankLine({{ $line->id }}, {{ $line->amount }}, this)" @endif>
                            <td class="px-6 py-4 text-xs font-medium text-gray-500">{{ $line->date->format('d M') }}</td>
                            <td class="px-6 py-4">
                                <p class="text-xs font-bold text-gray-900 group-hover:text-[#28A375]">{{ $line->description }}</p>
                                <p class="text-[10px] text-gray-400 font-mono">{{ $line->reference }}</p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-bold {{ $line->amount >= 0 ? 'text-[#28A375]' : 'text-red-500' }}">
                                    {{ $line->amount >= 0 ? '+' : '' }}{{ number_format($line->amount, 2) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: System Transactions -->
        <div class="lg:col-span-6 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                        <i data-lucide="cpu" class="w-4 h-4 text-blue-500"></i>
                        System Transactions
                    </h3>
                    <span class="text-xs font-bold text-gray-500">{{ $unreconciledItems->count() }} available</span>
                </div>
                <div class="overflow-y-auto" style="max-height: 400px;">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-100 sticky top-0 z-10">
                            <tr>
                                <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 uppercase">Entry Ref</th>
                                <th class="px-6 py-3 text-right text-[10px] font-bold text-gray-500 uppercase">D/C</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($unreconciledItems as $item)
                            <tr class="cursor-pointer hover:bg-blue-50 group" onclick="selectSystemItem({{ $item->id }}, {{ $item->credit > 0 ? -$item->credit : $item->debit }}, this)">
                                <td class="px-6 py-4 text-xs font-medium text-gray-500">{{ $item->entry->date->format('d M') }}</td>
                                <td class="px-6 py-4">
                                    <p class="text-xs font-bold text-gray-900 group-hover:text-blue-600">{{ $item->entry->reference }}</p>
                                    <p class="text-[10px] text-gray-400">{{ \Illuminate\Support\Str::limit($item->entry->description, 20) }}</p>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @php $amt = $item->credit > 0 ? -$item->credit : $item->debit; @endphp
                                    <span class="text-sm font-bold {{ $amt >= 0 ? 'text-[#28A375]' : 'text-red-500' }}">
                                        {{ $amt >= 0 ? '+' : '' }}{{ number_format($amt, 2) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Match Card -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6 relative overflow-hidden ring-4 ring-gray-50">
                <div class="flex flex-col gap-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-3 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                            <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Bank Amount</p>
                            <p id="bankDisplay" class="text-lg font-bold text-gray-300">Select Line</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                            <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">System Amount</p>
                            <p id="systemDisplay" class="text-lg font-bold text-gray-300">Select Entry</p>
                        </div>
                    </div>

                    <form action="{{ route('accounting.reconciliation.reconcile', $statement->id) }}" method="POST" id="reconcileForm">
                        @csrf
                        <input type="hidden" name="line_id" id="selectedLineId">
                        <input type="hidden" name="journal_item_id" id="selectedItemId">
                        
                        <div id="diffAlert" class="mb-4 hidden">
                            <div class="p-3 bg-red-50 border border-red-100 rounded-xl flex items-center gap-2 text-xs font-bold text-red-600">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                Amounts do not match exactly.
                            </div>
                        </div>

                        <button type="submit" id="reconcileBtn" disabled
                                class="w-full py-3 bg-gray-100 text-gray-400 rounded-xl text-sm font-bold transition-all flex items-center justify-center gap-2 cursor-not-allowed">
                            <i data-lucide="link" class="w-4 h-4"></i>
                            Reconcile Match
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let selectedLineId = null;
    let selectedItemId = null;
    let bankAmount = 0;
    let systemAmount = 0;

    function selectBankLine(id, amount, el) {
        document.querySelectorAll('tr').forEach(r => r.classList.remove('bg-[#28A375]/10', 'ring-1', 'ring-[#28A375]'));
        
        selectedLineId = id;
        bankAmount = amount;
        el.classList.add('bg-[#28A375]/10', 'ring-1', 'ring-[#28A375]');
        
        document.getElementById('selectedLineId').value = id;
        const bankDisp = document.getElementById('bankDisplay');
        bankDisp.innerText = (amount >= 0 ? '+' : '') + amount.toLocaleString(undefined, {minimumFractionDigits: 2});
        bankDisp.classList.remove('text-gray-300');
        bankDisp.classList.add('text-gray-900');
        
        validateMatch();
    }

    function selectSystemItem(id, amount, el) {
        document.querySelectorAll('.lg\:col-span-6 tr').forEach(r => {
             if (r.contains(el)) el.classList.add('bg-blue-50', 'ring-1', 'ring-blue-500');
             else if (r.closest('.lg\:col-span-6')) r.classList.remove('bg-blue-50', 'ring-1', 'ring-blue-500');
        });

        // Simplified row targeting for system side
        const systemRows = document.querySelectorAll('.lg\:col-span-6 table tbody tr');
        systemRows.forEach(r => r.classList.remove('bg-blue-50', 'ring-1', 'ring-blue-500'));
        el.classList.add('bg-blue-50', 'ring-1', 'ring-blue-500');
        
        selectedItemId = id;
        systemAmount = amount;
        
        document.getElementById('selectedItemId').value = id;
        const sysDisp = document.getElementById('systemDisplay');
        sysDisp.innerText = (amount >= 0 ? '+' : '') + amount.toLocaleString(undefined, {minimumFractionDigits: 2});
        sysDisp.classList.remove('text-gray-300');
        sysDisp.classList.add('text-gray-900');
        
        validateMatch();
    }

    function validateMatch() {
        const btn = document.getElementById('reconcileBtn');
        const alert = document.getElementById('diffAlert');
        
        if (selectedLineId && selectedItemId) {
            if (Math.abs(bankAmount - systemAmount) < 0.01) {
                btn.disabled = false;
                btn.classList.remove('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');
                btn.classList.add('bg-[#28A375]', 'text-white', 'shadow-lg', 'shadow-green-100', 'hover:bg-[#229967]');
                alert.classList.add('hidden');
            } else {
                btn.disabled = true;
                btn.classList.add('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');
                btn.classList.remove('bg-[#28A375]', 'text-white', 'shadow-lg');
                alert.classList.remove('hidden');
            }
        }
    }
</script>
@endsection
