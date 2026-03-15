@extends('admin.admin_master')
@section('admin')
    <div x-data="{ isSaving: false }">

        @section('title')
            Chart of Accounts - XaliyePro
        @endsection

        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Chart of Accounts</h1>
                <p class="text-gray-500 mt-1">Manage your accounting structure and accounts</p>
            </div>
            <button onclick="openModal('addAccountModal')"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-medium hover:bg-[#229967]">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Add New Account
            </button>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            @include('admin.partials.stats_card', [
                'title' => 'Total Accounts',
                'value' => number_format($stats['total']),
                'icon' => 'list',
                'color' => '#28A375',
                'iconBg' => 'bg-[#28A375]',
                'iconShadow' => 'shadow-green-100',
            ])

            @include('admin.partials.stats_card', [
                'title' => 'Assets',
                'value' => number_format($stats['assets']),
                'icon' => 'trending-up',
                'color' => '#3B82F6',
                'iconBg' => 'bg-blue-500',
                'iconShadow' => 'shadow-blue-100',
            ])

            @include('admin.partials.stats_card', [
                'title' => 'Liabilities',
                'value' => number_format($stats['liabilities']),
                'icon' => 'trending-down',
                'color' => '#EF4444',
                'iconBg' => 'bg-red-500',
                'iconShadow' => 'shadow-red-100',
            ])

            @include('admin.partials.stats_card', [
                'title' => 'Equity',
                'value' => number_format($stats['equity']),
                'icon' => 'wallet',
                'color' => '#8B5CF6',
                'iconBg' => 'bg-purple-500',
                'iconShadow' => 'shadow-purple-100',
            ])

            @include('admin.partials.stats_card', [
                'title' => 'Revenue',
                'value' => number_format($stats['revenue']),
                'icon' => 'dollar-sign',
                'color' => '#10B981',
                'iconBg' => 'bg-green-500',
                'iconShadow' => 'shadow-green-100',
            ])
        </div>

        <!-- Search and Filter -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <form action="{{ route('accounting.accounts.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4"
                id="filterForm">
                <div class="flex-1">
                    <div class="relative">
                        <i data-lucide="search" class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search accounts by name, code or type..." oninput="debounceFilter()"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                    </div>
                </div>

                <select name="type" onchange="this.form.submit()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                    <option {{ request('type') == 'All Account Types' ? 'selected' : '' }}>All Account Types</option>
                    <option {{ request('type') == 'Assets' ? 'selected' : '' }}>Assets</option>
                    <option {{ request('type') == 'Liabilities' ? 'selected' : '' }}>Liabilities</option>
                    <option {{ request('type') == 'Equity' ? 'selected' : '' }}>Equity</option>
                    <option {{ request('type') == 'Revenue' ? 'selected' : '' }}>Revenue</option>
                    <option {{ request('type') == 'Expenses' ? 'selected' : '' }}>Expenses</option>
                </select>

                <select name="status" onchange="this.form.submit()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                    <option {{ request('status') == 'All Status' ? 'selected' : '' }}>All Status</option>
                    <option {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                    <option {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </form>
        </div>

        @php
            $sections = [
                ['type' => 'asset', 'name' => 'Assets', 'color' => 'blue', 'icon' => 'trending-up'],
                ['type' => 'liability', 'name' => 'Liabilities', 'color' => 'red', 'icon' => 'trending-down'],
                ['type' => 'equity', 'name' => 'Equity', 'color' => 'purple', 'icon' => 'wallet'],
                ['type' => 'revenue', 'name' => 'Revenue', 'color' => 'green', 'icon' => 'dollar-sign'],
                ['type' => 'expense', 'name' => 'Expenses', 'color' => 'orange', 'icon' => 'credit-card'],
            ];
        @endphp

        @foreach ($sections as $section)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                <div class="px-6 py-4 border-b border-gray-200 bg-{{ $section['color'] }}-50">
                    <h2 class="text-lg font-bold text-{{ $section['color'] }}-900 flex items-center gap-2">
                        <i data-lucide="{{ $section['icon'] }}" class="w-5 h-5"></i>
                        {{ $section['name'] }}
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Code</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Account Name</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Balance</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @if ($accounts->where('type', $section['type'])->count() > 0)
                                @foreach ($accounts->where('type', $section['type']) as $account)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-900 font-mono">{{ $account->code }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $account->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $account->sub_type }}</td>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                        {{ number_format($account->current_balance, 2) }} {{ $account->currency }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $account->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($account->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button onclick="openEditAccountModal(JSON.parse(this.dataset.account))"
                                                data-account="{{ json_encode($account) }}"
                                                class="p-1.5 text-gray-400 hover:text-blue-600 transition-colors"
                                                title="Edit Account">
                                                <i data-lucide="edit" class="w-4 h-4"></i>
                                            </button>
                                            <form action="{{ route('accounting.accounts.destroy', $account->id) }}" method="POST"
                                                class="inline delete-account-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmDeleteAccount(this.form)"
                                                    class="p-1.5 text-gray-400 hover:text-red-600 transition-colors"
                                                    title="Delete Account">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-sm text-center text-gray-500">No {{ $section['name'] }}
                                        accounts found.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

        <!-- Edit Account Modal -->
        <div id="editAccountModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-md transition-opacity" aria-hidden="true"
                    onclick="closeModal('editAccountModal')"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal Content -->
                <div
                    class="inline-block align-middle bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-2xl sm:w-full border border-gray-100">
                    <!-- Modal Header -->
                    <div
                        class="px-8 py-4 border-b border-gray-100 flex items-center justify-between bg-white sticky top-0 z-10">
                        <h3 class="text-xl font-bold text-gray-900">Edit Account</h3>
                        <button onclick="closeModal('editAccountModal')"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i data-lucide="x" class="w-6 h-6"></i>
                        </button>
                    </div>

                    <form id="editAccountForm" action="" method="POST" @submit="isSaving = true">
                        @csrf
                        @method('PUT')
                        <div class="p-6">
                            <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Account Code <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="code" id="edit_code" required placeholder="e.g., 1000"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Account Name <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="name" id="edit_name" required
                                            placeholder="e.g., Main Cash Account"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Account Type <span class="text-red-500">*</span>
                                        </label>
                                        <select name="type" id="edit_type" required onchange="updateEditSubTypes()"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all outline-none">
                                            <option value="">Select Type</option>
                                            <option value="asset">Asset</option>
                                            <option value="liability">Liability</option>
                                            <option value="equity">Equity</option>
                                            <option value="revenue">Revenue</option>
                                            <option value="expense">Expense</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Detail Type (Sub Type) <span class="text-red-500">*</span>
                                        </label>
                                        <select name="sub_type" id="edit_sub_type" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all outline-none">
                                            <option value="">Select Account Type First</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Parent Account
                                    </label>
                                    <select name="parent_id" id="edit_parent_id"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all outline-none">
                                        <option value="">None (Top Level Account)</option>
                                        @foreach ($parentAccounts as $parent)
                                            <option value="{{ $parent->id }}">{{ $parent->code }} - {{ $parent->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Opening Balance
                                        </label>
                                        <div class="relative">
                                            <span
                                                class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                                            <input type="number" name="opening_balance" id="edit_opening_balance"
                                                step="0.01" placeholder="0.00"
                                                class="w-full pl-7 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Currency
                                        </label>
                                        <select name="currency" id="edit_currency"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all outline-none">
                                            <option value="USD">USD - US Dollar</option>
                                            <option value="EUR">EUR - Euro</option>
                                            <option value="GBP">GBP - British Pound</option>
                                            <option value="JPY">JPY - Japanese Yen</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Status <span class="text-red-500">*</span>
                                        </label>
                                        <select name="status" id="edit_status" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all outline-none">
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                    <div class="flex items-end pb-1 h-full">
                                        <label class="flex items-center gap-2 cursor-pointer group">
                                            <input type="checkbox" name="is_tax_account" id="edit_is_tax_account" value="1"
                                                class="w-4 h-4 text-[#28A375] border-gray-300 rounded focus:ring-[#28A375] transition-all">
                                            <span
                                                class="text-sm font-medium text-gray-700 group-hover:text-[#28A375] transition-colors">Tax
                                                Account</span>
                                        </label>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Description
                                    </label>
                                    <textarea name="description" id="edit_description" rows="2"
                                        placeholder="Enter account description and notes..."
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all resize-none"></textarea>
                                </div>
                            </div>

                            <!-- Modal Footer Buttons -->
                            <div class="flex gap-4 pt-4 mt-2 border-t border-gray-50">
                                <button type="submit" :disabled="isSaving"
                                    class="flex-1 px-6 py-3 bg-[#28A375] text-white rounded-lg text-sm font-bold hover:bg-[#229967] transition-all flex items-center justify-center gap-2 shadow-sm">
                                    <span x-show="!isSaving" class="flex items-center gap-2">
                                        <i data-lucide="save" class="w-4 h-4"></i>
                                        Update Account
                                    </span>
                                    <div x-show="isSaving" class="flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4 text-white" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        <span>Updating...</span>
                                    </div>
                                </button>
                                <button @click="closeModal('editAccountModal')" type="button" :disabled="isSaving"
                                    class="flex-1 px-6 py-3 border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Add New Account Modal -->
        <div id="addAccountModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-md transition-opacity" aria-hidden="true"
                    onclick="closeModal('addAccountModal')"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal Content -->
                <div
                    class="inline-block align-middle bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-2xl sm:w-full border border-gray-100">
                    <!-- Modal Header -->
                    <div
                        class="px-8 py-4 border-b border-gray-100 flex items-center justify-between bg-white sticky top-0 z-10">
                        <h3 class="text-xl font-bold text-gray-900">Add New Account</h3>
                        <button onclick="closeModal('addAccountModal')"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i data-lucide="x" class="w-6 h-6"></i>
                        </button>
                    </div>

                    <form id="addAccountForm" action="{{ route('accounting.accounts.store') }}" method="POST"
                        @submit="isSaving = true">
                        @csrf
                        <div class="p-6">
                            <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Account Code <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="code" required placeholder="e.g., 1000"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Account Name <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="name" required placeholder="e.g., Main Cash Account"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Account Type <span class="text-red-500">*</span>
                                        </label>
                                        <select name="type" id="add_type" required onchange="updateAddSubTypes()"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all outline-none">
                                            <option value="">Select Type</option>
                                            <option value="asset">Asset</option>
                                            <option value="liability">Liability</option>
                                            <option value="equity">Equity</option>
                                            <option value="revenue">Revenue</option>
                                            <option value="expense">Expense</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Detail Type (Sub Type) <span class="text-red-500">*</span>
                                        </label>
                                        <select name="sub_type" id="add_sub_type" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all outline-none">
                                            <option value="">Select Account Type First</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Parent Account
                                    </label>
                                    <select name="parent_id"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all outline-none">
                                        <option value="">None (Top Level Account)</option>
                                        @foreach ($parentAccounts as $parent)
                                            <option value="{{ $parent->id }}">{{ $parent->code }} - {{ $parent->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Opening Balance
                                        </label>
                                        <div class="relative">
                                            <span
                                                class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                                            <input type="number" name="opening_balance" step="0.01" placeholder="0.00"
                                                class="w-full pl-7 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Currency
                                        </label>
                                        <select name="currency"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all outline-none">
                                            <option value="USD">USD - US Dollar</option>
                                            <option value="EUR">EUR - Euro</option>
                                            <option value="GBP">GBP - British Pound</option>
                                            <option value="JPY">JPY - Japanese Yen</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Status <span class="text-red-500">*</span>
                                        </label>
                                        <select name="status" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all outline-none">
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                    <div class="flex items-end pb-1 h-full">
                                        <label class="flex items-center gap-2 cursor-pointer group">
                                            <input type="checkbox" name="is_tax_account" value="1"
                                                class="w-4 h-4 text-[#28A375] border-gray-300 rounded focus:ring-[#28A375] transition-all">
                                            <span
                                                class="text-sm font-medium text-gray-700 group-hover:text-[#28A375] transition-colors">Tax
                                                Account</span>
                                        </label>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Description
                                    </label>
                                    <textarea name="description" rows="2"
                                        placeholder="Enter account description and notes..."
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all resize-none"></textarea>
                                </div>
                            </div>

                            <!-- Modal Footer Buttons (Aligned with Employee Modal) -->
                            <div class="flex gap-4 pt-4 mt-2 border-t border-gray-50">
                                <button type="submit" :disabled="isSaving"
                                    class="flex-1 px-6 py-3 bg-[#28A375] text-white rounded-lg text-sm font-bold hover:bg-[#229967] transition-all flex items-center justify-center gap-2 shadow-sm">
                                    <span x-show="!isSaving" class="flex items-center gap-2">
                                        <i data-lucide="save" class="w-4 h-4"></i>
                                        Save Account
                                    </span>
                                    <div x-show="isSaving" class="flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4 text-white" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        <span>Saving...</span>
                                    </div>
                                </button>
                                <button @click="closeModal('addAccountModal')" type="button" :disabled="isSaving"
                                    class="flex-1 px-6 py-3 border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </form>
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

            const detailTypes = {
                asset: ['Bank and Cash', 'Accounts Receivable', 'Current Asset', 'Inventory', 'Prepayments', 'Fixed Asset', 'Non-current Asset'],
                liability: ['Accounts Payable', 'Credit Card', 'Current Liability', 'Payroll Payable', 'Tax Payable', 'Non-current Liability'],
                equity: ['Equity', 'Retained Earnings', 'Current Year Earnings', 'Owner Contribution', 'Owner Draw'],
                revenue: ['Operating Revenue', 'Sales', 'Product Income', 'Service Income', 'Other Income', 'Discounts Given'],
                expense: ['Operating Expense', 'Cost of Goods Sold (COGS)', 'Depreciation', 'Payroll Expense', 'Rent or Lease', 'Taxes and Licenses']
            };

            function populateSubTypes(typeSelectId, subTypeSelectId, selectedValue = '') {
                const type = document.getElementById(typeSelectId).value;
                const subTypeSelect = document.getElementById(subTypeSelectId);

                subTypeSelect.innerHTML = '<option value="">Select Detail Type</option>';

                if (type && detailTypes[type]) {
                    detailTypes[type].forEach(subType => {
                        const option = document.createElement('option');
                        option.value = subType;
                        option.textContent = subType;
                        if (subType === selectedValue) {
                            option.selected = true;
                        }
                        subTypeSelect.appendChild(option);
                    });
                }
            }

            function updateAddSubTypes() {
                populateSubTypes('add_type', 'add_sub_type');
            }

            function updateEditSubTypes() {
                populateSubTypes('edit_type', 'edit_sub_type');
            }

            function openEditAccountModal(account) {
                const form = document.getElementById('editAccountForm');
                form.action = "{{ route('accounting.accounts.update', 99999) }}".replace('99999', account.id);

                document.getElementById('edit_code').value = account.code;
                document.getElementById('edit_name').value = account.name;
                document.getElementById('edit_type').value = account.type;

                // Populate SubTypes before setting the value
                populateSubTypes('edit_type', 'edit_sub_type', account.sub_type);

                document.getElementById('edit_parent_id').value = account.parent_id || '';
                document.getElementById('edit_opening_balance').value = account.opening_balance;
                document.getElementById('edit_currency').value = account.currency;
                document.getElementById('edit_status').value = account.status;
                document.getElementById('edit_is_tax_account').checked = account.is_tax_account;
                document.getElementById('edit_description').value = account.description || '';

                openModal('editAccountModal');
            }

            function confirmDeleteAccount(form) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This account and its history might be permanently affected!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#28A375',
                    cancelButtonColor: '#EF4444',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        container: 'swal2-standard',
                        popup: 'rounded-[2rem]',
                        confirmButton: 'rounded-lg px-6 py-2.5',
                        cancelButton: 'rounded-lg px-6 py-2.5'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }

            let filterTimeout;
            function debounceFilter() {
                clearTimeout(filterTimeout);
                filterTimeout = setTimeout(() => {
                    document.getElementById('filterForm').submit();
                }, 500);
            }
        </script>

    </div>
@endsection
