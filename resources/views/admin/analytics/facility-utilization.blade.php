@extends('layouts.admin')

@section('title', 'Facility Utilization - Admin')

@section('page-content')
<div class="space-y-6 print-area">
    <!-- Header with Date Filter -->
    <div class="bg-white rounded-lg shadow-sm p-6 no-print">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-lgu-headline">Facility Utilization Report</h2>
                <p class="text-gray-600 mt-1">Track facility usage and identify optimization opportunities</p>
            </div>
            
            <!-- Date Range Filter & Export Buttons -->
            <div class="flex flex-wrap items-end gap-3">
                <form method="GET" action="{{ route('admin.analytics.facility-utilization') }}" class="flex flex-wrap items-end gap-3">
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
                        <a href="{{ route('admin.analytics.export-facility-utilization-excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-lgu-bg">
                            <i data-lucide="file-spreadsheet" class="w-4 h-4 inline mr-2"></i>
                            Export as Excel
                        </a>
                        <a href="{{ route('admin.analytics.facility-utilization.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-lgu-bg">
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
            <h2 class="text-xl font-semibold text-gray-700">Facility Utilization Report</h2>
            <p class="text-gray-600">Period: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Facilities -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-medium text-gray-600">Total Facilities</h3>
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i data-lucide="building" class="w-5 h-5 text-blue-600"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-lgu-headline">{{ $facilities->count() }}</p>
            <p class="text-xs text-gray-500 mt-2">Active facilities</p>
        </div>

        <!-- High Performing -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-medium text-gray-600">High Performing</h3>
                <div class="p-2 bg-green-100 rounded-lg">
                    <i data-lucide="trending-up" class="w-5 h-5 text-green-600"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-green-600">{{ $highPerforming->count() }}</p>
            <p class="text-xs text-gray-500 mt-2">> 70% utilization</p>
        </div>

        <!-- Underutilized -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-medium text-gray-600">Underutilized</h3>
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-yellow-600"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-yellow-600">{{ $underutilized->count() }}</p>
            <p class="text-xs text-gray-500 mt-2">< 30% utilization</p>
        </div>
    </div>

    <!-- Facility Utilization Table -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-lgu-headline mb-4">Facility Utilization Details</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Facility</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">City</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Bookings</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Confirmed</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Cancelled</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Utilization</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($facilities as $facility)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $facility->name }}</div>
                            <div class="text-xs text-gray-500">Capacity: {{ $facility->capacity }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-600">{{ $facility->city_name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm text-gray-900">{{ $facility->total_bookings }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm text-green-600 font-medium">{{ $facility->confirmed_bookings }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm text-red-600">{{ $facility->cancelled_bookings }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-2">
                                <div class="w-20 bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full
                                        @if($facility->utilization_rate > 70) bg-green-600
                                        @elseif($facility->utilization_rate > 30) bg-yellow-500
                                        @else bg-red-500
                                        @endif"
                                        style="width: {{ min($facility->utilization_rate, 100) }}%">
                                    </div>
                                </div>
                                <span class="text-sm font-medium
                                    @if($facility->utilization_rate > 70) text-green-600
                                    @elseif($facility->utilization_rate > 30) text-yellow-600
                                    @else text-red-600
                                    @endif">
                                    {{ number_format($facility->utilization_rate, 1) }}%
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm font-semibold text-lgu-headline">₱{{ number_format($facility->total_revenue ?? 0, 2) }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-2 text-gray-400"></i>
                            <p>No facility data available.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Underutilized Facilities Alert -->
    @if($underutilized->count() > 0)
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-lg">
        <div class="flex items-start">
            <i data-lucide="alert-triangle" class="w-6 h-6 text-yellow-600 mr-3 flex-shrink-0 mt-0.5"></i>
            <div>
                <h3 class="text-lg font-semibold text-yellow-800 mb-2">Underutilized Facilities</h3>
                <p class="text-yellow-700 mb-3">The following facilities have utilization rates below 30% and may need attention:</p>
                <ul class="list-disc list-inside text-yellow-700 space-y-1">
                    @foreach($underutilized as $facility)
                    <li>{{ $facility->name }} ({{ $facility->city_name ?? 'N/A' }}) - {{ number_format($facility->utilization_rate, 1) }}%</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif
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
    /* Better table printing */
    table {
        page-break-inside: auto;
    }
    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Initialize Lucide icons
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}
</script>
@endpush
@endsection

