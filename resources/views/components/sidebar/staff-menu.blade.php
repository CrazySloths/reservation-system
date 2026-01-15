<!-- MAIN SECTION -->
<div class="px-gr-md mb-gr-lg">
    <h4 class="text-gray-400 text-caption font-semibold uppercase tracking-wider mb-gr-xs">Main</h4>
    <ul class="space-y-gr-xs">
        <li>
            <a href="{{ route('staff.dashboard') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Dashboard</span>
            </a>
        </li>
    </ul>
</div>

<!-- BOOKING VERIFICATION SUBMODULE -->
<div class="px-gr-md mb-gr-lg">
    <h4 class="text-gray-400 text-caption font-semibold uppercase tracking-wider mb-gr-xs">Booking Verification</h4>
    <ul class="space-y-gr-xs">
        <li>
            <a href="{{ route('staff.verification-queue') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('staff.verification-queue') ? 'active' : '' }}">
                <i data-lucide="clock" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Verification Queue</span>
                @php
                    // Get conflict count for staff notification
                    $conflictCount = \Illuminate\Support\Facades\DB::connection('facilities_db')
                        ->table('bookings as a')
                        ->join('bookings as b', function ($join) {
                            $join->on('a.facility_id', '=', 'b.facility_id')
                                ->on('a.event_date', '=', 'b.event_date')
                                ->whereRaw('a.id < b.id')
                                ->whereRaw('(a.start_time < b.end_time AND a.end_time > b.start_time)');
                        })
                        ->whereIn('a.status', ['pending', 'staff_verified'])
                        ->whereIn('b.status', ['pending', 'staff_verified'])
                        ->where('a.event_date', '>=', now()->toDateString())
                        ->distinct('a.id')
                        ->count();
                @endphp
                @if($conflictCount > 0)
                    <span class="ml-auto text-xs bg-lgu-tertiary text-white px-2 py-0.5 rounded-full font-semibold">{{ $conflictCount }}</span>
                @endif
            </a>
        </li>
        <li>
            <a href="{{ route('staff.bookings.index') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('staff.bookings.*') ? 'active' : '' }}">
                <i data-lucide="calendar-check" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>All Bookings</span>
            </a>
        </li>
        <li>
            <a href="{{ route('staff.calendar') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('staff.calendar') ? 'active' : '' }}">
                <i data-lucide="calendar-days" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Calendar View</span>
            </a>
        </li>
    </ul>
</div>

<!-- FACILITIES INFORMATION SUBMODULE (Read-Only Access) -->
<div class="px-gr-md mb-gr-lg">
    <h4 class="text-gray-400 text-caption font-semibold uppercase tracking-wider mb-gr-xs">Facilities</h4>
    <ul class="space-y-gr-xs">
        <li>
            <a href="{{ route('staff.facilities.index') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('staff.facilities.*') ? 'active' : '' }}">
                <i data-lucide="building-2" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>View Facilities</span>
            </a>
        </li>
        <li>
            <a href="{{ route('staff.equipment.index') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('staff.equipment.*') ? 'active' : '' }}">
                <i data-lucide="package" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Equipment List</span>
            </a>
        </li>
        <li>
            <a href="{{ route('staff.pricing.index') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('staff.pricing.*') ? 'active' : '' }}">
                <i data-lucide="tag" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Pricing Info</span>
            </a>
        </li>
    </ul>
</div>

<!-- REPORTS & ANALYTICS SUBMODULE (Limited Access) -->
<div class="px-gr-md mb-gr-lg">
    <h4 class="text-gray-400 text-caption font-semibold uppercase tracking-wider mb-gr-xs">Reports</h4>
    <ul class="space-y-gr-xs">
        <li>
            <a href="{{ route('staff.statistics.index') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('staff.statistics.*') ? 'active' : '' }}">
                <i data-lucide="file-bar-chart" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>My Statistics</span>
            </a>
        </li>
        <li>
            <a href="{{ route('staff.activity-log.index') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('staff.activity-log.*') ? 'active' : '' }}">
                <i data-lucide="file-text" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Activity Log</span>
            </a>
        </li>
    </ul>
</div>

<!-- COMMUNICATIONS SUBMODULE -->
<div class="px-gr-md mb-gr-lg">
    <h4 class="text-gray-400 text-caption font-semibold uppercase tracking-wider mb-gr-xs">Communications</h4>
    <ul class="space-y-gr-xs">
        <li>
            <a href="#" onclick="showComingSoon('Send Notifications'); return false;" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 opacity-60">
                <i data-lucide="send" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Send Notification</span>
                <span class="ml-auto text-xs bg-gray-500 text-white px-2 py-0.5 rounded-full">Soon</span>
            </a>
        </li>
        <li>
            <a href="#" onclick="showComingSoon('Message Templates'); return false;" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 opacity-60">
                <i data-lucide="mail" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Templates</span>
                <span class="ml-auto text-xs bg-gray-500 text-white px-2 py-0.5 rounded-full">Soon</span>
            </a>
        </li>
    </ul>
</div>
