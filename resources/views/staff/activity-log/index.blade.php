@extends('layouts.staff')

@section('page-title', 'Activity Log')
@section('page-subtitle', 'Complete history of your actions and verifications')

@section('page-content')
<div class="space-y-gr-lg">
    {{-- Header with Stats --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-h1 font-bold text-lgu-headline mb-gr-xs">Activity Log</h1>
            <p class="text-body text-lgu-paragraph">Track all your actions and verifications</p>
        </div>
        <div class="flex items-center gap-gr-sm">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 px-gr-md py-gr-sm">
                <div class="text-caption text-gray-600 uppercase mb-1">Total Activities</div>
                <div class="text-h2 font-bold text-lgu-headline">{{ number_format($totalActivities) }}</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 px-gr-md py-gr-sm">
                <div class="text-caption text-gray-600 uppercase mb-1">Today</div>
                <div class="text-h2 font-bold text-lgu-button">{{ $todayActivities }}</div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-gr-lg">
        <form method="GET" action="{{ route('staff.activity-log.index') }}" class="space-y-gr-md">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-gr-md">
                <div>
                    <label for="search" class="block text-small font-semibold text-lgu-headline mb-gr-xs">Search</label>
                    <input type="text" 
                           id="search" 
                           name="search" 
                           value="{{ $search }}" 
                           placeholder="Search activities..."
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lgu-highlight focus:border-transparent">
                </div>
                <div>
                    <label for="action" class="block text-small font-semibold text-lgu-headline mb-gr-xs">Action Type</label>
                    <select id="action" 
                            name="action" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lgu-highlight focus:border-transparent">
                        <option value="">All Actions</option>
                        @foreach($availableActions as $availableAction)
                            <option value="{{ $availableAction }}" {{ $action == $availableAction ? 'selected' : '' }}>
                                {{ ucfirst($availableAction) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="date_from" class="block text-small font-semibold text-lgu-headline mb-gr-xs">Date From</label>
                    <input type="date" 
                           id="date_from" 
                           name="date_from" 
                           value="{{ $dateFrom }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lgu-highlight focus:border-transparent">
                </div>
                <div>
                    <label for="date_to" class="block text-small font-semibold text-lgu-headline mb-gr-xs">Date To</label>
                    <input type="date" 
                           id="date_to" 
                           name="date_to" 
                           value="{{ $dateTo }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lgu-highlight focus:border-transparent">
                </div>
            </div>
            <div class="flex items-center gap-gr-sm">
                <button type="submit" 
                        class="inline-flex items-center px-gr-md py-gr-sm bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:bg-opacity-90 transition-colors duration-200">
                    <i data-lucide="filter" class="w-5 h-5 mr-gr-xs"></i>
                    Apply Filters
                </button>
                @if($search || $action || $dateFrom || $dateTo)
                    <a href="{{ route('staff.activity-log.index') }}" 
                       class="inline-flex items-center px-gr-md py-gr-sm bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors duration-200">
                        <i data-lucide="x" class="w-5 h-5 mr-gr-xs"></i>
                        Clear Filters
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Activity Timeline --}}
    @if($activities->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-gr-lg">
            <div class="space-y-gr-md">
                @foreach($activities as $activity)
                    <div class="flex gap-gr-md p-gr-md rounded-lg border border-gray-100 hover:border-lgu-button hover:shadow-md transition-all duration-200">
                        <!-- Action Icon -->
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center
                                {{ $activity->action === 'verify' ? 'bg-blue-100 text-blue-600' : '' }}
                                {{ $activity->action === 'approve' ? 'bg-green-100 text-green-600' : '' }}
                                {{ $activity->action === 'reject' ? 'bg-red-100 text-red-600' : '' }}
                                {{ $activity->action === 'update' ? 'bg-amber-100 text-amber-600' : '' }}
                                {{ $activity->action === 'create' ? 'bg-purple-100 text-purple-600' : '' }}
                                {{ !in_array($activity->action, ['verify', 'approve', 'reject', 'update', 'create']) ? 'bg-gray-100 text-gray-600' : '' }}">
                                @if($activity->action === 'verify')
                                    <i data-lucide="check-circle" class="w-6 h-6"></i>
                                @elseif($activity->action === 'approve')
                                    <i data-lucide="thumbs-up" class="w-6 h-6"></i>
                                @elseif($activity->action === 'reject')
                                    <i data-lucide="thumbs-down" class="w-6 h-6"></i>
                                @elseif($activity->action === 'update')
                                    <i data-lucide="edit" class="w-6 h-6"></i>
                                @elseif($activity->action === 'create')
                                    <i data-lucide="plus-circle" class="w-6 h-6"></i>
                                @else
                                    <i data-lucide="activity" class="w-6 h-6"></i>
                                @endif
                            </div>
                        </div>

                        <!-- Activity Details -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-gr-sm mb-2">
                                <div>
                                    <h3 class="text-body font-bold text-gray-900">
                                        {{ ucfirst($activity->action) }} {{ $activity->model }}
                                    </h3>
                                    <p class="text-small text-gray-600">
                                        {{ \Carbon\Carbon::parse($activity->created_at)->format('M j, Y g:i A') }}
                                        <span class="text-gray-400">•</span>
                                        {{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}
                                    </p>
                                </div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $activity->action === 'approve' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $activity->action === 'reject' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $activity->action === 'verify' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $activity->action === 'update' ? 'bg-amber-100 text-amber-800' : '' }}
                                    {{ $activity->action === 'create' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ !in_array($activity->action, ['verify', 'approve', 'reject', 'update', 'create']) ? 'bg-gray-100 text-gray-800' : '' }}">
                                    {{ ucfirst($activity->action) }}
                                </span>
                            </div>

                            <!-- Booking Info if Available -->
                            @if(isset($activity->booking))
                                <div class="bg-gray-50 rounded-lg p-gr-sm mb-gr-sm">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-gr-sm text-small">
                                        <div>
                                            <span class="text-gray-600 font-medium">Booking:</span>
                                            <span class="text-gray-900 font-semibold ml-1">{{ $activity->booking->booking_reference }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600 font-medium">Facility:</span>
                                            <span class="text-gray-900 ml-1">{{ $activity->booking->facility_name }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600 font-medium">Status:</span>
                                            <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold
                                                {{ $activity->booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $activity->booking->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                                {{ $activity->booking->status === 'pending' ? 'bg-amber-100 text-amber-800' : '' }}
                                                {{ $activity->booking->status === 'staff_verified' ? 'bg-blue-100 text-blue-800' : '' }}">
                                                {{ ucfirst(str_replace('_', ' ', $activity->booking->status)) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Changes Details -->
                            @if($activity->changes_array && count($activity->changes_array) > 0)
                                <div class="text-small">
                                    <button type="button" 
                                            onclick="toggleChanges({{ $activity->id }})"
                                            class="text-lgu-button hover:text-lgu-highlight font-semibold flex items-center gap-1">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                        View Changes
                                    </button>
                                    <div id="changes-{{ $activity->id }}" class="hidden mt-2 p-gr-sm bg-blue-50 rounded border border-blue-100">
                                        <pre class="text-xs text-gray-700 whitespace-pre-wrap overflow-x-auto">{{ json_encode($activity->changes_array, JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                </div>
                            @endif

                            <!-- IP Address and User Agent (collapsed by default) -->
                            <div class="mt-2 text-xs text-gray-500">
                                <span class="font-medium">IP:</span> {{ $activity->ip_address }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Pagination --}}
        @if($activities->hasPages())
            <div class="mt-gr-lg">
                {{ $activities->links() }}
            </div>
        @endif
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <i data-lucide="inbox" class="w-16 h-16 text-gray-300 mb-gr-md mx-auto"></i>
            <h3 class="text-h3 font-bold text-lgu-headline mb-gr-xs">No Activities Found</h3>
            <p class="text-body text-gray-600">No activities match your current filters.</p>
            @if($search || $action || $dateFrom || $dateTo)
                <a href="{{ route('staff.activity-log.index') }}" 
                   class="inline-flex items-center mt-gr-md px-gr-md py-gr-sm bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:bg-opacity-90 transition-colors duration-200">
                    <i data-lucide="refresh-cw" class="w-5 h-5 mr-gr-xs"></i>
                    Clear Filters
                </a>
            @endif
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}

function toggleChanges(activityId) {
    const changesDiv = document.getElementById('changes-' + activityId);
    if (changesDiv.classList.contains('hidden')) {
        changesDiv.classList.remove('hidden');
    } else {
        changesDiv.classList.add('hidden');
    }
}
</script>
@endpush
@endsection

