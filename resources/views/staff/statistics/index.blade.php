@extends('layouts.staff')

@section('page-title', 'My Statistics')
@section('page-subtitle', 'Your personal performance metrics and activity summary')

@section('page-content')
<div class="space-y-gr-lg">
    {{-- Header with Date Range --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-h1 font-bold text-lgu-headline mb-gr-xs">My Statistics</h1>
            <p class="text-body text-lgu-paragraph">
                Period: {{ $startDate->format('M j, Y') }} - {{ $endDate->format('M j, Y') }} (Last 30 days)
            </p>
        </div>
    </div>

    {{-- Key Metrics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-gr-md">
        <!-- Total Verifications -->
        <div class="bg-white rounded-xl shadow-lg p-gr-lg border-2 border-lgu-green">
            <div class="flex items-center justify-between mb-gr-sm">
                <div style="width: 64px; height: 64px; background-color: #2C5E3F; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                </div>
                <span class="text-4xl font-bold text-lgu-headline">{{ $totalVerifications }}</span>
            </div>
            <h3 class="text-lg font-semibold mb-1 text-lgu-headline">Total Verifications</h3>
            <p class="text-sm text-lgu-paragraph">Last 30 days</p>
        </div>

        <!-- Total Approvals -->
        <div class="bg-white rounded-xl shadow-lg p-gr-lg border-2 border-lgu-green">
            <div class="flex items-center justify-between mb-gr-sm">
                <div style="width: 64px; height: 64px; background-color: #2C5E3F; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path>
                    </svg>
                </div>
                <span class="text-4xl font-bold text-lgu-headline">{{ $totalApprovals }}</span>
            </div>
            <h3 class="text-lg font-semibold mb-1 text-lgu-headline">Approvals</h3>
            <p class="text-sm text-lgu-paragraph">Confirmed bookings</p>
        </div>

        <!-- Total Rejections -->
        <div class="bg-white rounded-xl shadow-lg p-gr-lg border-2 border-lgu-tertiary">
            <div class="flex items-center justify-between mb-gr-sm">
                <div class="w-16 h-16 bg-lgu-tertiary rounded-lg flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10 15v4a3 3 0 0 0 3 3l4-9V2H5.72a2 2 0 0 0-2 1.7l-1.38 9a2 2 0 0 0 2 2.3zm7-13h2.67A2.31 2.31 0 0 1 22 4v7a2.31 2.31 0 0 1-2.33 2H17"></path>
                    </svg>
                </div>
                <span class="text-4xl font-bold text-lgu-headline">{{ $totalRejections }}</span>
            </div>
            <h3 class="text-lg font-semibold mb-1 text-lgu-headline">Rejections</h3>
            <p class="text-sm text-lgu-paragraph">Declined requests</p>
        </div>

        <!-- Pending Queue -->
        <div class="bg-white rounded-xl shadow-lg p-gr-lg border-2 border-lgu-highlight">
            <div class="flex items-center justify-between mb-gr-sm">
                <div class="w-16 h-16 bg-lgu-highlight rounded-lg flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-lgu-button-text" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <span class="text-4xl font-bold text-lgu-headline">{{ $pendingVerifications }}</span>
            </div>
            <h3 class="text-lg font-semibold mb-1 text-lgu-headline">Pending Queue</h3>
            <p class="text-sm text-lgu-paragraph">Awaiting verification</p>
        </div>
    </div>

    {{-- Activity Summary --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-gr-md">
        <!-- Today's Activity -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-gr-lg">
            <div class="flex items-center gap-gr-sm mb-gr-md">
                <div class="bg-lgu-green bg-opacity-10 p-3 rounded-lg">
                    <i data-lucide="calendar-days" class="w-6 h-6 text-lgu-green"></i>
                </div>
                <div>
                    <h3 class="text-h3 font-bold text-lgu-headline">Today's Activity</h3>
                    <p class="text-caption text-gray-600">{{ \Carbon\Carbon::now()->format('F j, Y') }}</p>
                </div>
            </div>
            <div class="text-4xl font-bold text-lgu-button">{{ $todayActivity }}</div>
            <p class="text-small text-gray-600 mt-2">actions performed today</p>
        </div>

        <!-- This Week -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-gr-lg">
            <div class="flex items-center gap-gr-sm mb-gr-md">
                <div class="bg-blue-50 p-3 rounded-lg">
                    <i data-lucide="calendar-range" class="w-6 h-6 text-blue-600"></i>
                </div>
                <div>
                    <h3 class="text-h3 font-bold text-lgu-headline">This Week</h3>
                    <p class="text-caption text-gray-600">Week {{ \Carbon\Carbon::now()->weekOfYear }}</p>
                </div>
            </div>
            <div class="text-4xl font-bold text-blue-600">{{ $weekActivity }}</div>
            <p class="text-small text-gray-600 mt-2">actions this week</p>
        </div>

        <!-- Avg Response Time -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-gr-lg">
            <div class="flex items-center gap-gr-sm mb-gr-md">
                <div class="bg-purple-50 p-3 rounded-lg">
                    <i data-lucide="timer" class="w-6 h-6 text-purple-600"></i>
                </div>
                <div>
                    <h3 class="text-h3 font-bold text-lgu-headline">Avg Response Time</h3>
                    <p class="text-caption text-gray-600">Hours to verify</p>
                </div>
            </div>
            <div class="text-4xl font-bold text-purple-600">
                {{ $avgResponseTime && $avgResponseTime->avg_hours ? number_format($avgResponseTime->avg_hours, 1) : '0' }}h
            </div>
            <p class="text-small text-gray-600 mt-2">average response time</p>
        </div>
    </div>

    {{-- Activity Chart (Last 7 Days) --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-gr-lg">
        <h2 class="text-h2 font-bold text-lgu-headline mb-gr-md flex items-center">
            <i data-lucide="bar-chart-3" class="w-6 h-6 mr-gr-sm text-lgu-button"></i>
            Activity Trend (Last 7 Days)
        </h2>
        <div class="flex items-end justify-between gap-gr-sm h-48">
            @foreach($activityByDay as $day)
                <div class="flex-1 flex flex-col items-center justify-end h-full">
                    <div class="w-full bg-lgu-button hover:bg-lgu-highlight transition-colors rounded-t-lg flex items-end justify-center text-white font-semibold" 
                         style="height: {{ $day['count'] > 0 ? (($day['count'] / max(array_column($activityByDay, 'count'))) * 100) : 5 }}%;"
                         title="{{ $day['count'] }} actions">
                        @if($day['count'] > 0)
                            <span class="text-sm mb-2">{{ $day['count'] }}</span>
                        @endif
                    </div>
                    <div class="text-xs text-gray-600 mt-2 font-semibold">{{ $day['day'] }}</div>
                    <div class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($day['date'])->format('M j') }}</div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Two Column Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-gr-md">
        <!-- Top Facilities -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-gr-lg">
            <h2 class="text-h2 font-bold text-lgu-headline mb-gr-md flex items-center">
                <i data-lucide="building-2" class="w-6 h-6 mr-gr-sm text-lgu-button"></i>
                Most Verified Facilities
            </h2>
            @if($topFacilities->count() > 0)
                <div class="space-y-gr-sm">
                    @foreach($topFacilities as $index => $facility)
                        <div class="flex items-center gap-gr-sm p-gr-sm rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center font-bold text-white
                                {{ $index === 0 ? 'bg-gradient-to-br from-yellow-400 to-yellow-500' : '' }}
                                {{ $index === 1 ? 'bg-gradient-to-br from-gray-400 to-gray-500' : '' }}
                                {{ $index === 2 ? 'bg-gradient-to-br from-orange-400 to-orange-500' : '' }}
                                {{ $index > 2 ? 'bg-gradient-to-br from-blue-400 to-blue-500' : '' }}">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-small font-semibold text-gray-900 truncate">{{ $facility->facility_name }}</p>
                            </div>
                            <div class="flex-shrink-0 bg-lgu-green bg-opacity-10 px-3 py-1 rounded-full">
                                <span class="text-sm font-bold text-lgu-green">{{ $facility->verification_count }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-gr-lg text-gray-500">
                    <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-gr-sm text-gray-300"></i>
                    <p class="text-body">No verification data available yet</p>
                </div>
            @endif
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-gr-lg">
            <h2 class="text-h2 font-bold text-lgu-headline mb-gr-md flex items-center">
                <i data-lucide="activity" class="w-6 h-6 mr-gr-sm text-lgu-button"></i>
                Recent Verifications
            </h2>
            @if($recentVerifications->count() > 0)
                <div class="space-y-gr-sm max-h-96 overflow-y-auto">
                    @foreach($recentVerifications as $activity)
                        <div class="p-gr-sm rounded-lg border border-gray-100 hover:border-lgu-button transition-colors">
                            <div class="flex items-start justify-between gap-gr-sm mb-1">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold
                                    {{ $activity->action === 'approve' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $activity->action === 'reject' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $activity->action === 'verify' ? 'bg-blue-100 text-blue-800' : '' }}">
                                    {{ ucfirst($activity->action) }}
                                </span>
                                <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm font-semibold text-gray-900">{{ $activity->facility_name }}</p>
                            <p class="text-xs text-gray-600">{{ $activity->booking_reference }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-gr-lg text-gray-500">
                    <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-gr-sm text-gray-300"></i>
                    <p class="text-body">No recent activity</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Action Button --}}
    <div class="flex justify-center">
        <a href="{{ route('staff.activity-log.index') }}" 
           class="inline-flex items-center px-gr-lg py-gr-md bg-lgu-button text-lgu-button-text font-bold rounded-lg hover:bg-lgu-hover transition shadow-lg">
            <i data-lucide="file-text" class="w-5 h-5 mr-gr-sm"></i>
            View Detailed Activity Log
        </a>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Force initialize Lucide icons immediately
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
    console.log('Lucide icons initialized immediately');
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
        console.log('Lucide icons initialized on DOMContentLoaded');
    }
});

// Multiple reinitializations to catch late-loading elements
setTimeout(function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
        console.log('Lucide icons initialized after 100ms');
    }
}, 100);

setTimeout(function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
        console.log('Lucide icons initialized after 500ms');
    }
}, 500);

setTimeout(function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
        console.log('Lucide icons initialized after 1000ms');
    }
}, 1000);
</script>
@endpush
@endsection

