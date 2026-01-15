@extends('layouts.citizen')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Overview and Statistics')

@section('page-content')
<div class="space-y-6">
    <!-- Welcome Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Welcome back, {{ session('user_name', 'Citizen') }}!</h1>
            <p class="text-gray-600 mt-1">Manage your facility reservations and profile</p>
        </div>
    </div>
    
    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Active Bookings -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-check text-blue-600"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/><path d="m9 16 2 2 4-4"/></svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $activeBookings ?? 0 }}</h2>
                    <p class="text-gray-600">Active Bookings</p>
            </div>
        </div>
    </div>
    
        <!-- Completed -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check-circle text-green-600"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $completedBookings ?? 0 }}</h2>
                    <p class="text-gray-600">Completed</p>
            </div>
        </div>
    </div>
    
        <!-- Total Spent -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 flex items-center justify-center">
                    <span class="text-3xl font-bold text-purple-600">₱</span>
                </div>
                <div class="ml-4">
                    <h2 class="text-2xl font-bold text-gray-900">₱{{ number_format($totalSpent ?? 0, 2) }}</h2>
                    <p class="text-gray-600">Total Spent</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Available Facilities -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Available Facilities</h2>
            <p class="text-gray-600 mb-6">Browse and book public facilities easily.</p>
            <a href="{{ route('citizen.browse-facilities') }}" class="inline-flex items-center px-6 py-3 bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:bg-lgu-highlight transition shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/><path d="M10 6h4"/><path d="M10 10h4"/><path d="M10 14h4"/><path d="M10 18h4"/></svg>
                Browse Facilities
            </a>
        </div>

        <!-- Facility Calendar -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Facility Calendar</h2>
            <p class="text-gray-600 mb-6">View available dates and existing reservations.</p>
            <a href="{{ route('citizen.facility-calendar') }}" class="inline-flex items-center px-6 py-3 bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:bg-lgu-highlight transition shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/><path d="M16 18h.01"/></svg>
                View Calendar
            </a>
        </div>
    </div>

    <!-- Recent Activity / Upcoming Bookings -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Upcoming Bookings -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-900">Upcoming Bookings</h2>
                <a href="{{ route('citizen.reservations') }}" class="text-sm text-lgu-button hover:text-lgu-highlight font-medium">View All</a>
            </div>
            
            @if(isset($upcomingBookings) && $upcomingBookings->count() > 0)
                <div class="space-y-3">
                    @foreach($upcomingBookings as $booking)
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                            <div class="flex items-center">
                                <div class="p-2 bg-lgu-bg rounded-lg mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-lgu-button"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">Facility ID: {{ $booking->facility_id }}</h4>
                                    <p class="text-xs text-gray-600">{{ \Carbon\Carbon::parse($booking->start_time)->format('M d, Y • h:i A') }}</p>
                                </div>
                            </div>
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-lgu-bg text-lgu-headline">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-x text-gray-400"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/><path d="m14 14-4 4"/><path d="m10 14 4 4"/></svg>
                    </div>
                    <p class="text-gray-500 font-medium">No upcoming bookings</p>
                    <p class="text-sm text-gray-400 mt-1">Book a facility to get started</p>
                </div>
            @endif
        </div>

        <!-- Pending Payments -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-900">Pending Payments</h2>
                <a href="{{ route('citizen.payment-slips') }}" class="text-sm text-lgu-button hover:text-lgu-highlight font-medium">View All</a>
            </div>
            
            @if(isset($pendingPayments) && $pendingPayments->count() > 0)
                <div class="space-y-3">
                    @foreach($pendingPayments as $payment)
                        <div class="flex items-center justify-between p-3 border border-yellow-200 bg-yellow-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="p-2 bg-yellow-100 rounded-lg mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-alert-circle text-yellow-600"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">{{ $payment->slip_number }}</h4>
                                    <p class="text-xs text-gray-600">₱{{ number_format($payment->amount_due, 2) }} • Due {{ \Carbon\Carbon::parse($payment->payment_deadline)->diffForHumans() }}</p>
                                </div>
                            </div>
                            <a href="{{ route('citizen.payment-slips.show', $payment->id) }}" class="text-yellow-600 hover:text-yellow-800 text-sm font-medium">
                                Pay Now
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check-circle text-gray-400"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                    </div>
                    <p class="text-gray-500 font-medium">No pending payments</p>
                    <p class="text-sm text-gray-400 mt-1">All payments are up to date</p>
                </div>
            @endif
        </div>
    </div>

    <!-- System Announcements -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-megaphone text-blue-600"><path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/></svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">System Announcements</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Welcome to the LGU1 Public Facilities Reservation System!</li>
                        <li>All reservations require advance booking and approval.</li>
                        <li>City residents receive a 30% discount when booking facilities in their area.</li>
                        <li>Senior Citizens, PWDs, and Students receive an additional 20% discount.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Dashboard interactivity can be added here
document.addEventListener('DOMContentLoaded', function() {
    console.log('Citizen Dashboard Loaded');
});
</script>
@endpush
@endsection
