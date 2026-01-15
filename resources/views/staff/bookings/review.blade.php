@extends('layouts.staff')

@section('title', 'Review Booking')
@section('page-title', 'Booking Review')
@section('page-subtitle', 'Verify booking details and documents')

@section('page-content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-gr-lg">
    <!-- Main Content - Left Side -->
    <div class="lg:col-span-2 space-y-gr-lg">
        <!-- Facility & Event Details -->
        <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke p-gr-lg">
            <h2 class="text-h3 font-bold text-lgu-headline mb-gr-md flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building mr-2">
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
                Facility & Event Details
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-gr-md">
                <div>
                    <label class="text-caption font-semibold text-gray-500 uppercase">Facility</label>
                    <p class="text-body font-semibold text-gray-900 mt-1">{{ $booking->facility_name }}</p>
                </div>
                <div>
                    <label class="text-caption font-semibold text-gray-500 uppercase">Location</label>
                    <p class="text-body font-semibold text-gray-900 mt-1">{{ $booking->city_name }}</p>
                </div>
                <div>
                    <label class="text-caption font-semibold text-gray-500 uppercase">Event Date</label>
                    <p class="text-body font-semibold text-gray-900 mt-1">{{ \Carbon\Carbon::parse($booking->start_time)->format('F d, Y') }}</p>
                </div>
                <div>
                    <label class="text-caption font-semibold text-gray-500 uppercase">Time</label>
                    <p class="text-body font-semibold text-gray-900 mt-1">
                        {{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}
                    </p>
                </div>
                <div>
                    <label class="text-caption font-semibold text-gray-500 uppercase">Expected Attendees</label>
                    <p class="text-body font-semibold text-gray-900 mt-1">{{ number_format($booking->expected_attendees) }} people</p>
                </div>
                <div>
                    <label class="text-caption font-semibold text-gray-500 uppercase">Event Purpose</label>
                    <p class="text-body font-semibold text-gray-900 mt-1">{{ $booking->purpose ?? 'N/A' }}</p>
                </div>
                <div class="md:col-span-2">
                    <label class="text-caption font-semibold text-gray-500 uppercase">Event Description</label>
                    <p class="text-body text-gray-700 mt-1">{{ $booking->event_description ?? 'No description provided' }}</p>
                </div>
            </div>
        </div>

        <!-- Applicant Information -->
        <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke p-gr-lg">
            <h2 class="text-h3 font-bold text-lgu-headline mb-gr-md flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user mr-2">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                Applicant Information
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-gr-md">
                <div>
                    <label class="text-caption font-semibold text-gray-500 uppercase">Full Name</label>
                    <p class="text-body font-semibold text-gray-900 mt-1">{{ $user->full_name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="text-caption font-semibold text-gray-500 uppercase">Email</label>
                    <p class="text-body font-semibold text-gray-900 mt-1">{{ $user->email }}</p>
                </div>
                <div>
                    <label class="text-caption font-semibold text-gray-500 uppercase">Mobile Number</label>
                    <p class="text-body font-semibold text-gray-900 mt-1">{{ $user->mobile_number ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="text-caption font-semibold text-gray-500 uppercase">Birthdate</label>
                    <p class="text-body font-semibold text-gray-900 mt-1">{{ $user->birthdate ? \Carbon\Carbon::parse($user->birthdate)->format('F d, Y') : 'N/A' }}</p>
                </div>
                <div>
                    <label class="text-caption font-semibold text-gray-500 uppercase">Gender</label>
                    <p class="text-body font-semibold text-gray-900 mt-1">{{ $user->gender ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="text-caption font-semibold text-gray-500 uppercase">Civil Status</label>
                    <p class="text-body font-semibold text-gray-900 mt-1">{{ $user->civil_status ?? 'N/A' }}</p>
                </div>
                <div class="md:col-span-2">
                    <label class="text-caption font-semibold text-gray-500 uppercase">Current Address</label>
                    <p class="text-body font-semibold text-gray-900 mt-1">
                        @if($user->current_address || $user->barangay || $user->philippineCity)
                            {{ $user->current_address ?? '' }}
                            @if($user->current_address && ($user->barangay || $user->philippineCity)), @endif
                            @if($user->barangay)
                                Barangay {{ $user->barangay->name }}
                                @if($user->philippineCity), @endif
                            @endif
                            @if($user->philippineCity)
                                {{ $user->philippineCity->name }}
                            @endif
                        @else
                            N/A
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Equipment Requested -->
        @if(count($equipment) > 0)
        <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke p-gr-lg">
            <h2 class="text-h3 font-bold text-lgu-headline mb-gr-md flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-box mr-2">
                    <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/>
                    <path d="m3.3 7 8.7 5 8.7-5"/>
                    <path d="M12 22V12"/>
                </svg>
                Equipment Requested
            </h2>
            
            <div class="space-y-3">
                @foreach($equipment as $item)
                <div class="flex items-center justify-between py-3 border-b border-gray-200 last:border-0">
                    <div>
                        <p class="font-semibold text-gray-900">{{ $item->name }}</p>
                        <p class="text-small text-gray-600">Quantity: {{ $item->pivot->quantity }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-gray-900">₱{{ number_format($item->pivot->price_per_unit * $item->pivot->quantity, 2) }}</p>
                        <p class="text-small text-gray-600">₱{{ number_format($item->pivot->price_per_unit, 2) }} × {{ $item->pivot->quantity }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Uploaded Documents -->
        @if(count($documents) > 0)
        <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke p-gr-lg">
            <h2 class="text-h3 font-bold text-lgu-headline mb-gr-md flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text mr-2">
                    <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/>
                    <path d="M14 2v4a2 2 0 0 0 2 2h4"/>
                    <path d="M10 9H8"/>
                    <path d="M16 13H8"/>
                    <path d="M16 17H8"/>
                </svg>
                Uploaded Documents
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-gr-sm">
                @foreach($documents as $doc)
                <button type="button" onclick="openDocumentModal('{{ asset('storage/' . $doc->path) }}', '{{ $doc->type }}')"
                   class="flex items-center p-3 rounded-lg border border-gray-300 hover:border-lgu-button hover:bg-gray-50 transition-colors cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-image text-gray-400 mr-3 flex-shrink-0">
                        <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/>
                        <path d="M14 2v4a2 2 0 0 0 2 2h4"/>
                        <circle cx="10" cy="13" r="2"/>
                        <path d="m20 17-1.296-1.296a2.41 2.41 0 0 0-3.408 0L9 22"/>
                    </svg>
                    <div class="flex-1 min-w-0 text-left">
                        <p class="text-small font-medium text-gray-900 truncate">{{ $doc->type }}</p>
                        <p class="text-xs text-gray-500">Click to view</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-maximize-2 text-gray-400 flex-shrink-0">
                        <polyline points="15 3 21 3 21 9"/>
                        <polyline points="9 21 3 21 3 15"/>
                        <line x1="21" x2="14" y1="3" y2="10"/>
                        <line x1="3" x2="10" y1="21" y2="14"/>
                    </svg>
                </button>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Right Sidebar - Actions -->
    <div class="lg:col-span-1">
        <div class="sticky top-6 space-y-gr-md">
            <!-- Pricing Summary -->
            <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke p-gr-md">
                <h3 class="text-h4 font-bold text-gray-900 mb-gr-md">Pricing Summary</h3>
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-small">
                        <span class="text-gray-600">Facility Fee</span>
                        <span class="font-semibold text-gray-900">₱{{ number_format($booking->base_rate ?? 0, 2) }}</span>
                    </div>
                    @if(count($equipment) > 0)
                    <div class="flex justify-between text-small">
                        <span class="text-gray-600">Equipment</span>
                        <span class="font-semibold text-gray-900">₱{{ number_format($equipment->sum(function($item) { return $item->pivot->price_per_unit * $item->pivot->quantity; }), 2) }}</span>
                    </div>
                    @endif
                    
                    @if(($booking->total_discount ?? 0) > 0)
                    <div class="mt-2 pt-2 border-t border-gray-100">
                        @if(($booking->resident_discount_amount ?? 0) > 0)
                        <div class="flex justify-between text-small">
                            <span class="text-green-600">
                                <i data-lucide="badge-percent" class="w-3 h-3 inline mr-1"></i>
                                Resident Discount ({{ $booking->resident_discount_rate }}%)
                            </span>
                            <span class="font-semibold text-green-600">-₱{{ number_format($booking->resident_discount_amount, 2) }}</span>
                        </div>
                        @endif
                        @if(($booking->special_discount_amount ?? 0) > 0)
                        <div class="flex justify-between text-small mt-1">
                            <span class="text-green-600">
                                <i data-lucide="sparkles" class="w-3 h-3 inline mr-1"></i>
                                {{ ucfirst(str_replace('_', ' ', $booking->special_discount_type ?? 'Special')) }} ({{ $booking->special_discount_rate }}%)
                            </span>
                            <span class="font-semibold text-green-600">-₱{{ number_format($booking->special_discount_amount, 2) }}</span>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
                <div class="pt-4 border-t border-gray-200">
                    <div class="flex justify-between">
                        <span class="text-body font-bold text-gray-900">Total Amount</span>
                        <span class="text-h4 font-bold text-lgu-button">₱{{ number_format($booking->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Schedule Conflict Warning -->
            @if($booking->status === 'pending' && $conflictCheck['hasConflict'])
            <div class="bg-red-50 border-2 border-red-300 rounded-xl p-gr-md">
                <div class="flex items-start mb-gr-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-alert-triangle text-red-600 mr-2 flex-shrink-0">
                        <path d="m21.73 18-8-14a2 2 0 0 0-3.46 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
                        <path d="M12 9v4"/>
                        <path d="M12 17h.01"/>
                    </svg>
                    <div>
                        <h3 class="text-h4 font-bold text-red-800 mb-1">Schedule Conflict Detected</h3>
                        <p class="text-small text-red-700">{{ $conflictCheck['conflictCount'] }} booking(s) already approved for overlapping times</p>
                    </div>
                </div>

                <div class="space-y-2 mb-gr-sm">
                    @foreach($conflictCheck['conflicts'] as $conflict)
                    <div class="bg-white border border-red-200 rounded-lg p-3">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900">{{ $conflict->facility->name }}</p>
                                <div class="flex items-center gap-4 mt-1 text-xs text-gray-600">
                                    <span class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar mr-1">
                                            <path d="M8 2v4"/>
                                            <path d="M16 2v4"/>
                                            <rect width="18" height="18" x="3" y="4" rx="2"/>
                                            <path d="M3 10h18"/>
                                        </svg>
                                        {{ \Carbon\Carbon::parse($conflict->event_date ?? $conflict->start_time)->format('M d, Y') }}
                                    </span>
                                    <span class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock mr-1">
                                            <circle cx="12" cy="12" r="10"/>
                                            <polyline points="12 6 12 12 16 14"/>
                                        </svg>
                                        {{ \Carbon\Carbon::parse($conflict->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($conflict->end_time)->format('g:i A') }}
                                    </span>
                                    <span class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-hash mr-1">
                                            <line x1="4" x2="20" y1="9" y2="9"/>
                                            <line x1="4" x2="20" y1="15" y2="15"/>
                                            <line x1="10" x2="8" y1="3" y2="21"/>
                                            <line x1="16" x2="14" y1="3" y2="21"/>
                                        </svg>
                                        BK-{{ str_pad($conflict->id, 6, '0', STR_PAD_LEFT) }}
                                    </span>
                                </div>
                                <div class="mt-2">
                                    @if($conflict->status === 'staff_verified')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Approved (Awaiting Payment)
                                        </span>
                                    @elseif($conflict->status === 'paid' || $conflict->status === 'confirmed')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            Paid & Confirmed
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="bg-red-100 border border-red-200 rounded-lg p-3">
                    <p class="text-xs text-red-800 font-medium">
                        <strong>Recommendation:</strong> Reject this booking due to schedule conflict, or verify with the conflicting booking holder first.
                    </p>
                </div>
            </div>
            @endif

            <!-- Verification Actions -->
            @if($booking->status === 'pending')
            <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke p-gr-md">
                <h3 class="text-h4 font-bold text-gray-900 mb-gr-md">Verification Actions</h3>
                
                <!-- Verify Form -->
                <form action="{{ route('staff.bookings.verify', $booking->id) }}" method="POST" id="verifyForm" class="mb-gr-sm">
                    @csrf
                    <div class="mb-gr-sm">
                        <label for="staff_notes" class="block text-small font-medium text-gray-700 mb-2">Staff Notes (Optional)</label>
                        <textarea name="staff_notes" id="staff_notes" rows="3" 
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-lgu-button focus:border-lgu-button text-small"
                                  placeholder="Add any verification notes..."></textarea>
                    </div>
                    <button type="button" onclick="confirmVerify()" 
                            class="w-full px-4 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors shadow-sm flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check-circle mr-2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <path d="m9 11 3 3L22 4"/>
                        </svg>
                        Verify & Approve
                    </button>
                </form>

                <!-- Reject Form -->
                <form action="{{ route('staff.bookings.reject', $booking->id) }}" method="POST" id="rejectForm">
                    @csrf
                    <input type="hidden" name="rejection_reason" id="rejection_reason">
                    <button type="button" onclick="confirmReject()" 
                            class="w-full px-4 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors shadow-sm flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x-circle mr-2">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="m15 9-6 6"/>
                            <path d="m9 9 6 6"/>
                        </svg>
                        Reject Booking
                    </button>
                </form>

                <a href="{{ route('staff.verification-queue') }}" 
                   class="block mt-gr-sm text-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-small font-medium">
                    Cancel
                </a>
            </div>
            @else
            <!-- Booking Status (Read-only) -->
            <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke p-gr-md">
                <h3 class="text-h4 font-bold text-gray-900 mb-gr-sm">Booking Status</h3>
                
                @if($booking->status === 'staff_verified')
                    <div class="flex items-center p-gr-sm bg-green-50 border border-green-200 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check-circle text-green-600 mr-2 flex-shrink-0">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <path d="m9 11 3 3L22 4"/>
                        </svg>
                        <div>
                            <p class="font-semibold text-green-800">Approved</p>
                            <p class="text-xs text-green-700">This booking has been verified and approved</p>
                        </div>
                    </div>
                @elseif($booking->status === 'rejected')
                    <div class="flex items-center p-gr-sm bg-red-50 border border-red-200 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x-circle text-red-600 mr-2 flex-shrink-0">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="m15 9-6 6"/>
                            <path d="m9 9 6 6"/>
                        </svg>
                        <div>
                            <p class="font-semibold text-red-800">Rejected</p>
                            <p class="text-xs text-red-700">This booking has been rejected</p>
                            @if($booking->rejection_reason)
                                <p class="text-xs text-red-600 mt-1"><strong>Reason:</strong> {{ $booking->rejection_reason }}</p>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="flex items-center p-gr-sm bg-gray-50 border border-gray-200 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-info text-gray-600 mr-2 flex-shrink-0">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 16v-4"/>
                            <path d="M12 8h.01"/>
                        </svg>
                        <div>
                            <p class="font-semibold text-gray-800 capitalize">{{ str_replace('_', ' ', $booking->status) }}</p>
                            <p class="text-xs text-gray-600">Current booking status</p>
                        </div>
                    </div>
                @endif

                <a href="{{ route('staff.bookings.index') }}" 
                   class="block mt-gr-sm text-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-small font-medium">
                    Back to Booking History
                </a>
            </div>
            @endif

            <!-- Booking Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-gr-sm">
                <div class="flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-info text-blue-600 mr-2 mt-0.5 flex-shrink-0">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 16v-4"/>
                        <path d="M12 8h.01"/>
                    </svg>
                    <div>
                        <p class="text-xs font-semibold text-blue-900 mb-1">Booking Reference</p>
                        <p class="text-xs text-blue-800">{{ $booking->booking_reference ?? 'BK-' . str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</p>
                        <p class="text-xs text-blue-700 mt-2">Submitted {{ \Carbon\Carbon::parse($booking->created_at)->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Document Viewer Modal -->
<div id="documentModal" class="hidden fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center p-8" onclick="closeDocumentModal()">
    <div class="relative w-full max-w-2xl bg-white rounded-xl shadow-2xl flex flex-col overflow-hidden" style="max-height: 80vh;" onclick="event.stopPropagation()">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-5 py-3 border-b border-gray-200 flex-shrink-0">
            <h3 id="modalDocumentTitle" class="text-base font-bold text-gray-900">Document</h3>
            <button onclick="closeDocumentModal()" class="p-1.5 hover:bg-gray-100 rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x text-gray-600">
                    <path d="M18 6 6 18"/>
                    <path d="m6 6 12 12"/>
                </svg>
            </button>
        </div>
        
        <!-- Modal Body -->
        <div class="flex-1 p-4 bg-gray-50 flex items-center justify-center overflow-hidden">
            <img id="modalDocumentImage" src="" alt="Document" class="max-w-full max-h-full object-contain rounded-lg shadow-lg" style="max-height: calc(80vh - 80px);">
        </div>
    </div>
</div>

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
        confirmButtonText: 'OK'
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

// Document Modal Functions
function openDocumentModal(imagePath, documentType) {
    document.getElementById('modalDocumentImage').src = imagePath;
    document.getElementById('modalDocumentTitle').textContent = documentType;
    document.getElementById('documentModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
}

function closeDocumentModal() {
    document.getElementById('documentModal').classList.add('hidden');
    document.body.style.overflow = ''; // Restore scrolling
}

// Close modal on Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeDocumentModal();
    }
});

function confirmVerify() {
    // Check if there are schedule conflicts (passed from controller)
    const hasConflict = {{ $conflictCheck['hasConflict'] ? 'true' : 'false' }};
    const conflictCount = {{ $conflictCheck['conflictCount'] }};

    if (hasConflict) {
        // Show stronger warning if conflicts exist
        Swal.fire({
            title: 'Schedule Conflict Detected!',
            html: `
                <div class="text-left">
                    <p class="mb-3 text-red-600 font-semibold">
                        ${conflictCount} booking(s) already approved for overlapping times at this facility.
                    </p>
                    <p class="mb-3 text-gray-700">
                        <strong>Warning:</strong> Approving this booking may cause double-booking issues.
                    </p>
                    <p class="text-sm text-gray-600">
                        <strong>Recommended action:</strong> Reject this booking and inform the applicant that the time slot is already reserved.
                    </p>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            showDenyButton: true,
            confirmButtonColor: '#dc2626',
            denyButtonColor: '#16a34a',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Approve Anyway',
            denyButtonText: 'Reject Booking',
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'order-3',
                denyButton: 'order-1',
                cancelButton: 'order-2'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // User chose to approve anyway despite conflict
                document.getElementById('verifyForm').submit();
            } else if (result.isDenied) {
                // User chose to reject - trigger reject flow
                confirmReject();
            }
        });
    } else {
        // No conflicts - show normal confirmation
        Swal.fire({
            title: 'Verify This Booking?',
            text: 'This will forward the booking to admin for final approval.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Verify',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('verifyForm').submit();
            }
        });
    }
}

function confirmReject() {
    Swal.fire({
        title: 'Reject This Booking?',
        text: 'Please provide a reason for rejection:',
        input: 'textarea',
        inputPlaceholder: 'Enter rejection reason...',
        inputAttributes: {
            'aria-label': 'Enter rejection reason'
        },
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Reject Booking',
        cancelButtonText: 'Cancel',
        inputValidator: (value) => {
            if (!value) {
                return 'Please provide a rejection reason!';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('rejection_reason').value = result.value;
            document.getElementById('rejectForm').submit();
        }
    });
}
</script>
@endpush
@endsection

