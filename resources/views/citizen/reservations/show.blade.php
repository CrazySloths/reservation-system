@extends('layouts.citizen')

@section('title', 'Booking Details')
@section('page-title', 'Booking Details')
@section('page-subtitle', 'Reference #' . $booking->id)

@section('page-content')
<div class="space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('citizen.reservations') }}" 
           class="inline-flex items-center text-lgu-button hover:text-lgu-highlight font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                <path d="m12 19-7-7 7-7"/><path d="M19 12H5"/>
            </svg>
            Back to My Reservations
        </a>
    </div>

    <!-- Status Alert -->
    @php
        $statusInfo = match($booking->status) {
            'pending' => [
                'bg' => 'bg-yellow-50',
                'border' => 'border-yellow-500', 
                'text' => 'text-yellow-800',
                'icon' => 'text-yellow-500',
                'label' => 'Pending Review',
                'message' => 'Your booking is being reviewed by our staff. We\'ll notify you once it\'s verified.'
            ],
            'staff_verified' => [
                'bg' => 'bg-purple-50',
                'border' => 'border-purple-500',
                'text' => 'text-purple-800',
                'icon' => 'text-purple-500',
                'label' => 'Verified',
                'message' => 'Your booking has been verified! A payment slip will be generated shortly.'
            ],
            'payment_pending' => [
                'bg' => 'bg-orange-50',
                'border' => 'border-orange-500',
                'text' => 'text-orange-800',
                'icon' => 'text-orange-500',
                'label' => 'Awaiting Payment',
                'message' => 'Please settle your payment to confirm this booking.'
            ],
            'paid' => [
                'bg' => 'bg-cyan-50',
                'border' => 'border-cyan-500',
                'text' => 'text-cyan-800',
                'icon' => 'text-cyan-500',
                'label' => 'Payment Verified',
                'message' => 'Your payment has been verified by the treasurer! Awaiting admin final confirmation.'
            ],
            'confirmed' => [
                'bg' => 'bg-green-50',
                'border' => 'border-green-500',
                'text' => 'text-green-800',
                'icon' => 'text-green-500',
                'label' => 'Confirmed',
                'message' => 'Your booking is confirmed! See you on the scheduled date.'
            ],
            'completed' => [
                'bg' => 'bg-blue-50',
                'border' => 'border-blue-500',
                'text' => 'text-blue-800',
                'icon' => 'text-blue-500',
                'label' => 'Completed',
                'message' => 'This booking has been completed. Thank you for using our facility!'
            ],
            'cancelled' => [
                'bg' => 'bg-gray-50',
                'border' => 'border-gray-500',
                'text' => 'text-gray-800',
                'icon' => 'text-gray-500',
                'label' => 'Cancelled',
                'message' => 'This booking has been cancelled.'
            ],
            'rejected' => [
                'bg' => 'bg-red-50',
                'border' => 'border-red-500',
                'text' => 'text-red-800',
                'icon' => 'text-red-500',
                'label' => 'Rejected',
                'message' => 'Unfortunately, this booking was rejected.'
            ],
            default => [
                'bg' => 'bg-gray-50',
                'border' => 'border-gray-500',
                'text' => 'text-gray-800',
                'icon' => 'text-gray-500',
                'label' => ucfirst($booking->status),
                'message' => ''
            ]
        };
    @endphp

    <div class="{{ $statusInfo['bg'] }} border-l-4 {{ $statusInfo['border'] }} p-5 rounded-lg shadow-sm">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 {{ $statusInfo['icon'] }}" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-4">
                <h3 class="text-base font-bold {{ $statusInfo['text'] }}">
                    {{ $statusInfo['label'] }}
                </h3>
                <p class="text-sm {{ $statusInfo['text'] }} mt-1 opacity-90">
                    {{ $statusInfo['message'] }}
                </p>
            </div>
        </div>
    </div>

    <!-- Event Completed - Leave Review Alert -->
    @if($canReview)
        <div class="bg-gradient-to-r from-yellow-50 to-amber-50 border-l-4 border-lgu-highlight p-5 rounded-lg shadow-md">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i data-lucide="message-circle" class="w-7 h-7 text-lgu-highlight"></i>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-bold text-lgu-headline">We'd Love Your Feedback!</h3>
                    <p class="text-sm text-gray-700 mt-1 mb-3">
                        Your event has ended. Please take a moment to share your experience and help us improve our services.
                    </p>
                    <a href="{{ route('citizen.reviews.create', $booking->id) }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-lgu-highlight text-lgu-button-text font-bold rounded-lg hover:bg-lgu-hover transition shadow-md">
                        <i data-lucide="star" class="w-5 h-5"></i>
                        Leave a Review Now
                    </a>
                </div>
            </div>
        </div>
    @elseif($existingReview)
        <div class="bg-blue-50 border-l-4 border-blue-500 p-5 rounded-lg shadow-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i data-lucide="check-circle" class="w-6 h-6 text-blue-500"></i>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-base font-bold text-blue-800">Thank You for Your Review!</h3>
                    <p class="text-sm text-blue-700 mt-1">
                        You've already submitted feedback for this booking. You can view or edit your review anytime.
                    </p>
                </div>
            </div>
        </div>
    @endif

    @if($booking->status === 'staff_verified')
        <!-- Payment Deadline Countdown -->
        @php
            $deadline = $booking->getPaymentDeadline();
            $hoursRemaining = $booking->getHoursUntilDeadline();
            $isOverdue = $booking->isPaymentOverdue();
            $isCritical = $booking->isDeadlineCritical();
            $isApproaching = $booking->isDeadlineApproaching();
        @endphp

        @if($isOverdue)
            <div class="bg-red-50 border-l-4 border-red-500 p-5 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i data-lucide="alert-circle" class="w-6 h-6 text-red-500"></i>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-base font-bold text-red-800">Payment Deadline Passed</h3>
                        <p class="text-sm text-red-700 mt-1">
                            The 48-hour payment deadline has passed. This booking will be automatically expired soon.
                        </p>
                        <p class="text-sm text-red-600 mt-2">
                            <strong>Deadline was:</strong> {{ $deadline->format('M d, Y h:i A') }}
                        </p>
                    </div>
                </div>
            </div>
        @elseif($isCritical)
            <div class="bg-orange-50 border-l-4 border-orange-500 p-5 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i data-lucide="clock-alert" class="w-6 h-6 text-orange-500"></i>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-base font-bold text-orange-800">URGENT: Payment Deadline Approaching</h3>
                        <p class="text-sm text-orange-700 mt-1">
                            Less than 6 hours remaining! Please submit your payment immediately to secure this booking.
                        </p>
                        <div class="mt-3 bg-white border border-orange-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-caption text-orange-600 font-medium">Time Remaining</p>
                                    <p class="text-h3 font-bold text-orange-600" id="countdown-timer">
                                        {{ $booking->formatTimeRemaining() }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-caption text-orange-600 font-medium">Deadline</p>
                                    <p class="text-small font-bold text-orange-700">
                                        {{ $deadline->format('M d, Y') }}<br>
                                        {{ $deadline->format('h:i A') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($isApproaching)
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-5 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i data-lucide="clock" class="w-6 h-6 text-yellow-500"></i>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-base font-bold text-yellow-800">Payment Deadline Reminder</h3>
                        <p class="text-sm text-yellow-700 mt-1">
                            Less than 24 hours remaining. Please submit your payment soon to avoid expiration.
                        </p>
                        <div class="mt-3 bg-white border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-caption text-yellow-600 font-medium">Time Remaining</p>
                                    <p class="text-h3 font-bold text-yellow-600" id="countdown-timer">
                                        {{ $booking->formatTimeRemaining() }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-caption text-yellow-600 font-medium">Deadline</p>
                                    <p class="text-small font-bold text-yellow-700">
                                        {{ $deadline->format('M d, Y') }}<br>
                                        {{ $deadline->format('h:i A') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-green-50 border-l-4 border-green-500 p-5 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i data-lucide="check-circle" class="w-6 h-6 text-green-500"></i>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-base font-bold text-green-800">Payment Required Within 48 Hours</h3>
                        <p class="text-sm text-green-700 mt-1">
                            Your booking has been verified! Please submit payment before the deadline to confirm your reservation.
                        </p>
                        <div class="mt-3 bg-white border border-green-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-caption text-green-600 font-medium">Time Remaining</p>
                                    <p class="text-h3 font-bold text-green-600" id="countdown-timer">
                                        {{ $booking->formatTimeRemaining() }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-caption text-green-600 font-medium">Deadline</p>
                                    <p class="text-small font-bold text-green-700">
                                        {{ $deadline->format('M d, Y') }}<br>
                                        {{ $deadline->format('h:i A') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Facility Information -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $booking->facility_name }}</h2>
                    <div class="flex items-center gap-2 mt-2">
                        @if($booking->city_code)
                            <span class="px-2 py-1 bg-lgu-bg text-lgu-headline text-xs font-semibold rounded">
                                {{ $booking->city_code }}
                            </span>
                        @endif
                        <span class="text-sm text-gray-600">{{ $booking->facility_address }}</span>
                    </div>
                </div>

                @if($booking->facility_image)
                    <img src="{{ asset('storage/' . $booking->facility_image) }}" 
                         alt="{{ $booking->facility_name }}" 
                         class="w-full h-64 object-cover">
                @else
                    <div class="w-full h-64 bg-lgu-bg flex items-center justify-center">
                        <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                @endif

                <div class="p-6">
                    <p class="text-gray-700 mb-4">{{ $booking->facility_description }}</p>
                    <div class="flex items-center text-sm text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        Capacity: {{ $booking->facility_capacity }} people
                    </div>
                </div>
            </div>

            <!-- Booking Details -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Booking Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Date</label>
                        <p class="text-lg font-semibold text-gray-900">{{ \Carbon\Carbon::parse($booking->start_time)->format('F d, Y') }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Time</label>
                        <p class="text-lg font-semibold text-gray-900">{{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Purpose</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $booking->purpose }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Expected Attendees</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $booking->expected_attendees }} people</p>
                    </div>
                    @if($booking->special_requests)
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium text-gray-600">Special Requests</label>
                            <p class="text-gray-900">{{ $booking->special_requests }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Selected Equipment -->
            @if($equipment->isNotEmpty())
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Selected Equipment</h3>
                    <div class="space-y-3">
                        @foreach($equipment as $item)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $item->equipment_name }}</p>
                                    <p class="text-sm text-gray-600">Quantity: {{ $item->quantity }} × ₱{{ number_format($item->price_per_unit, 2) }}</p>
                                </div>
                                <p class="text-lg font-bold text-lgu-headline">₱{{ number_format($item->subtotal, 2) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Uploaded Documents -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Documents</h3>
                <div class="space-y-4">
                    <!-- Valid ID - Front -->
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-lgu-button mr-3">
                                <rect width="20" height="14" x="2" y="7" rx="2"/><path d="M2 12h20"/><path d="M7 15h3"/><path d="M7 19h7"/>
                            </svg>
                            <div>
                                <p class="font-medium text-gray-900">Valid ID - Front</p>
                                <p class="text-sm text-gray-600">Required for verification</p>
                            </div>
                        </div>
                        @if($booking->valid_id_front_path)
                            <a href="{{ asset('storage/' . $booking->valid_id_front_path) }}" target="_blank"
                               class="px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition text-sm font-medium">
                                View
                            </a>
                        @else
                            <span class="px-4 py-2 bg-gray-100 text-gray-500 rounded-lg text-sm font-medium">
                                Not Uploaded
                            </span>
                        @endif
                    </div>

                    <!-- Valid ID - Back -->
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-lgu-button mr-3">
                                <rect width="20" height="14" x="2" y="7" rx="2"/><path d="M2 12h20"/><path d="M7 15h3"/><path d="M7 19h7"/>
                            </svg>
                            <div>
                                <p class="font-medium text-gray-900">Valid ID - Back</p>
                                <p class="text-sm text-gray-600">Required for verification</p>
                            </div>
                        </div>
                        @if($booking->valid_id_back_path)
                            <a href="{{ asset('storage/' . $booking->valid_id_back_path) }}" target="_blank"
                               class="px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition text-sm font-medium">
                                View
                            </a>
                        @else
                            <span class="px-4 py-2 bg-gray-100 text-gray-500 rounded-lg text-sm font-medium">
                                Not Uploaded
                            </span>
                        @endif
                    </div>

                    <!-- Selfie with ID -->
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-lgu-button mr-3">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                            </svg>
                            <div>
                                <p class="font-medium text-gray-900">Selfie with ID</p>
                                <p class="text-sm text-gray-600">Required for verification</p>
                            </div>
                        </div>
                        @if($booking->valid_id_selfie_path)
                            <a href="{{ asset('storage/' . $booking->valid_id_selfie_path) }}" target="_blank"
                               class="px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition text-sm font-medium">
                                View
                            </a>
                        @else
                            <span class="px-4 py-2 bg-gray-100 text-gray-500 rounded-lg text-sm font-medium">
                                Not Uploaded
                            </span>
                        @endif
                    </div>

                    <!-- Special Discount ID -->
                    @if($booking->special_discount_type)
                        @php
                            // Check if Valid ID Type matches the discount type (auto-applied scenario)
                            $isAutoApplied = false;
                            if (($booking->valid_id_type === 'School ID' && $booking->special_discount_type === 'student') ||
                                ($booking->valid_id_type === 'Senior Citizen ID' && $booking->special_discount_type === 'senior') ||
                                ($booking->valid_id_type === 'PWD ID' && $booking->special_discount_type === 'pwd')) {
                                $isAutoApplied = true;
                            }
                        @endphp

                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-lgu-button mr-3">
                                    <path d="M4 10V4a2 2 0 0 1 2-2h8.5L20 7.5V20a2 2 0 0 1-2 2H4"/><path d="M14 2v6h6"/><circle cx="10" cy="16" r="3"/><path d="m7 20 3-2 3 2"/>
                                </svg>
                                <div>
                                    <p class="font-medium text-gray-900">{{ ucfirst($booking->special_discount_type) }} ID</p>
                                    <p class="text-sm text-gray-600">For {{ number_format($booking->special_discount_rate, 0) }}% discount
                                        @if($isAutoApplied)
                                            <span class="text-blue-600">(See Valid ID above)</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @if($isAutoApplied)
                                <span class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg text-sm font-medium">
                                    Same as Valid ID
                                </span>
                            @elseif($booking->special_discount_id_path)
                                <a href="{{ asset('storage/' . $booking->special_discount_id_path) }}" target="_blank"
                                   class="px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition text-sm font-medium">
                                    View
                                </a>
                            @else
                                <span class="px-4 py-2 bg-gray-100 text-gray-500 rounded-lg text-sm font-medium">
                                    Not Uploaded
                                </span>
                            @endif
                        </div>
                    @endif

                    <!-- Supporting Documents -->
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-lgu-button mr-3">
                                <path d="M4 22h14a2 2 0 0 0 2-2V7.5L14.5 2H6a2 2 0 0 0-2 2v4"/><polyline points="14 2 14 8 20 8"/><path d="M3 15h6"/><path d="M6 12v6"/>
                            </svg>
                            <div>
                                <p class="font-medium text-gray-900">Supporting Documents (Optional)</p>
                                <p class="text-sm text-gray-600">Additional documents</p>
                            </div>
                        </div>
                        @if($booking->supporting_doc_path)
                            <a href="{{ asset('storage/' . $booking->supporting_doc_path) }}" target="_blank"
                               class="px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition text-sm font-medium">
                                View
                            </a>
                        @else
                            <span class="px-4 py-2 bg-gray-100 text-gray-500 rounded-lg text-sm font-medium">
                                Not Uploaded
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Rejection/Cancellation Reason -->
            @if(in_array($booking->status, ['rejected', 'cancelled']) && $booking->rejected_reason)
                <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                    <h3 class="text-lg font-bold text-red-800 mb-2">
                        {{ $booking->status === 'rejected' ? 'Rejection Reason' : 'Cancellation Reason' }}
                    </h3>
                    <p class="text-red-700">{{ $booking->rejected_reason }}</p>
                </div>
            @endif
        </div>

        <!-- Sidebar: Pricing Summary & Actions -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow rounded-lg p-6 sticky top-8">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Pricing Summary</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Base Rate (3 hours):</span>
                        <span class="font-semibold">₱{{ number_format($booking->base_rate, 2) }}</span>
                    </div>
                    @if($booking->extension_rate > 0)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Extension:</span>
                            <span class="font-semibold">₱{{ number_format($booking->extension_rate, 2) }}</span>
                        </div>
                    @endif
                    @if($booking->equipment_total > 0)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Equipment:</span>
                            <span class="font-semibold">₱{{ number_format($booking->equipment_total, 2) }}</span>
                        </div>
                    @endif
                    <div class="border-t border-gray-200 pt-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-semibold">₱{{ number_format($booking->subtotal, 2) }}</span>
                        </div>
                    </div>
                    @if($booking->resident_discount_amount > 0)
                        <div class="flex justify-between text-green-600">
                            <span>Resident Discount ({{ number_format($booking->resident_discount_rate, 0) }}%):</span>
                            <span>- ₱{{ number_format($booking->resident_discount_amount, 2) }}</span>
                        </div>
                    @endif
                    @if($booking->special_discount_amount > 0)
                        <div class="flex justify-between text-green-600">
                            <span>{{ ucfirst($booking->special_discount_type) }} Discount ({{ number_format($booking->special_discount_rate, 0) }}%):</span>
                            <span>- ₱{{ number_format($booking->special_discount_amount, 2) }}</span>
                        </div>
                    @endif
                    @if($booking->total_discount > 0)
                        <div class="border-t border-gray-200 pt-3">
                            <div class="flex justify-between text-green-600 font-bold">
                                <span>Total Discount:</span>
                                <span>- ₱{{ number_format($booking->total_discount, 2) }}</span>
                            </div>
                        </div>
                    @endif
                    <div class="border-t-2 border-gray-300 pt-3">
                        <div class="flex justify-between text-lg font-bold text-lgu-headline">
                            <span>Total Amount:</span>
                            <span>₱{{ number_format($booking->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 space-y-3">
                    @if($booking->status === 'payment_pending')
                        <a href="{{ route('citizen.payment-slips') }}" 
                           class="block w-full px-4 py-3 bg-lgu-button text-lgu-button-text text-center font-semibold rounded-lg hover:bg-lgu-highlight transition">
                            View Payment Slip
                        </a>
                    @endif

                    <!-- Leave Review Button (shows when event has passed and no review exists) -->
                    @if($canReview)
                        <a href="{{ route('citizen.reviews.create', $booking->id) }}" 
                           class="block w-full px-4 py-3 bg-lgu-highlight text-lgu-button-text text-center font-bold rounded-lg hover:bg-lgu-hover transition shadow-md flex items-center justify-center gap-2">
                            <i data-lucide="star" class="w-5 h-5"></i>
                            Leave a Review
                        </a>
                    @elseif($existingReview)
                        <a href="{{ route('citizen.reviews.edit', $existingReview->id) }}" 
                           class="block w-full px-4 py-3 bg-blue-50 text-blue-700 text-center font-semibold rounded-lg hover:bg-blue-100 transition flex items-center justify-center gap-2 border-2 border-blue-200">
                            <i data-lucide="check-circle" class="w-5 h-5"></i>
                            Review Submitted
                        </a>
                    @endif

                    @if(in_array($booking->status, ['pending', 'staff_verified', 'payment_pending']))
                        <button type="button" onclick="cancelBooking({{ $booking->id }})"
                                class="block w-full px-4 py-3 bg-red-100 text-red-700 text-center font-semibold rounded-lg hover:bg-red-200 transition">
                            Cancel Booking
                        </button>
                    @endif

                    <a href="{{ route('citizen.reservations') }}" 
                       class="block w-full px-4 py-3 bg-gray-200 text-gray-700 text-center font-semibold rounded-lg hover:bg-gray-300 transition">
                        Back to List
                    </a>
                </div>

                <!-- Booking Timeline -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h4 class="text-sm font-bold text-gray-900 mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 text-lgu-button">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                        Booking Timeline
                    </h4>
                    <div class="space-y-4 relative">
                        <!-- Timeline Line -->
                        <div class="absolute left-1.5 top-2 bottom-2 w-0.5 bg-gray-300"></div>
                        
                        <!-- Created -->
                        <div class="flex items-start relative">
                            <div class="w-3 h-3 bg-lgu-button rounded-full mt-1 mr-4 ring-4 ring-lgu-bg z-10"></div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900 text-sm">Created</p>
                                <p class="text-xs text-gray-600">{{ \Carbon\Carbon::parse($booking->created_at)->format('M d, Y g:i A') }}</p>
                            </div>
                        </div>
                        
                        <!-- Current Status -->
                        <div class="flex items-start relative">
                            @php
                                $statusDotColor = match($booking->status) {
                                    'pending' => 'bg-yellow-500',
                                    'staff_verified' => 'bg-purple-500',
                                    'payment_pending' => 'bg-orange-500',
                                    'confirmed' => 'bg-green-500',
                                    'completed' => 'bg-blue-500',
                                    'cancelled' => 'bg-gray-500',
                                    'rejected' => 'bg-red-500',
                                    default => 'bg-gray-500'
                                };
                            @endphp
                            <div class="w-3 h-3 {{ $statusDotColor }} rounded-full mt-1 mr-4 ring-4 ring-white z-10 animate-pulse"></div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900 text-sm">{{ $statusInfo['label'] }}</p>
                                <p class="text-xs text-gray-600">Current Status</p>
                            </div>
                        </div>
                        
                        <!-- Last Updated -->
                        <div class="flex items-start relative">
                            <div class="w-3 h-3 bg-gray-300 rounded-full mt-1 mr-4 ring-4 ring-white z-10"></div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900 text-sm">Last Updated</p>
                                <p class="text-xs text-gray-600">{{ \Carbon\Carbon::parse($booking->updated_at)->format('M d, Y g:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Upload Document with SweetAlert2
function openUploadModal(documentType) {
    const docLabels = {
        'valid_id': 'Valid Government ID',
        'special_discount_id': 'Special Discount ID',
        'supporting_doc': 'Supporting Document'
    };

    Swal.fire({
        title: 'Upload Document',
        html: `
            <div class="text-left">
                <p class="text-gray-700 mb-4">Document Type: <span class="font-semibold text-lgu-headline">${docLabels[documentType] || 'Document'}</span></p>
                <div class="mb-4">
                    <input type="file" 
                           id="document" 
                           accept="image/*,.pdf" 
                           class="block w-full text-sm text-gray-900 border-2 border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none focus:border-lgu-button">
                    <p class="mt-2 text-xs text-gray-500">Accepted: JPG, PNG, PDF (Max 5MB)</p>
                </div>
            </div>
        `,
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#047857',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Upload',
        cancelButtonText: 'Cancel',
        customClass: {
            popup: 'rounded-xl',
            confirmButton: 'px-6 py-2.5 rounded-lg font-semibold cursor-pointer',
            cancelButton: 'px-6 py-2.5 rounded-lg font-semibold cursor-pointer'
        },
        preConfirm: () => {
            const file = document.getElementById('document').files[0];
            if (!file) {
                Swal.showValidationMessage('Please select a file to upload');
                return false;
            }
            if (file.size > 5 * 1024 * 1024) {
                Swal.showValidationMessage('File size must not exceed 5MB');
                return false;
            }
            return { file, documentType };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Uploading...',
                text: 'Please wait while we upload your document',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // Create FormData and upload
            const formData = new FormData();
            formData.append('document', result.value.file);
            formData.append('document_type', result.value.documentType);

            fetch(`/citizen/reservations/{{ $booking->id }}/upload`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Document uploaded successfully!',
                        confirmButtonColor: '#047857',
                        confirmButtonText: 'OK',
                        customClass: {
                            popup: 'rounded-xl',
                            confirmButton: 'px-6 py-2.5 rounded-lg font-semibold cursor-pointer'
                        }
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Upload Failed',
                        text: data.message || 'An error occurred while uploading',
                        confirmButtonColor: '#dc2626',
                        customClass: {
                            popup: 'rounded-xl',
                            confirmButton: 'px-6 py-2.5 rounded-lg font-semibold cursor-pointer'
                        }
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An unexpected error occurred. Please try again.',
                    confirmButtonColor: '#dc2626',
                    customClass: {
                        popup: 'rounded-xl',
                        confirmButton: 'px-6 py-2.5 rounded-lg font-semibold cursor-pointer'
                    }
                });
                console.error('Upload error:', error);
            });
        }
    });
}

// Cancel Booking with SweetAlert2
function cancelBooking(bookingId) {
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
            form.action = '/citizen/reservations/{{ $booking->id }}/cancel';

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

@if($booking->status === 'staff_verified' && !$booking->isPaymentOverdue())
// Countdown Timer - Update every minute
@php
    $deadline = $booking->getPaymentDeadline();
    $deadlineTimestamp = $deadline ? $deadline->timestamp * 1000 : null;
@endphp

@if($deadlineTimestamp)
function updateCountdown() {
    const deadlineTime = {{ $deadlineTimestamp }};
    const now = Date.now();
    const difference = deadlineTime - now;

    if (difference <= 0) {
        // Deadline passed, reload page to show "overdue" message
        location.reload();
        return;
    }

    // Calculate time components
    const days = Math.floor(difference / (1000 * 60 * 60 * 24));
    const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));

    // Format countdown string
    let countdownText = '';
    if (days > 0) {
        countdownText = `${days}d ${hours}h ${minutes}m`;
    } else if (hours > 0) {
        countdownText = `${hours}h ${minutes}m`;
    } else {
        countdownText = `${minutes}m`;
    }

    // Update the countdown timer element
    const timerElement = document.getElementById('countdown-timer');
    if (timerElement) {
        timerElement.textContent = countdownText;
    }
}

// Update countdown immediately
updateCountdown();

// Update countdown every minute
setInterval(updateCountdown, 60000);
@endif
@endif
</script>
@endpush
@endsection

