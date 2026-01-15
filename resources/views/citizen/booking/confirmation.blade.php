@extends('layouts.citizen')

@section('title', 'Booking Confirmation')
@section('page-title', 'Booking Confirmed!')
@section('page-subtitle', 'Your booking has been submitted successfully')

@section('page-content')
<div class="max-w-4xl mx-auto">
    <!-- Success Message -->
    <div class="bg-green-50 border-2 border-green-500 rounded-lg p-8 text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-green-500 text-white rounded-full mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check">
                <path d="M20 6 9 17l-5-5"/>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-green-800 mb-2">Booking Submitted Successfully!</h2>
        <p class="text-green-700">Booking Reference: <span class="font-mono font-bold">#BK{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</span></p>
    </div>

    <!-- What's Next -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
        <h3 class="text-lg font-bold text-blue-900 mb-3">What happens next?</h3>
        <ol class="list-decimal list-inside space-y-2 text-sm text-blue-800">
            <li><strong>Staff Verification</strong> - Our staff will review your booking request and uploaded documents (usually within 1-2 business days)</li>
            <li><strong>Admin Approval</strong> - After staff verification, an admin will approve your booking</li>
            <li><strong>Payment</strong> - Once approved, you'll receive a payment slip with instructions</li>
            <li><strong>Confirmation</strong> - After payment verification, your booking will be confirmed</li>
        </ol>
    </div>

    <!-- Booking Details -->
    <div class="bg-white shadow-lg rounded-lg p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Booking Summary</h2>

        <div class="space-y-6">
            <!-- Facility & Date -->
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">Facility & Date</h3>
                <div class="space-y-2">
                    <div class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building-2 text-lgu-button mt-0.5 mr-3">
                            <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/>
                            <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/>
                            <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/>
                        </svg>
                        <div>
                            <p class="font-medium text-gray-900">{{ $facility->name }}</p>
                            <p class="text-sm text-gray-600">{{ $facility->address }}</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar text-lgu-button mt-0.5 mr-3">
                            <path d="M8 2v4"/>
                            <path d="M16 2v4"/>
                            <rect width="18" height="18" x="3" y="4" rx="2"/>
                            <path d="M3 10h18"/>
                        </svg>
                        <div>
                            <p class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($booking->start_time)->format('l, F j, Y') }}</p>
                            <p class="text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - 
                                {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Equipment -->
            @if($equipment->isNotEmpty())
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">Equipment</h3>
                    <div class="space-y-2">
                        @foreach($equipment as $item)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-700">{{ $item->name }} ({{ $item->quantity }}x)</span>
                                <span class="font-medium">₱{{ number_format($item->subtotal, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Pricing Breakdown -->
            <div class="border-t pt-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-4">Pricing Breakdown</h3>
                
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Base Rate</span>
                        <span>₱{{ number_format($booking->base_rate, 2) }}</span>
                    </div>

                    @if($booking->extension_rate > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Extension</span>
                            <span>₱{{ number_format($booking->extension_rate, 2) }}</span>
                        </div>
                    @endif

                    @if($booking->equipment_total > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Equipment</span>
                            <span>₱{{ number_format($booking->equipment_total, 2) }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between font-medium border-t pt-2">
                        <span>Subtotal</span>
                        <span>₱{{ number_format($booking->subtotal, 2) }}</span>
                    </div>

                    @if($booking->resident_discount_amount > 0)
                        <div class="flex justify-between text-sm text-green-600">
                            <span>Resident Discount ({{ $booking->resident_discount_rate }}%)</span>
                            <span>-₱{{ number_format($booking->resident_discount_amount, 2) }}</span>
                        </div>
                    @endif

                    @if($booking->special_discount_amount > 0)
                        <div class="flex justify-between text-sm text-green-600">
                            <span>{{ ucfirst($booking->special_discount_type) }} Discount ({{ $booking->special_discount_rate }}%)</span>
                            <span>-₱{{ number_format($booking->special_discount_amount, 2) }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between text-xl font-bold text-lgu-button border-t-2 pt-4">
                        <span>Total Amount</span>
                        <span>₱{{ number_format($booking->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock text-yellow-600 mr-3">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                    <div>
                        <p class="font-medium text-yellow-900">Status: <span class="uppercase">{{ $booking->status }}</span></p>
                        <p class="text-sm text-yellow-700">Awaiting staff verification</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-8">
            <a href="{{ route('citizen.reservations') }}" 
               class="px-6 py-3 bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:bg-lgu-highlight transition text-center shadow">
                View My Bookings
            </a>
            <a href="{{ route('citizen.browse-facilities') }}" 
               class="px-6 py-3 border-2 border-lgu-button text-lgu-button font-semibold rounded-lg hover:bg-lgu-bg transition text-center">
                Book Another Facility
            </a>
        </div>
    </div>
</div>
@endsection

