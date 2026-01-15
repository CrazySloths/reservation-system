@extends('layouts.admin')

@section('title', 'Booking Statistics - Admin')

@section('page-content')
<div class="space-y-6 print-area">
    <!-- Header with Date Filter -->
    <div class="bg-white rounded-lg shadow-sm p-6 no-print">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-lgu-headline">Booking Statistics</h2>
                <p class="text-gray-600 mt-1">Comprehensive booking analytics and trends</p>
            </div>
            
            <!-- Date Range Filter & Print Button -->
            <div class="flex flex-wrap items-end gap-3">
                <form method="GET" action="{{ route('admin.analytics.booking-statistics') }}" class="flex flex-wrap items-end gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" 
                               class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-lgu-headline focus:border-lgu-headline">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" 
                               class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-lgu-headline focus:border-lgu-headline">
                    </div>
                    <button type="submit" class="px-6 py-2 bg-lgu-button text-lgu-button-text rounded-lg hover:opacity-90 transition-all font-semibold">
                        <i data-lucide="filter" class="w-4 h-4 inline mr-1"></i>
                        Filter
                    </button>
                </form>
                <button onclick="window.print()" class="px-6 py-2 border-2 border-lgu-stroke text-lgu-headline rounded-lg hover:bg-lgu-bg transition-all font-semibold">
                    <i data-lucide="printer" class="w-4 h-4 inline mr-1"></i>
                    Print
                </button>
                
                <!-- Export Dropdown -->
                <div class="relative inline-block" x-data="{ open: false }">
                    <button @click="open = !open" class="px-6 py-2 bg-lgu-secondary text-white rounded-lg hover:opacity-90 transition-all font-semibold flex items-center">
                        <i data-lucide="download" class="w-4 h-4 mr-1"></i>
                        Export
                        <i data-lucide="chevron-down" class="w-4 h-4 ml-1"></i>
                    </button>
                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-10">
                        <a href="{{ route('admin.analytics.export-booking-statistics-excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-lgu-bg">
                            <i data-lucide="file-spreadsheet" class="w-4 h-4 inline mr-2"></i>
                            Export as Excel
                        </a>
                        <a href="{{ route('admin.analytics.export-booking-statistics-pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-lgu-bg">
                            <i data-lucide="file-text" class="w-4 h-4 inline mr-2"></i>
                            Export as PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Print Header (hidden on screen, shown when printing) -->
    <div class="hidden print:block mb-6">
        <div class="text-center mb-4">
            <h1 class="text-2xl font-bold text-lgu-headline">Local Government Unit</h1>
            <h2 class="text-xl font-semibold text-gray-700">Booking Statistics Report</h2>
            <p class="text-gray-600">Period: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
        </div>
    </div>

    <!-- Key Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Bookings -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-medium text-gray-600">Total Bookings</h3>
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i data-lucide="calendar-check" class="w-5 h-5 text-blue-600"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-lgu-headline">{{ number_format($totalBookings) }}</p>
            <p class="text-xs text-gray-500 mt-2">All booking requests</p>
        </div>

        <!-- Confirmed Bookings -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-medium text-gray-600">Confirmed</h3>
                <div class="p-2 bg-green-100 rounded-lg">
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-green-600">{{ number_format($paidBookings) }}</p>
            <p class="text-xs text-gray-500 mt-2">Paid & confirmed</p>
        </div>

        <!-- Conversion Rate -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-medium text-gray-600">Conversion Rate</h3>
                <div class="p-2 bg-purple-100 rounded-lg">
                    <i data-lucide="trending-up" class="w-5 h-5 text-purple-600"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-purple-600">{{ number_format($conversionRate, 1) }}%</p>
            <p class="text-xs text-gray-500 mt-2">Booking to payment rate</p>
        </div>

        <!-- Average Value -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-medium text-gray-600">Avg. Booking Value</h3>
                <div class="p-2 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <span class="text-xl font-bold text-yellow-600">₱</span>
                </div>
            </div>
            <p class="text-3xl font-bold text-lgu-headline">₱{{ number_format($avgBookingValue, 2) }}</p>
            <p class="text-xs text-gray-500 mt-2">Per booking</p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Booking Trend Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-lgu-headline mb-4">Daily Booking Trend (Last 30 Days)</h3>
            <div id="trendChart" style="height: 300px;"></div>
        </div>

        <!-- Bookings by Status -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-lgu-headline mb-4">Bookings by Status</h3>
            <div id="statusChart" style="height: 300px;"></div>
        </div>
    </div>

    <!-- Additional Metrics Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Peak Booking Hours -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-lgu-headline mb-4">Peak Booking Hours</h3>
            <div class="space-y-3">
                @forelse($peakHours as $hour)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-lgu-headline/10 rounded-lg">
                            <i data-lucide="clock" class="w-4 h-4 text-lgu-headline"></i>
                        </div>
                        <span class="text-gray-700 font-medium">
                            {{ sprintf('%02d:00', $hour->hour) }} - {{ sprintf('%02d:00', $hour->hour + 1) }}
                        </span>
                    </div>
                    <span class="text-gray-600">{{ $hour->count }} bookings</span>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">No data available</p>
                @endforelse
            </div>
        </div>

        <!-- Peak Booking Days -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-lgu-headline mb-4">Peak Booking Days</h3>
            <div class="space-y-3">
                @forelse($peakDays as $day)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-teal-500/10 rounded-lg">
                            <i data-lucide="calendar" class="w-4 h-4 text-teal-500"></i>
                        </div>
                        <span class="text-gray-700 font-medium">{{ $day->day_name }}</span>
                    </div>
                    <span class="text-gray-600">{{ $day->count }} bookings</span>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">No data available</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Popular Facilities Table -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-lgu-headline mb-4">Most Popular Facilities</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Facility</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">City</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Bookings</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($popularFacilities as $index => $facility)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($index === 0)
                                    <span class="inline-flex items-center justify-center w-8 h-8 bg-yellow-100 rounded-full">
                                        <i data-lucide="award" class="w-5 h-5 text-yellow-600"></i>
                                    </span>
                                @elseif($index === 1)
                                    <span class="inline-flex items-center justify-center w-8 h-8 bg-gray-200 rounded-full">
                                        <i data-lucide="award" class="w-5 h-5 text-gray-600"></i>
                                    </span>
                                @elseif($index === 2)
                                    <span class="inline-flex items-center justify-center w-8 h-8 bg-orange-100 rounded-full">
                                        <i data-lucide="award" class="w-5 h-5 text-orange-600"></i>
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center w-8 h-8 bg-gray-50 text-gray-600 rounded-full font-medium">{{ $index + 1 }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $facility->facility_name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-600">{{ $facility->city_name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm font-semibold text-lgu-headline">{{ $facility->booking_count }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm font-semibold text-green-600">₱{{ number_format($facility->total_revenue, 2) }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-2 text-gray-400"></i>
                            <p>No facility data available for the selected period.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/apexcharts@3.45.1/dist/apexcharts.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        // Daily Trend Chart
        const trendContainer = document.querySelector("#trendChart");
        if (trendContainer) {
            const trendData = @json($trendData);
            
            const trendOptions = {
                series: [{
                    name: 'Bookings',
                    data: trendData.map(item => item.count)
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
                    categories: trendData.map(item => item.date),
                    labels: {
                        rotate: -45,
                        rotateAlways: true,
                        hideOverlappingLabels: true,
                        style: {
                            fontSize: '10px'
                        },
                        tickAmount: 10
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function (val) {
                            return Math.floor(val);
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
                            return val + ' bookings';
                        }
                    }
                }
            };
            
            const trendChart = new ApexCharts(trendContainer, trendOptions);
            trendChart.render();
        }

        // Status Breakdown Chart
        const statusContainer = document.querySelector("#statusChart");
        if (statusContainer) {
            const statusData = @json($bookingsByStatus);
            
            const statusOptions = {
                series: statusData.map(item => item.count),
                chart: {
                    type: 'donut',
                    height: 300
                },
                labels: statusData.map(item => item.status.charAt(0).toUpperCase() + item.status.slice(1)),
                colors: ['#faae2b', '#0f3d3e', '#14b8a6', '#10b981', '#3b82f6', '#ef4444'],
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + ' bookings';
                        }
                    }
                }
            };
            
            const statusChart = new ApexCharts(statusContainer, statusOptions);
            statusChart.render();
        }

        // Initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }, 100);
});
</script>
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
    /* Better table printing */
    table {
        page-break-inside: auto;
    }
    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }
    /* Ensure charts print properly */
    #trendChart, #statusChart {
        page-break-inside: avoid;
    }
}
</style>
@endpush
@endsection

