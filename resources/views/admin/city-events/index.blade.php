@extends('layouts.admin')

@section('page-title', 'City Events Management')
@section('page-subtitle', 'Manage government events and priority bookings')

@section('page-content')
<div class="space-y-6">
    <!-- Header with Actions -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-lgu-headline">City Events</h2>
            <p class="text-sm text-lgu-paragraph mt-1">Schedule government events and manage conflicting reservations</p>
        </div>
        <a href="{{ route('admin.city-events.create') }}" 
           class="btn-primary flex items-center gap-2">
            <i data-lucide="calendar-plus" class="w-5 h-5"></i>
            <span>Create City Event</span>
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form method="GET" action="{{ route('admin.city-events.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-lgu-headline mb-2">
                        Search
                    </label>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}"
                        placeholder="Search events..."
                        class="w-full px-4 py-2 border-2 border-lgu-stroke rounded-lg focus:border-lgu-highlight focus:outline-none text-lgu-paragraph"
                    />
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-lgu-headline mb-2">
                        Status
                    </label>
                    <select 
                        name="status"
                        class="w-full px-4 py-2 border-2 border-lgu-stroke rounded-lg focus:border-lgu-highlight focus:outline-none text-lgu-paragraph"
                    >
                        <option value="">All Status</option>
                        <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <!-- Event Type Filter -->
                <div>
                    <label class="block text-sm font-medium text-lgu-headline mb-2">
                        Event Type
                    </label>
                    <select 
                        name="event_type"
                        class="w-full px-4 py-2 border-2 border-lgu-stroke rounded-lg focus:border-lgu-highlight focus:outline-none text-lgu-paragraph"
                    >
                        <option value="">All Types</option>
                        <option value="government" {{ request('event_type') === 'government' ? 'selected' : '' }}>Government</option>
                        <option value="emergency" {{ request('event_type') === 'emergency' ? 'selected' : '' }}>Emergency</option>
                        <option value="maintenance" {{ request('event_type') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                </div>

                <!-- Date Range -->
                <div>
                    <label class="block text-sm font-medium text-lgu-headline mb-2">
                        Date From
                    </label>
                    <input 
                        type="date" 
                        name="date_from" 
                        value="{{ request('date_from') }}"
                        class="w-full px-4 py-2 border-2 border-lgu-stroke rounded-lg focus:border-lgu-highlight focus:outline-none text-lgu-paragraph"
                    />
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn-primary flex items-center gap-2">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    <span>Apply Filters</span>
                </button>
                <a href="{{ route('admin.city-events.index') }}" class="btn-secondary flex items-center gap-2">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    <span>Clear</span>
                </a>
            </div>
        </form>
    </div>

    <!-- City Events Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        @if($cityEvents->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-lgu-bg">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-lgu-headline">
                                Event Title
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-lgu-headline">
                                Facility
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-lgu-headline">
                                Date & Time
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-lgu-headline">
                                Type
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-lgu-headline">
                                Status
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-lgu-headline">
                                Affected Bookings
                            </th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-lgu-headline">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-lgu-stroke">
                        @foreach($cityEvents as $event)
                        <tr class="hover:bg-lgu-bg transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-lgu-headline">{{ $event->event_title }}</div>
                                <div class="text-xs text-lgu-paragraph mt-1">{{ Str::limit($event->event_description, 50) }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-lgu-paragraph">
                                {{ $event->facility->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-lgu-paragraph">
                                    {{ $event->start_time->format('M d, Y') }}
                                </div>
                                <div class="text-xs text-lgu-paragraph">
                                    {{ $event->start_time->format('g:i A') }} - {{ $event->end_time->format('g:i A') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium
                                    @if($event->event_type === 'government') bg-blue-100 text-blue-800
                                    @elseif($event->event_type === 'emergency') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                    @if($event->event_type === 'government')
                                        <i data-lucide="building-2" class="w-3 h-3"></i>
                                    @elseif($event->event_type === 'emergency')
                                        <i data-lucide="alert-circle" class="w-3 h-3"></i>
                                    @else
                                        <i data-lucide="wrench" class="w-3 h-3"></i>
                                    @endif
                                    {{ ucfirst($event->event_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium
                                    @if($event->status === 'scheduled') bg-green-100 text-green-800
                                    @elseif($event->status === 'ongoing') bg-blue-100 text-blue-800
                                    @elseif($event->status === 'completed') bg-gray-100 text-gray-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($event->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($event->affected_bookings_count > 0)
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        <i data-lucide="alert-triangle" class="w-3 h-3"></i>
                                        {{ $event->affected_bookings_count }}
                                    </span>
                                @else
                                    <span class="text-sm text-lgu-paragraph">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.city-events.show', $event) }}" 
                                       class="p-2 text-lgu-headline hover:bg-lgu-bg rounded-lg transition-colors"
                                       title="View Details">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    @if($event->status === 'scheduled')
                                    <a href="{{ route('admin.city-events.edit', $event) }}" 
                                       class="p-2 text-lgu-headline hover:bg-lgu-bg rounded-lg transition-colors"
                                       title="Edit">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </a>
                                    <button onclick="deleteEvent({{ $event->id }})" 
                                            class="p-2 text-lgu-tertiary hover:bg-red-50 rounded-lg transition-colors"
                                            title="Delete">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-lgu-stroke">
                {{ $cityEvents->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <i data-lucide="calendar-off" class="w-16 h-16 mx-auto text-gray-400 mb-4"></i>
                <h3 class="text-lg font-semibold text-lgu-headline mb-2">No City Events Found</h3>
                <p class="text-sm text-lgu-paragraph mb-6">
                    @if(request()->hasAny(['search', 'status', 'event_type', 'date_from']))
                        No city events match your current filters.
                    @else
                        Start by creating your first city event.
                    @endif
                </p>
                @if(!request()->hasAny(['search', 'status', 'event_type', 'date_from']))
                <a href="{{ route('admin.city-events.create') }}" class="btn-primary inline-flex items-center gap-2">
                    <i data-lucide="calendar-plus" class="w-5 h-5"></i>
                    <span>Create City Event</span>
                </a>
                @endif
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
// Initialize Lucide icons
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}

// Delete city event
function deleteEvent(eventId) {
    Swal.fire({
        title: 'Archive City Event?',
        text: 'This city event will be archived. You can restore it later if needed.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#faae2b',
        cancelButtonColor: '#fa5246',
        confirmButtonText: 'Yes, archive it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/city-events/${eventId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Archived!',
                        text: data.message,
                        confirmButtonColor: '#faae2b'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message,
                        confirmButtonColor: '#fa5246'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to archive city event. Please try again.',
                    confirmButtonColor: '#fa5246'
                });
            });
        }
    });
}
</script>
@endpush
@endsection

