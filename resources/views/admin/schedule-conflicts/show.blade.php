@extends('layouts.admin')

@section('page-title', 'Conflict Details')
@section('page-subtitle', 'Review and resolve booking conflict')

@section('page-content')
<div class="space-y-gr-lg">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <a href="{{ route('admin.schedule-conflicts.index') }}" class="inline-flex items-center text-lgu-button hover:underline mb-gr-sm">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Conflicts
            </a>
            <h1 class="text-h1 font-bold text-lgu-headline">Conflict Details</h1>
        </div>
    </div>

    {{-- Main Booking Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-lgu-tertiary p-gr-lg">
        <div class="flex items-center gap-gr-sm mb-gr-md">
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-small font-semibold bg-red-100 text-red-800">
                <i data-lucide="alert-triangle" class="w-4 h-4 mr-2"></i>
                Primary Booking (#{{ $booking->id }})
            </span>
        </div>

        <h2 class="text-h2 font-bold text-lgu-headline mb-gr-md">{{ $booking->facility->name }}</h2>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-gr-md mb-gr-lg">
            <div>
                <div class="text-caption text-gray-600 uppercase mb-1">Event Date</div>
                <div class="text-body font-semibold">{{ \Carbon\Carbon::parse($booking->event_date)->format('F j, Y') }}</div>
            </div>
            <div>
                <div class="text-caption text-gray-600 uppercase mb-1">Time</div>
                <div class="text-body font-semibold">{{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}</div>
            </div>
            <div>
                <div class="text-caption text-gray-600 uppercase mb-1">Booked By</div>
                <div class="text-body font-semibold">{{ $booking->user_name ?? 'N/A' }}</div>
            </div>
            <div>
                <div class="text-caption text-gray-600 uppercase mb-1">Status</div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold 
                    @if($booking->status == 'confirmed') bg-green-100 text-green-800
                    @elseif($booking->status == 'paid') bg-blue-100 text-blue-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                </span>
            </div>
        </div>

        <div class="bg-gray-50 rounded-lg p-gr-md">
            <h3 class="text-small font-semibold text-lgu-headline mb-gr-sm">Event Purpose</h3>
            <p class="text-body text-gray-700">{{ $booking->purpose ?? 'Not specified' }}</p>
        </div>
    </div>

    {{-- Conflicting Bookings --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-gr-lg">
        <h2 class="text-h2 font-bold text-lgu-headline mb-gr-md">
            Conflicting Bookings ({{ $conflictingBookings->count() }})
        </h2>

        <div class="space-y-gr-md">
            @foreach($conflictingBookings as $conflicting)
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-gr-md">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-gr-sm mb-gr-sm">
                                <span class="text-body font-bold text-gray-900">Booking #{{ $conflicting->id }}</span>
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold 
                                    @if($conflicting->status == 'confirmed') bg-green-100 text-green-800
                                    @elseif($conflicting->status == 'paid') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $conflicting->status)) }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-gr-md text-small mb-gr-sm">
                                <div class="flex items-center text-gray-700">
                                    <i data-lucide="clock" class="w-4 h-4 mr-2"></i>
                                    <span>{{ \Carbon\Carbon::parse($conflicting->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($conflicting->end_time)->format('g:i A') }}</span>
                                </div>
                                <div class="flex items-center text-gray-700">
                                    <i data-lucide="user" class="w-4 h-4 mr-2"></i>
                                    <span>{{ $conflicting->user_name ?? 'N/A' }}</span>
                                </div>
                            </div>

                            @if($conflicting->purpose)
                                <div class="text-small text-gray-600">
                                    <strong>Purpose:</strong> {{ $conflicting->purpose }}
                                </div>
                            @endif
                        </div>

                        <a href="{{ route('admin.bookings.review', $conflicting->id) }}" class="inline-flex items-center px-gr-md py-gr-sm bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:bg-opacity-90 transition-colors duration-200">
                            <i data-lucide="eye" class="w-5 h-5 mr-gr-xs"></i>
                            Review Booking
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Resolution Actions --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-gr-lg">
        <h2 class="text-h2 font-bold text-lgu-headline mb-gr-md">Resolution Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-gr-md">
            <a href="{{ route('admin.bookings.review', $booking->id) }}" class="flex items-center justify-center px-gr-lg py-gr-md bg-blue-100 text-blue-800 font-semibold rounded-lg hover:bg-blue-200 transition-colors duration-200">
                <i data-lucide="file-text" class="w-5 h-5 mr-2"></i>
                Review Primary Booking
            </a>
            <a href="{{ route('admin.maintenance.create') }}?facility_id={{ $booking->facility_id }}" class="flex items-center justify-center px-gr-lg py-gr-md bg-amber-100 text-amber-800 font-semibold rounded-lg hover:bg-amber-200 transition-colors duration-200">
                <i data-lucide="wrench" class="w-5 h-5 mr-2"></i>
                Schedule Maintenance
            </a>
            <a href="{{ route('admin.calendar') }}?facility={{ $booking->facility_id }}" class="flex items-center justify-center px-gr-lg py-gr-md bg-green-100 text-green-800 font-semibold rounded-lg hover:bg-green-200 transition-colors duration-200">
                <i data-lucide="calendar" class="w-5 h-5 mr-2"></i>
                View Calendar
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}
</script>
@endpush
@endsection

