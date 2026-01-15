@extends('layouts.admin')

@section('page-content')
<div class="space-y-gr-xl">
    <!-- Enhanced Header Section with Golden Ratio -->
    <div class="bg-lgu-headline rounded-2xl p-gr-xl text-white shadow-lgu-lg overflow-hidden relative">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg">
                <pattern id="pattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                    <circle cx="10" cy="10" r="1" fill="currentColor"/>
                </pattern>
                <rect width="100%" height="100%" fill="url(#pattern)"/>
            </svg>
        </div>
        
        <div class="relative z-10 flex items-center justify-between">
            <div class="space-y-gr-md">
                <div class="flex items-center gap-8">
                    <div class="w-20 h-20 bg-lgu-highlight/20 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/10">
                        <svg class="w-10 h-10 text-lgu-highlight" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                        </svg>
                    </div>
            <div>
                        <h1 class="text-h1 text-white mb-gr-xs">LGU Admin Dashboard</h1>
                        <p class="text-gray-200 text-body">LGU1 Public Facilities Reservation System</p>
                    </div>
                </div>
            </div>
            <div class="text-right space-y-gr-sm">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-gr-lg py-gr-md border border-white/20">
                    <p class="text-body text-gray-200 font-medium mb-gr-xs" id="current-date">{{ now()->format('l, F j, Y') }}</p>
                    <p class="text-h2 font-bold text-lgu-highlight" id="current-time-main">{{ now()->format('g:i A') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Critical Alerts Bar -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-gr-lg items-stretch">
        <!-- Payment Pending -->
        <a href="{{ route('admin.payment-queue') }}" class="bg-yellow-50 border border-yellow-200 rounded-xl p-gr-lg shadow-sm hover:shadow-md transition-all hover:scale-105">
            <div class="flex items-center h-full">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-amber-500 rounded-xl flex items-center justify-center shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock text-white">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                    </svg>
                    </div>
                </div>
                <div class="ml-gr-sm">
                    <p class="text-small font-semibold text-amber-800">Awaiting Payment</p>
                    <p class="text-h2 font-bold text-amber-900">{{ $stats['payment_pending'] }}</p>
                </div>
            </div>
        </a>

        <!-- Payment Verification -->
        <a href="{{ route('admin.bookings.index') }}?status=paid" class="bg-blue-50 border border-blue-200 rounded-xl p-gr-lg shadow-sm hover:shadow-md transition-all hover:scale-105">
            <div class="flex items-center h-full">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-credit-card text-white">
                            <rect width="20" height="14" x="2" y="5" rx="2"/>
                            <line x1="2" x2="22" y1="10" y2="10"/>
                    </svg>
                    </div>
                </div>
                <div class="ml-gr-sm">
                    <p class="text-small font-semibold text-blue-800">Payment Verification</p>
                    <p class="text-h2 font-bold text-blue-900">{{ $stats['payment_verification'] }}</p>
                </div>
            </div>
        </a>

        <!-- Confirmed Bookings -->
        <a href="{{ route('admin.bookings.index') }}?status=confirmed" class="bg-green-50 border border-green-200 rounded-xl p-gr-lg shadow-sm hover:shadow-md transition-all hover:scale-105">
            <div class="flex items-center h-full">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check-circle text-white">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <path d="m9 11 3 3L22 4"/>
                    </svg>
                    </div>
                </div>
                <div class="ml-gr-sm">
                    <p class="text-small font-semibold text-emerald-800">Confirmed</p>
                    <p class="text-h2 font-bold text-emerald-900">{{ $stats['confirmed'] }}</p>
                </div>
            </div>
        </a>

        <!-- Today's Events -->
        <a href="{{ route('admin.calendar') }}" class="bg-purple-50 border border-purple-200 rounded-xl p-gr-lg shadow-sm hover:shadow-md transition-all hover:scale-105">
            <div class="flex items-center h-full">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-check text-white">
                            <path d="M8 2v4"/>
                            <path d="M16 2v4"/>
                            <rect width="18" height="18" x="3" y="4" rx="2"/>
                            <path d="M3 10h18"/>
                            <path d="m9 16 2 2 4-4"/>
                    </svg>
                    </div>
                </div>
                <div class="ml-gr-sm">
                    <p class="text-small font-semibold text-purple-800">Today's Events</p>
                    <p class="text-h2 font-bold text-purple-900">{{ $stats['todays_events'] }}</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Main Content Grid with Golden Ratio -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-gr-lg">
        
        <!-- Left Column: Monthly Overview & Quick Actions -->
        <div class="lg:col-span-2 space-y-gr-lg">
            
            <!-- Revenue Statistics -->
            <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke p-gr-lg">
                <h3 class="text-h3 text-lgu-headline mb-gr-md flex items-center gap-2">
                    <i data-lucide="trending-up" class="w-6 h-6"></i>
                    Revenue Overview
                </h3>
                <div class="grid grid-cols-2 gap-gr-md">
                    <div class="bg-green-50 rounded-xl p-gr-md border border-green-200">
                        <p class="text-small text-green-700 font-medium mb-1">Total Revenue</p>
                        <p class="text-h2 font-bold text-green-900">₱{{ number_format($stats['total_revenue'], 2) }}</p>
                        <p class="text-caption text-green-600">All time</p>
                    </div>
                    <div class="bg-blue-50 rounded-xl p-gr-md border border-blue-200">
                        <p class="text-small text-blue-700 font-medium mb-1">This Month</p>
                        <p class="text-h2 font-bold text-blue-900">₱{{ number_format($stats['monthly_revenue'], 2) }}</p>
                        <p class="text-caption text-blue-600">{{ \Carbon\Carbon::now()->format('F Y') }}</p>
        </div>
    </div>

                <!-- Revenue Trend Chart -->
                <div class="mt-gr-lg">
                    <p class="text-small text-lgu-paragraph font-medium mb-gr-sm">Revenue Trend (Last 7 Days)</p>
                    <div id="revenueChart" style="height: 180px;"></div>
                </div>
            </div>

            <!-- Booking Statistics -->
            <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke p-gr-lg">
                <h3 class="text-h3 text-lgu-headline mb-gr-md flex items-center gap-2">
                    <i data-lucide="bar-chart-3" class="w-6 h-6"></i>
                    Booking Statistics
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-gr-sm">
                    <div class="text-center p-gr-sm bg-lgu-bg rounded-lg">
                        <p class="text-h3 font-bold text-lgu-headline">{{ $stats['confirmed'] }}</p>
                        <p class="text-caption text-lgu-paragraph">Confirmed</p>
                    </div>
                    <div class="text-center p-gr-sm bg-green-50 rounded-lg">
                        <p class="text-h3 font-bold text-green-700">{{ $stats['payment_verification'] }}</p>
                        <p class="text-caption text-green-600">Paid</p>
                    </div>
                    <div class="text-center p-gr-sm bg-yellow-50 rounded-lg">
                        <p class="text-h3 font-bold text-yellow-700">{{ $stats['payment_pending'] }}</p>
                        <p class="text-caption text-yellow-600">Awaiting Payment</p>
                    </div>
                    <div class="text-center p-gr-sm bg-red-50 rounded-lg">
                        <p class="text-h3 font-bold text-red-700">{{ $stats['rejected'] }}</p>
                        <p class="text-caption text-red-600">Rejected</p>
                    </div>
                </div>
            </div>

            <!-- Most Popular Facility -->
            <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke p-gr-lg">
                <h3 class="text-h3 text-lgu-headline mb-gr-md flex items-center gap-2">
                    <i data-lucide="trophy" class="w-6 h-6"></i>
                    Most Popular This Month
                </h3>
                <div class="bg-lgu-highlight/10 rounded-xl p-gr-md border-2 border-lgu-highlight/20">
                    <p class="text-h2 font-bold text-lgu-headline">{{ $stats['popular_facility']['name'] }}</p>
                    <p class="text-body text-lgu-paragraph">{{ $stats['popular_facility']['count'] }} booking(s) this month</p>
                            </div>
                        </div>

            <!-- System Info -->
            <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke p-gr-lg">
                <h3 class="text-h3 text-lgu-headline mb-gr-md flex items-center gap-2">
                    <i data-lucide="building-2" class="w-6 h-6"></i>
                    System Information
                </h3>
                <div class="space-y-gr-sm">
                    <div class="flex items-center justify-between py-gr-xs border-b border-gray-100">
                        <span class="text-small text-gray-600">Total Facilities</span>
                        <span class="text-body font-semibold text-lgu-headline">{{ $stats['total_facilities'] }}</span>
                            </div>
                    <div class="flex items-center justify-between py-gr-xs border-b border-gray-100">
                        <span class="text-small text-gray-600">Pending Verification</span>
                        <span class="text-body font-semibold text-yellow-600">{{ $stats['pending_verification'] }}</span>
                        </div>
                    <div class="flex items-center justify-between py-gr-xs">
                        <span class="text-small text-gray-600">Expired (Unpaid)</span>
                        <span class="text-body font-semibold text-orange-600">{{ $stats['expired'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke p-gr-lg">
                <h3 class="text-h3 text-lgu-headline mb-gr-md flex items-center gap-2">
                    <i data-lucide="zap" class="w-6 h-6"></i>
                    Quick Actions
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-gr-sm">
                    <a href="{{ route('admin.payment-queue') }}" 
                       class="flex flex-col items-center p-gr-md bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors border border-yellow-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock text-yellow-600 mb-gr-xs">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <span class="text-small font-medium text-yellow-900">Payment Queue</span>
                    </a>
                    
                    <a href="{{ route('admin.bookings.index') }}" 
                       class="flex flex-col items-center p-gr-md bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors border border-blue-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-check text-blue-600 mb-gr-xs">
                            <path d="M8 2v4"/>
                            <path d="M16 2v4"/>
                            <rect width="18" height="18" x="3" y="4" rx="2"/>
                            <path d="M3 10h18"/>
                            <path d="m9 16 2 2 4-4"/>
                        </svg>
                        <span class="text-small font-medium text-blue-900">All Bookings</span>
                    </a>
                    
                    <a href="{{ route('admin.calendar') }}" 
                       class="flex flex-col items-center p-gr-md bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors border border-purple-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-days text-purple-600 mb-gr-xs">
                            <path d="M8 2v4"/>
                            <path d="M16 2v4"/>
                            <rect width="18" height="18" x="3" y="4" rx="2"/>
                            <path d="M3 10h18"/>
                            <path d="M8 14h.01"/>
                            <path d="M12 14h.01"/>
                            <path d="M16 14h.01"/>
                            <path d="M8 18h.01"/>
                            <path d="M12 18h.01"/>
                            <path d="M16 18h.01"/>
                        </svg>
                        <span class="text-small font-medium text-purple-900">View Calendar</span>
                    </a>
                    
                    <a href="{{ route('citizen.browse-facilities') }}" 
                       class="flex flex-col items-center p-gr-md bg-green-50 rounded-lg hover:bg-green-100 transition-colors border border-green-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building-2 text-green-600 mb-gr-xs">
                            <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/>
                            <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/>
                            <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/>
                            <path d="M10 6h4"/>
                            <path d="M10 10h4"/>
                            <path d="M10 14h4"/>
                            <path d="M10 18h4"/>
                        </svg>
                        <span class="text-small font-medium text-green-900">Facilities</span>
                    </a>
                </div>
            </div>

        </div>

        <!-- Right Column: Recent Activity -->
        <div class="space-y-gr-lg">
            
            <!-- Recent Bookings -->
            <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke p-gr-lg">
                <h3 class="text-h3 text-lgu-headline mb-gr-md flex items-center gap-2">
                    <i data-lucide="activity" class="w-6 h-6"></i>
                    Recent Bookings
                </h3>
                @if($recentBookings->count() > 0)
                    <div class="space-y-gr-sm">
                        @foreach($recentBookings as $booking)
                            <div class="border border-lgu-stroke rounded-lg p-gr-sm hover:shadow-sm transition-shadow">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                        <p class="text-small font-semibold text-lgu-headline">{{ $booking->facility->name ?? 'N/A' }}</p>
                                        <p class="text-caption text-lgu-paragraph">{{ $booking->purpose ?? 'No purpose specified' }}</p>
                                        <p class="text-caption text-gray-500">
                                            {{ $booking->start_time ? \Carbon\Carbon::parse($booking->start_time)->format('M j, Y @ g:i A') : 'No date' }}
                                        </p>
                                </div>
                                    <span class="inline-flex items-center px-gr-xs py-1 rounded-full text-caption font-medium
                                        @if($booking->status === 'confirmed') bg-purple-100 text-purple-800
                                        @elseif($booking->status === 'paid') bg-blue-100 text-blue-800
                                        @elseif($booking->status === 'staff_verified') bg-green-100 text-green-800
                                        @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($booking->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
                    <div class="mt-gr-md">
                        <a href="{{ route('admin.bookings.index') }}" class="text-small text-lgu-button hover:underline font-medium">
                            View All Bookings →
                        </a>
                    </div>
                @else
                    <p class="text-body text-lgu-paragraph text-center py-gr-lg">No recent bookings</p>
                @endif
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- Real-time clock and charts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons first
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    
    // Real-time clock functionality
    function updateDateTime() {
        const now = new Date();
        const dateElement = document.getElementById('current-date');
        const timeElement = document.getElementById('current-time-main');
        
        if (dateElement) {
            const dateString = now.toLocaleDateString('en-US', { 
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            dateElement.textContent = dateString;
        }
        
        if (timeElement) {
            const timeString = now.toLocaleTimeString('en-US', { 
                hour12: true,
                hour: 'numeric',
                minute: '2-digit'
            });
            timeElement.textContent = timeString;
        }
    }

    // Update date/time immediately and then every second
    updateDateTime();
    setInterval(updateDateTime, 1000);

    // Revenue Chart using ApexCharts (per ARCHITECTURE.md)
    // Wait for ApexCharts to load
    if (typeof ApexCharts !== 'undefined') {
        const chartElement = document.querySelector('#revenueChart');
        if (chartElement) {
            // Debug: Check what data we're receiving
            const revenueData = @json($stats['daily_revenue_data']);
            const revenueLabels = @json($stats['daily_revenue_labels']);
            console.log('Revenue Chart Data:', revenueData);
            console.log('Revenue Chart Labels:', revenueLabels);
            
            const options = {
                series: [{
                    name: 'Daily Revenue',
                    data: revenueData
                }],
                chart: {
                    type: 'area',
                    height: 180,
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
                    colors: ['#10b981']
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0.1,
                        stops: [0, 100]
                    },
                    colors: ['#10b981']
                },
                colors: ['#10b981'],
                xaxis: {
                    categories: revenueLabels,
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
            
            const chart = new ApexCharts(chartElement, options);
            chart.render();
        }
    } else {
        console.error('ApexCharts library not loaded');
    }
});
</script>
@endpush
