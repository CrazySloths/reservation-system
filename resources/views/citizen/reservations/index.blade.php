@extends('layouts.citizen')

@section('title', 'My Reservations')
@section('page-title', 'My Reservations')
@section('page-subtitle', 'Track and manage your facility bookings')

@section('page-content')
<div class="space-y-6">
    <!-- Quick Actions -->
    <div class="flex justify-end">
        <a href="{{ route('citizen.browse-facilities') }}" 
           class="inline-flex items-center px-6 py-3 bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:bg-lgu-highlight transition-all shadow-lg hover:shadow-xl cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                <path d="M5 12h14"/><path d="M12 5v14"/>
            </svg>
            New Booking
        </a>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-100">
        <div class="space-y-6">
            <!-- Search Bar -->
            <div>
                <label for="search" class="block text-sm font-semibold text-gray-700 mb-2">Search Bookings</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                        </svg>
                    </div>
                    <input type="text" 
                           id="search" 
                           value="{{ $search }}" 
                           placeholder="Search by facility name, reference #, or purpose..." 
                           class="block w-full pl-11 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-lgu-button focus:border-lgu-button transition-colors"
                           onkeyup="liveSearch(this.value)">
                </div>
            </div>

            <!-- Status Filters -->
            <div>
                <div class="flex items-center justify-between mb-3">
                    <label class="block text-sm font-semibold text-gray-700">Filter by Status</label>
                    <a href="{{ route('citizen.reservation.history') }}" 
                       class="text-sm text-lgu-button hover:text-lgu-highlight font-semibold inline-flex items-center gap-1 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                            <path d="M3 3v5h5"/>
                        </svg>
                        View Booking History
                    </a>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('citizen.reservations', ['status' => 'all', 'search' => $search]) }}" 
                       class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all shadow-sm hover:shadow-md cursor-pointer {{ $status === 'all' ? 'bg-lgu-button text-lgu-button-text shadow-lg scale-105' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-block mr-1">
                            <rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/>
                        </svg>
                        All <span class="ml-1 px-2 py-0.5 bg-white/30 rounded-full text-xs">{{ $statusCounts['all'] }}</span>
                    </a>
                    <a href="{{ route('citizen.reservations', ['status' => 'active', 'search' => $search]) }}" 
                       class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all shadow-sm hover:shadow-md cursor-pointer {{ $status === 'active' ? 'bg-lgu-button text-lgu-button-text shadow-lg scale-105' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-block mr-1">
                            <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                        </svg>
                        Active <span class="ml-1 px-2 py-0.5 bg-white/30 rounded-full text-xs">{{ $statusCounts['active'] }}</span>
                    </a>
                    <a href="{{ route('citizen.reservations', ['status' => 'completed', 'search' => $search]) }}" 
                       class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all shadow-sm hover:shadow-md cursor-pointer {{ $status === 'completed' ? 'bg-lgu-button text-lgu-button-text shadow-lg scale-105' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-block mr-1">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Completed <span class="ml-1 px-2 py-0.5 bg-white/30 rounded-full text-xs">{{ $statusCounts['completed'] }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bookings List -->
    @if($bookings->isEmpty())
        <div class="bg-white shadow-xl rounded-2xl p-16 text-center border border-gray-100">
            <div class="mx-auto w-24 h-24 bg-lgu-bg rounded-full flex items-center justify-center mb-6 shadow-lg">
                <svg class="w-12 h-12 text-lgu-button" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-3">No Reservations Found</h3>
            <p class="text-gray-600 mb-8 max-w-md mx-auto">You haven't made any bookings yet{{ $search ? ' matching your search' : '' }}. Start by browsing our available facilities!</p>
            <a href="{{ route('citizen.browse-facilities') }}" 
               class="inline-flex items-center px-8 py-4 bg-lgu-button text-lgu-button-text font-bold rounded-xl hover:bg-lgu-highlight transition-all shadow-lg hover:shadow-xl cursor-pointer transform hover:scale-105">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                    <path d="M5 12h14"/><path d="M12 5v14"/>
                </svg>
                Browse Facilities
            </a>
        </div>
    @else
        <div class="space-y-5">
            @foreach($bookings as $booking)
                @php
                    $statusBadge = match($booking->status) {
                        'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'border' => 'border-yellow-300', 'label' => 'Pending Review'],
                        'staff_verified' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'border' => 'border-purple-300', 'label' => 'Verified'],
                        'payment_pending' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'border' => 'border-orange-300', 'label' => 'Awaiting Payment'],
                        'paid' => ['bg' => 'bg-cyan-100', 'text' => 'text-cyan-800', 'border' => 'border-cyan-300', 'label' => 'Payment Verified'],
                        'confirmed' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'border' => 'border-green-300', 'label' => 'Confirmed'],
                        'completed' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'border' => 'border-blue-300', 'label' => 'Completed'],
                        'cancelled' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'border' => 'border-gray-300', 'label' => 'Cancelled'],
                        'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'border' => 'border-red-300', 'label' => 'Rejected'],
                        'expired' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'border' => 'border-gray-300', 'label' => 'Expired'],
                        default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'border' => 'border-gray-300', 'label' => ucfirst($booking->status)]
                    };
                    $startTime = \Carbon\Carbon::parse($booking->start_time);
                    $isUpcoming = $startTime->isFuture();
                @endphp
                
                <div class="bg-white shadow-lg rounded-xl overflow-hidden hover:shadow-2xl transition-all duration-300 border border-gray-100 hover:border-lgu-button/30 transform hover:-translate-y-1">
                    <div class="flex flex-col md:flex-row">
                        <!-- Facility Image -->
                        <div class="md:w-96 h-64 md:h-auto bg-gray-200 flex-shrink-0 relative group">
                            @if($booking->facility_image)
                                <img src="{{ asset('storage/' . $booking->facility_image) }}" 
                                     alt="{{ $booking->facility_name }}" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-lgu-button/30">
                                    <svg class="w-20 h-20 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                            @endif
                            @if($isUpcoming)
                                <div class="absolute top-3 left-3 px-3 py-1.5 bg-lgu-button text-lgu-button-text text-xs font-bold rounded-full shadow-lg z-10">
                                    Upcoming
                                </div>
                            @endif
                        </div>

                        <!-- Booking Details -->
                        <div class="flex-1 p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2 flex-wrap">
                                        <h3 class="text-xl font-bold text-gray-900">{{ $booking->facility_name }}</h3>
                                        @if($booking->city_code)
                                            <span class="px-3 py-1 bg-lgu-bg text-lgu-headline text-xs font-bold rounded-lg">
                                                {{ $booking->city_code }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600 mb-1">
                                        <span class="font-medium">Reference #:</span> 
                                        <span class="font-bold text-lgu-headline">BK{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</span>
                                    </p>
                                    <p class="text-sm text-gray-700">
                                        <span class="font-medium">Purpose:</span> {{ $booking->purpose }}
                                    </p>
                                </div>

                                <!-- Status Badge -->
                                <span class="px-4 py-2 rounded-full text-sm font-bold {{ $statusBadge['bg'] }} {{ $statusBadge['text'] }} border-2 {{ $statusBadge['border'] }} whitespace-nowrap shadow-sm">
                                    {{ $statusBadge['label'] }}
                                </span>
                            </div>

                            <!-- Booking Info Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5 p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center text-sm">
                                    <div class="w-10 h-10 bg-lgu-button/10 rounded-lg flex items-center justify-center mr-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-lgu-button">
                                            <rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 font-medium">Date</p>
                                        <p class="font-bold text-gray-900">{{ $startTime->format('M d, Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center text-sm">
                                    <div class="w-10 h-10 bg-lgu-button/10 rounded-lg flex items-center justify-center mr-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-lgu-button">
                                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 font-medium">Time</p>
                                        <p class="font-bold text-gray-900">{{ $startTime->format('g:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center text-sm">
                                    <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center mr-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-600">
                                            <circle cx="8" cy="8" r="6"/><path d="M18.09 10.37A6 6 0 1 1 10.34 18"/><path d="M7 6h1v4"/><path d="m16.71 13.88.7.71-2.82 2.82"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 font-medium">Total Amount</p>
                                        <p class="font-bold text-green-600 text-lg">₱{{ number_format($booking->total_amount, 2) }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center gap-3 flex-wrap">
                                <a href="{{ route('citizen.reservations.show', $booking->id) }}" 
                                   class="px-5 py-2.5 bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:bg-lgu-highlight transition-all shadow-md hover:shadow-lg text-sm cursor-pointer">
                                    View Details →
                                </a>

                                @if(in_array($booking->status, ['pending', 'staff_verified', 'payment_pending']))
                                    <button type="button" 
                                            onclick="confirmCancel({{ $booking->id }})"
                                            class="px-5 py-2.5 bg-red-50 text-red-700 font-semibold rounded-lg hover:bg-red-100 transition-all border-2 border-red-200 hover:border-red-300 text-sm cursor-pointer">
                                        Cancel Booking
                                    </button>
                                @endif

                                @if($booking->status === 'payment_pending')
                                    <a href="{{ route('citizen.payment-slips') }}" 
                                       class="px-5 py-2.5 bg-orange-50 text-orange-700 font-semibold rounded-lg hover:bg-orange-100 transition-all border-2 border-orange-200 hover:border-orange-300 text-sm cursor-pointer inline-flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                            <rect width="20" height="14" x="2" y="5" rx="2"/>
                                            <line x1="2" x2="22" y1="10" y2="10"/>
                                        </svg>
                                        View Payment Slip
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="bg-white shadow-lg rounded-xl p-5 border border-gray-100">
            {{ $bookings->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
// Live Search Function
let searchTimeout;
function liveSearch(query) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        const currentStatus = '{{ $status }}';
        window.location.href = `{{ route('citizen.reservations') }}?status=${currentStatus}&search=${encodeURIComponent(query)}`;
    }, 500);
}

// Cancel Booking with SweetAlert2
function confirmCancel(bookingId) {
    Swal.fire({
        title: 'Cancel Booking?',
        html: `
            <div class="text-left">
                <p class="text-gray-600 mb-4">Please provide a reason for cancelling this booking:</p>
                <textarea id="cancellation_reason" 
                          class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" 
                          rows="4" 
                          placeholder="Enter your reason here..."
                          style="resize: none;"></textarea>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Cancel Booking',
        cancelButtonText: 'Keep Booking',
        customClass: {
            popup: 'rounded-xl',
            confirmButton: 'px-6 py-2.5 rounded-lg font-semibold cursor-pointer',
            cancelButton: 'px-6 py-2.5 rounded-lg font-semibold cursor-pointer'
        },
        preConfirm: () => {
            const reason = document.getElementById('cancellation_reason').value.trim();
            if (!reason) {
                Swal.showValidationMessage('Please provide a cancellation reason');
                return false;
            }
            if (reason.length < 10) {
                Swal.showValidationMessage('Reason must be at least 10 characters');
                return false;
            }
            return reason;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Processing...',
                text: 'Cancelling your booking',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/citizen/reservations/${bookingId}/cancel`;

            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';

            const reasonInput = document.createElement('input');
            reasonInput.type = 'hidden';
            reasonInput.name = 'cancellation_reason';
            reasonInput.value = result.value;

            form.appendChild(csrfToken);
            form.appendChild(reasonInput);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Success/Error Messages
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session('success') }}',
        confirmButtonColor: '#047857',
        confirmButtonText: 'OK',
        customClass: {
            popup: 'rounded-xl',
            confirmButton: 'px-6 py-2.5 rounded-lg font-semibold cursor-pointer'
        }
    });
@endif

@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '{{ session('error') }}',
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'OK',
        customClass: {
            popup: 'rounded-xl',
            confirmButton: 'px-6 py-2.5 rounded-lg font-semibold cursor-pointer'
        }
    });
@endif
</script>
@endpush
@endsection

