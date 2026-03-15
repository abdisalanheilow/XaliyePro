@extends('admin.admin_master')
@section('admin')
    <div x-data="journalApp()">

        @section('title')
            Journal Entries - XaliyePro
        @endsection

        {{-- Page Header --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Journal Entries</h1>
                <p class="text-gray-500 mt-1">Create and manage manual journal entries</p>
            </div>
            <button @click="openAddModal()"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-medium hover:bg-[#229967] transition-all">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Add New Entry
            </button>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            @include('admin.partials.stats_card', [
                'title' => 'Total Entries',
                'value' => number_format($stats['total']),
                'icon' => 'pie-chart',
                'color' => '#28A375',
                'iconBg' => 'bg-[#28A375]',
                'iconShadow' => 'shadow-green-100',
                'subtitle' => 'All time'
            ])

            @include('admin.partials.stats_card', [
                'title' => 'Posted',
                'value' => number_format($stats['posted']),
                'icon' => 'check-circle',
                'color' => '#10B981',
                'iconBg' => 'bg-green-500',
                'iconShadow' => 'shadow-green-100',
                'subtitle' => ($stats['total'] > 0 ? round($stats['posted'] / $stats['total'] * 100, 1) : 0) . '% of total'
            ])

            @include('admin.partials.stats_card', [
                'title' => 'Draft',
                'value' => number_format($stats['draft']),
                'icon' => 'edit',
                'color' => '#F59E0B',
                'iconBg' => 'bg-yellow-500',
                'iconShadow' => 'shadow-yellow-100',
                'subtitle' => 'Pending review'
            ])

            @include('admin.partials.stats_card', [
                'title' => 'Total Amount',
                'value' => '$' . number_format($stats['total_amount'], 0),
                'icon' => 'dollar-sign',
                'color' => '#3B82F6',
                'iconBg' => 'bg-blue-500',
                'iconShadow' => 'shadow-blue-100',
                'subtitle' => 'Debit / Credit'
            ])
        </div>

        {{-- Search & Filter --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <form action="{{ route('accounting.journal.index') }}" method="GET"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="lg:col-span-2">
                    <div class="relative">
                        <i data-lucide="search" class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search by reference, description..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                    </div>
                </div>
                <select name="status" onchange="this.form.submit()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                    <option value="All Status" {{ request('status', 'All Status') == 'All Status' ? 'selected' : '' }}>All
                        Status
                    </option>
                    <option value="Posted" {{ request('status') == 'Posted' ? 'selected' : '' }}>Posted</option>
                    <option value="Draft" {{ request('status') == 'Draft' ? 'selected' : '' }}>Draft</option>
                </select>
                <button type="submit"
                    class="px-4 py-2 bg-[#28A375] text-white rounded-lg text-sm font-medium hover:bg-[#229967] transition-all">
                    Search
                </button>
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">All Journal Entries</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Reference</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Created By</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @if ($journalEntries->count() > 0)
                            @foreach ($journalEntries as $entry)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $entry->date->format('Y-m-d') }}</td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-mono font-semibold text-blue-600">{{ $entry->reference }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">{{ $entry->description }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900 text-right">
                                    ${{ number_format($entry->total_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $entry->user->name ?? 'System' }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    {{ $entry->status === 'posted' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($entry->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="viewEntry({{ $entry->id }})"
                                            class="p-1.5 text-gray-400 hover:text-blue-600 transition-colors" title="View">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </button>
                                        <button
                                            @click="editEntry({{ $entry->id }}, '{{ $entry->date->format('Y-m-d') }}', '{{ addslashes($entry->description) }}', '{{ $entry->status }}', {{ $entry->items->toJson() }})"
                                            class="p-1.5 text-gray-400 hover:text-[#28A375] transition-colors" title="Edit">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </button>
                                            <button type="button" @click="window.confirmDelete('{{ route('accounting.journal.destroy', $entry->id) }}')"
                                                class="p-1.5 text-gray-400 hover:text-red-600 transition-colors" title="Delete">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                    <div class="flex flex-col items-center gap-2">
                                        <i data-lucide="inbox" class="w-8 h-8 text-gray-300"></i>
                                        <p>No journal entries found.</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($journalEntries->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-sm text-gray-500">
                        Showing <span class="font-semibold text-gray-900">{{ $journalEntries->firstItem() }}</span>
                        to <span class="font-semibold text-gray-900">{{ $journalEntries->lastItem() }}</span>
                        of <span class="font-semibold text-gray-900">{{ $journalEntries->total() }}</span> results
                    </div>
                    <div class="flex items-center gap-2">
                        @if ($journalEntries->onFirstPage())
                            <button
                                class="px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-400 bg-white cursor-not-allowed"
                                disabled>Previous</button>
                        @else
                            <a href="{{ $journalEntries->previousPageUrl() }}"
                                class="px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Previous</a>
                        @endif
                        @foreach ($journalEntries->getUrlRange(1, $journalEntries->lastPage()) as $page => $url)
                            <a href="{{ $url }}"
                                class="px-3 py-2 rounded-lg text-sm font-medium {{ $page == $journalEntries->currentPage() ? 'bg-[#28A375] text-white' : 'border border-gray-300 text-gray-700 bg-white hover:bg-gray-50' }}">{{ $page }}</a>
                        @endforeach
                        @if ($journalEntries->hasMorePages())
                            <a href="{{ $journalEntries->nextPageUrl() }}"
                                class="px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Next</a>
                        @else
                            <button
                                class="px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-400 bg-white cursor-not-allowed"
                                disabled>Next</button>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{---------- ADD MODAL ----------}}
        <div x-show="showAdd" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-md" @click="showAdd = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div
                    class="inline-block align-middle bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-4xl sm:w-full border border-gray-100">
                    <div
                        class="px-8 py-4 border-b border-gray-100 flex items-center justify-between bg-white sticky top-0 z-10">
                        <h3 class="text-xl font-bold text-gray-900">Add New Journal Entry</h3>
                        <button @click="showAdd = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i data-lucide="x" class="w-6 h-6"></i>
                        </button>
                    </div>
                    <form id="addJournalForm" action="{{ route('accounting.journal.store') }}" method="POST"
                        @submit="isSaving = true">
                        @csrf
                        <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto custom-scrollbar">
                            {{-- Top fields --}}
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Entry Date <span
                                            class="text-red-500">*</span></label>
                                    <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Reference</label>
                                    <input type="text" placeholder="Auto-generated" readonly
                                        class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status <span
                                            class="text-red-500">*</span></label>
                                    <select name="status" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent outline-none">
                                        <option value="draft">Draft</option>
                                        <option value="posted">Posted</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description <span
                                        class="text-red-500">*</span></label>
                                <textarea name="description" rows="2" required
                                    placeholder="Enter journal entry description..."
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent resize-none"></textarea>
                            </div>

                            {{-- Journal Lines --}}
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-semibold text-gray-900">Journal Lines</h4>
                                    <button type="button" @click="addLine('addLinesTable')"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-medium hover:bg-blue-700 transition-all">
                                        <i data-lucide="plus" class="w-3 h-3"></i> Add Line
                                    </button>
                                </div>
                                <div class="border border-gray-200 rounded-2xl overflow-hidden">
                                    <table class="w-full">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600">
                                                    Account</th>
                                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600">
                                                    Description</th>
                                                <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-600">Debit
                                                </th>
                                                <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-600">
                                                    Credit</th>
                                                <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-600">
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="addLinesTable" class="divide-y divide-gray-100">
                                            <tr x-html="lineRowHTML(0, 'add')"></tr>
                                            <tr x-html="lineRowHTML(1, 'add')"></tr>
                                        </tbody>
                                        <tfoot class="bg-gray-50 font-semibold border-t border-gray-200">
                                            <tr>
                                                <td colspan="2" class="px-4 py-2.5 text-sm text-gray-700">Total</td>
                                                <td class="px-4 py-2.5 text-sm text-right" id="addTotalDebit">$0.00</td>
                                                <td class="px-4 py-2.5 text-sm text-right" id="addTotalCredit">$0.00</td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div id="addBalanceAlert"
                                    class="mt-2 bg-green-50 border border-green-200 rounded-xl p-3 text-sm text-green-800 flex items-center gap-2">
                                    <i data-lucide="check-circle" class="w-4 h-4 flex-shrink-0"></i>
                                    Entry is perfectly balanced. Debits equal credits.
                                </div>
                            </div>
                        </div>

                        <div class="px-6 pb-6 flex gap-4 pt-4 border-t border-gray-50">
                            <button type="submit" :disabled="isSaving"
                                class="flex-1 px-6 py-3 bg-[#28A375] text-white rounded-lg text-sm font-bold hover:bg-[#229967] transition-all flex items-center justify-center gap-2">
                                <span x-show="!isSaving" class="flex items-center gap-2">
                                    <i data-lucide="save" class="w-4 h-4"></i> Save Entry
                                </span>
                                <span x-show="isSaving" class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-white" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Saving...
                                </span>
                            </button>
                            <button type="button" @click="showAdd = false" :disabled="isSaving"
                                class="flex-1 px-6 py-3 border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{---------- VIEW MODAL ----------}}
        <div x-show="showView" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-md" @click="showView = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div
                    class="inline-block align-middle bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-2xl sm:w-full border border-gray-100">
                    <div class="px-8 py-4 border-b border-gray-100 flex items-center justify-between bg-white">
                        <h3 class="text-xl font-bold text-gray-900">Journal Entry Details</h3>
                        <button @click="showView = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i data-lucide="x" class="w-6 h-6"></i>
                        </button>
                    </div>
                    <div class="p-8 max-h-[80vh] overflow-y-auto custom-scrollbar" x-show="viewData">
                        <div class="bg-gray-50 rounded-2xl p-5 mb-6 grid grid-cols-3 gap-4">
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Reference</p>
                                <p class="text-sm font-bold text-gray-900" x-text="viewData?.reference"></p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Date</p>
                                <p class="text-sm font-bold text-gray-900" x-text="viewData?.date"></p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Status</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold"
                                    :class="viewData?.status === 'posted' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
                                    x-text="viewData?.status ? (viewData.status.charAt(0).toUpperCase() + viewData.status.slice(1)) : ''"></span>
                            </div>
                        </div>
                        <div class="mb-6">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Description</p>
                            <p class="text-sm text-gray-600 leading-relaxed" x-text="viewData?.description"></p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Journal Lines</p>
                            <div class="border border-gray-100 rounded-xl overflow-hidden">
                                <table class="w-full">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500">Account</th>
                                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-500">Debit</th>
                                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-500">Credit</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <template x-for="item in viewData?.items" :key="item.id">
                                            <tr>
                                                <td class="px-4 py-3">
                                                    <div class="text-sm font-medium text-gray-900"
                                                        x-text="item.account?.code + ' - ' + item.account?.name"></div>
                                                    <div class="text-xs text-gray-500" x-text="item.account?.sub_type">
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-sm font-semibold text-right"
                                                    x-text="item.debit > 0 ? window.ERP_CONFIG.currency_symbol + parseFloat(item.debit).toLocaleString(undefined, {minimumFractionDigits:window.ERP_CONFIG.decimal_precision}) : '-'">
                                                </td>
                                                <td class="px-4 py-3 text-sm font-semibold text-right"
                                                    x-text="item.credit > 0 ? window.ERP_CONFIG.currency_symbol + parseFloat(item.credit).toLocaleString(undefined, {minimumFractionDigits:window.ERP_CONFIG.decimal_precision}) : '-'">
                                                </td>
                                            </tr>
                                        </template>
                                        <tr class="bg-gray-50 font-bold">
                                            <td class="px-4 py-3 text-sm text-gray-900">Total</td>
                                            <td class="px-4 py-3 text-sm text-gray-900 text-right"
                                                x-text="window.ERP_CONFIG.currency_symbol + (viewData?.total_amount ? parseFloat(viewData.total_amount).toLocaleString(undefined, {minimumFractionDigits:window.ERP_CONFIG.decimal_precision}) : '0.00')">
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-900 text-right"
                                                x-text="window.ERP_CONFIG.currency_symbol + (viewData?.total_amount ? parseFloat(viewData.total_amount).toLocaleString(undefined, {minimumFractionDigits:window.ERP_CONFIG.decimal_precision}) : '0.00')">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="flex gap-4 pt-6 mt-6 border-t border-gray-50">
                            <button @click="showView = false"
                                class="flex-1 px-6 py-3 border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50">Close</button>
                            <button
                                class="flex-1 px-6 py-3 bg-[#28A375] text-white rounded-lg text-sm font-bold hover:bg-[#229967] flex items-center justify-center gap-2">
                                <i data-lucide="printer" class="w-4 h-4"></i> Print
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{---------- EDIT MODAL ----------}}
        <div x-show="showEdit" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-md" @click="showEdit = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div
                    class="inline-block align-middle bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-4xl sm:w-full border border-gray-100">
                    <div
                        class="px-8 py-4 border-b border-gray-100 flex items-center justify-between bg-white sticky top-0 z-10">
                        <h3 class="text-xl font-bold text-gray-900">Edit Journal Entry</h3>
                        <button @click="showEdit = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i data-lucide="x" class="w-6 h-6"></i>
                        </button>
                    </div>
                    <form :action="'{{ route('accounting.journal.update', ':id') }}'.replace(':id', editId)" method="POST"
                        @submit="isSaving = true">
                        @csrf @method('PUT')
                        <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto custom-scrollbar">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Entry Date <span
                                            class="text-red-500">*</span></label>
                                    <input type="date" name="date" :value="editDate" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Reference</label>
                                    <input type="text" :value="editRef" readonly
                                        class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status <span
                                            class="text-red-500">*</span></label>
                                    <select name="status" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent outline-none">
                                        <option value="draft" :selected="editStatus === 'draft'">Draft</option>
                                        <option value="posted" :selected="editStatus === 'posted'">Posted</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description <span
                                        class="text-red-500">*</span></label>
                                <textarea name="description" rows="2" required :value="editDescription"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent resize-none"></textarea>
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-semibold text-gray-900">Journal Lines</h4>
                                    <button type="button" @click="addLine('editLinesTable')"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-medium hover:bg-blue-700 transition-all">
                                        <i data-lucide="plus" class="w-3 h-3"></i> Add Line
                                    </button>
                                </div>
                                <div class="border border-gray-200 rounded-2xl overflow-hidden">
                                    <table class="w-full">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600">
                                                    Account</th>
                                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600">
                                                    Description</th>
                                                <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-600">Debit
                                                </th>
                                                <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-600">
                                                    Credit</th>
                                                <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-600">
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="editLinesTable" class="divide-y divide-gray-100"></tbody>
                                        <tfoot class="bg-gray-50 font-semibold border-t border-gray-200">
                                            <tr>
                                                <td colspan="2" class="px-4 py-2.5 text-sm text-gray-700">Total</td>
                                                <td class="px-4 py-2.5 text-sm text-right" id="editTotalDebit">$0.00</td>
                                                <td class="px-4 py-2.5 text-sm text-right" id="editTotalCredit">$0.00</td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div id="editBalanceAlert"
                                    class="mt-2 bg-green-50 border border-green-200 rounded-xl p-3 text-sm text-green-800 flex items-center gap-2">
                                    <i data-lucide="check-circle" class="w-4 h-4 flex-shrink-0"></i>
                                    Entry is perfectly balanced. Debits equal credits.
                                </div>
                            </div>
                        </div>
                        <div class="px-6 pb-6 flex gap-4 pt-4 border-t border-gray-50">
                            <button type="submit" :disabled="isSaving"
                                class="flex-1 px-6 py-3 bg-[#28A375] text-white rounded-lg text-sm font-bold hover:bg-[#229967] transition-all flex items-center justify-center gap-2">
                                <span x-show="!isSaving" class="flex items-center gap-2">
                                    <i data-lucide="save" class="w-4 h-4"></i> Update Entry
                                </span>
                                <span x-show="isSaving" class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-white" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Saving...
                                </span>
                            </button>
                            <button type="button" @click="showEdit = false" :disabled="isSaving"
                                class="flex-1 px-6 py-3 border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <script>
        const accountsList = @json($accounts->map(fn($a) => ['id' => $a->id, 'code' => $a->code, 'name' => $a->name]));

        function journalApp() {
            return {
                showAdd: false,
                showView: false,
                showEdit: false,
                isSaving: false,
                viewData: null,
                editId: null,
                editDate: '',
                editRef: '',
                editStatus: 'draft',
                editDescription: '',
                lineCount: 2,

                openAddModal() {
                    this.showAdd = true;
                    this.$nextTick(() => {
                        this.renderAddLines();
                        this.updateTotals('add');
                        if (typeof lucide !== 'undefined') lucide.createIcons();
                    });
                },

                renderAddLines() {
                    const tbody = document.getElementById('addLinesTable');
                    if (!tbody) return;
                    tbody.innerHTML = '';
                    for (let i = 0; i < this.lineCount; i++) {
                        const tr = document.createElement('tr');
                        tr.innerHTML = this.buildLineHTML(i, 'add');
                        tbody.appendChild(tr);
                    }
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                },

                buildLineHTML(index, prefix, prefill = {}) {
                    const accountOptions = accountsList.map(a =>
                        `<option value="${a.id}" ${prefill.account_id == a.id ? 'selected' : ''}>${a.code} - ${a.name}</option>`
                    ).join('');
                    return `
                            <td class="px-4 py-2.5">
                                <select name="lines[${index}][account_id]" required onchange="window.journalUpdateTotals && journalUpdateTotals('${prefix}')"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] outline-none">
                                    <option value="">Select Account</option>
                                    ${accountOptions}
                                </select>
                            </td>
                            <td class="px-4 py-2.5">
                                <input type="text" name="lines[${index}][description]" value="${prefill.description || ''}"
                                    placeholder="Line description"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375]">
                            </td>
                            <td class="px-4 py-2.5">
                                <input type="number" step="0.01" min="0" name="lines[${index}][debit]" value="${prefill.debit || '0.00'}" required
                                    placeholder="0.00" oninput="journalUpdateTotals('${prefix}')"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-right focus:ring-2 focus:ring-[#28A375]">
                            </td>
                            <td class="px-4 py-2.5">
                                <input type="number" step="0.01" min="0" name="lines[${index}][credit]" value="${prefill.credit || '0.00'}" required
                                    placeholder="0.00" oninput="journalUpdateTotals('${prefix}')"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-right focus:ring-2 focus:ring-[#28A375]">
                            </td>
                            <td class="px-4 py-2.5 text-center">
                                <button type="button" tabindex="-1" onclick="this.closest('tr').remove(); journalUpdateTotals('${prefix}')"
                                    class="p-1.5 text-gray-400 hover:text-red-600 transition-colors">
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </button>
                            </td>
                        `;
                },

                addLine(tableId, prefix) {
                    const tbody = document.getElementById(tableId);
                    const rows = tbody ? tbody.querySelectorAll('tr').length : 0;
                    const tr = document.createElement('tr');
                    const pfx = tableId.startsWith('add') ? 'add' : 'edit';
                    tr.innerHTML = this.buildLineHTML(rows, pfx);
                    tbody.appendChild(tr);
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                },

                updateTotals(prefix) { journalUpdateTotals(prefix); },

                viewEntry(id) {
                    fetch(`/accounting/journal-entries/${id}`)
                        .then(r => r.json())
                        .then(data => {
                            this.viewData = data;
                            this.showView = true;
                            this.$nextTick(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); });
                        });
                },

                editEntry(id, date, description, status, items) {
                    this.editId = id;
                    this.editDate = date;
                    this.editDescription = description;
                    this.editStatus = status;
                    this.showEdit = true;
                    this.$nextTick(() => {
                        const tbody = document.getElementById('editLinesTable');
                        if (!tbody) return;
                        tbody.innerHTML = '';
                        items.forEach((item, i) => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = this.buildLineHTML(i, 'edit', item);
                            tbody.appendChild(tr);
                        });
                        journalUpdateTotals('edit');
                        if (typeof lucide !== 'undefined') lucide.createIcons();
                    });
                },

                lineRowHTML(index, prefix) {
                    return this.buildLineHTML(index, prefix);
                }
            };
        }

        function journalUpdateTotals(prefix) {
            const form = prefix === 'add'
                ? document.getElementById('addJournalForm')
                : document.querySelector('[x-show="showEdit"]');
            if (!form) return;

            const debits = form ? form.querySelectorAll('[name*="[debit]"]') : [];
            const credits = form ? form.querySelectorAll('[name*="[credit]"]') : [];
            let totalD = 0, totalC = 0;
            debits.forEach(i => totalD += parseFloat(i.value) || 0);
            credits.forEach(i => totalC += parseFloat(i.value) || 0);

            const fmtD = document.getElementById(prefix + 'TotalDebit');
            const fmtC = document.getElementById(prefix + 'TotalCredit');
            const alert = document.getElementById(prefix + 'BalanceAlert');

            if (fmtD) fmtD.textContent = window.ERP_CONFIG.currency_symbol + totalD.toLocaleString(undefined, { minimumFractionDigits: window.ERP_CONFIG.decimal_precision });
            if (fmtC) fmtC.textContent = window.ERP_CONFIG.currency_symbol + totalC.toLocaleString(undefined, { minimumFractionDigits: window.ERP_CONFIG.decimal_precision });

            if (alert) {
                const diff = Math.abs(totalD - totalC);
                const balanced = diff < 0.01;

                alert.className = `mt-2 border rounded-xl p-3 text-sm flex items-center gap-2 ${balanced
                    ? 'bg-green-50 border-green-200 text-green-800'
                    : 'bg-red-50 border-red-200 text-red-800'}`;

                alert.innerHTML = balanced
                    ? '<i data-lucide="check-circle" class="w-4 h-4 flex-shrink-0"></i> Entry is perfectly balanced. Debits equal credits.'
                    : `<i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0"></i> Out of balance by ${window.ERP_CONFIG.currency_symbol}${diff.toLocaleString(undefined, { minimumFractionDigits: window.ERP_CONFIG.decimal_precision })}`;

                if (typeof lucide !== 'undefined') lucide.createIcons();
            }
        }

    </script>
@endsection
