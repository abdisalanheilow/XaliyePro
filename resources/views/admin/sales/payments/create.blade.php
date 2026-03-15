@extends('admin.admin_master')

@section('title', 'Record Customer Payment - XaliyePro')

@section('admin')
<div class="space-y-6" x-data="{ 
    isSaving: false,
    selectedCustomer: '',
    selectedInvoice: '',
    amount: 0,
    invoices: [
        @foreach ($invoices as $invoice)
        { id: '{{ $invoice->id }}', no: '{{ $invoice->invoice_no }}', customer_id: '{{ $invoice->customer_id }}', balance: {{ $invoice->balance_amount }} },
        @endforeach
    ],
    get filteredInvoices() {
        if (!this.selectedCustomer) return [];
        return this.invoices.filter(i => i.customer_id == this.selectedCustomer);
    },
    updateAmount() {
        const inv = this.invoices.find(i => i.id == this.selectedInvoice);
        if (inv) this.amount = inv.balance;
    }
}">
    <form action="{{ route('sales.payments.store') }}" method="POST" @submit="isSaving = true">
        @csrf
        <div class="flex items-center justify-between mb-6">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('sales.payments.index') }}" class="text-gray-500 hover:text-gray-700">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">Record Payment-In</h1>
                </div>
                <p class="text-sm text-gray-500">Log money received from a customer</p>
            </div>
            <button type="submit" :disabled="isSaving" class="px-4 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] transition-all flex items-center gap-2 shadow-sm active:scale-95 disabled:opacity-50">
                <template x-if="!isSaving">
                    <div class="flex items-center gap-2">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        Post Payment
                    </div>
                </template>
                <template x-if="isSaving">
                    <div class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Posting...
                    </div>
                </template>
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Payment Details -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl border border-gray-200 p-8 shadow-sm">
                    <div class="flex items-center gap-2 mb-8">
                        <div class="p-2 bg-green-50 rounded-lg">
                            <i data-lucide="banknote" class="w-5 h-5 text-[#28A375]"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900">Payment Information</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Customer <span class="text-red-500">*</span></label>
                            <select name="customer_id" x-model="selectedCustomer" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] transition-all">
                                <option value="">Select Customer</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Number</label>
                            <input type="text" name="payment_no" value="{{ $paymentNo }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm font-bold text-gray-600" readonly>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Invoice (Optional)</label>
                            <select name="sales_invoice_id" x-model="selectedInvoice" @change="updateAmount()" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] transition-all">
                                <option value="">Advance Payment / General</option>
                                <template x-for="inv in filteredInvoices" :key="inv.id">
                                    <option :value="inv.id" x-text="inv.no + ' (Bal: $' + inv.balance + ')'"></option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Amount Received <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 font-bold">$</div>
                                <input type="number" name="amount" x-model="amount" step="0.01" required class="w-full pl-8 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] transition-all font-bold text-gray-900">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Date <span class="text-red-500">*</span></label>
                            <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] transition-all">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Method <span class="text-red-500">*</span></label>
                            <select name="payment_method" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] transition-all">
                                <option value="Cash">Cash</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Check">Check</option>
                                <option value="Credit Card">Credit Card</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-8 shadow-sm">
                    <div class="flex items-center gap-2 mb-6">
                        <i data-lucide="message-square" class="w-5 h-5 text-gray-400"></i>
                        <h2 class="text-lg font-bold text-gray-900">Notes & Reference</h2>
                    </div>
                    <div class="space-y-4">
                        <input type="text" name="reference_no" placeholder="Transaction ID / Check Number" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] transition-all">
                        <textarea name="notes" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] outline-none transition-all placeholder:text-gray-400" placeholder="Add any internal remarks..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Right Column: Account Selection -->
            <div class="space-y-6">
                <div class="bg-white rounded-xl border border-gray-200 p-8 shadow-sm sticky top-6">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="p-2 bg-blue-50 rounded-lg">
                            <i data-lucide="shield-check" class="w-5 h-5 text-blue-600"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900">Deposit To</h2>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Account <span class="text-red-500">*</span></label>
                            <select name="account_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] transition-all bg-gray-50/50">
                                <option value="">Select Asset Account</option>
                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->name }} ({{ number_format($account->balance, 2) }})</option>
                                @endforeach
                            </select>
                            <p class="mt-2 text-[11px] text-gray-400 leading-relaxed font-medium">Select the bank or cash account where this payment will be deposited.</p>
                        </div>

                        <div class="pt-6 border-t border-gray-100">
                            <div class="flex items-center justify-between text-gray-500 mb-2">
                                <span class="text-xs font-bold uppercase tracking-wider">Amount To Post</span>
                            </div>
                            <div class="text-3xl font-black text-[#28A375] tracking-tighter" x-text="'$' + parseFloat(amount || 0).toLocaleString(undefined, {minimumFractionDigits: 2})"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
