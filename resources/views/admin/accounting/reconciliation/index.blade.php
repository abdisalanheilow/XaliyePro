@extends('admin.admin_master')
@section('admin')
<div class="px-8 py-6">
    <!-- Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Bank Reconciliation</h1>
            <p class="text-gray-500 mt-1">Match bank statements with your general ledger transactions</p>
        </div>
        <a href="{{ route('accounting.reconciliation.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-medium hover:bg-[#229967] transition-all shadow-sm">
            <i data-lucide="plus" class="w-4 h-4"></i>
            New Bank Statement
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-sm text-gray-500 mb-1">Total Statements</p>
                    <h3 class="text-2xl font-bold text-gray-900">{{ $statements->total() }}</h3>
                </div>
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-sm text-gray-500 mb-1">Awaiting Reconciliation</p>
                    <h3 class="text-2xl font-bold text-orange-600">{{ $statements->where('status', 'draft')->count() }}</h3>
                </div>
                <div class="w-10 h-10 bg-orange-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i data-lucide="clock" class="w-5 h-5 text-orange-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-sm text-gray-500 mb-1">Fully Reconciled</p>
                    <h3 class="text-2xl font-bold text-[#28A375]">{{ $statements->where('status', 'reconciled')->count() }}</h3>
                </div>
                <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i data-lucide="check-circle" class="w-5 h-5 text-[#28A375]"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-sm text-gray-500 mb-1">Bank Accounts</p>
                    <h3 class="text-2xl font-bold text-purple-600">{{ $statements->pluck('account_id')->unique()->count() }}</h3>
                </div>
                <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i data-lucide="landmark" class="w-5 h-5 text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Statements Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900">Recent Bank Statements</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Bank Account</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Statement No</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Period</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Opening Balance</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Closing Balance</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @if(count($statements) > 0)
                        @foreach ($statements as $statement)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-xs uppercase">
                                    {{ substr($statement->account->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $statement->account->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $statement->account->code }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $statement->statement_no }}</td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-600">{{ $statement->start_date->format('j M Y') }} - {{ $statement->end_date->format('j M Y') }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-900 text-right">
                            {{ number_format($statement->opening_balance, 2) }}
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-900 text-right">
                            {{ number_format($statement->closing_balance, 2) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if ($statement->status == 'reconciled')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                    Fully Reconciled
                                </span>
                            @elseif ($statement->status == 'partial')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-800">
                                    Partial
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800">
                                    Draft
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('accounting.reconciliation.show', $statement->id) }}" 
                                class="inline-flex items-center gap-1 text-sm font-bold text-[#28A375] hover:text-[#229967] transition-colors">
                                <i data-lucide="arrow-right-circle" class="w-4 h-4"></i>
                                Reconcile
                            </a>
                        </td>
                    </tr>
                        @endforeach
                    @else
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <i data-lucide="inbox" class="w-12 h-12 text-gray-300"></i>
                                <p class="text-gray-500 font-medium">No reconciliation statements found</p>
                                <a href="{{ route('accounting.reconciliation.create') }}" class="text-[#28A375] text-sm font-bold hover:underline">Start your first reconciliation</a>
                            </div>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        @if ($statements->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            {{ $statements->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
