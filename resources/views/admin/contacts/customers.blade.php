@extends('admin.admin_master')
@section('admin')
    <div x-data="customersApp()">
        @section('title')
            Customers - XaliyePro
        @endsection

        {{-- Page Header --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Customers</h1>
                <p class="text-gray-500 mt-1">Manage your customer database and relationships</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button @click="isImportModalOpen = true; selectedFile = null" type="button"
                    class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-bold hover:bg-gray-50 flex items-center gap-2 shadow-sm transition-colors relative h-[38px]">
                    <i data-lucide="upload" class="w-4 h-4"></i> Import
                </button>

                <a href="{{ route('contacts.customers.export') }}"
                    class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-bold hover:bg-gray-50 flex items-center gap-2 shadow-sm transition-colors h-[38px] flex items-center">
                    <i data-lucide="download" class="w-4 h-4"></i> Export
                </a>

                <button
                    @click="showAdd = true; $nextTick(() => { if(typeof lucide !== 'undefined') lucide.createIcons(); })"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-[#28A375] text-white rounded-lg text-sm font-medium hover:bg-[#229967] transition-all shadow-sm h-[38px]">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Add Customer
                </button>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            @include('admin.partials.stats_card', [
                'title' => 'Total Customers',
                'value' => number_format($stats['total']),
                'icon' => 'users',
                'trendValue' => '+8.5%',
                'subtitle' => 'vs last month'
            ])

            @include('admin.partials.stats_card', [
                'title' => 'Active Customers',
                'value' => number_format($stats['active']),
                'icon' => 'user-check',
                'color' => '#10B981',
                'iconBg' => 'bg-green-500',
                'trendValue' => ($stats['total'] > 0 ? round($stats['active'] / $stats['total'] * 100, 1) : 0) . '%',
                'subtitle' => 'of total list'
            ])

            @include('admin.partials.stats_card', [
                'title' => 'Total Receivables',
                'value' => '$' . number_format($stats['total_receivable'], 0),
                'icon' => 'dollar-sign',
                'color' => '#3B82F6',
                'iconBg' => 'bg-blue-500',
                'iconShadow' => 'shadow-blue-100',
                'trendValue' => '+12%',
                'trendColor' => 'text-blue-500',
                'trendIcon' => 'trending-up',
                'subtitle' => 'revenue growth'
            ])

            @include('admin.partials.stats_card', [
                'title' => 'Total Sales',
                'value' => '$' . number_format($stats['total_sales'], 0),
                'icon' => 'shopping-cart',
                'color' => '#8B5CF6',
                'iconBg' => 'bg-purple-500',
                'iconShadow' => 'shadow-purple-100',
                'subtitle' => 'Lifetime Average'
            ])
        </div>

        {{-- Search & Filter --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <form action="{{ route('contacts.customers.index') }}" method="GET"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="lg:col-span-2">
                    <div class="relative">
                        <i data-lucide="search" class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search by name, email, phone..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                    </div>
                </div>

                <select name="status" onchange="this.form.submit()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                    <option value="All Status" {{ request('status', 'All Status') == 'All Status' ? 'selected' : '' }}>All
                        Status</option>
                    <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                </select>

                <select name="type" onchange="this.form.submit()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                    <option value="All Types" {{ request('type', 'All Types') == 'All Types' ? 'selected' : '' }}>All Types
                    </option>
                    <option value="Individual" {{ request('type') == 'Individual' ? 'selected' : '' }}>Individual</option>
                    <option value="Company" {{ request('type') == 'Company' ? 'selected' : '' }}>Company</option>
                </select>
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">All Customers</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Type</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Total Sales</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Balance</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @if ($customers->count() > 0)
                            @foreach ($customers as $customer)
                            @php
                                $colors = [
                                    'bg-blue-100 text-blue-600',
                                    'bg-green-100 text-green-600',
                                    'bg-purple-100 text-purple-600',
                                    'bg-orange-100 text-orange-600',
                                    'bg-rose-100 text-rose-600'
                                ];
                                $color = $colors[$customer->id % count($colors)];
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 {{ explode(' ', $color)[0] }} rounded-full flex items-center justify-center flex-shrink-0 shadow-sm">
                                            <span
                                                class="text-sm font-bold {{ explode(' ', $color)[1] }}">{{ $customer->initials }}</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">{{ $customer->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $customer->customer_id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $customer->email ?? '—' }}</div>
                                    <div class="text-xs text-gray-500">{{ $customer->phone ?? '—' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $customer->type === 'company' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ ucfirst($customer->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900 text-right whitespace-nowrap">
                                    ${{ number_format($customer->total_sales, 2) }}
                                </td>
                                <td
                                    class="px-6 py-4 text-sm font-semibold text-right whitespace-nowrap {{ $customer->balance > 0 ? 'text-orange-600' : 'text-green-600' }}">
                                    ${{ number_format($customer->balance, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $customer->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                                        {{ ucfirst($customer->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('contacts.customers.details', $customer->id) }}"
                                            class="p-1.5 text-gray-400 hover:text-blue-600 transition-colors"
                                            title="View Details">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                        <button data-customer="{{ json_encode($customer) }}"
                                            @click="editCustomer({{ $customer->id }}, JSON.parse($el.dataset.customer))"
                                            class="p-1.5 text-gray-400 hover:text-[#28A375] transition-colors" title="Edit">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </button>
                                        <form action="{{ route('contacts.customers.destroy', $customer->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" @click="confirmDeleteCustomer($el.form)"
                                                class="p-1.5 text-gray-400 hover:text-red-600 transition-colors" title="Delete">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center gap-3">
                                        <i data-lucide="users" class="w-12 h-12 text-gray-300"></i>
                                        <p class="font-medium">No customers found.</p>
                                        <p class="text-sm">Add your first customer to get started.</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            @if ($customers->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4 bg-gray-50/50">
                    <div class="text-sm text-gray-500">
                        Showing <span class="font-semibold text-gray-900">{{ $customers->firstItem() }}</span>
                        to <span class="font-semibold text-gray-900">{{ $customers->lastItem() }}</span>
                        of <span class="font-semibold text-gray-900">{{ $customers->total() }}</span> results
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        {{ $customers->links() }}
                    </div>
                </div>
            @endif
        </div>

        {{-- ADD MODAL --}}
        <div x-show="showAdd" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-md" @click="showAdd = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div
                    class="inline-block align-middle bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-3xl sm:w-full border border-gray-100">
                    <div
                        class="px-8 py-4 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white z-10">
                        <h3 class="text-xl font-bold text-gray-900">Add New Customer</h3>
                        <button @click="showAdd = false" class="text-gray-400 hover:text-gray-600"><i data-lucide="x"
                                class="w-6 h-6"></i></button>
                    </div>
                    <form action="{{ route('contacts.customers.store') }}" method="POST" @submit="isSaving = true">
                        @csrf
                        <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto custom-scrollbar">
                            {{-- Customer Type --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Customer Type</label>
                                <div class="grid grid-cols-2 gap-4">
                                    <label class="block p-4 border-2 rounded-xl cursor-pointer transition-all"
                                        :class="addForm.type === 'individual' ? 'border-[#28A375] bg-green-50' : 'border-gray-200 hover:border-gray-300'">
                                        <div class="flex items-center gap-3">
                                            <input type="radio" name="type" value="individual" x-model="addForm.type"
                                                class="w-4 h-4 text-[#28A375] focus:ring-[#28A375]">
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">Individual</p>
                                                <p class="text-xs text-gray-500">Personal customer</p>
                                            </div>
                                        </div>
                                    </label>
                                    <label class="block p-4 border-2 rounded-xl cursor-pointer transition-all"
                                        :class="addForm.type === 'company' ? 'border-[#28A375] bg-green-50' : 'border-gray-200 hover:border-gray-300'">
                                        <div class="flex items-center gap-3">
                                            <input type="radio" name="type" value="company" x-model="addForm.type"
                                                class="w-4 h-4 text-[#28A375] focus:ring-[#28A375]">
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">Company</p>
                                                <p class="text-xs text-gray-500">Business customer</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            {{-- Basic Info --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="name" required placeholder="Enter customer or company name"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                    <input type="email" name="email" placeholder="customer@example.com"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                    <input type="tel" name="phone" placeholder="+1 (555) 000-0000"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent outline-none">
                                </div>
                            </div>

                            {{-- Address --}}
                            <div class="space-y-4 pt-2">
                                <h4 class="text-sm font-semibold text-gray-800 flex items-center gap-2"><i
                                        data-lucide="map-pin" class="w-4 h-4 text-[#28A375]"></i> Address Details</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Street Address</label>
                                        <input type="text" name="address" placeholder="Address line"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                        <input type="text" name="city" placeholder="City"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                                        <select name="country"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] outline-none">
                                            <option value="">Select Country</option>
                                            <option value="Kenya">Kenya</option>
                                            <option value="Tanzania">Tanzania</option>
                                            <option value="Uganda">Uganda</option>
                                            <option value="Rwanda">Rwanda</option>
                                            <option value="Burundi">Burundi</option>
                                            <option value="South Sudan">South Sudan</option>
                                            <option value="Ethiopia">Ethiopia</option>
                                            <option value="Somalia">Somalia</option>
                                            <option value="Djibouti">Djibouti</option>
                                            <option value="Eritrea">Eritrea</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Financial & Other --}}
                            <div class="space-y-4 pt-2">
                                <h4 class="text-sm font-semibold text-gray-800 flex items-center gap-2"><i
                                        data-lucide="credit-card" class="w-4 h-4 text-[#28A375]"></i> Financial Settings
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Opening Balance</label>
                                        <input type="number" name="balance" step="0.01" placeholder="0.00"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Terms</label>
                                        <select name="payment_terms"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] outline-none">
                                            <option value="net_30">Net 30</option>
                                            <option value="net_15">Net 15</option>
                                            <option value="due_on_receipt">Due on Receipt</option>
                                            <option value="net_60">Net 60</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Credit Limit</label>
                                        <input type="number" name="credit_limit" step="0.01" placeholder="0.00"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                        <select name="status"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] outline-none">
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                <textarea name="notes" rows="3" placeholder="Additional information..."
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent outline-none resize-none"></textarea>
                            </div>
                        </div>
                        <div class="px-6 pb-6 flex gap-4 pt-4 border-t border-gray-50">
                            <button type="submit" :disabled="isSaving"
                                class="flex-1 px-6 py-3 bg-[#28A375] text-white rounded-lg text-sm font-bold hover:bg-[#229967] transition-all flex items-center justify-center gap-2">
                                <span x-show="!isSaving" class="flex items-center gap-2"><i data-lucide="save"
                                        class="w-4 h-4"></i> Save Customer</span>
                                <span x-show="isSaving" class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Saving...
                                </span>
                            </button>
                            <button type="button" @click="showAdd = false"
                                class="flex-1 px-6 py-3 border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- EDIT MODAL --}}
        <div x-show="showEdit" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-md" @click="showEdit = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div
                    class="inline-block align-middle bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-3xl sm:w-full border border-gray-100">
                    <div
                        class="px-8 py-4 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white z-10">
                        <h3 class="text-xl font-bold text-gray-900">Edit Customer</h3>
                        <button @click="showEdit = false" class="text-gray-400 hover:text-gray-600"><i data-lucide="x"
                                class="w-6 h-6"></i></button>
                    </div>
                    <form :action="'{{ route('contacts.customers.update', ':id') }}'.replace(':id', editId)" method="POST"
                        @submit="isSaving = true">
                        @csrf @method('PUT')
                        <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto custom-scrollbar">
                            {{-- Reuse Add Modal Fields with :value bindings --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Customer Type</label>
                                <div class="grid grid-cols-2 gap-4">
                                    <label class="block p-4 border-2 rounded-xl cursor-pointer transition-all"
                                        :class="editForm.type === 'individual' ? 'border-[#28A375] bg-green-50' : 'border-gray-200 hover:border-gray-300'">
                                        <div class="flex items-center gap-3">
                                            <input type="radio" name="type" value="individual" x-model="editForm.type"
                                                class="w-4 h-4 text-[#28A375]">
                                            <span class="text-sm font-semibold text-gray-900">Individual</span>
                                        </div>
                                    </label>
                                    <label class="block p-4 border-2 rounded-xl cursor-pointer transition-all"
                                        :class="editForm.type === 'company' ? 'border-[#28A375] bg-green-50' : 'border-gray-200 hover:border-gray-300'">
                                        <div class="flex items-center gap-3">
                                            <input type="radio" name="type" value="company" x-model="editForm.type"
                                                class="w-4 h-4 text-[#28A375]">
                                            <span class="text-sm font-semibold text-gray-900">Company</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                    <input type="text" name="name" :value="editForm.name" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input type="email" name="email" :value="editForm.email"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                    <input type="tel" name="phone" :value="editForm.phone"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] outline-none">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Street Address</label>
                                    <input type="text" name="address" :value="editForm.address"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                    <input type="text" name="city" :value="editForm.city"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                                    <select name="country"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] outline-none">
                                        <option value="">Select Country</option>
                                        <option value="Kenya" :selected="editForm.country === 'Kenya'">Kenya</option>
                                        <option value="Tanzania" :selected="editForm.country === 'Tanzania'">Tanzania
                                        </option>
                                        <option value="Uganda" :selected="editForm.country === 'Uganda'">Uganda</option>
                                        <option value="Rwanda" :selected="editForm.country === 'Rwanda'">Rwanda</option>
                                        <option value="Burundi" :selected="editForm.country === 'Burundi'">Burundi</option>
                                        <option value="South Sudan" :selected="editForm.country === 'South Sudan'">South
                                            Sudan
                                        </option>
                                        <option value="Ethiopia" :selected="editForm.country === 'Ethiopia'">Ethiopia
                                        </option>
                                        <option value="Somalia" :selected="editForm.country === 'Somalia'">Somalia</option>
                                        <option value="Djibouti" :selected="editForm.country === 'Djibouti'">Djibouti
                                        </option>
                                        <option value="Eritrea" :selected="editForm.country === 'Eritrea'">Eritrea</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Opening Balance</label>
                                    <input type="number" name="balance" :value="editForm.balance" step="0.01"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Terms</label>
                                    <select name="payment_terms"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] outline-none">
                                        <option value="net_30" :selected="editForm.payment_terms === 'net_30'">Net 30
                                        </option>
                                        <option value="net_15" :selected="editForm.payment_terms === 'net_15'">Net 15
                                        </option>
                                        <option value="due_on_receipt"
                                            :selected="editForm.payment_terms === 'due_on_receipt'">
                                            Due on Receipt</option>
                                        <option value="net_60" :selected="editForm.payment_terms === 'net_60'">Net 60
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Credit Limit</label>
                                    <input type="number" name="credit_limit" :value="editForm.credit_limit" step="0.01"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select name="status"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] outline-none">
                                        <option value="active" :selected="editForm.status === 'active'">Active</option>
                                        <option value="inactive" :selected="editForm.status === 'inactive'">Inactive
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                <textarea name="notes" rows="3" :value="editForm.notes"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] outline-none resize-none"></textarea>
                            </div>
                        </div>
                        <div class="px-6 pb-6 flex gap-4 pt-4 border-t border-gray-50">
                            <button type="submit" :disabled="isSaving"
                                class="flex-1 px-6 py-3 bg-[#28A375] text-white rounded-lg text-sm font-bold hover:bg-[#229967] transition-all flex items-center justify-center gap-2">
                                <span x-show="!isSaving" class="flex items-center gap-2"><i data-lucide="save"
                                        class="w-4 h-4"></i> Update Customer</span>
                                <span x-show="isSaving" class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Saving...
                                </span>
                            </button>
                            <button type="button" @click="showEdit = false"
                                class="flex-1 px-6 py-3 border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @include('admin.contacts.modals.import_customer_modal')

        <script>
            function customersApp() {
                return {
                    showAdd: false,
                    showEdit: false,
                    isSaving: false,
                    isImportModalOpen: false,
                    selectedFile: null,
                    editId: null,
                    editForm: {},
                    addForm: { type: 'individual' },
                    editCustomer(id, data) {
                        this.editId = id;
                        this.editForm = { ...data };
                        this.showEdit = true;
                        this.$nextTick(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); });
                    },
                    confirmDeleteCustomer(form) {
                        Swal.fire({
                            title: 'Delete Customer?',
                            text: 'This action cannot be undone and may affect associated transaction history.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#EF4444',
                            cancelButtonColor: '#6B7280',
                            confirmButtonText: 'Yes, delete',
                            cancelButtonText: 'Cancel',
                            customClass: {
                                popup: 'rounded-2xl',
                                confirmButton: 'rounded-lg',
                                cancelButton: 'rounded-lg'
                            }
                        }).then((result) => { if (result.isConfirmed) form.submit(); });
                    }
                }
            }
        </script>
    </div>
@endsection
