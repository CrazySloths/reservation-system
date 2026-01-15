@extends('layouts.treasurer')

@section('title', 'Monthly Summary Report')
@section('page-title', 'Monthly Summary Report')

@section('page-content')

<div class="space-y-gr-lg">
    <!-- Month Filter & Export -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('treasurer.reports.monthly-summary') }}" class="flex flex-col md:flex-row items-end gap-4">
            <div class="flex-1">
                <label for="month" class="block text-small font-semibold text-gray-700 mb-gr-xs">Select Month</label>
                <input type="month" 
                       name="month" 
                       id="month" 
                       value="{{ $selectedMonth->format('Y-m') }}"
                       max="{{ now()->format('Y-m') }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-lgu-button focus:border-transparent">
            </div>
            
            <button type="submit" 
                    class="bg-lgu-button text-lgu-button-text px-6 py-2.5 rounded-lg hover:bg-lgu-highlight transition-colors font-semibold inline-flex items-center gap-2">
                <i data-lucide="search" class="w-4 h-4"></i>
                View Report
            </button>
            
            @if($stats['total_transactions'] > 0)
            <a href="{{ route('treasurer.reports.monthly-summary.export', ['month' => $selectedMonth->format('Y-m')]) }}" 
               class="bg-green-600 text-white px-6 py-2.5 rounded-lg hover:bg-green-700 transition-colors font-semibold inline-flex items-center gap-2">
                <i data-lucide="download" class="w-4 h-4"></i>
                Export PDF
            </a>
            @endif
        </form>
    </div>

    <!-- Report Month Header -->
    <div class="text-center">
        <h2 class="text-h3 font-bold text-lgu-headline">{{ $selectedMonth->format('F Y') }}</h2>
        <p class="text-small text-gray-600 mt-gr-xs">Monthly Revenue Summary</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-gr-md">
        <!-- Total Revenue -->
        <div class="bg-green-500 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-gr-sm">
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-6 h-6"></i>
                </div>
            </div>
            <p class="text-caption opacity-90 mb-gr-xs">Total Revenue</p>
            <p class="text-2xl font-bold">₱{{ number_format($stats['total_revenue'], 2) }}</p>
        </div>

        <!-- Total Transactions -->
        <div class="bg-blue-500 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-gr-sm">
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <i data-lucide="file-check" class="w-6 h-6"></i>
                </div>
            </div>
            <p class="text-caption opacity-90 mb-gr-xs">Total Transactions</p>
            <p class="text-2xl font-bold">{{ $stats['total_transactions'] }}</p>
        </div>

        <!-- Average Transaction -->
        <div class="bg-purple-500 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-gr-sm">
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <i data-lucide="bar-chart-2" class="w-6 h-6"></i>
                </div>
            </div>
            <p class="text-caption opacity-90 mb-gr-xs">Average Transaction</p>
            <p class="text-2xl font-bold">₱{{ number_format($stats['average_transaction'], 2) }}</p>
        </div>

        <!-- Days with Collections -->
        <div class="bg-orange-500 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-gr-sm">
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <i data-lucide="calendar" class="w-6 h-6"></i>
                </div>
            </div>
            <p class="text-caption opacity-90 mb-gr-xs">Active Days</p>
            <p class="text-2xl font-bold">{{ $stats['days_with_collections'] }}</p>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-gr-md">
        <!-- Daily Collections Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-body font-bold text-lgu-headline mb-gr-md flex items-center">
                <i data-lucide="line-chart" class="w-5 h-5 mr-gr-xs text-lgu-button"></i>
                Daily Collections Trend
            </h3>
            <div id="dailyCollectionsChart"></div>
        </div>

        <!-- Payment Methods Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-body font-bold text-lgu-headline mb-gr-md flex items-center">
                <i data-lucide="pie-chart" class="w-5 h-5 mr-gr-xs text-lgu-button"></i>
                Payment Methods Distribution
            </h3>
            <div id="paymentMethodsChart"></div>
        </div>
    </div>

    <!-- Payment Method Breakdown -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-body font-bold text-lgu-headline mb-gr-md flex items-center">
            <i data-lucide="credit-card" class="w-5 h-5 mr-gr-xs text-lgu-button"></i>
            Payment Method Summary
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-gr-sm">
            <!-- Cash -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <i data-lucide="banknote" class="w-4 h-4 text-green-600"></i>
                    </div>
                    <p class="font-semibold text-green-800">Cash</p>
                </div>
                <p class="text-xl font-bold text-green-900">₱{{ number_format($methodBreakdown['cash']['amount'], 2) }}</p>
                <p class="text-caption text-green-700 mt-1">{{ $methodBreakdown['cash']['count'] }} transactions</p>
            </div>

            <!-- GCash -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i data-lucide="smartphone" class="w-4 h-4 text-blue-600"></i>
                    </div>
                    <p class="font-semibold text-blue-800">GCash</p>
                </div>
                <p class="text-xl font-bold text-blue-900">₱{{ number_format($methodBreakdown['gcash']['amount'], 2) }}</p>
                <p class="text-caption text-blue-700 mt-1">{{ $methodBreakdown['gcash']['count'] }} transactions</p>
            </div>

            <!-- PayMaya -->
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <i data-lucide="credit-card" class="w-4 h-4 text-purple-600"></i>
                    </div>
                    <p class="font-semibold text-purple-800">PayMaya</p>
                </div>
                <p class="text-xl font-bold text-purple-900">₱{{ number_format($methodBreakdown['paymaya']['amount'], 2) }}</p>
                <p class="text-caption text-purple-700 mt-1">{{ $methodBreakdown['paymaya']['count'] }} transactions</p>
            </div>

            <!-- Bank Transfer -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i data-lucide="building-2" class="w-4 h-4 text-yellow-600"></i>
                    </div>
                    <p class="font-semibold text-yellow-800">Bank Transfer</p>
                </div>
                <p class="text-xl font-bold text-yellow-900">₱{{ number_format($methodBreakdown['bank_transfer']['amount'], 2) }}</p>
                <p class="text-caption text-yellow-700 mt-1">{{ $methodBreakdown['bank_transfer']['count'] }} transactions</p>
            </div>

            <!-- Credit Card -->
            <div class="bg-pink-50 border border-pink-200 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 bg-pink-100 rounded-full flex items-center justify-center">
                        <i data-lucide="credit-card" class="w-4 h-4 text-pink-600"></i>
                    </div>
                    <p class="font-semibold text-pink-800">Credit Card</p>
                </div>
                <p class="text-xl font-bold text-pink-900">₱{{ number_format($methodBreakdown['credit_card']['amount'], 2) }}</p>
                <p class="text-caption text-pink-700 mt-1">{{ $methodBreakdown['credit_card']['count'] }} transactions</p>
            </div>
        </div>
    </div>

    <!-- Top Facilities -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-body font-bold text-lgu-headline mb-gr-md flex items-center">
            <i data-lucide="award" class="w-5 h-5 mr-gr-xs text-lgu-button"></i>
            Top 5 Revenue-Generating Facilities
        </h3>

        @if($topFacilities->count() > 0)
        <div class="space-y-3">
            @foreach($topFacilities as $facilityName => $data)
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-lgu-button rounded-full flex items-center justify-center text-white font-bold">
                        {{ $loop->iteration }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">{{ $facilityName }}</p>
                        <p class="text-small text-gray-600">{{ $data['bookings'] }} booking(s)</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-lg font-bold text-lgu-headline">₱{{ number_format($data['revenue'], 2) }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-center text-gray-500 py-8">No facility data available for this month.</p>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Check if ApexCharts is loaded
    if (typeof ApexCharts === 'undefined') {
        console.error('ApexCharts not loaded');
        return;
    }

    // Daily Collections Chart
    const dailyCollections = @json($dailyCollections);
    const dailyDates = Object.keys(dailyCollections).sort();
    const dailyAmounts = dailyDates.map(date => dailyCollections[date].total);

    const dailyOptions = {
        series: [{
            name: 'Collections',
            data: dailyAmounts
        }],
        chart: {
            type: 'area',
            height: 300,
            toolbar: {
                show: false
            },
            zoom: {
                enabled: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 3,
            colors: ['#0f5b3a']
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.1,
                stops: [0, 100]
            },
            colors: ['#0f5b3a']
        },
        colors: ['#0f5b3a'],
        xaxis: {
            categories: dailyDates.map(date => {
                const d = new Date(date);
                return d.getDate();
            }),
            title: {
                text: 'Day of Month',
                style: {
                    fontSize: '12px',
                    fontWeight: 600,
                    color: '#6b7280'
                }
            },
            labels: {
                style: {
                    fontSize: '11px',
                    colors: '#64748b'
                }
            }
        },
        yaxis: {
            forceNiceScale: true,
            labels: {
                formatter: function(value) {
                    if (value >= 1000000) {
                        return '₱' + (value / 1000000).toFixed(1) + 'M';
                    } else if (value >= 1000) {
                        return '₱' + (value / 1000).toFixed(1) + 'K';
                    }
                    return '₱' + Math.round(value).toLocaleString('en-US');
                },
                style: {
                    fontSize: '11px',
                    colors: '#64748b'
                }
            },
            title: {
                text: 'Amount (₱)',
                style: {
                    fontSize: '12px',
                    fontWeight: 600,
                    color: '#6b7280'
                }
            }
        },
        tooltip: {
            y: {
                formatter: function(value) {
                    return '₱' + value.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                }
            }
        },
        grid: {
            borderColor: '#e2e8f0',
            strokeDashArray: 4
        }
    };

    const dailyChart = new ApexCharts(document.querySelector('#dailyCollectionsChart'), dailyOptions);
    dailyChart.render();

    // Payment Methods Chart (Donut)
    const methodBreakdown = @json($methodBreakdown);
    const methodLabels = [];
    const methodValues = [];
    
    Object.keys(methodBreakdown).forEach(method => {
        if (methodBreakdown[method].amount > 0) {
            methodLabels.push(method.replace('_', ' ').toUpperCase());
            methodValues.push(methodBreakdown[method].amount);
        }
    });

    const methodOptions = {
        series: methodValues,
        chart: {
            type: 'donut',
            height: 300
        },
        labels: methodLabels,
        colors: ['#0f5b3a', '#3b82f6', '#8b5cf6', '#f59e0b', '#ec4899'],
        legend: {
            position: 'bottom',
            fontSize: '12px',
            fontWeight: 600
        },
        dataLabels: {
            enabled: true,
            formatter: function(val, opts) {
                return val.toFixed(1) + '%';
            },
            style: {
                fontSize: '12px',
                fontWeight: 'bold'
            }
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total',
                            fontSize: '14px',
                            fontWeight: 600,
                            color: '#374151',
                            formatter: function (w) {
                                const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                return '₱' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                            }
                        }
                    }
                }
            }
        },
        tooltip: {
            y: {
                formatter: function(value) {
                    return '₱' + value.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                }
            }
        }
    };

    const methodChart = new ApexCharts(document.querySelector('#paymentMethodsChart'), methodOptions);
    methodChart.render();
});
</script>
@endpush
