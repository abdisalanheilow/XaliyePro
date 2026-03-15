@extends('admin.admin_master')

@section('title', 'Payment Voucher Details - XaliyePro')

@section('admin')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('purchases.payments.index') }}"
                class="w-10 h-10 bg-white border border-gray-200 rounded-lg flex items-center justify-center text-gray-500 hover:text-gray-700 transition-all active:scale-95 shadow-sm">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Payment Voucher</h1>
                <p class="text-sm text-gray-500">Voucher #{{ $payment->payment_no }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20">
                Completed
            </span>
            <div class="h-8 w-px bg-gray-200 mx-2"></div>
            <button onclick="window.print()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                <i data-lucide="printer" class="w-4 h-4"></i>
                Print
            </button>
            <form action="{{ route('purchases.payments.destroy', $payment->id) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="button" onclick="confirmDelete(this)" class="px-4 py-2 border border-red-200 text-red-600 rounded-lg text-sm font-semibold hover:bg-red-50 flex items-center gap-2 transition-all">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                    Delete
                </button>
            </form>
        </div>
    </div>

    <!-- Voucher Card -->
    <div class="bg-white rounded-3xl border border-gray-200 shadow-xl overflow-hidden print:shadow-none print:border-none">
        <!-- Top Receipt Header -->
        <div class="p-10 bg-gradient-to-br from-gray-900 to-gray-800 text-white relative">
            <div class="absolute top-0 right-0 p-10">
                <div class="w-20 h-20 bg-white/10 rounded-3xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                    <i data-lucide="credit-card" class="w-10 h-10 text-white"></i>
                </div>
            </div>
            <div class="relative">
                <p class="text-green-400 font-bold uppercase tracking-widest text-xs mb-3">Vendor Payment Out</p>
                <h2 class="text-4xl font-black tracking-tighter mb-1">${{ number_format($payment->amount, 2) }}</h2>
                <p class="text-gray-400 text-sm">Paid on {{ $payment->payment_date->format('F d, Y') }}</p>
            </div>
        </div>

        <!-- Voucher Body -->
        <div class="p-10 space-y-10">
            <!-- Info Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-10">
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Vendor Name</p>
                    <p class="text-base font-bold text-gray-900">{{ $payment->vendor->name ?? 'Unknown Vendor' }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $payment->vendor->phone ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Payment Method</p>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                        <p class="text-base font-bold text-gray-900">{{ $payment->payment_method }}</p>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Ref: {{ $payment->reference_no ?? 'None' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Paid From</p>
                    <p class="text-base font-bold text-gray-900">{{ $payment->account->name ?? 'N/A' }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $payment->branch->name ?? 'Main Branch' }}</p>
                </div>
            </div>

            <div class="h-px bg-gray-100 italic flex items-center justify-center">
                <span class="bg-white px-4 text-[10px] font-bold text-gray-300 uppercase tracking-[0.3em]">Transaction Link</span>
            </div>

            <!-- Detail Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <div class="space-y-6">
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Linked Purchase Bill</p>
                        @if ($payment->bill)
                            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 group hover:border-[#28A375]/30 transition-all">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-bold text-gray-900">{{ $payment->bill->bill_no }}</span>
                                    <a href="{{ route('purchases.bills.show', $payment->purchase_bill_id) }}" class="text-[#28A375] hover:underline text-xs font-bold">View Bill</a>
                                </div>
                                <p class="text-xs text-gray-500">Total Bill Amount: ${{ number_format($payment->bill->grand_total, 2) }}</p>
                            </div>
                        @else
                            <p class="text-sm font-semibold text-gray-400 italic">Independent Payment (No direct bill link)</p>
                        @endif
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Internal Note</p>
                        <div class="p-4 bg-yellow-50/50 rounded-2xl border border-yellow-100">
                            <p class="text-sm text-gray-600 leading-relaxed">{{ $payment->notes ?? 'No internal notes recorded for this payment voucher.' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Signature -->
            <div class="pt-10 flex items-end justify-between border-t border-gray-100">
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Recorded By</p>
                    <p class="text-sm font-bold text-gray-900">{{ $payment->user->name ?? 'System' }}</p>
                </div>
                <div class="text-right">
                    <div class="mb-4">
                        <div class="inline-block px-4 py-2 bg-green-50 rounded-full border border-green-100">
                            <span class="text-[10px] font-black text-green-700 uppercase tracking-widest">Authorized Transaction</span>
                        </div>
                    </div>
                    <p class="text-[10px] text-gray-400 italic">This is a system generated payment voucher.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete(button) {
        Swal.fire({
            title: 'Delete Payment?',
            text: "This will reverse the paid amount on the linked bill. Are you sure?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#gray-400',
            confirmButtonText: 'Yes, delete it!',
            customClass: {
                popup: 'rounded-3xl',
                confirmButton: 'rounded-lg px-6 py-2.5',
                cancelButton: 'rounded-lg px-6 py-2.5'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                button.closest('form').submit();
            }
        });
    }
</script>
@endpush
@endsection
