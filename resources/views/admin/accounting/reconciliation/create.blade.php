@extends('admin.admin_master')
@section('admin')
<div class="px-8 py-6">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">New Bank Statement</h1>
            <p class="text-gray-500 mt-1">Initialize reconciliation by entering statement details</p>
        </div>
        <a href="{{ route('accounting.reconciliation.index') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all">
           <i data-lucide="arrow-left" class="w-4 h-4"></i>
           Back to List
        </a>
    </div>

    <div class="max-w-4xl mx-auto">
        <form action="{{ route('accounting.reconciliation.store') }}" method="POST">
            @csrf
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i data-lucide="file-plus" class="w-5 h-5 text-[#28A375]"></i>
                        Statement Details
                    </h2>
                </div>
                
                <div class="p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">Bank Account <span class="text-red-500">*</span></label>
                            <select name="account_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all" required>
                                <option value="">Select Bank Account</option>
                                @foreach ($bankAccounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">Statement Number <span class="text-red-500">*</span></label>
                            <input type="text" name="statement_no" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all" placeholder="e.g. STMT-2026-03" required>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">Start Date <span class="text-red-500">*</span></label>
                            <input type="date" name="start_date" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all" required>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">End Date <span class="text-red-500">*</span></label>
                            <input type="date" name="end_date" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all" required>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">Opening Balance <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">$</span>
                                <input type="number" step="0.01" name="opening_balance" class="w-full pl-8 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all font-mono" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">Closing Balance <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">$</span>
                                <input type="number" step="0.01" name="closing_balance" class="w-full pl-8 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all font-mono" placeholder="0.00" required>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">Notes (Optional)</label>
                        <textarea name="notes" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all resize-none" placeholder="Additional reference notes..."></textarea>
                    </div>
                </div>

                <div class="px-8 py-6 border-t border-gray-100 bg-gray-50/50 flex items-center justify-end gap-4">
                    <button type="button" onclick="history.back()" class="px-6 py-2.5 rounded-xl text-sm font-bold text-gray-600 hover:bg-gray-100 transition-all">Cancel</button>
                    <button type="submit" class="px-8 py-2.5 bg-[#28A375] text-white rounded-xl text-sm font-bold hover:bg-[#229967] transition-all shadow-lg shadow-green-200">
                        Create & Start Reconciling
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
