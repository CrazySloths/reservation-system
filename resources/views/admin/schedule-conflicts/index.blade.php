@extends('layouts.admin')

@section('page-title', 'Schedule Conflicts Monitor')
@section('page-subtitle', 'Detect and resolve booking conflicts')

@section('page-content')
<div class="space-y-gr-lg">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-h1 font-bold text-lgu-headline mb-gr-xs">Schedule Conflicts Monitor</h1>
            <p class="text-body text-lgu-paragraph">Monitor and resolve booking conflicts across all facilities</p>
        </div>
        <div class="flex items-center gap-gr-sm">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 px-gr-md py-gr-sm">
                <div class="text-caption text-gray-600 uppercase mb-1">Total Conflicts</div>
                <div class="text-h2 font-bold text-lgu-tertiary">{{ $totalConflicts }}</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 px-gr-md py-gr-sm">
                <div class="text-caption text-gray-600 uppercase mb-1">Pending</div>
                <div class="text-h2 font-bold text-amber-600">{{ $pendingConflicts }}</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 px-gr-md py-gr-sm">
                <div class="text-caption text-gray-600 uppercase mb-1">Resolved</div>
                <div class="text-h2 font-bold text-green-600">{{ $resolvedConflicts }}</div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-gr-lg">
        <form method="GET" action="{{ route('admin.schedule-conflicts.index') }}" class="space-y-gr-md">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-gr-md">
                {{-- Facility Filter --}}
                <div>
                    <label for="facility_id" class="block text-small font-semibold text-lgu-headline mb-gr-xs">Facility</label>
                    <select id="facility_id" name="facility_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lgu-highlight focus:border-transparent">
                        <option value="">All Facilities</option>
                        @foreach($facilities as $facility)
                            <option value="{{ $facility->facility_id }}" {{ $facilityId == $facility->facility_id ? 'selected' : '' }}>
                                {{ $facility->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Date Filter --}}
                <div>
                    <label for="date_filter" class="block text-small font-semibold text-lgu-headline mb-gr-xs">Time Period</label>
                    <select id="date_filter" name="date_filter" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lgu-highlight focus:border-transparent">
                        <option value="all" {{ ($dateFilter ?? 'all') == 'all' ? 'selected' : '' }}>All Time</option>
                        <option value="future" {{ $dateFilter == 'future' ? 'selected' : '' }}>Future Conflicts</option>
                        <option value="past" {{ $dateFilter == 'past' ? 'selected' : '' }}>Past Conflicts</option>
                    </select>
                </div>

                {{-- Status Filter --}}
                <div>
                    <label for="status" class="block text-small font-semibold text-lgu-headline mb-gr-xs">Status</label>
                    <select id="status" name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lgu-highlight focus:border-transparent">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ ($statusFilter ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="resolved" {{ ($statusFilter ?? '') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                    </select>
                </div>

                {{-- Actions --}}
                <div class="flex items-end gap-gr-sm">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center px-gr-md py-gr-sm bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:bg-opacity-90 transition-colors duration-200">
                        <i data-lucide="filter" class="w-5 h-5 mr-gr-xs"></i>
                        Apply Filters
                    </button>
                    <a href="{{ route('admin.schedule-conflicts.index') }}" class="inline-flex items-center px-gr-md py-gr-sm bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors duration-200">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Conflicts List --}}
    @if($conflicts->count() > 0)
        <div class="space-y-gr-md">
            @foreach($conflicts as $conflict)
                <div class="bg-white rounded-xl shadow-sm border border-lgu-tertiary p-gr-lg">
                    {{-- Conflict Status Badge --}}
                    <div class="flex items-start justify-between mb-gr-md pb-gr-md border-b border-gray-200">
                        <div class="flex-1">
                            <div class="flex items-center gap-gr-sm mb-gr-sm">
                                @if($conflict->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                        <i data-lucide="alert-triangle" class="w-3 h-3 mr-1"></i>
                                        Pending Response
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                        Resolved
                                    </span>
                                @endif
                                <span class="text-small text-gray-600">Conflict #{{ $conflict->id }}</span>
                            </div>
                            
                            {{-- City Event Info --}}
                            <h3 class="text-h3 font-bold text-lgu-button mb-gr-xs">
                                <i data-lucide="flag" class="w-5 h-5 inline mr-2"></i>
                                {{ $conflict->cityEvent->event_title }}
                            </h3>
                            <div class="grid grid-cols-2 gap-gr-md text-small mb-gr-md">
                                <div class="flex items-center text-gray-600">
                                    <i data-lucide="calendar" class="w-4 h-4 mr-2"></i>
                                    <span>{{ $conflict->cityEvent->start_time->format('F j, Y') }}</span>
                                </div>
                                <div class="flex items-center text-gray-600">
                                    <i data-lucide="clock" class="w-4 h-4 mr-2"></i>
                                    <span>{{ $conflict->cityEvent->start_time->format('g:i A') }} - {{ $conflict->cityEvent->end_time->format('g:i A') }}</span>
                                </div>
                                <div class="flex items-center text-gray-600">
                                    <i data-lucide="map-pin" class="w-4 h-4 mr-2"></i>
                                    <span>{{ $conflict->cityEvent->facility->name }}</span>
                                </div>
                                <div class="flex items-center text-gray-600">
                                    <i data-lucide="tag" class="w-4 h-4 mr-2"></i>
                                    <span>{{ ucfirst($conflict->cityEvent->event_type) }}</span>
                                </div>
                            </div>

                            {{-- Affected Booking Info --}}
                            <div class="bg-red-50 rounded-lg p-gr-sm border border-red-200">
                                <h4 class="text-small font-semibold text-red-800 mb-2">Affected Citizen Booking:</h4>
                                <div class="grid grid-cols-2 gap-gr-md text-small">
                                    <div class="flex items-center text-gray-700">
                                        <i data-lucide="bookmark" class="w-4 h-4 mr-2"></i>
                                        <span>Booking #{{ $conflict->booking->id }}</span>
                                    </div>
                                    <div class="flex items-center text-gray-700">
                                        <i data-lucide="user" class="w-4 h-4 mr-2"></i>
                                        <span>{{ $conflict->booking->user_name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-center text-gray-700">
                                        <i data-lucide="clock" class="w-4 h-4 mr-2"></i>
                                        <span>{{ \Carbon\Carbon::parse($conflict->booking->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($conflict->booking->end_time)->format('g:i A') }}</span>
                                    </div>
                                    <div class="flex items-center text-gray-700">
                                        <i data-lucide="credit-card" class="w-4 h-4 mr-2"></i>
                                        <span>₱{{ number_format($conflict->booking->total_amount, 2) }}</span>
                                    </div>
                                </div>
                                
                                @if($conflict->status === 'resolved')
                                    <div class="mt-gr-sm pt-gr-sm border-t border-red-200">
                                        <span class="text-xs font-semibold text-green-700">
                                            <i data-lucide="check" class="w-3 h-3 inline mr-1"></i>
                                            Resolution: {{ ucfirst($conflict->citizen_choice) }}
                                            @if($conflict->citizen_choice === 'refund' && $conflict->refund_method)
                                                ({{ ucfirst($conflict->refund_method) }})
                                            @endif
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('admin.schedule-conflicts.show', $conflict->id) }}" class="inline-flex items-center px-gr-md py-gr-sm bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:bg-opacity-90 transition-colors duration-200">
                            <i data-lucide="eye" class="w-5 h-5 mr-gr-xs"></i>
                            View Details
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($conflicts->hasPages())
            <div class="mt-gr-lg">
                {{ $conflicts->links() }}
            </div>
        @endif
    @else
        {{-- No Conflicts --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <div class="flex flex-col items-center justify-center">
                <i data-lucide="check-circle" class="w-16 h-16 text-green-500 mb-gr-md"></i>
                <h3 class="text-h3 font-bold text-lgu-headline mb-gr-xs">No Schedule Conflicts Found</h3>
                <p class="text-body text-gray-600">All bookings are scheduled without conflicts.</p>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
// Initialize Lucide icons
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}
</script>
@endpush
@endsection

