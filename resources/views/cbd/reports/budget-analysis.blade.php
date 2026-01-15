@extends('layouts.cbd')

@section('title', 'Budget Analysis - CBD')

@section('page-content')
<div class="space-y-6">
    <!-- Header with Fiscal Year Selector -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-lgu-headline">Budget Analysis Report</h2>
                <p class="text-gray-600 mt-1">Monitor budget allocation and utilization for fiscal year {{ $fiscalYear }}</p>
            </div>
            
            <form method="GET" action="{{ route('cbd.reports.budget-analysis') }}" class="flex items-center gap-3">
                <select name="fiscal_year" onchange="this.form.submit()" class="px-4 py-2 border border-lgu-stroke rounded-lg focus:ring-2 focus:ring-lgu-highlight focus:border-lgu-highlight">
                    @foreach($fiscalYears as $year)
                        <option value="{{ $year }}" {{ $year == $fiscalYear ? 'selected' : '' }}>
                            FY {{ $year }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <!-- Budget Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Allocated -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-600">Total Allocated</h3>
                <i data-lucide="wallet" class="w-5 h-5 text-blue-500"></i>
            </div>
            <p class="text-3xl font-bold text-lgu-headline">₱{{ number_format($totalAllocated, 2) }}</p>
            <p class="text-xs text-gray-500 mt-2">Budget for FY {{ $fiscalYear }}</p>
        </div>

        <!-- Total Spent -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-600">Total Spent</h3>
                <i data-lucide="trending-down" class="w-5 h-5 text-orange-500"></i>
            </div>
            <p class="text-3xl font-bold text-lgu-headline">₱{{ number_format($totalSpent, 2) }}</p>
            <p class="text-xs text-gray-500 mt-2">{{ number_format($utilizationPercentage, 1) }}% utilized</p>
        </div>

        <!-- Total Remaining -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-600">Remaining Budget</h3>
                <i data-lucide="piggy-bank" class="w-5 h-5 text-green-500"></i>
            </div>
            <p class="text-3xl font-bold text-lgu-headline">₱{{ number_format($totalRemaining, 2) }}</p>
            <p class="text-xs text-gray-500 mt-2">Available for allocation</p>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-600">Total Revenue</h3>
                <i data-lucide="trending-up" class="w-5 h-5 text-purple-500"></i>
            </div>
            <p class="text-3xl font-bold text-lgu-headline">₱{{ number_format($totalRevenue, 2) }}</p>
            <p class="text-xs text-gray-500 mt-2">Income for FY {{ $fiscalYear }}</p>
        </div>
    </div>

    <!-- Budget Utilization by Category -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-lgu-headline">Budget Utilization by Category</h3>
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600">Overall Utilization:</span>
                <span class="px-3 py-1 bg-lgu-highlight/10 text-lgu-highlight rounded-full text-sm font-semibold">
                    {{ number_format($utilizationPercentage, 1) }}%
                </span>
            </div>
        </div>

        @if($budgetAllocations->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Category</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Allocated</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Spent</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Remaining</th>
                        <th class="text-center py-3 px-4 text-sm font-semibold text-gray-700">Utilization</th>
                        <th class="text-center py-3 px-4 text-sm font-semibold text-gray-700">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($budgetAllocations as $allocation)
                    @php
                        $utilization = $allocation->allocated_amount > 0 ? ($allocation->spent_amount / $allocation->allocated_amount) * 100 : 0;
                        $statusColor = $utilization >= 100 ? 'red' : ($utilization >= 80 ? 'yellow' : 'green');
                        $statusText = $utilization >= 100 ? 'Over Budget' : ($utilization >= 80 ? 'Warning' : 'Normal');
                    @endphp
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                        <td class="py-4 px-4">
                            <div>
                                <p class="font-medium text-gray-900">{{ $categoryLabels[$allocation->category] ?? ucfirst($allocation->category) }}</p>
                                @if($allocation->category_name)
                                <p class="text-xs text-gray-500">{{ $allocation->category_name }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="py-4 px-4 text-right font-medium text-gray-900">
                            ₱{{ number_format($allocation->allocated_amount, 2) }}
                        </td>
                        <td class="py-4 px-4 text-right text-orange-600 font-medium">
                            ₱{{ number_format($allocation->spent_amount, 2) }}
                        </td>
                        <td class="py-4 px-4 text-right text-green-600 font-medium">
                            ₱{{ number_format($allocation->remaining_amount, 2) }}
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex flex-col items-center">
                                <span class="text-sm font-semibold mb-1">{{ number_format($utilization, 1) }}%</span>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full transition-all" 
                                         style="width: {{ min($utilization, 100) }}%; background-color: {{ $statusColor === 'red' ? '#ef4444' : ($statusColor === 'yellow' ? '#f59e0b' : '#10b981') }}"></div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4 text-center">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusColor === 'red' ? 'bg-red-100 text-red-800' : ($statusColor === 'yellow' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                {{ $statusText }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr class="font-semibold">
                        <td class="py-4 px-4">TOTAL</td>
                        <td class="py-4 px-4 text-right">₱{{ number_format($totalAllocated, 2) }}</td>
                        <td class="py-4 px-4 text-right text-orange-600">₱{{ number_format($totalSpent, 2) }}</td>
                        <td class="py-4 px-4 text-right text-green-600">₱{{ number_format($totalRemaining, 2) }}</td>
                        <td class="py-4 px-4 text-center">{{ number_format($utilizationPercentage, 1) }}%</td>
                        <td class="py-4 px-4"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="text-center py-12">
            <i data-lucide="alert-circle" class="w-12 h-12 mx-auto mb-3 text-gray-400"></i>
            <p class="text-gray-600">No budget allocations found for FY {{ $fiscalYear }}</p>
            <p class="text-sm text-gray-500 mt-1">Budget allocations can be managed by administrators</p>
        </div>
        @endif
    </div>

    <!-- Recent Expenditures -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-lgu-headline mb-6">Recent Expenditures</h3>

        @if($recentExpenditures->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Date</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Category</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Description</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Vendor</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentExpenditures as $expenditure)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                        <td class="py-4 px-4 text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($expenditure->expenditure_date)->format('M d, Y') }}
                        </td>
                        <td class="py-4 px-4">
                            <span class="px-2 py-1 bg-lgu-highlight/10 text-lgu-highlight rounded text-xs font-medium">
                                {{ $categoryLabels[$expenditure->category] ?? ucfirst($expenditure->category) }}
                            </span>
                        </td>
                        <td class="py-4 px-4 text-sm text-gray-900">
                            {{ $expenditure->description }}
                        </td>
                        <td class="py-4 px-4 text-sm text-gray-600">
                            {{ $expenditure->vendor_name ?? 'N/A' }}
                        </td>
                        <td class="py-4 px-4 text-right font-medium text-gray-900">
                            ₱{{ number_format($expenditure->amount, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-8">
            <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-3 text-gray-400"></i>
            <p class="text-gray-600">No expenditures recorded yet for FY {{ $fiscalYear }}</p>
        </div>
        @endif
    </div>

    <!-- Budget vs Revenue Analysis -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-lgu-headline mb-6">Budget vs Revenue Analysis</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Net Position -->
            <div class="p-6 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Net Position</h4>
                <p class="text-3xl font-bold text-lgu-headline">
                    ₱{{ number_format($totalRevenue - $totalSpent, 2) }}
                </p>
                <p class="text-sm text-gray-600 mt-2">Revenue minus Expenditures</p>
            </div>

            <!-- Budget Efficiency -->
            <div class="p-6 bg-gradient-to-br from-green-50 to-green-100 rounded-lg">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Budget Efficiency</h4>
                <p class="text-3xl font-bold text-lgu-headline">
                    {{ $totalAllocated > 0 ? number_format(($totalRevenue / $totalAllocated) * 100, 1) : 0 }}%
                </p>
                <p class="text-sm text-gray-600 mt-2">Revenue to Budget Ratio</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Initialize Lucide icons
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}
</script>
@endpush
@endsection
