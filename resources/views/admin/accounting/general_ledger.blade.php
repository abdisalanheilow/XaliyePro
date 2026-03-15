@extends('admin.admin_master')
@section('admin')
    <div>
        @section('title')
            General Ledger - XaliyePro
        @endsection

        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">General Ledger</h1>
                <p class="text-gray-500 mt-1">View all posted transactions and account balances</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    Export
                </button>
                <button
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-medium hover:bg-[#229967]">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    Print
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            @include('admin.partials.stats_card', [
                'title' => 'Total Debits',
                'value' => '$' . number_format($stats['total_debit'], 2),
                'icon' => 'trending-up',
                'color' => '#3B82F6',
                'iconBg' => 'bg-blue-500',
                'iconShadow' => 'shadow-blue-100',
                'subtitle' => 'Total across selected period'
            ])

            @include('admin.partials.stats_card', [
                'title' => 'Total Credits',
                'value' => '$' . number_format($stats['total_credit'], 2),
                'icon' => 'trending-down',
                'color' => '#10B981',
                'iconBg' => 'bg-green-500',
                'iconShadow' => 'shadow-green-100',
                'subtitle' => 'Total across selected period'
            ])

            @include('admin.partials.stats_card', [
                'title' => 'Transactions',
                'value' => number_format($stats['transaction_count']),
                'icon' => 'file-text',
                'color' => '#8B5CF6',
                'iconBg' => 'bg-purple-500',
                'iconShadow' => 'shadow-purple-100',
                'subtitle' => 'Total unique entries'
            ])

            @include('admin.partials.stats_card', [
                'title' => 'Active Accounts',
                'value' => number_format($stats['active_accounts']),
                'icon' => 'list',
                'color' => '#F59E0B',
                'iconBg' => 'bg-orange-500',
                'iconShadow' => 'shadow-orange-100',
                'subtitle' => 'With transaction history'
            ])
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <form action="{{ route('accounting.ledger.index') }}" method="GET"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Date From</label>
                    <input type="date" name="from_date" value="{{ request('from_date') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Date To</label>
                    <input type="date" name="to_date" value="{{ request('to_date') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Account</label>
                    <select name="account_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                        <option value="">All Accounts</option>
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>
                                {{ $account->code }} - {{ $account->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Transaction Type</label>
                    <select name="type"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                        <option value="">All Types</option>
                        <option value="Journal Entry">Journal Entry</option>
                        <option value="Sales">Sales</option>
                        <option value="Purchase">Purchase</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-[#28A375] text-white rounded-lg text-sm font-medium hover:bg-[#229967]">
                        <i data-lucide="filter" class="w-4 h-4"></i>
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Ledger Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">Transaction Entries</h2>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-500">Showing {{ $entries->count() }} results</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Reference</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Account</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Debit</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Credit</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @if ($entries->count() > 0)
                            @foreach ($entries as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $item->entry->date->format('Y-m-d') }}</td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-mono text-blue-600">{{ $item->entry->reference }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->account->code }} -
                                        {{ $item->account->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->account->sub_type }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $item->description ?: $item->entry->description }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900 text-right">
                                    {{ $item->debit > 0 ? '$' . number_format($item->debit, 2) : '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900 text-right">
                                    {{ $item->credit > 0 ? '$' . number_format($item->credit, 2) : '-' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button onclick="viewTransaction({{ json_encode($item->entry->load('items.account')) }})"
                                        class="p-1.5 text-gray-400 hover:text-blue-600">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center gap-2">
                                        <i data-lucide="inbox" class="w-8 h-8 text-gray-300"></i>
                                        <p>No transactions found for the current filters.</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- View Transaction Modal -->
    <div id="viewTransactionModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-[2rem] shadow-xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col">
            <div class="px-8 py-4 border-b border-gray-100 flex items-center justify-between bg-white">
                <h2 class="text-xl font-bold text-gray-900">Transaction Details</h2>
                <button onclick="closeModal('viewTransactionModal')" class="p-1 text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>

            <div class="p-8 overflow-y-auto">
                <div class="bg-gray-50 rounded-2xl p-6 mb-8 grid grid-cols-3 gap-6">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Reference</p>
                        <p class="text-sm font-bold text-gray-900" id="modal_ref"></p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Date</p>
                        <p class="text-sm font-bold text-gray-900" id="modal_date"></p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Status</p>
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">Posted</span>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Description</h3>
                    <p class="text-sm text-gray-600 leading-relaxed" id="modal_desc"></p>
                </div>

                <div class="mb-8">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Transaction Lines</h3>
                    <div class="border border-gray-100 rounded-2xl overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Account</th>
                                    <th
                                        class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Debit</th>
                                    <th
                                        class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Credit</th>
                                </tr>
                            </thead>
                            <tbody id="modal_items" class="divide-y divide-gray-100">
                                <!-- Items injected here -->
                            </tbody>
                            <tfoot class="bg-gray-50 font-bold">
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">Total</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 text-right" id="modal_total_debit"></td>
                                    <td class="px-4 py-3 text-sm text-gray-900 text-right" id="modal_total_credit"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="flex gap-4 pt-4 border-t border-gray-50">
                    <button onclick="closeModal('viewTransactionModal')"
                        class="flex-1 px-6 py-3 border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all">
                        Close
                    </button>
                    <button
                        class="flex-1 px-6 py-3 bg-[#28A375] text-white rounded-lg text-sm font-bold hover:bg-[#229967] transition-all flex items-center justify-center gap-2">
                        <i data-lucide="printer" class="w-4 h-4"></i>
                        Print Details
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        function viewTransaction(entry) {
            document.getElementById('modal_ref').textContent = entry.reference;
            document.getElementById('modal_date').textContent = new Date(entry.date).toLocaleDateString();
            document.getElementById('modal_desc').textContent = entry.description;

            let itemsHtml = '';
            let totalDebit = 0;
            let totalCredit = 0;

            entry.items.forEach(item => {
                totalDebit += parseFloat(item.debit);
                totalCredit += parseFloat(item.credit);
                itemsHtml += `
                        <tr>
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-gray-900">${item.account.code} - ${item.account.name}</div>
                                <div class="text-xs text-gray-500">${item.account.sub_type}</div>
                            </td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-900 text-right">${item.debit > 0 ? '$' + parseFloat(item.debit).toLocaleString(undefined, { minimumFractionDigits: 2 }) : '-'}</td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-900 text-right">${item.credit > 0 ? '$' + parseFloat(item.credit).toLocaleString(undefined, { minimumFractionDigits: 2 }) : '-'}</td>
                        </tr>
                    `;
            });

            document.getElementById('modal_items').innerHTML = itemsHtml;
            document.getElementById('modal_total_debit').textContent = '$' + totalDebit.toLocaleString(undefined, { minimumFractionDigits: 2 });
            document.getElementById('modal_total_credit').textContent = '$' + totalCredit.toLocaleString(undefined, { minimumFractionDigits: 2 });

            openModal('viewTransactionModal');
        }
    </script>
@endsection
