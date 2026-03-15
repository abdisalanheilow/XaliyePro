@extends('admin.admin_master')

@section('title', 'Record Vendor Payment - XaliyePro')

@section('admin')
<div x-data="{ isSaving: false }">
<form action="{{ route('purchases.payments.store') }}" method="POST" id="vendorPaymentForm" @submit="isSaving = true">
    @csrf
    <div class="space-y-6">
        <!-- Page Title -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('purchases.payments.index') }}" class="text-gray-500 hover:text-gray-700">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">Record Vendor Payment</h1>
                </div>
                <p class="text-sm text-gray-500">Record a payment made to a vendor (Payment-Out)</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('purchases.payments.index') }}" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" :disabled="isSaving" class="px-4 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] inline-flex items-center gap-2 transition-all active:scale-95 shadow-sm disabled:opacity-50">
                    <template x-if="!isSaving">
                        <div class="flex items-center gap-2">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            Save Payment
                        </div>
                    </template>
                    <template x-if="isSaving">
                        <div class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Saving...
                        </div>
                    </template>
                </button>
            </div>
        </div>

        <!-- Payment Details Card -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm overflow-hidden relative">
            <div class="flex items-center gap-3 mb-8">
                <div class="p-2.5 bg-green-50 rounded-xl">
                    <i data-lucide="credit-card" class="w-6 h-6 text-[#28A375]"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Payment Information</h2>
                    <p class="text-sm text-gray-500">Enter the payment specifics below</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-6">
                <!-- Row 1 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Vendor <span class="text-red-500">*</span>
                    </label>
                    <select name="vendor_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                        <option value="">Select Vendor</option>
                        @foreach ($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ ($bill && $bill->vendor_id == $vendor->id) ? 'selected' : '' }}>
                                {{ $vendor->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Payment Number <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="payment_no" value="{{ $paymentNo }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm font-bold text-gray-600 focus:outline-none" readonly>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Link to Bill (Optional)</label>
                    <select name="purchase_bill_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                        <option value="">Independent Payment</option>
                        @foreach ($bills as $unpaidBill)
                            <option value="{{ $unpaidBill->id }}" {{ ($bill && $bill->id == $unpaidBill->id) ? 'selected' : '' }}>
                                {{ $unpaidBill->bill_no }} - Bal: ${{ number_format($unpaidBill->balance_amount, 2) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Row 2 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Payment Date <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i data-lucide="calendar" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                        <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Payment Method <span class="text-red-500">*</span>
                    </label>
                    <select name="payment_method" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                        <option value="Cash">Cash</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Cheque">Cheque</option>
                        <option value="Credit Card">Credit Card</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Reference # (Cheque/Trans Id)</label>
                    <input type="text" name="reference_no" placeholder="Enter reference number" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                </div>

                <!-- Row 3 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Payment Amount <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-semibold">$</span>
                        <input type="number" name="amount" value="{{ $bill ? $bill->balance_amount : '0.00' }}" min="0.01" step="0.01" required class="w-full pl-8 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm font-bold text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Payment Account <span class="text-red-500">*</span>
                    </label>
                    <select name="account_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                        <option value="">Select Account</option>
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end pb-1">
                    <p class="text-xs text-gray-400 italic">Verify amount before saving.</p>
                </div>
            </div>

            <div class="mt-8">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Notes / Internal Remarks</label>
                <textarea name="notes" rows="3" placeholder="Add any payment notes here..." class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] resize-none transition-all placeholder:text-gray-400"></textarea>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="bg-[#28A375]/5 border border-[#28A375]/20 rounded-xl p-4 flex items-start gap-3">
            <i data-lucide="info" class="w-5 h-5 text-[#28A375] mt-0.5"></i>
            <p class="text-sm text-[#28A375]">
                Recording this payment will update the vendor's balance and the associated bill status automatically. 
                Appropriate accounting entries will be generated in the background.
            </p>
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
