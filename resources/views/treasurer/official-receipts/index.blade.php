@extends('layouts.treasurer')

@section('title', 'Official Receipts')

@section('page-title', 'Official Receipts')

@section('page-content')
<div class="space-y-6">
    
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Total Receipts -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-2">Total Official Receipts</p>
                    <h3 class="text-3xl font-bold text-lgu-headline">{{ number_format($stats['total_receipts']) }}</h3>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i data-lucide="file-text" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>

        <!-- Today's Receipts -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-2">Today's Receipts</p>
                    <h3 class="text-3xl font-bold text-lgu-button">{{ number_format($stats['today_receipts']) }}</h3>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i data-lucide="calendar-check" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('treasurer.official-receipts') }}" class="space-y-4">
            
            <!-- Search Bar -->
            <div class="relative">
                <label for="search" class="block text-sm font-semibold text-gray-700 mb-2">Search Receipts</label>
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           id="search" 
                           value="{{ request('search') }}"
                           placeholder="OR #, Slip #, Citizen Name..."
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-lgu-button focus:border-transparent">
                    <i data-lucide="search" class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                </div>
            </div>

            <!-- Date Range -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="date_from" class="block text-sm font-semibold text-gray-700 mb-2">Date From</label>
                    <input type="date" 
                           name="date_from" 
                           id="date_from" 
                           value="{{ request('date_from') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-lgu-button focus:border-transparent">
                </div>

                <div>
                    <label for="date_to" class="block text-sm font-semibold text-gray-700 mb-2">Date To</label>
                    <input type="date" 
                           name="date_to" 
                           id="date_to" 
                           value="{{ request('date_to') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-lgu-button focus:border-transparent">
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3">
                <button type="submit" class="bg-lgu-button text-lgu-button-text px-6 py-2.5 rounded-lg hover:bg-lgu-highlight transition-colors font-semibold inline-flex items-center gap-2">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    Apply Filters
                </button>
                <a href="{{ route('treasurer.official-receipts') }}" class="border border-gray-300 text-gray-700 px-6 py-2.5 rounded-lg hover:bg-gray-50 transition-colors font-semibold inline-flex items-center gap-2">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Official Receipts Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-lgu-headline">All Official Receipts</h2>
            <p class="text-sm text-gray-600 mt-1">Complete list of all generated official receipts</p>
        </div>

        @if($receipts->count() > 0)
        <table class="w-full table-fixed divide-y divide-gray-200">
            <colgroup>
                <col style="width: 11%;"> <!-- OR # -->
                <col style="width: 11%;"> <!-- Slip # -->
                <col style="width: 20%;"> <!-- Citizen -->
                <col style="width: 15%;"> <!-- Facility -->
                <col style="width: 11%;"> <!-- Amount -->
                <col style="width: 10%;"> <!-- Payment Method -->
                <col style="width: 12%;"> <!-- Date Issued -->
                <col style="width: 10%;"> <!-- Actions -->
            </colgroup>
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-2 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">OR #</th>
                    <th class="px-2 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Slip #</th>
                    <th class="px-2 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Citizen</th>
                    <th class="px-2 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Facility</th>
                    <th class="px-2 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Amount</th>
                    <th class="px-2 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Payment</th>
                    <th class="px-2 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Date Issued</th>
                    <th class="px-2 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($receipts as $receipt)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-2 py-2 whitespace-nowrap">
                        <span class="text-sm font-bold text-green-600">{{ $receipt->or_number }}</span>
                    </td>
                    <td class="px-2 py-2 whitespace-nowrap">
                        <span class="text-sm font-semibold text-lgu-button">{{ $receipt->slip_number }}</span>
                    </td>
                    <td class="px-2 py-2">
                        <div class="text-sm font-semibold text-gray-900 truncate">{{ $receipt->applicant_name }}</div>
                        <div class="text-xs text-gray-500 mt-1 truncate">{{ $receipt->applicant_email }}</div>
                    </td>
                    <td class="px-2 py-2">
                        <div class="text-sm text-gray-900 truncate">{{ $receipt->facility_name }}</div>
                    </td>
                    <td class="px-2 py-2 whitespace-nowrap">
                        <span class="text-sm font-bold text-lgu-headline">₱{{ number_format($receipt->amount_due, 2) }}</span>
                    </td>
                    <td class="px-2 py-2 whitespace-nowrap">
                        @php
                            $methodColors = [
                                'cash' => 'bg-green-100 text-green-700',
                                'gcash' => 'bg-blue-100 text-blue-700',
                                'paymaya' => 'bg-purple-100 text-purple-700',
                                'bank' => 'bg-indigo-100 text-indigo-700'
                            ];
                            $color = $methodColors[$receipt->payment_method] ?? 'bg-gray-100 text-gray-700';
                        @endphp
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold {{ $color }}">
                            {{ strtoupper($receipt->payment_method ?? 'N/A') }}
                        </span>
                    </td>
                    <td class="px-2 py-2 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($receipt->paid_at)->format('M d, Y') }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($receipt->paid_at)->format('h:i A') }}</div>
                    </td>
                    <td class="px-2 py-2 whitespace-nowrap">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('treasurer.official-receipts.show', $receipt->id) }}" 
                               class="text-lgu-button hover:text-lgu-highlight font-semibold text-sm inline-flex items-center gap-1">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                                View
                            </a>
                            <span class="text-gray-300">|</span>
                            <a href="{{ route('treasurer.official-receipts.print', $receipt->id) }}" 
                               target="_blank"
                               class="text-blue-600 hover:text-blue-800 font-semibold text-sm inline-flex items-center gap-1">
                                <i data-lucide="download" class="w-4 h-4"></i>
                                Download
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $receipts->withQueryString()->links() }}
        </div>
        @else
        <div class="p-12 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                <i data-lucide="file-text" class="w-8 h-8 text-gray-400"></i>
            </div>
            <h3 class="text-base font-semibold text-gray-900 mb-2">No Official Receipts</h3>
            <p class="text-sm text-gray-500">
                @if(request()->has('search') || request()->has('date_from'))
                    No receipts found matching your filters.
                @else
                    No official receipts have been generated yet.
                @endif
            </p>
        </div>
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
        
        // Reinitialize after a short delay to catch any dynamically loaded content
        setTimeout(function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }, 100);
    });
</script>
@endpush

