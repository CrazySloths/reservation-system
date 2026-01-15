@extends('layouts.citizen')

@section('page-title', 'Booking Conflicts')
@section('page-subtitle', 'Manage conflicts with city events')

@section('page-content')
<div class="space-y-6">
    <!-- Success Message -->
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-600 p-4 rounded-lg">
        <div class="flex items-start">
            <i data-lucide="check-circle" class="w-5 h-5 text-green-600 mr-3 flex-shrink-0 mt-0.5"></i>
            <p class="text-sm text-green-800">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <!-- Error Messages -->
    @if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-600 p-4 rounded-lg">
        <div class="flex items-start">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 mr-3 flex-shrink-0 mt-0.5"></i>
            <div>
                <p class="font-semibold text-red-900">Error</p>
                <ul class="list-disc list-inside text-sm text-red-800 mt-2 space-y-1">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <div>
        <h2 class="text-2xl font-bold text-lgu-headline">Booking Conflicts</h2>
        <p class="text-sm text-lgu-paragraph mt-1">Your bookings that conflict with city events</p>
    </div>

    <!-- Filter Tabs -->
    <div class="flex gap-2 border-b border-gray-200">
        <a href="{{ route('citizen.conflicts.index', ['filter' => 'pending']) }}" 
           class="px-4 py-2 -mb-px {{ request('filter', 'pending') === 'pending' ? 'border-b-2 border-lgu-highlight text-lgu-highlight font-semibold' : 'text-gray-600 hover:text-gray-800' }}">
            <i data-lucide="alert-circle" class="w-4 h-4 inline-block mr-1"></i>
            Pending
        </a>
        <a href="{{ route('citizen.conflicts.index', ['filter' => 'resolved']) }}" 
           class="px-4 py-2 -mb-px {{ request('filter') === 'resolved' ? 'border-b-2 border-lgu-highlight text-lgu-highlight font-semibold' : 'text-gray-600 hover:text-gray-800' }}">
            <i data-lucide="check-circle" class="w-4 h-4 inline-block mr-1"></i>
            Resolved
        </a>
        <a href="{{ route('citizen.conflicts.index', ['filter' => 'all']) }}" 
           class="px-4 py-2 -mb-px {{ request('filter') === 'all' ? 'border-b-2 border-lgu-highlight text-lgu-highlight font-semibold' : 'text-gray-600 hover:text-gray-800' }}">
            <i data-lucide="list" class="w-4 h-4 inline-block mr-1"></i>
            All
        </a>
    </div>

    @if($conflicts->count() > 0)
        <div class="space-y-4">
            @foreach($conflicts as $conflict)
            <div class="bg-white rounded-xl shadow-sm overflow-hidden
                @if($conflict->status === 'pending') border-l-4 border-orange-500 @else border-l-4 border-green-500 @endif">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-3">
                                @if($conflict->status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        <i data-lucide="alert-circle" class="w-3 h-3"></i>
                                        Action Required
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i data-lucide="check-circle" class="w-3 h-3"></i>
                                        Resolved
                                    </span>
                                @endif
                                <span class="text-sm text-lgu-paragraph">
                                    @if($conflict->status === 'pending')
                                        Respond by {{ $conflict->response_deadline->format('M d, Y g:i A') }}
                                    @else
                                        Resolved on {{ $conflict->resolved_at->format('M d, Y') }}
                                    @endif
                                </span>
                            </div>

                            <h3 class="text-lg font-bold text-lgu-headline mb-2">
                                {{ $conflict->cityEvent->event_title }}
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <p class="text-xs text-lgu-paragraph uppercase tracking-wide mb-1">Your Booking</p>
                                    <p class="text-sm font-semibold text-lgu-headline">{{ $conflict->facilityDetails->name ?? 'N/A' }}</p>
                                    <p class="text-sm text-lgu-paragraph">
                                        {{ \Carbon\Carbon::parse($conflict->bookingDetails->start_time)->format('M d, Y g:i A') }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-lgu-paragraph uppercase tracking-wide mb-1">City Event</p>
                                    <p class="text-sm font-semibold text-lgu-headline">{{ ucfirst($conflict->cityEvent->event_type) }}</p>
                                    <p class="text-sm text-lgu-paragraph">
                                        {{ $conflict->cityEvent->start_time->format('M d, Y g:i A') }}
                                    </p>
                                </div>
                            </div>

                            @if($conflict->status === 'resolved')
                                <div class="bg-green-50 rounded-lg p-3">
                                    <p class="text-sm text-green-800">
                                        <span class="font-semibold">Choice: </span>
                                        {{ ucfirst(str_replace('_', ' ', $conflict->citizen_choice)) }}
                                    </p>
                                </div>
                            @endif
                        </div>

                        @if($conflict->status === 'pending')
                        <div class="flex-shrink-0 ml-4">
                            <a href="{{ route('citizen.conflicts.show', $conflict) }}" 
                               class="btn-primary inline-flex items-center gap-2">
                                <i data-lucide="calendar" class="w-4 h-4"></i>
                                <span>Resolve</span>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $conflicts->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <i data-lucide="check-circle" class="w-16 h-16 mx-auto text-green-500 mb-4"></i>
            <h3 class="text-lg font-semibold text-lgu-headline mb-2">
                @if(request('filter') === 'resolved')
                    No Resolved Conflicts
                @elseif(request('filter') === 'all')
                    No Conflicts
                @else
                    No Pending Conflicts
                @endif
            </h3>
            <p class="text-sm text-lgu-paragraph">
                @if(request('filter') === 'resolved')
                    You don't have any resolved conflicts in your history.
                @elseif(request('filter') === 'all')
                    You don't have any booking conflicts at this time.
                @else
                    You don't have any pending booking conflicts that require action.
                @endif
            </p>
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

