@extends('layouts.admin')

@section('page-title', 'Maintenance Schedule')
@section('page-subtitle', 'Manage facility maintenance and downtime')

@section('page-content')
<div class="space-y-gr-lg">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-h1 font-bold text-lgu-headline mb-gr-xs">Maintenance Schedule</h1>
            <p class="text-body text-lgu-paragraph">Schedule and track facility maintenance periods</p>
        </div>
        <a href="{{ route('admin.maintenance.create') }}" class="inline-flex items-center px-gr-lg py-gr-md bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:bg-opacity-90 transition-colors duration-200">
            <i data-lucide="plus" class="w-5 h-5 mr-gr-xs"></i>
            Schedule Maintenance
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-gr-md">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-gr-lg">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-caption text-gray-600 uppercase mb-1">Upcoming Maintenance</div>
                    <div class="text-h1 font-bold text-lgu-headline">{{ $upcomingCount }}</div>
                </div>
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                    <i data-lucide="calendar-clock" class="w-8 h-8 text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-gr-lg">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-caption text-gray-600 uppercase mb-1">Ongoing Maintenance</div>
                    <div class="text-h1 font-bold text-amber-600">{{ $ongoingCount }}</div>
                </div>
                <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center">
                    <i data-lucide="wrench" class="w-8 h-8 text-amber-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-gr-lg">
        <form method="GET" action="{{ route('admin.maintenance.index') }}" class="space-y-gr-md">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-gr-md">
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

                <div>
                    <label for="type" class="block text-small font-semibold text-lgu-headline mb-gr-xs">Type</label>
                    <select id="type" name="type" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lgu-highlight focus:border-transparent">
                        <option value="">All Types</option>
                        @foreach($maintenanceTypes as $key => $label)
                            <option value="{{ $key }}" {{ $type == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="time_filter" class="block text-small font-semibold text-lgu-headline mb-gr-xs">Time Period</label>
                    <select id="time_filter" name="time_filter" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lgu-highlight focus:border-transparent">
                        <option value="upcoming" {{ $timeFilter == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                        <option value="ongoing" {{ $timeFilter == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                        <option value="past" {{ $timeFilter == 'past' ? 'selected' : '' }}>Past</option>
                        <option value="all" {{ $timeFilter == 'all' ? 'selected' : '' }}>All</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-gr-sm">
                <button type="submit" class="inline-flex items-center px-gr-md py-gr-sm bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:bg-opacity-90 transition-colors duration-200">
                    <i data-lucide="filter" class="w-5 h-5 mr-gr-xs"></i>
                    Apply Filters
                </button>
                <a href="{{ route('admin.maintenance.index') }}" class="inline-flex items-center px-gr-md py-gr-sm bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors duration-200">
                    <i data-lucide="x" class="w-5 h-5 mr-gr-xs"></i>
                    Clear
                </a>
            </div>
        </form>
    </div>

    {{-- Maintenance List --}}
    @if($schedules->count() > 0)
        <div class="space-y-gr-md">
            @foreach($schedules as $schedule)
                @php
                    $isOngoing = $schedule->start_date <= now()->toDateString() && $schedule->end_date >= now()->toDateString();
                    $isPast = $schedule->end_date < now()->toDateString();
                @endphp
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-gr-lg">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-gr-sm mb-gr-sm">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold 
                                    @if($isOngoing) bg-amber-100 text-amber-800
                                    @elseif($isPast) bg-gray-100 text-gray-600
                                    @else bg-blue-100 text-blue-800
                                    @endif">
                                    @if($isOngoing)
                                        <i data-lucide="wrench" class="w-3 h-3 inline mr-1"></i> Ongoing
                                    @elseif($isPast)
                                        <i data-lucide="check-circle" class="w-3 h-3 inline mr-1"></i> Completed
                                    @else
                                        <i data-lucide="calendar-clock" class="w-3 h-3 inline mr-1"></i> Upcoming
                                    @endif
                                </span>
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                    {{ $maintenanceTypes[$schedule->maintenance_type] ?? $schedule->maintenance_type }}
                                </span>
                            </div>

                            <h3 class="text-h3 font-bold text-lgu-headline mb-gr-xs">{{ $schedule->facility->name }}</h3>

                            <div class="grid grid-cols-2 md:grid-cols-3 gap-gr-md text-small mb-gr-sm">
                                <div class="flex items-center text-gray-600">
                                    <i data-lucide="calendar" class="w-4 h-4 mr-2"></i>
                                    <span>{{ \Carbon\Carbon::parse($schedule->start_date)->format('M j') }} - {{ \Carbon\Carbon::parse($schedule->end_date)->format('M j, Y') }}</span>
                                </div>
                                @if($schedule->start_time && $schedule->end_time)
                                    <div class="flex items-center text-gray-600">
                                        <i data-lucide="clock" class="w-4 h-4 mr-2"></i>
                                        <span>{{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('g:i A') }}</span>
                                    </div>
                                @else
                                    <div class="flex items-center text-gray-600">
                                        <i data-lucide="clock" class="w-4 h-4 mr-2"></i>
                                        <span>All Day</span>
                                    </div>
                                @endif
                                @if($schedule->is_recurring)
                                    <div class="flex items-center text-gray-600">
                                        <i data-lucide="repeat" class="w-4 h-4 mr-2"></i>
                                        <span>Recurring ({{ ucfirst($schedule->recurring_pattern) }})</span>
                                    </div>
                                @endif
                            </div>

                            <p class="text-body text-gray-700">{{ $schedule->description }}</p>
                        </div>

                        <form method="POST" action="{{ route('admin.maintenance.destroy', $schedule->id) }}" onsubmit="return confirm('Cancel this maintenance schedule?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200" title="Cancel Maintenance">
                                <i data-lucide="trash-2" class="w-5 h-5"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($schedules->hasPages())
            <div class="mt-gr-lg">
                {{ $schedules->links() }}
            </div>
        @endif
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <i data-lucide="calendar-clock" class="w-16 h-16 text-gray-300 mb-gr-md mx-auto"></i>
            <h3 class="text-h3 font-bold text-lgu-headline mb-gr-xs">No Maintenance Scheduled</h3>
            <p class="text-body text-gray-600 mb-gr-md">Schedule maintenance to block booking slots</p>
            <a href="{{ route('admin.maintenance.create') }}" class="inline-flex items-center px-gr-lg py-gr-md bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:bg-opacity-90 transition-colors duration-200">
                <i data-lucide="plus" class="w-5 h-5 mr-gr-xs"></i>
                Schedule Maintenance
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script>
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}
</script>
@endpush
@endsection

