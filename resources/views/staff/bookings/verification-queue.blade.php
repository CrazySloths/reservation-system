@extends('layouts.staff')

@section('title', 'Verification Queue')
@section('page-title', 'Booking Verification Queue')
@section('page-subtitle', 'Review and verify citizen booking requests')

@section('page-content')
<div class="space-y-gr-lg">
    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke p-gr-md">
        <form method="GET" action="{{ route('staff.verification-queue') }}" class="grid grid-cols-1 md:grid-cols-4 gap-gr-md">
            <!-- Facility Filter -->
            <div>
                <label for="facility_id" class="block text-small font-medium text-gray-700 mb-2">Facility</label>
                <select name="facility_id" id="facility_id" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-lgu-button focus:border-lgu-button text-small">
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
                       class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-lgu-button focus:border-lgu-button text-small">
            </div>

            <!-- Date To -->
            <div>
                <label for="date_to" class="block text-small font-medium text-gray-700 mb-2">Event Date To</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" 
                       class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-lgu-button focus:border-lgu-button text-small">
            </div>

            <!-- Submit Button -->
            <div class="flex items-end">
                <button type="submit" class="w-full px-6 py-2.5 bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:bg-lgu-highlight transition-colors shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-filter inline-block mr-1">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                    </svg>
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Bookings List -->
    @if($bookings->count() > 0)
        <div class="space-y-gr-md">
            @foreach($bookings as $booking)
            <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke hover:border-lgu-button transition-colors overflow-hidden">
                <div class="p-gr-md">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-gr-md">
                        <!-- Booking Info -->
                        <div class="flex-1">
                            <div class="flex items-start gap-gr-sm mb-gr-sm">
                                <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building text-amber-600">
                                        <rect width="16" height="20" x="4" y="2" rx="2" ry="2"/>
                                        <path d="M9 22v-4h6v4"/>
                                        <path d="M8 6h.01"/>
                                        <path d="M16 6h.01"/>
                                        <path d="M12 6h.01"/>
                                        <path d="M12 10h.01"/>
                                        <path d="M12 14h.01"/>
                                        <path d="M16 10h.01"/>
                                        <path d="M16 14h.01"/>
                                        <path d="M8 10h.01"/>
                                        <path d="M8 14h.01"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-h4 font-bold text-lgu-headline mb-1">{{ $booking->facility->name ?? 'N/A' }}</h3>
                                    <div class="flex flex-wrap gap-2 mb-gr-sm">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $booking->facility->lguCity->city_name ?? 'N/A' }}
                                        </span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock mr-1">
                                                <circle cx="12" cy="12" r="10"/>
                                                <polyline points="12 6 12 12 16 14"/>
                                            </svg>
                                            Pending
                                        </span>
                                    </div>
                                    
                                    <!-- Event Details -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-gr-md gap-y-2 text-small text-gray-600">
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar mr-2 text-gray-400">
                                                <path d="M8 2v4"/>
                                                <path d="M16 2v4"/>
                                                <rect width="18" height="18" x="3" y="4" rx="2"/>
                                                <path d="M3 10h18"/>
                                            </svg>
                                            {{ \Carbon\Carbon::parse($booking->event_date)->format('M d, Y') }}
                                        </div>
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock mr-2 text-gray-400">
                                                <circle cx="12" cy="12" r="10"/>
                                                <polyline points="12 6 12 12 16 14"/>
                                            </svg>
                                            {{ $booking->start_time }} - {{ $booking->end_time }}
                                        </div>
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users mr-2 text-gray-400">
                                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                                <circle cx="9" cy="7" r="4"/>
                                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                            </svg>
                                            {{ number_format($booking->expected_attendees) }} attendees
                                        </div>
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text mr-2 text-gray-400">
                                                <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/>
                                                <path d="M14 2v4a2 2 0 0 0 2 2h4"/>
                                                <path d="M10 9H8"/>
                                                <path d="M16 13H8"/>
                                                <path d="M16 17H8"/>
                                            </svg>
                                            {{ $booking->purpose ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submitted Date -->
                            <div class="mt-gr-sm pt-gr-sm border-t border-gray-200">
                                <p class="text-xs text-gray-500">
                                    Submitted: {{ \Carbon\Carbon::parse($booking->created_at)->format('M d, Y g:i A') }}
                                    ({{ \Carbon\Carbon::parse($booking->created_at)->diffForHumans() }})
                                </p>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="flex-shrink-0">
                            <a href="{{ route('staff.bookings.review', $booking->id) }}" 
                               class="inline-flex items-center px-6 py-3 bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:bg-lgu-highlight transition-colors shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye mr-2">
                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                Review
                            </a>
                        </div>
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
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-gr-md">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check-circle text-gray-400">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <path d="m9 11 3 3L22 4"/>
                </svg>
            </div>
            <h3 class="text-h4 font-bold text-gray-900 mb-2">No Pending Bookings</h3>
            <p class="text-body text-gray-600 mb-gr-md">
                There are currently no bookings waiting for verification. Great job!
            </p>
            <a href="{{ route('staff.dashboard') }}" class="inline-flex items-center text-lgu-button hover:text-lgu-highlight font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left mr-2">
                    <path d="m12 19-7-7 7-7"/>
                    <path d="M19 12H5"/>
                </svg>
                Back to Dashboard
            </a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Display success/error messages
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session('success') }}',
        confirmButtonColor: '#faae2b',
        confirmButtonText: 'OK',
        timer: 3000
    });
@endif

@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: '{{ session('error') }}',
        confirmButtonColor: '#fa5246',
        confirmButtonText: 'OK'
    });
@endif
</script>
@endpush

