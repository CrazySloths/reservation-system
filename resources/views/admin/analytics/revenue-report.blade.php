@extends('layouts.admin')

@section('title', 'Revenue Report - Admin')

@section('page-content')
<div class="space-y-6 print-area">
    <!-- Header with Date Filter -->
    <div class="bg-white rounded-lg shadow-sm p-6 no-print">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-lgu-headline">Revenue Report</h2>
                <p class="text-gray-600 mt-1">Financial performance and revenue analysis</p>
            </div>
            
            <!-- Date Range Filter & Actions -->
            <div class="flex flex-wrap items-end gap-3">
                <form method="GET" action="{{ route('admin.analytics.revenue-report') }}" class="flex flex-wrap items-end gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" 
                               class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0f3d3e] focus:border-[#0f3d3e]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" 
                               class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0f3d3e] focus:border-[#0f3d3e]">
                    </div>
                    <button type="submit" class="px-6 py-2 bg-lgu-button text-lgu-button-text rounded-lg hover:opacity-90 transition-all font-semibold">
                        <i data-lucide="filter" class="w-4 h-4 inline mr-2"></i>
                        Filter
                    </button>
                </form>
                <button onclick="window.print()" class="px-6 py-2 border-2 border-lgu-stroke text-lgu-headline rounded-lg hover:bg-lgu-bg transition-all font-semibold">
                    <i data-lucide="printer" class="w-4 h-4 inline mr-2"></i>
                    Print Report
                </button>
            </div>
        </div>
    </div>
    
    <!-- Print Header (hidden on screen, shown when printing) -->
    <div class="hidden print:block mb-6">
        <div class="text-center mb-4">
            <h1 class="text-2xl font-bold text-lgu-headline">Local Government Unit</h1>
            <h2 class="text-xl font-semibold text-gray-700 mt-2">Revenue Report</h2>
            <p class="text-sm text-gray-600 mt-1">{{ \Carbon\Carbon::parse($startDate)->format('F d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('F d, Y') }}</p>
            <p class="text-xs text-gray-500 mt-1">Generated on: {{ now()->format('F d, Y h:i A') }}</p>
        </div>
        <hr class="border-gray-300 mb-4">
    </div>

    <!-- Revenue Summary Card -->
    <div class="bg-lgu-headline rounded-lg shadow-lg p-8 border-2 border-lgu-highlight no-print">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-white text-sm mb-2 font-semibold uppercase tracking-wide">Total Revenue</p>
                <h3 class="text-5xl font-bold text-white">₱{{ number_format($totalRevenue, 2) }}</h3>
                <p class="text-white text-sm mt-2 font-medium">{{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
            </div>
            <div class="p-4 bg-lgu-highlight rounded-full flex items-center justify-center">
                <span class="text-5xl font-bold text-lgu-headline">₱</span>
            </div>
        </div>
    </div>
    
    <!-- Print-only Revenue Summary -->
    <div class="hidden print:block bg-white border-2 border-lgu-headline rounded-lg p-8 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm mb-2 font-semibold uppercase tracking-wide text-lgu-paragraph">Total Revenue</p>
                <h3 class="text-5xl font-bold text-lgu-headline">₱{{ number_format($totalRevenue, 2) }}</h3>
                <p class="text-sm mt-2 font-medium text-lgu-paragraph">{{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Monthly Revenue Trend -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-lgu-headline mb-4">Revenue Trend (Last 6 Months)</h3>
            <div id="revenueChart" style="height: 300px;"></div>
        </div>

        <!-- Revenue by Payment Method -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-lgu-headline mb-4">Revenue by Payment Method</h3>
            <div id="paymentMethodChart" style="height: 300px;"></div>
        </div>
    </div>

    <!-- Revenue by Facility Table -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-lgu-headline mb-4">Revenue by Facility</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Facility</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">City</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Bookings</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">% of Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($revenueByFacility as $facility)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $facility->facility_name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-600">{{ $facility->city_name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm text-gray-900">{{ $facility->total_bookings }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm font-semibold text-green-600">₱{{ number_format($facility->total_revenue, 2) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm text-gray-600">{{ $totalRevenue > 0 ? number_format(($facility->total_revenue / $totalRevenue) * 100, 1) : 0 }}%</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-2 text-gray-400"></i>
                            <p>No revenue data available for the selected period.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-sm font-bold text-gray-900">TOTAL</td>
                        <td class="px-6 py-4 text-right text-sm font-bold text-green-600">₱{{ number_format($totalRevenue, 2) }}</td>
                        <td class="px-6 py-4 text-right text-sm font-bold text-gray-900">100%</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Payment Method Breakdown -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-lgu-headline mb-4">Payment Method Breakdown</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse($revenueByPaymentMethod as $method)
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-600 capitalize">{{ str_replace('_', ' ', $method->payment_method) }}</span>
                    <i data-lucide="wallet" class="w-4 h-4 text-gray-400"></i>
                </div>
                <p class="text-2xl font-bold text-lgu-headline">₱{{ number_format($method->total_amount, 2) }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $method->transaction_count }} transaction{{ $method->transaction_count > 1 ? 's' : '' }}</p>
            </div>
            @empty
            <div class="col-span-4 text-center py-8 text-gray-500">
                No payment data available
            </div>
            @endforelse
        </div>
    </div>
</div>

@push('styles')
<style>
@media print {
    body * {
        visibility: hidden;
    }
    .print-area, .print-area * {
        visibility: visible;
    }
    .print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .no-print {
        display: none !important;
    }
    .print\:block {
        display: block !important;
    }
    /* Ensure charts print properly */
    #revenueChart, #paymentMethodChart {
        page-break-inside: avoid;
    }
    /* Better table printing */
    table {
        page-break-inside: auto;
    }
    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }
    thead {
        display: table-header-group;
    }
    tfoot {
        display: table-footer-group;
    }
}
</style>
@endpush

@push('styles')
<style>
@media print {
    body * {
        visibility: hidden;
    }
    .print-area, .print-area * {
        visibility: visible;
    }
    .print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .no-print {
        display: none !important;
    }
    .print\:block {
        display: block !important;
    }
    @page {
        size: A4;
        margin: 1cm;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/apexcharts@3.45.1/dist/apexcharts.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        // Monthly Revenue Trend Chart
        const revenueContainer = document.querySelector("#revenueChart");
        if (revenueContainer) {
            const revenueData = @json($monthlyRevenue);
            
            const revenueOptions = {
                series: [{
                    name: 'Revenue',
                    data: revenueData.map(item => parseFloat(item.revenue) || 0)
                }],
                chart: {
                    height: 300,
                    type: 'area',
                    toolbar: { show: false }
                },
                colors: ['#0f3d3e'],
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2 },
                xaxis: {
                    categories: revenueData.map(item => item.month),
                    labels: {
                        rotate: -45,
                        rotateAlways: true,
                        hideOverlappingLabels: true,
                        style: {
                            fontSize: '10px'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function (val) {
                            return '₱' + val.toLocaleString();
                        }
                    }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.3,
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return '₱' + val.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        }
                    }
                }
            };
            
            const revenueChart = new ApexCharts(revenueContainer, revenueOptions);
            revenueChart.render();
        }

        // Payment Method Chart
        const paymentContainer = document.querySelector("#paymentMethodChart");
        if (paymentContainer) {
            const paymentData = @json($revenueByPaymentMethod);
            
            const paymentOptions = {
                series: paymentData.map(item => parseFloat(item.total_amount) || 0),
                chart: {
                    type: 'donut',
                    height: 300
                },
                labels: paymentData.map(item => {
                    const method = item.payment_method || '';
                    return method.charAt(0).toUpperCase() + method.slice(1).replace('_', ' ');
                }),
                colors: ['#0f3d3e', '#14b8a6', '#faae2b', '#ffa8ba'],
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return '₱' + val.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        }
                    }
                }
            };
            
            const paymentChart = new ApexCharts(paymentContainer, paymentOptions);
            paymentChart.render();
        }

        // Initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }, 100);
});
</script>
@endpush
@endsection

