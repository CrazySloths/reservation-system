@extends('layouts.admin')

@section('title', 'Citizen Analytics - Admin')

@section('page-content')
<div class="space-y-6 print-area">
    <!-- Header with Date Filter -->
    <div class="bg-white rounded-lg shadow-sm p-6 no-print">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-lgu-headline">Citizen Analytics</h2>
                <p class="text-gray-600 mt-1">User engagement and behavior insights</p>
            </div>
            
            <!-- Date Range Filter & Export Buttons -->
            <div class="flex flex-wrap items-end gap-3">
                <form method="GET" action="{{ route('admin.analytics.citizen-analytics') }}" class="flex flex-wrap items-end gap-3">
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
                        <a href="{{ route('admin.analytics.export-citizen-analytics-excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-lgu-bg">
                            <i data-lucide="file-spreadsheet" class="w-4 h-4 inline mr-2"></i>
                            Export as Excel
                        </a>
                        <a href="{{ route('admin.analytics.citizen-analytics.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-lgu-bg">
                            <i data-lucide="file-text" class="w-4 h-4 inline mr-2"></i>
                            Export as CSV
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
            <h2 class="text-xl font-semibold text-gray-700">Citizen Analytics Report</h2>
            <p class="text-gray-600">Period: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
        </div>
    </div>

    <!-- Key Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Active Citizens -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-medium text-gray-600">Active Citizens</h3>
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i data-lucide="users" class="w-5 h-5 text-blue-600"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-lgu-headline">{{ number_format($totalCitizens) }}</p>
            <p class="text-xs text-gray-500 mt-2">Made bookings in period</p>
        </div>

        <!-- New Citizens -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-medium text-gray-600">New Citizens</h3>
                <div class="p-2 bg-green-100 rounded-lg">
                    <i data-lucide="user-plus" class="w-5 h-5 text-green-600"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-green-600">{{ number_format($newCitizens) }}</p>
            <p class="text-xs text-gray-500 mt-2">First-time bookers</p>
        </div>

        <!-- Repeat Customers -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-medium text-gray-600">Repeat Customers</h3>
                <div class="p-2 bg-purple-100 rounded-lg">
                    <i data-lucide="repeat" class="w-5 h-5 text-purple-600"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-purple-600">{{ number_format($repeatCustomers) }}</p>
            <p class="text-xs text-gray-500 mt-2">Made 2+ bookings</p>
        </div>

        <!-- Avg. Bookings/Citizen -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-medium text-gray-600">Avg. Bookings</h3>
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <i data-lucide="activity" class="w-5 h-5 text-yellow-600"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-lgu-headline">{{ number_format($avgBookingsPerCitizen, 1) }}</p>
            <p class="text-xs text-gray-500 mt-2">Per citizen</p>
        </div>
    </div>

    <!-- Citizen Growth Trend Chart -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-lgu-headline mb-4">Citizen Growth Trend (Last 12 Months)</h3>
        <div id="growthChart" style="height: 300px;"></div>
    </div>

    <!-- Top Citizens Table -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-lgu-headline mb-4">Top Citizens by Bookings</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Citizen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Bookings</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Spent</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($topCitizens as $index => $citizen)
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
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $citizen->full_name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-600">{{ $citizen->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm font-semibold text-lgu-headline">{{ $citizen->total_bookings }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm font-semibold text-green-600">₱{{ number_format($citizen->total_spent, 2) }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-2 text-gray-400"></i>
                            <p>No citizen data available for the selected period.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Insights Card -->
    <div class="bg-blue-50 border-l-4 border-blue-400 p-6 rounded-lg">
        <div class="flex items-start">
            <i data-lucide="lightbulb" class="w-6 h-6 text-blue-600 mr-3 flex-shrink-0 mt-0.5"></i>
            <div>
                <h3 class="text-lg font-semibold text-blue-800 mb-2">Key Insights</h3>
                <ul class="list-disc list-inside text-blue-700 space-y-1">
                    <li>{{ number_format(($repeatCustomers / max($totalCitizens, 1)) * 100, 1) }}% of citizens are repeat customers</li>
                    <li>Average citizen makes {{ number_format($avgBookingsPerCitizen, 1) }} bookings in the selected period</li>
                    <li>{{ number_format($newCitizens) }} new citizens joined during this period</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/apexcharts@3.45.1/dist/apexcharts.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        // Citizen Growth Trend Chart
        const growthContainer = document.querySelector("#growthChart");
        if (growthContainer) {
            const growthData = @json($monthlyGrowth);
            
            const growthOptions = {
                series: [{
                    name: 'Active Citizens',
                    data: growthData.map(item => item.citizen_count)
                }],
                chart: {
                    height: 300,
                    type: 'line',
                    toolbar: { show: false }
                },
                colors: ['#0f3d3e'],
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 3 },
                xaxis: {
                    categories: growthData.map(item => {
                        const date = new Date(item.month + '-01');
                        return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
                    }),
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
                            return Math.floor(val);
                        }
                    }
                },
                markers: {
                    size: 5,
                    colors: ['#0f3d3e'],
                    strokeColors: '#fff',
                    strokeWidth: 2,
                    hover: {
                        size: 7
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + ' citizens';
                        }
                    }
                }
            };
            
            const growthChart = new ApexCharts(growthContainer, growthOptions);
            growthChart.render();
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
    #growthChart {
        page-break-inside: avoid;
    }
}
</style>
@endpush
@endsection

