@extends('layouts.staff')

@section('title', 'Booking History')

@section('page-title', 'Booking History')

@section('page-subtitle', 'View all processed booking requests (approved, rejected, completed, cancelled)')

@section('page-content')
<div class="max-w-7xl mx-auto">
    @if(session('success'))
    <div class="mb-gr-md p-4 bg-green-50 border border-green-200 rounded-lg flex items-start">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check-circle text-green-600 mr-3 flex-shrink-0 mt-0.5">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
            <path d="m9 11 3 3L22 4"/>
        </svg>
        <p class="text-small text-green-800">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Filters Section -->
    <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke p-gr-lg mb-gr-md">
        <form method="GET" action="{{ route('staff.bookings.index') }}" class="space-y-gr-sm">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-gr-sm">
                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-small font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" id="status" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-lgu-button focus:border-lgu-button text-small">
                        <option value="">All Status</option>
                        <option value="staff_verified" {{ request('status') == 'staff_verified' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <!-- Facility Filter -->
                <div>
                    <label for="facility_id" class="block text-small font-medium text-gray-700 mb-2">Facility</label>
                    <select name="facility_id" id="facility_id" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-lgu-button focus:border-lgu-button text-small">
                        <option value="">All Facilities</option>
                        @foreach($facilities as $facility)
                            <option value="{{ $facility->facility_id }}" {{ request('facility_id') == $facility->facility_id ? 'selected' : '' }}>
                                {{ $facility->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date From -->
                <div>
                    <label for="date_from" class="block text-small font-medium text-gray-700 mb-2">Event Date From</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" 
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-lgu-button focus:border-lgu-button text-small">
                </div>

                <!-- Date To -->
                <div>
                    <label for="date_to" class="block text-small font-medium text-gray-700 mb-2">Event Date To</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" 
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-lgu-button focus:border-lgu-button text-small">
                </div>
            </div>

            <!-- Search and Filter Button -->
            <div class="flex flex-col sm:flex-row gap-gr-xs">
                <div class="flex-1">
                    <label for="search" class="block text-small font-medium text-gray-700 mb-2">Search by Booking ID</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                           placeholder="Enter booking ID..." 
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-lgu-button focus:border-lgu-button text-small">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="px-gr-md py-2.5 bg-lgu-button text-white font-semibold rounded-lg hover:bg-lgu-button-hover transition-colors shadow-sm flex items-center whitespace-nowrap">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search mr-2">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.3-4.3"/>
                        </svg>
                        Filter
                    </button>
                    @if(request()->hasAny(['status', 'facility_id', 'date_from', 'date_to', 'search']))
                    <a href="{{ route('staff.bookings.index') }}" class="px-gr-md py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors flex items-center whitespace-nowrap">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x mr-2">
                            <path d="M18 6 6 18"/>
                            <path d="m6 6 12 12"/>
                        </svg>
                        Clear
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Bookings List -->
    @if($bookings->count() > 0)
    <div class="space-y-gr-sm">
        @foreach($bookings as $booking)
        <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke p-gr-md hover:shadow-md transition-shadow">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-gr-sm">
                <!-- Left: Booking Info -->
                <div class="flex-1">
                    <div class="flex items-start gap-gr-md">
                        <!-- Facility Icon -->
                        <div class="flex-shrink-0 w-10 h-10 bg-lgu-highlight/10 rounded-lg flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building-2 text-lgu-button">
                                <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/>
                                <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/>
                                <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/>
                                <path d="M10 6h4"/>
                                <path d="M10 10h4"/>
                                <path d="M10 14h4"/>
                                <path d="M10 18h4"/>
                            </svg>
                        </div>

                        <!-- Booking Details -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-gr-xs mb-gr-xs">
                                <h3 class="text-h4 font-bold text-lgu-headline">{{ $booking->facility->name ?? 'N/A' }}</h3>
                                @if($booking->status == 'pending')
                                    <span class="inline-flex items-center px-gr-xs py-1 rounded-full text-caption font-medium bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                @elseif($booking->status == 'staff_verified' || $booking->status == 'approved')
                                    <span class="inline-flex items-center px-gr-xs py-1 rounded-full text-caption font-medium bg-green-100 text-green-800">
                                        Approved
                                    </span>
                                @elseif($booking->status == 'rejected')
                                    <span class="inline-flex items-center px-gr-xs py-1 rounded-full text-caption font-medium bg-red-100 text-red-800">
                                        Rejected
                                    </span>
                                @elseif($booking->status == 'completed')
                                    <span class="inline-flex items-center px-gr-xs py-1 rounded-full text-caption font-medium bg-blue-100 text-blue-800">
                                        Completed
                                    </span>
                                @elseif($booking->status == 'cancelled')
                                    <span class="inline-flex items-center px-gr-xs py-1 rounded-full text-caption font-medium bg-gray-100 text-gray-800">
                                        Cancelled
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-gr-xs py-1 rounded-full text-caption font-medium bg-gray-100 text-gray-800">
                                        {{ ucfirst($booking->status ?? 'Unknown') }}
                                    </span>
                                @endif
                            </div>
                            
                            <p class="text-small text-gray-600 mb-gr-sm">{{ $booking->facility->lguCity->city_name ?? 'N/A' }}</p>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-gr-xs text-caption text-gray-600">
                                <div class="flex items-center gap-gr-xs">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar flex-shrink-0">
                                        <path d="M8 2v4"/>
                                        <path d="M16 2v4"/>
                                        <rect width="18" height="18" x="3" y="4" rx="2"/>
                                        <path d="M3 10h18"/>
                                    </svg>
                                    <span>{{ \Carbon\Carbon::parse($booking->event_date ?? $booking->start_time)->format('M d, Y') }}</span>
                                </div>
                                <div class="flex items-center gap-gr-xs">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock flex-shrink-0">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                    <span>{{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}</span>
                                </div>
                                <div class="flex items-center gap-gr-xs">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users flex-shrink-0">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                    <span>{{ $booking->expected_attendees ?? 'N/A' }} attendees</span>
                                </div>
                                <div class="flex items-center gap-gr-xs">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-hash flex-shrink-0">
                                        <line x1="4" x2="20" y1="9" y2="9"/>
                                        <line x1="4" x2="20" y1="15" y2="15"/>
                                        <line x1="10" x2="8" y1="3" y2="21"/>
                                        <line x1="16" x2="14" y1="3" y2="21"/>
                                    </svg>
                                    <span>BK-{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</span>
                                </div>
                            </div>

                            <div class="mt-gr-sm text-caption text-gray-500">
                                Submitted: {{ \Carbon\Carbon::parse($booking->created_at)->format('M d, Y h:i A') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Action Button -->
                <div class="flex-shrink-0">
                    <a href="{{ route('staff.bookings.review', $booking->id) }}" 
                       class="inline-flex items-center px-gr-md py-2.5 bg-lgu-button text-white font-semibold rounded-lg hover:bg-lgu-button-hover transition-colors shadow-sm whitespace-nowrap">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye mr-2">
                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        View Details
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-gr-lg">
        {{ $bookings->links() }}
    </div>
    @else
    <!-- Empty State -->
    <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke p-gr-xl text-center">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-inbox mx-auto text-gray-400 mb-gr-sm">
            <polyline points="22 12 16 12 14 15 10 15 8 12 2 12"/>
            <path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/>
        </svg>
        <h3 class="text-h3 font-bold text-gray-900 mb-2">No Bookings Found</h3>
        <p class="text-body text-gray-600">
            @if(request()->hasAny(['status', 'facility_id', 'date_from', 'date_to', 'search']))
                No bookings match your filter criteria. Try adjusting your filters.
            @else
                There are no booking records yet.
            @endif
        </p>
    </div>
    @endif
</div>
@endsection

