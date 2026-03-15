@extends('admin.admin_master')

@section('title', 'Revenue Trends')

@section('admin')
    <div class="px-8 py-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8 no-print">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Revenue Trends</h2>
                <p class="text-sm text-gray-500 mt-1">12-month historical look into revenue growth and fluctuations</p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="window.print()"
                    class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors inline-flex items-center gap-2">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    Print
                </button>
            </div>
        </div>

        <!-- Main Trend Chart Card -->
        <div class="bg-white rounded-3xl border border-gray-200 p-8 shadow-sm relative overflow-hidden mb-8">
            <div class="relative z-10 flex flex-col lg:flex-row items-start justify-between mb-10">
                <div>
                     <h3 class="text-lg font-bold text-gray-900 mb-1">Monthly Revenue Performance</h3>
                     <p class="text-sm text-gray-500">Track current year vs. previous months performance</p>
                </div>
                <!-- Mini Stats from Trend -->
                <div class="flex items-center gap-12 mt-6 lg:mt-0">
                    <div>
                         <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Best Month</p>
                         <p class="text-xl font-bold text-blue-600">${{ number_format(max($data), 2) }}</p>
                    </div>
                    <div>
                         <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Avg Monthly</p>
                         <p class="text-xl font-bold text-gray-800">${{ count($data) > 0 ? number_format(array_sum($data) / count($data), 2) : '0.00' }}</p>
                    </div>
                </div>
            </div>

            <!-- Chart Canvas -->
            <div class="h-[450px] w-full">
                <canvas id="revenueTrendChart"></canvas>
            </div>
        </div>

        <!-- Trend Data Details (Monthly Table) -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
             <div class="p-6 border-b border-gray-100">
                 <h3 class="font-bold text-gray-900 capitalize">Historical Data Breakdown</h3>
             </div>
             <table class="w-full">
                 <thead>
                     <tr class="bg-gray-50 text-left border-b border-gray-200">
                         <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Month Period</th>
                         <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-widest">Revenue Value</th>
                         <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-widest">Growth %</th>
                     </tr>
                 </thead>
                 <tbody class="divide-y divide-gray-100">
                     @php $prev = 0; @endphp
                     @foreach (array_reverse(array_keys($data)) as $idx)
                         @php 
                            $curr = $data[$idx];
                            $month = $months[$idx];
                            // Growth calculation (compare with the month *after* it in chronological order, which is the previous in the data array)
                            $nextIdx = $idx - 1;
                            $growth = 0;
                            if ($nextIdx >= 0 && $data[$idx] > 0) {
                                // wait, data is from newest [11] to oldest [0] or vice versa?
                                // current logic: for($i=11; $i>=0; $i--) means [0] is 11 months ago, [11] is current month.
                                // so [idx-1] is previous month.
                            }
                            $growthHtml = '<span class="text-gray-400 italic">--</span>';
                            if ($idx > 0 && $data[$idx-1] > 0) {
                                $growth = (($data[$idx] - $data[$idx-1]) / $data[$idx-1]) * 100;
                                if ($growth > 0) {
                                    $growthHtml = '<span class="text-green-600 font-bold">+' . number_format($growth, 1) . '% ↑</span>';
                                } else if ($growth < 0) {
                                    $growthHtml = '<span class="text-red-500 font-bold">' . number_format($growth, 1) . '% ↓</span>';
                                } else {
                                    $growthHtml = '<span class="text-gray-600 font-bold">0.0%</span>';
                                }
                            }
                         @endphp
                         <tr class="hover:bg-gray-50/50 transition-colors">
                             <td class="px-6 py-5 text-sm font-bold text-gray-900">{{ $month }}</td>
                             <td class="px-6 py-5 text-sm font-bold text-gray-900 text-right">${{ number_format($curr, 2) }}</td>
                             <td class="px-6 py-5 text-sm text-center">{!! $growthHtml !!}</td>
                         </tr>
                     @endforeach
                 </tbody>
             </table>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('revenueTrendChart').getContext('2d');
            
            // Create Gradient
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(37, 99, 235, 0.2)');
            gradient.addColorStop(1, 'rgba(37, 99, 235, 0.0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($months) !!},
                    datasets: [{
                        label: 'Revenue Trend ($)',
                        data: {!! json_encode($data) !!},
                        borderColor: '#2563EB',
                        borderWidth: 4,
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#2563EB',
                        pointBorderWidth: 3,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        pointHoverBorderWidth: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1E293B',
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 14 },
                            padding: 12,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return '$' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                borderDash: [5, 5],
                                color: '#E2E8F0'
                            },
                            ticks: {
                                font: { size: 12, weight: '500' },
                                color: '#64748B',
                                callback: function(value) {
                                    if (value >= 1000) return '$' + (value/1000) + 'k';
                                    return '$' + value;
                                }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { size: 12, weight: '600' },
                                color: '#64748B'
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
@endsection
