@extends('admin.admin_master')

@section('title', 'Trial Balance')

@section('admin')
    <style>
        @media print {

            .layout-sidebar,
            .layout-header,
            .no-print {
                display: none !important;
            }

            body {
                background: white !important;
            }

            .print-full-width {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
        }
    </style>

    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Top Navigation -->
        <header class="bg-white border-b border-gray-200 px-8 py-5 no-print">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Trial Balance</h2>
                    <p class="text-sm text-gray-500 mt-1">Verification of total debits and credits</p>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="window.print()"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors inline-flex items-center gap-2">
                        <i data-lucide="printer" class="w-4 h-4"></i>
                        Print
                    </button>
                    <button
                        class="px-4 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] transition-colors inline-flex items-center gap-2">
                        <i data-lucide="download" class="w-4 h-4"></i>
                        Export
                    </button>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto px-8 py-6">
            <!-- Report Filters -->
            <form method="GET" action="{{ route('reports.trial-balance') }}"
                class="bg-white rounded-lg border border-gray-200 p-5 mb-6 no-print">
                <div class="flex flex-col lg:flex-row items-center gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">As of Date</label>
                        <input type="date" name="report_date" value="{{ $reportDate }}"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                    </div>
                    <div class="pt-7">
                        <button type="submit"
                            class="px-6 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] transition-colors inline-flex items-center gap-2">
                            <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                            Update
                        </button>
                    </div>
                </div>
            </form>

            <div class="bg-white rounded-lg border border-gray-200 print-full-width">
                <!-- Report Header -->
                <div class="border-b border-gray-200 px-8 py-8 text-center">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $settings?->company_name ?? 'XaliyePro' }}</h1>
                    <h2 class="text-xl font-bold text-gray-900 mt-2">TRIAL BALANCE</h2>
                    <p class="text-sm text-gray-600 mt-1">As of: {{ date('F d, Y', strtotime($reportDate)) }}</p>
                </div>

                <!-- Trial Balance Content -->
                <div class="p-8">
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="px-4 py-4 text-left text-sm font-bold text-gray-900 w-24">Code</th>
                                    <th class="px-4 py-4 text-left text-sm font-bold text-gray-900">Account Name</th>
                                    <th class="px-4 py-4 text-right text-sm font-bold text-gray-900 w-48">Debit</th>
                                    <th class="px-4 py-4 text-right text-sm font-bold text-gray-900 w-48">Credit</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @if(count($trialBalanceData) > 0)
                                    @foreach ($trialBalanceData as $data)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-4 py-4 text-sm font-medium text-gray-600">{{ $data['code'] }}</td>
                                        <td class="px-4 py-4 text-sm font-medium text-gray-900">{{ $data['name'] }}</td>
                                        <td class="px-4 py-4 text-sm font-semibold text-gray-900 text-right">
                                            {{ $data['debit'] > 0 ? '$' . number_format($data['debit'], 2) : '-' }}
                                        </td>
                                        <td class="px-4 py-4 text-sm font-semibold text-gray-900 text-right">
                                            {{ $data['credit'] > 0 ? '$' . number_format($data['credit'], 2) : '-' }}
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="px-4 py-12 text-center">
                                            <div class="flex flex-col items-center gap-2">
                                                <i data-lucide="info" class="w-8 h-8 text-gray-300"></i>
                                                <p class="text-gray-500 text-sm italic">No account movements found as of
                                                    this date.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-100 border-t-2 border-gray-900">
                                    <td colspan="2" class="px-4 py-5 text-base font-bold text-gray-900 uppercase">
                                        Total
                                    </td>
                                    <td
                                        class="px-4 py-5 text-base font-bold text-gray-900 text-right border-double border-b-4 border-gray-400">
                                        ${{ number_format($totalDebit, 2) }}
                                    </td>
                                    <td
                                        class="px-4 py-5 text-base font-bold text-gray-900 text-right border-double border-b-4 border-gray-400">
                                        ${{ number_format($totalCredit, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Integrity Check -->
                    @if (abs($totalDebit - $totalCredit) < 0.01)
                        <div class="mt-8 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3">
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0">
                                <i data-lucide="check" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-green-900">Balanced</h4>
                                <p class="text-xs text-green-700">Total debits match total credits successfully.</p>
                            </div>
                        </div>
                    @else
                        <div class="mt-8 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center gap-3">
                            <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center flex-shrink-0">
                                <i data-lucide="alert-triangle" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-red-900">Unbalanced</h4>
                                <p class="text-xs text-red-700">Warning: There is a difference of
                                    ${{ number_format(abs($totalDebit - $totalCredit), 2) }} between total debits and
                                    credits.</p>
                            </div>
                        </div>
                    @endif

                    <!-- Footer -->
                    <div class="mt-12 pt-6 border-t border-gray-200 text-center">
                        <p class="text-xs text-gray-500">Report generated on {{ date('F d, Y \a\t h:i A') }}</p>
                        <p class="text-xs font-medium text-gray-600 mt-1">{{ $settings?->company_name ?? 'XaliyePro' }} -
                            Internal Use Only</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection
