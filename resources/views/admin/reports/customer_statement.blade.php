@extends('admin.admin_master')

@section('title', 'Customer Statement')

@section('admin')
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white border-b border-gray-200 px-8 py-5 no-print">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Customer Statement</h2>
                    <p class="text-sm text-gray-500 mt-1">Detailed ledger of customer transactions and balance</p>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="window.print()" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors inline-flex items-center gap-2">
                        <i data-lucide="printer" class="w-4 h-4"></i> Print
                    </button>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto px-8 py-6">
            <!-- Selector/Filters -->
            <form method="GET" action="{{ route('reports.customer-statement') }}" class="bg-white rounded-xl border border-gray-200 p-6 mb-6 shadow-sm no-print">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div class="md:col-span-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Select Customer</label>
                        <select name="customer_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375]">
                            <option value="">Choose a customer...</option>
                            @php /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Customer[] $customers */ @endphp
                            @foreach ($customers as $customer)
                                @php /** @var \App\Models\Customer $customer */ @endphp
                                <option value="{{ $customer->id }}" {{ (string)$customerId === (string)$customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">From</label>
                        <input type="date" name="from_date" value="{{ $fromDate }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">To</label>
                        <input type="date" name="to_date" value="{{ $toDate }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                    <button type="submit" class="w-full px-6 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-bold hover:bg-[#229967]">Generate Statement</button>
                </div>
            </form>

            @if ($customer)
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden p-8">
                <div class="flex justify-between items-start mb-8">
                    <div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Customer Details</h4>
                        <h3 class="text-xl font-black text-gray-900">{{ $customer->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $customer->email }}</p>
                        <p class="text-sm text-gray-500">{{ $customer->phone }}</p>
                    </div>
                    <div class="text-right">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Statement Period</h4>
                        <p class="text-sm font-bold text-gray-900">{{ date('M d, Y', strtotime($fromDate)) }} - {{ date('M d, Y', strtotime($toDate)) }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-3 border-y border-gray-100 py-4 mb-6">
                    <div class="text-center">
                        <p class="text-[10px] font-bold text-gray-400 uppercase">Opening Balance</p>
                        <p class="text-sm font-bold text-gray-900">${{ number_format($openingBalance, 2) }}</p>
                    </div>
                    <div class="text-center border-x border-gray-100">
                        <p class="text-[10px] font-bold text-gray-400 uppercase">Current Activity</p>
                        <p class="text-sm font-bold text-gray-900">${{ number_format($transactions->sum('debit') - $transactions->sum('credit'), 2) }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-[10px] font-bold text-gray-400 uppercase">Closing Balance</p>
                        <p class="text-lg font-black text-[#28A375]">${{ number_format($openingBalance + $transactions->sum('debit') - $transactions->sum('credit'), 2) }}</p>
                    </div>
                </div>

                <table class="w-full border-collapse">
                    <thead>
                        <tr class="text-left bg-gray-50 border-b border-gray-200">
                            <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase">Ref / Type</th>
                            <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-500 uppercase">Debit</th>
                            <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-500 uppercase">Credit</th>
                            <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-500 uppercase">Balance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <!-- Opening Balance Row -->
                        <tr class="bg-gray-50/30">
                            <td class="px-4 py-3 text-xs text-gray-500">{{ date('M d, Y', strtotime($fromDate)) }}</td>
                            <td class="px-4 py-3 text-xs font-bold text-gray-600">Opening Balance</td>
                            <td class="px-4 py-3 text-right"></td>
                            <td class="px-4 py-3 text-right"></td>
                            <td class="px-4 py-3 text-right text-xs font-bold text-gray-900">${{ number_format($openingBalance, 2) }}</td>
                        </tr>

                        @php $runningBalance = $openingBalance; @endphp
                        @foreach ($transactions as $transaction)
                        @php $runningBalance += ($transaction['debit'] - $transaction['credit']); @endphp
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-3 text-xs text-gray-600">{{ $transaction['date']->format('M d, Y') }}</td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-bold text-gray-900">{{ $transaction['reference'] }}</span>
                                <span class="px-1.5 py-0.5 ml-2 bg-gray-100 text-gray-500 rounded text-[8px] font-bold uppercase tracking-tight">{{ $transaction['type'] }}</span>
                            </td>
                            <td class="px-4 py-3 text-right text-xs font-semibold text-gray-900">{{ $transaction['debit'] > 0 ? '$'.number_format($transaction['debit'], 2) : '-' }}</td>
                            <td class="px-4 py-3 text-right text-xs font-semibold text-gray-900">{{ $transaction['credit'] > 0 ? '$'.number_format($transaction['credit'], 2) : '-' }}</td>
                            <td class="px-4 py-3 text-right text-xs font-bold text-gray-900">${{ number_format($runningBalance, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center text-gray-400">
                <i data-lucide="user" class="w-12 h-12 mx-auto mb-4 opacity-20"></i>
                <p class="text-lg font-medium">Please select a customer to view their statement</p>
            </div>
            @endif
        </main>
    </div>
@endsection
