@extends('layouts.citizen')

@section('title', $facility->name)
@section('page-title', $facility->name)
@section('page-subtitle', 'Facility Details')

@section('page-content')
<div class="space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('citizen.browse-facilities') }}" 
           class="inline-flex items-center text-lgu-button hover:text-lgu-highlight font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                <path d="m12 19-7-7 7-7"/><path d="M19 12H5"/>
            </svg>
            Back to Browse Facilities
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Facility Image -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                @if($facility->image_path)
                    <img src="{{ asset('storage/' . $facility->image_path) }}" 
                         alt="{{ $facility->name }}" 
                         class="w-full h-96 object-cover">
                @else
                    <div class="w-full h-96 bg-lgu-bg flex items-center justify-center">
                        <svg class="w-32 h-32 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                @endif
            </div>

            <!-- Facility Description -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">About This Facility</h2>
                <p class="text-gray-700 leading-relaxed">{{ $facility->description }}</p>
            </div>

            <!-- Facility Details -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Facility Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-lgu-button mr-3 mt-1 flex-shrink-0">
                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Location</p>
                            <p class="text-base font-semibold text-gray-900">{{ $facility->address }}</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-lgu-button mr-3 mt-1 flex-shrink-0">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Capacity</p>
                            <p class="text-base font-semibold text-gray-900">{{ $facility->capacity }} people</p>
                        </div>
                    </div>

                    @if($facility->per_person_rate)
                        <div class="flex items-start">
                            <div class="w-5 h-5 flex items-center justify-center text-lgu-button font-bold text-lg mr-3 mt-1 flex-shrink-0">
                                ₱
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Per Person Rate</p>
                                <p class="text-base font-semibold text-gray-900">₱{{ number_format($facility->per_person_rate, 2) }}</p>
                            </div>
                        </div>
                    @endif

                    @if($facility->hourly_rate)
                        <div class="flex items-start">
                            <div class="w-5 h-5 flex items-center justify-center text-lgu-button font-bold text-lg mr-3 mt-1 flex-shrink-0">
                                ₱
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Hourly Rate</p>
                                <p class="text-base font-semibold text-gray-900">₱{{ number_format($facility->hourly_rate, 2) }}</p>
                            </div>
                        </div>
                    @endif

                    @if($facility->deposit_amount)
                        <div class="flex items-start">
                            <div class="w-5 h-5 flex items-center justify-center text-lgu-button font-bold text-lg mr-3 mt-1 flex-shrink-0">
                                ₱
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Deposit Required</p>
                                <p class="text-base font-semibold text-gray-900">₱{{ number_format($facility->deposit_amount, 2) }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Upcoming Bookings -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Upcoming Reservations</h2>
                @if($upcomingBookings->isEmpty())
                    <p class="text-gray-600">No upcoming reservations for this facility.</p>
                @else
                    <div class="space-y-3">
                        @foreach($upcomingBookings as $booking)
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg bg-gray-50">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-lgu-button mr-3">
                                        <rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ \Carbon\Carbon::parse($booking->start_time)->format('M d, Y') }}
                                        </p>
                                        <p class="text-xs text-gray-600">
                                            {{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} - 
                                            {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}
                                        </p>
                                    </div>
                                </div>
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">
                                    Reserved
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar: Booking Information -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow rounded-lg p-6 sticky top-8">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Book This Facility</h3>
                
                <!-- Pricing Summary -->
                <div class="space-y-3 mb-6">
                    @if($facility->per_person_rate)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Per Person:</span>
                            <span class="font-bold text-gray-900">₱{{ number_format($facility->per_person_rate, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Payment Type:</span>
                            <span class="text-gray-900">Per attendee</span>
                        </div>
                    @elseif($facility->hourly_rate)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Hourly Rate:</span>
                            <span class="font-bold text-gray-900">₱{{ number_format($facility->hourly_rate, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Min Duration:</span>
                            <span class="text-gray-900">{{ $facility->min_booking_hours }} hours</span>
                        </div>
                    @endif
                    @if($facility->deposit_amount)
                        <div class="border-t border-gray-200 pt-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Deposit:</span>
                                <span class="text-gray-900">₱{{ number_format($facility->deposit_amount, 2) }}</span>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Discount Information -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h4 class="text-sm font-bold text-blue-900 mb-2">Available Discounts</h4>
                    <ul class="text-xs text-blue-800 space-y-1">
                        <li>• Senior Citizen: 20% off (with valid ID)</li>
                        <li>• PWD: 20% off (with valid ID)</li>
                        <li>• Student: 10% off (with valid school ID)</li>
                    </ul>
                </div>

                <!-- Booking Requirements -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <h4 class="text-sm font-bold text-yellow-900 mb-2">Requirements</h4>
                    <ul class="text-xs text-yellow-800 space-y-1">
                        <li>• Book 7 days in advance</li>
                        <li>• Valid government ID required</li>
                        <li>• Staff approval needed</li>
                    </ul>
                </div>

                <!-- Book Now Button -->
                <a href="{{ route('citizen.booking.create', $facility->facility_id) }}" 
                   class="block w-full px-6 py-3 bg-lgu-button text-lgu-button-text text-center font-semibold rounded-lg hover:bg-lgu-highlight transition shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-block mr-2">
                        <rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/><path d="m9 16 2 2 4-4"/>
                    </svg>
                    Book Now
                </a>

                <a href="{{ route('citizen.facility-calendar', ['facility_id' => $facility->facility_id]) }}" 
                   class="block w-full mt-3 px-6 py-3 bg-gray-200 text-gray-700 text-center font-semibold rounded-lg hover:bg-gray-300 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-block mr-2">
                        <rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/>
                    </svg>
                    Check Availability
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

