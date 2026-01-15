@extends('layouts.treasurer')

@section('title', 'Treasurer Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Payment Collections Overview')

@section('page-content')

<!-- Welcome Section -->
<div class="mb-8">
    <h2 class="text-3xl font-bold text-lgu-headline">Welcome, {{ session('user_name') }}!</h2>
    <p class="text-lgu-paragraph mt-2">Here's an overview of today's payment collections and pending verifications.</p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <!-- Today's Collections -->
    <div class="bg-green-500 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm font-medium">Today's Collections</p>
                <h3 class="text-3xl font-bold mt-2">₱{{ number_format($todayCollections, 2) }}</h3>
                <p class="text-green-100 text-xs mt-1">{{ $paymentsVerifiedToday }} payment(s) verified</p>
            </div>
            <div class="bg-green-600 p-3 rounded-lg">
                <i data-lucide="trending-up" class="w-8 h-8 text-green-100"></i>
            </div>
        </div>
    </div>

    <!-- Monthly Collections -->
    <div class="bg-blue-500 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm font-medium">This Month</p>
                <h3 class="text-3xl font-bold mt-2">₱{{ number_format($monthlyCollections, 2) }}</h3>
                <p class="text-blue-100 text-xs mt-1">{{ date('F Y') }}</p>
            </div>
            <div class="bg-blue-600 p-3 rounded-lg">
                <i data-lucide="calendar" class="w-8 h-8 text-blue-100"></i>
            </div>
        </div>
    </div>

    <!-- Total Collections -->
    <div class="bg-purple-500 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-purple-100 text-sm font-medium">Total All-Time</p>
                <h3 class="text-3xl font-bold mt-2">₱{{ number_format($totalCollections, 2) }}</h3>
                <p class="text-purple-100 text-xs mt-1">All collections</p>
            </div>
            <div class="bg-purple-600 p-3 rounded-lg">
                <i data-lucide="wallet" class="w-8 h-8 text-purple-100"></i>
            </div>
        </div>
    </div>

    <!-- Pending Payments -->
    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-orange-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Pending Payments</p>
                <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $pendingPayments }}</h3>
                <a href="{{ route('treasurer.payment-verification') }}" class="text-orange-600 hover:text-orange-700 text-sm font-medium mt-2 inline-flex items-center">
                    Review Now
                    <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
                </a>
            </div>
            <div class="bg-orange-100 p-3 rounded-lg">
                <i data-lucide="clock" class="w-8 h-8 text-orange-600"></i>
            </div>
        </div>
    </div>

    <!-- Payments Verified Today -->
    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Verified Today</p>
                <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $paymentsVerifiedToday }}</h3>
                <p class="text-gray-500 text-sm mt-2">Payments processed</p>
            </div>
            <div class="bg-green-100 p-3 rounded-lg">
                <i data-lucide="check-circle" class="w-8 h-8 text-green-600"></i>
            </div>
        </div>
    </div>

    <!-- Expired Slips -->
    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Expired Slips</p>
                <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $expiredPaymentSlips }}</h3>
                <p class="text-gray-500 text-sm mt-2">Unpaid past deadline</p>
            </div>
            <div class="bg-red-100 p-3 rounded-lg">
                <i data-lucide="x-circle" class="w-8 h-8 text-red-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Daily Collections Chart -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Daily Collections (Last 7 Days)</h3>
        <div id="dailyCollectionsChart"></div>
    </div>

    <!-- Payment Methods Chart -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Payment Methods</h3>
        <div id="paymentMethodsChart"></div>
    </div>
</div>

<!-- Recent Payments Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-bold text-gray-900">Recent Payments</h3>
        <p class="text-sm text-gray-600">Latest verified payments</p>
    </div>
    
    @if($recentPayments->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slip #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Citizen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Facility</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paid At</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentPayments as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-lgu-button">{{ $payment->slip_number }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900">{{ $payment->applicant_name }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600">{{ $payment->facility_name }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-gray-900">₱{{ number_format($payment->amount_due, 2) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($payment->payment_method)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-400">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($payment->paid_at)->format('M d, Y h:i A') }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('treasurer.payment-slips.show', $payment->id) }}" class="text-lgu-button hover:text-lgu-highlight font-medium">
                                    View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="px-6 py-12 text-center">
            <i data-lucide="inbox" class="w-12 h-12 mx-auto text-gray-400 mb-4"></i>
            <p class="text-gray-500">No recent payments found</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Daily Collections Chart
    const dailyCollectionsData = @json($dailyCollectionsChart);
    const dailyOptions = {
        chart: {
            type: 'area',
            height: 300,
            toolbar: { show: false }
        },
        series: [{
            name: 'Collections',
            data: dailyCollectionsData.map(d => d.amount)
        }],
        xaxis: {
            categories: dailyCollectionsData.map(d => d.date)
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.3,
            }
        },
        colors: ['#0f5b3a'],
        dataLabels: { enabled: false },
        yaxis: {
            labels: {
                formatter: function(val) {
                    return '₱' + val.toLocaleString();
                }
            }
        }
    };
    const dailyChart = new ApexCharts(document.querySelector('#dailyCollectionsChart'), dailyOptions);
    dailyChart.render();

    // Payment Methods Chart
    const paymentMethodsData = @json($paymentsByMethod);
    const methodOptions = {
        chart: {
            type: 'donut',
            height: 300
        },
        series: paymentMethodsData.map(d => parseFloat(d.total)),
        labels: paymentMethodsData.map(d => d.payment_method ? d.payment_method.replace('_', ' ').toUpperCase() : 'Unknown'),
        colors: ['#0f5b3a', '#3b82f6', '#8b5cf6', '#f59e0b', '#10b981'],
        legend: {
            position: 'bottom'
        },
        dataLabels: {
            formatter: function(val) {
                return val.toFixed(1) + '%';
            }
        }
    };
    const methodChart = new ApexCharts(document.querySelector('#paymentMethodsChart'), methodOptions);
    methodChart.render();

    // Initialize Lucide icons with delay
    setTimeout(function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }, 200);
});

// Reinitialize on window load as backup
window.addEventListener('load', function() {
    setTimeout(function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }, 100);
});
</script>
@endpush

@endsection

