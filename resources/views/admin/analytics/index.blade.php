@extends('layouts.admin')

@section('title', 'Reports & Analytics Hub - Admin')

@section('page-content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-lgu-headline">Reports & Analytics Hub</h2>
                <p class="text-gray-600 mt-1">Comprehensive overview and access to all system reports</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500">Last updated:</span>
                <span class="text-sm font-semibold text-lgu-headline">{{ now()->format('M d, Y g:i A') }}</span>
            </div>
        </div>
    </div>

    <!-- Quick Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-lgu-primary">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Revenue (YTD)</p>
                    <h3 class="text-2xl font-bold text-lgu-headline mt-1">₱{{ number_format($totalRevenue ?? 0, 2) }}</h3>
                </div>
                <div class="w-12 h-12 bg-lgu-primary/10 rounded-full flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-6 h-6 text-lgu-primary"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-lgu-secondary">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Bookings</p>
                    <h3 class="text-2xl font-bold text-lgu-headline mt-1">{{ number_format($totalBookings ?? 0) }}</h3>
                </div>
                <div class="w-12 h-12 bg-lgu-secondary/10 rounded-full flex items-center justify-center">
                    <i data-lucide="calendar-check" class="w-6 h-6 text-lgu-secondary"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-lgu-tertiary">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Active Citizens</p>
                    <h3 class="text-2xl font-bold text-lgu-headline mt-1">{{ number_format($activeCitizens ?? 0) }}</h3>
                </div>
                <div class="w-12 h-12 bg-lgu-tertiary/10 rounded-full flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6 text-lgu-tertiary"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-lgu-highlight">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Facility Utilization</p>
                    <h3 class="text-2xl font-bold text-lgu-headline mt-1">{{ number_format($facilityUtilization ?? 0, 1) }}%</h3>
                </div>
                <div class="w-12 h-12 bg-lgu-highlight/10 rounded-full flex items-center justify-center">
                    <i data-lucide="building" class="w-6 h-6 text-lgu-highlight"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Reports -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-lgu-headline mb-4 flex items-center">
            <span class="w-5 h-5 mr-2 text-lgu-primary text-xl font-bold flex items-center justify-center">₱</span>
            Financial Reports
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Revenue Report -->
            <a href="{{ route('admin.analytics.revenue-report') }}" class="group block p-4 border-2 border-gray-200 rounded-lg hover:border-lgu-primary hover:shadow-md transition-all duration-200">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-lgu-primary group-hover:text-white transition-colors duration-200">
                        <i data-lucide="trending-up" class="w-5 h-5 text-green-600 group-hover:text-white"></i>
                    </div>
                    <span class="text-xs text-gray-500">Last 3 months</span>
                </div>
                <h4 class="font-semibold text-lgu-headline mb-1">Revenue Report</h4>
                <p class="text-sm text-gray-600">Track income trends, payment methods, and revenue projections</p>
            </a>

            <!-- Budget Analysis -->
            <a href="{{ route('admin.budget.index') }}" class="group block p-4 border-2 border-gray-200 rounded-lg hover:border-lgu-primary hover:shadow-md transition-all duration-200">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-lgu-primary group-hover:text-white transition-colors duration-200">
                        <i data-lucide="wallet" class="w-5 h-5 text-blue-600 group-hover:text-white"></i>
                    </div>
                    <span class="text-xs text-gray-500">Real-time</span>
                </div>
                <h4 class="font-semibold text-lgu-headline mb-1">Budget Management</h4>
                <p class="text-sm text-gray-600">Manage allocations, track expenditures, and monitor utilization</p>
            </a>
        </div>
    </div>

    <!-- Operational Reports -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-lgu-headline mb-4 flex items-center">
            <i data-lucide="activity" class="w-5 h-5 mr-2 text-lgu-secondary"></i>
            Operational Reports
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Booking Statistics -->
            <a href="{{ route('admin.analytics.booking-statistics') }}" class="group block p-4 border-2 border-gray-200 rounded-lg hover:border-lgu-secondary hover:shadow-md transition-all duration-200">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center group-hover:bg-lgu-secondary group-hover:text-white transition-colors duration-200">
                        <i data-lucide="calendar-check" class="w-5 h-5 text-purple-600 group-hover:text-white"></i>
                    </div>
                    <span class="text-xs text-gray-500">Last 3 months</span>
                </div>
                <h4 class="font-semibold text-lgu-headline mb-1">Booking Statistics</h4>
                <p class="text-sm text-gray-600">Analyze booking trends, status distribution, and peak periods</p>
            </a>

            <!-- Facility Utilization -->
            <a href="{{ route('admin.analytics.facility-utilization') }}" class="group block p-4 border-2 border-gray-200 rounded-lg hover:border-lgu-secondary hover:shadow-md transition-all duration-200">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center group-hover:bg-lgu-secondary group-hover:text-white transition-colors duration-200">
                        <i data-lucide="building" class="w-5 h-5 text-yellow-600 group-hover:text-white"></i>
                    </div>
                    <span class="text-xs text-gray-500">Last 3 months</span>
                </div>
                <h4 class="font-semibold text-lgu-headline mb-1">Facility Utilization</h4>
                <p class="text-sm text-gray-600">Monitor facility usage rates, capacity, and efficiency metrics</p>
            </a>

            <!-- Operational Metrics -->
            <a href="{{ route('admin.analytics.operational-metrics') }}" class="group block p-4 border-2 border-gray-200 rounded-lg hover:border-lgu-secondary hover:shadow-md transition-all duration-200">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center group-hover:bg-lgu-secondary group-hover:text-white transition-colors duration-200">
                        <i data-lucide="activity" class="w-5 h-5 text-red-600 group-hover:text-white"></i>
                    </div>
                    <span class="text-xs text-gray-500">Last 3 months</span>
                </div>
                <h4 class="font-semibold text-lgu-headline mb-1">Operational Metrics</h4>
                <p class="text-sm text-gray-600">Staff performance, processing times, and efficiency indicators</p>
            </a>
        </div>
    </div>

    <!-- User Analytics -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-lgu-headline mb-4 flex items-center">
            <i data-lucide="users" class="w-5 h-5 mr-2 text-lgu-tertiary"></i>
            User Analytics
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Citizen Analytics -->
            <a href="{{ route('admin.analytics.citizen-analytics') }}" class="group block p-4 border-2 border-gray-200 rounded-lg hover:border-lgu-tertiary hover:shadow-md transition-all duration-200">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center group-hover:bg-lgu-tertiary group-hover:text-white transition-colors duration-200">
                        <i data-lucide="users" class="w-5 h-5 text-indigo-600 group-hover:text-white"></i>
                    </div>
                    <span class="text-xs text-gray-500">Last 3 months</span>
                </div>
                <h4 class="font-semibold text-lgu-headline mb-1">Citizen Analytics</h4>
                <p class="text-sm text-gray-600">User registration trends, engagement metrics, and demographics</p>
            </a>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-gradient-to-r from-lgu-primary to-lgu-secondary rounded-lg shadow-sm p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold mb-1">Need a Custom Report?</h3>
                <p class="text-sm opacity-90">Contact the system administrator for custom analytics and data exports</p>
            </div>
            <button onclick="Swal.fire({
                icon: 'info',
                title: 'Custom Reports',
                text: 'Custom report functionality coming soon. Contact your IT department for specialized data requests.',
                confirmButtonColor: '#0f3d3e'
            })" class="px-6 py-2 bg-white text-lgu-primary rounded-lg hover:bg-gray-100 transition-colors duration-200 font-semibold">
                Request Custom Report
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>
@endpush
@endsection

