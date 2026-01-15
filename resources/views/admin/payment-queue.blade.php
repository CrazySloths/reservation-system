@extends('layouts.admin')

@section('page-title', 'Payment Queue')
@section('page-subtitle', 'Verify and manage payment submissions')

@section('page-content')
<div class="space-y-gr-xl">
    <!-- Page Header -->
    <div class="bg-yellow-500 rounded-2xl p-gr-xl shadow-lg">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-gr-md">
                <div class="w-16 h-16 bg-yellow-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="clock" class="w-8 h-8 text-white"></i>
                </div>
                <div>
                    <h1 class="text-h2 font-bold mb-gr-xs text-white">Payment Queue</h1>
                    <p class="text-body text-yellow-900">Bookings awaiting payment verification</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-caption text-yellow-900 mb-1">Pending Payments</p>
                <p class="text-h1 font-bold text-white">{{ $bookings->total() }}</p>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke p-gr-lg">
        <form method="GET" action="{{ route('admin.payment-queue') }}" class="flex flex-col md:flex-row gap-gr-md">
            <!-- Search -->
            <div class="flex-1">
                <label class="block text-small font-medium text-lgu-paragraph mb-gr-xs">Search</label>
                <div class="relative">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}"
                        placeholder="Search by booking ID or citizen name..." 
                        class="w-full px-gr-md py-gr-sm pl-10 border border-lgu-stroke rounded-lg focus:outline-none focus:ring-2 focus:ring-lgu-button text-small"
                    >
                    <i data-lucide="search" class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2"></i>
                </div>
            </div>

            <!-- Facility Filter -->
            <div class="w-full md:w-64">
                <label class="block text-small font-medium text-lgu-paragraph mb-gr-xs">Filter by Facility</label>
                <select 
                    name="facility_id" 
                    class="w-full px-gr-md py-gr-sm border border-lgu-stroke rounded-lg focus:outline-none focus:ring-2 focus:ring-lgu-button text-small"
                >
                    <option value="all">All Facilities</option>
                    @foreach($facilities as $facility)
                        <option value="{{ $facility->facility_id }}" {{ request('facility_id') == $facility->facility_id ? 'selected' : '' }}>
                            {{ $facility->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Submit Button -->
            <div class="flex items-end">
                <button type="submit" class="px-gr-lg py-gr-sm bg-lgu-button hover:bg-lgu-highlight text-lgu-button-text font-semibold rounded-lg transition-colors">
                    <i data-lucide="filter" class="w-5 h-5 inline mr-2"></i>
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    @if($bookings->isEmpty())
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke p-gr-xl text-center">
            <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-gr-md">
                <i data-lucide="check-circle" class="w-12 h-12 text-green-600"></i>
            </div>
            <h3 class="text-h3 font-bold text-lgu-headline mb-gr-sm">All Caught Up!</h3>
            <p class="text-body text-lgu-paragraph">No bookings are currently awaiting payment verification.</p>
        </div>
    @else
        <!-- Payment Queue List -->
        <div class="space-y-gr-md">
            @foreach($bookings as $booking)
                <div class="bg-white rounded-xl shadow-sm border-2 {{ $booking->is_overdue ? 'border-red-500' : 'border-yellow-200' }} p-gr-lg hover:shadow-md transition-shadow">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-gr-md">
                        <!-- Booking Info -->
                        <div class="flex-1">
                            <div class="flex items-start gap-gr-sm mb-gr-sm">
                                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="calendar" class="w-6 h-6 text-yellow-600"></i>
                                </div>
                                <div>
                                    <h3 class="text-h4 font-bold text-lgu-headline">{{ $booking->facility->name }}</h3>
                                    <p class="text-small text-lgu-paragraph">{{ $booking->facility->lguCity->city_name }}</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-gr-sm text-small">
                                <div>
                                    <p class="text-caption text-gray-500">Booking ID</p>
                                    <p class="font-semibold text-lgu-headline">#{{ $booking->id }}</p>
                                </div>
                                <div>
                                    <p class="text-caption text-gray-500">Citizen</p>
                                    <p class="font-semibold text-lgu-headline">{{ $booking->user_name ?? $booking->applicant_name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-caption text-gray-500">Date</p>
                                    <p class="font-semibold text-lgu-headline">{{ \Carbon\Carbon::parse($booking->start_time)->format('M d, Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-caption text-gray-500">Amount</p>
                                    <p class="font-semibold text-green-600">₱{{ number_format($booking->total_amount ?? 0, 2) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Timer & Actions -->
                        <div class="flex flex-col items-end gap-gr-sm">
                            <!-- Countdown Timer -->
                            <div class="text-right">
                                @if($booking->is_overdue)
                                    <div class="bg-red-100 border border-red-200 rounded-lg px-gr-md py-gr-sm">
                                        <p class="text-caption text-red-700 font-medium mb-1">⚠️ OVERDUE</p>
                                        <p class="text-small font-bold text-red-900">Payment Deadline Passed</p>
                                    </div>
                                @else
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg px-gr-md py-gr-sm">
                                        <p class="text-caption text-yellow-700 font-medium mb-1">Time Remaining</p>
                                        <p class="text-h3 font-bold text-yellow-900">{{ $booking->hours_remaining }}h</p>
                                        <p class="text-caption text-yellow-600">Deadline: {{ $booking->deadline->format('M d, g:i A') }}</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Action Button -->
                            <a href="{{ route('admin.bookings.review', $booking->id) }}" 
                               class="px-gr-lg py-gr-sm bg-lgu-button hover:bg-lgu-highlight text-lgu-button-text font-semibold rounded-lg transition-colors flex items-center gap-2">
                                <i data-lucide="eye" class="w-5 h-5"></i>
                                Review Booking
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($bookings->hasPages())
            <div class="flex justify-center mt-gr-lg">
                {{ $bookings->links() }}
            </div>
        @endif
    @endif
</div>
@endsection

