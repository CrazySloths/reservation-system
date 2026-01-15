@extends('layouts.treasurer')

@section('title', 'Daily Collections Report')
@section('page-title', 'Daily Collections Report')

@section('page-content')

<div class="space-y-gr-lg">
    <!-- Date Filter & Export -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('treasurer.reports.daily-collections') }}" class="flex flex-col md:flex-row items-end gap-4">
            <div class="flex-1">
                <label for="date" class="block text-small font-semibold text-gray-700 mb-gr-xs">Select Date</label>
                <input type="date" 
                       name="date" 
                       id="date" 
                       value="{{ $selectedDate->format('Y-m-d') }}"
                       max="{{ now()->format('Y-m-d') }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-lgu-button focus:border-transparent">
            </div>
            
            <button type="submit" 
                    class="bg-lgu-button text-lgu-button-text px-6 py-2.5 rounded-lg hover:bg-lgu-highlight transition-colors font-semibold inline-flex items-center gap-2">
                <i data-lucide="search" class="w-4 h-4"></i>
                View Report
            </button>
            
            @if($payments->count() > 0)
            <a href="{{ route('treasurer.reports.daily-collections.export', ['date' => $selectedDate->format('Y-m-d')]) }}" 
               class="bg-green-600 text-white px-6 py-2.5 rounded-lg hover:bg-green-700 transition-colors font-semibold inline-flex items-center gap-2">
                <i data-lucide="download" class="w-4 h-4"></i>
                Export PDF
            </a>
            @endif
        </form>
    </div>

    <!-- Report Date Header -->
    <div class="text-center">
        <h2 class="text-h3 font-bold text-lgu-headline">{{ $selectedDate->format('F d, Y') }}</h2>
        <p class="text-small text-gray-600 mt-gr-xs">{{ $selectedDate->format('l') }}</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-gr-md">
        <!-- Total Collections -->
        <div class="bg-green-500 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-gr-sm">
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <i data-lucide="coins" class="w-6 h-6"></i>
                </div>
            </div>
            <p class="text-caption opacity-90 mb-gr-xs">Total Collections</p>
            <p class="text-2xl font-bold">₱{{ number_format($stats['total_collections'], 2) }}</p>
        </div>

        <!-- Total Transactions -->
        <div class="bg-blue-500 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-gr-sm">
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <i data-lucide="file-text" class="w-6 h-6"></i>
                </div>
            </div>
            <p class="text-caption opacity-90 mb-gr-xs">Total Transactions</p>
            <p class="text-2xl font-bold">{{ $stats['total_transactions'] }}</p>
        </div>

        <!-- Average Transaction -->
        <div class="bg-purple-500 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-gr-sm">
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-6 h-6"></i>
                </div>
            </div>
            <p class="text-caption opacity-90 mb-gr-xs">Average Amount</p>
            <p class="text-2xl font-bold">
                ₱{{ $stats['total_transactions'] > 0 ? number_format($stats['total_collections'] / $stats['total_transactions'], 2) : '0.00' }}
            </p>
        </div>

        <!-- Cash Collections -->
        <div class="bg-orange-500 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-gr-sm">
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <i data-lucide="wallet" class="w-6 h-6"></i>
                </div>
            </div>
            <p class="text-caption opacity-90 mb-gr-xs">Cash Collections</p>
            <p class="text-2xl font-bold">₱{{ number_format($stats['cash'], 2) }}</p>
            <p class="text-caption opacity-75 mt-1">{{ $stats['cash_count'] }} transaction(s)</p>
        </div>
    </div>

    <!-- Payment Method Breakdown -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-body font-bold text-lgu-headline mb-gr-md flex items-center">
            <i data-lucide="pie-chart" class="w-5 h-5 mr-gr-xs text-lgu-button"></i>
            Payment Method Breakdown
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
                <p class="text-xl font-bold text-green-900">₱{{ number_format($stats['cash'], 2) }}</p>
                <p class="text-caption text-green-700 mt-1">{{ $stats['cash_count'] }} transaction(s)</p>
            </div>

            <!-- GCash -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i data-lucide="smartphone" class="w-4 h-4 text-blue-600"></i>
                    </div>
                    <p class="font-semibold text-blue-800">GCash</p>
                </div>
                <p class="text-xl font-bold text-blue-900">₱{{ number_format($stats['gcash'], 2) }}</p>
                <p class="text-caption text-blue-700 mt-1">{{ $stats['gcash_count'] }} transaction(s)</p>
            </div>

            <!-- PayMaya -->
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <i data-lucide="credit-card" class="w-4 h-4 text-purple-600"></i>
                    </div>
                    <p class="font-semibold text-purple-800">PayMaya</p>
                </div>
                <p class="text-xl font-bold text-purple-900">₱{{ number_format($stats['paymaya'], 2) }}</p>
                <p class="text-caption text-purple-700 mt-1">{{ $stats['paymaya_count'] }} transaction(s)</p>
            </div>

            <!-- Bank Transfer -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i data-lucide="building-2" class="w-4 h-4 text-yellow-600"></i>
                    </div>
                    <p class="font-semibold text-yellow-800">Bank</p>
                </div>
                <p class="text-xl font-bold text-yellow-900">₱{{ number_format($stats['bank_transfer'], 2) }}</p>
                <p class="text-caption text-yellow-700 mt-1">{{ $stats['bank_transfer_count'] }} transaction(s)</p>
            </div>

            <!-- Credit Card -->
            <div class="bg-pink-50 border border-pink-200 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 bg-pink-100 rounded-full flex items-center justify-center">
                        <i data-lucide="credit-card" class="w-4 h-4 text-pink-600"></i>
                    </div>
                    <p class="font-semibold text-pink-800">Card</p>
                </div>
                <p class="text-xl font-bold text-pink-900">₱{{ number_format($stats['credit_card'], 2) }}</p>
                <p class="text-caption text-pink-700 mt-1">{{ $stats['credit_card_count'] }} transaction(s)</p>
            </div>
        </div>
    </div>

    <!-- Transactions List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-body font-bold text-lgu-headline flex items-center">
                <i data-lucide="list" class="w-5 h-5 mr-gr-xs text-lgu-button"></i>
                All Transactions ({{ $payments->count() }})
            </h3>
        </div>

        @if($payments->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-caption font-bold text-gray-600 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-3 text-left text-caption font-bold text-gray-600 uppercase tracking-wider">OR #</th>
                        <th class="px-6 py-3 text-left text-caption font-bold text-gray-600 uppercase tracking-wider">Payor</th>
                        <th class="px-6 py-3 text-left text-caption font-bold text-gray-600 uppercase tracking-wider">Facility</th>
                        <th class="px-6 py-3 text-left text-caption font-bold text-gray-600 uppercase tracking-wider">Method</th>
                        <th class="px-6 py-3 text-right text-caption font-bold text-gray-600 uppercase tracking-wider">Amount</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($payments as $payment)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-small text-gray-900">
                            {{ \Carbon\Carbon::parse($payment->paid_at)->format('h:i A') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-small font-semibold text-green-600">{{ $payment->transaction_reference }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-small font-medium text-gray-900">{{ $payment->applicant_name }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-small text-gray-900">{{ $payment->facility_name }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $methodColors = [
                                    'cash' => 'bg-green-100 text-green-800',
                                    'gcash' => 'bg-blue-100 text-blue-800',
                                    'paymaya' => 'bg-purple-100 text-purple-800',
                                    'bank_transfer' => 'bg-yellow-100 text-yellow-800',
                                    'credit_card' => 'bg-pink-100 text-pink-800',
                                ];
                                $colorClass = $methodColors[$payment->payment_method] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-2 py-1 rounded-full text-caption font-semibold {{ $colorClass }}">
                                {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <span class="text-small font-bold text-lgu-headline">₱{{ number_format($payment->amount_due, 2) }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 border-t-2 border-gray-300">
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-right">
                            <span class="text-body font-bold text-gray-900">Total Collections:</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-body font-bold text-green-600">₱{{ number_format($stats['total_collections'], 2) }}</span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="p-12 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                <i data-lucide="inbox" class="w-8 h-8 text-gray-400"></i>
            </div>
            <h3 class="text-body font-semibold text-gray-900 mb-gr-xs">No Collections</h3>
            <p class="text-small text-gray-500">No payments were collected on {{ $selectedDate->format('F d, Y') }}.</p>
        </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Initialize Lucide icons
    lucide.createIcons();
</script>
@endpush
