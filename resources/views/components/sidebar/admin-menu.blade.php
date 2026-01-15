<!-- MAIN SECTION -->
<div class="px-gr-md mb-gr-lg">
    <h4 class="text-gray-400 text-caption font-semibold uppercase tracking-wider mb-gr-xs">Main</h4>
    <ul class="space-y-gr-xs">
        <li>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Dashboard</span>
            </a>
        </li>
    </ul>
</div>

<!-- BOOKING MANAGEMENT SUBMODULE -->
<div class="px-gr-md mb-gr-lg">
    <h4 class="text-gray-400 text-caption font-semibold uppercase tracking-wider mb-gr-xs">Booking Management</h4>
    <ul class="space-y-gr-xs">
        <li>
            <a href="{{ route('admin.bookings.index') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
                <i data-lucide="calendar-check" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>All Bookings</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.calendar') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.calendar') ? 'active' : '' }}">
                <i data-lucide="calendar-days" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Calendar View</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.schedule-conflicts.index') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.schedule-conflicts.*') ? 'active' : '' }}">
                <i data-lucide="alert-triangle" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Schedule Conflicts</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.maintenance.index') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.maintenance.*') ? 'active' : '' }}">
                <i data-lucide="wrench" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Maintenance Schedule</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.city-events.index') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.city-events.*') ? 'active' : '' }}">
                <i data-lucide="calendar-range" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>City Events</span>
            </a>
        </li>
    </ul>
</div>

<!-- GOVERNMENT PROGRAMS SUBMODULE (Energy Efficiency Integration) -->
<div class="px-gr-md mb-gr-lg">
    <h4 class="text-gray-400 text-caption font-semibold uppercase tracking-wider mb-gr-xs">Government Programs</h4>
    <ul class="space-y-gr-xs">
        <li>
            <a href="{{ route('admin.government-programs.index') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.government-programs.*') ? 'active' : '' }}">
                <i data-lucide="handshake" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Program Requests</span>
            </a>
        </li>
    </ul>
</div>

<!-- FINANCIAL MANAGEMENT SUBMODULE (Coming Soon) -->
<div class="px-gr-md mb-gr-lg">
    <h4 class="text-gray-400 text-caption font-semibold uppercase tracking-wider mb-gr-xs">Financial</h4>
    <ul class="space-y-gr-xs">
        <li>
            <a href="{{ route('admin.analytics.revenue-report') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.analytics.revenue-report') ? 'active' : '' }}">
                <i data-lucide="trending-up" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Revenue Reports</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.analytics.payments') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.analytics.payments') ? 'bg-lgu-green text-white' : 'text-lgu-green hover:bg-lgu-green hover:bg-opacity-10' }}">
                <i data-lucide="bar-chart-3" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Payment Analytics</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.transactions.index') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.transactions.*') ? 'bg-lgu-green text-white' : 'text-lgu-green hover:bg-lgu-green hover:bg-opacity-10' }}">
                <i data-lucide="receipt" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Transactions</span>
            </a>
        </li>
    </ul>
</div>

<!-- FACILITIES MANAGEMENT SUBMODULE (Coming Soon) -->
<div class="px-gr-md mb-gr-lg">
    <h4 class="text-gray-400 text-caption font-semibold uppercase tracking-wider mb-gr-xs">Facilities</h4>
    <ul class="space-y-gr-xs">
        <li>
            <a href="{{ route('admin.facilities.index') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.facilities.*') ? 'active' : '' }}">
                <i data-lucide="building-2" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Manage Facilities</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.equipment.index') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.equipment.*') ? 'active' : '' }}">
                <i data-lucide="package" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Equipment</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.pricing.index') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.pricing.*') ? 'active' : '' }}">
                <i data-lucide="tag" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Pricing</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.reviews.index') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                <i data-lucide="star" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Reviews Moderation</span>
            </a>
        </li>
    </ul>
</div>

<!-- USER MANAGEMENT SUBMODULE -->
<div class="px-gr-md mb-gr-lg">
    <h4 class="text-gray-400 text-caption font-semibold uppercase tracking-wider mb-gr-xs">Users</h4>
    <ul class="space-y-gr-xs">
        <li>
            <a href="{{ route('admin.staff.index') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">
                <i data-lucide="users" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Staff Accounts</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.citizens.index') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.citizens.*') ? 'active' : '' }}">
                <i data-lucide="user-check" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Citizens</span>
            </a>
        </li>
    </ul>
</div>

<!-- COMMUNICATIONS SUBMODULE (Coming Soon) -->
<div class="px-gr-md mb-gr-lg">
    <h4 class="text-gray-400 text-caption font-semibold uppercase tracking-wider mb-gr-xs">Communications</h4>
    <ul class="space-y-gr-xs">
        <li>
            <a href="#" onclick="showComingSoon('Email Notifications'); return false;" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 opacity-60">
                <i data-lucide="mail" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Email Settings</span>
                <span class="ml-auto text-xs bg-gray-500 text-white px-2 py-0.5 rounded-full">Soon</span>
            </a>
        </li>
        <li>
            <a href="#" onclick="showComingSoon('SMS Notifications'); return false;" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 opacity-60">
                <i data-lucide="message-square" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>SMS Settings</span>
                <span class="ml-auto text-xs bg-gray-500 text-white px-2 py-0.5 rounded-full">Soon</span>
            </a>
        </li>
    </ul>
</div>

<!-- REPORTS & ANALYTICS SUBMODULE -->
<div class="px-gr-md mb-gr-lg">
    <h4 class="text-gray-400 text-caption font-semibold uppercase tracking-wider mb-gr-xs">Reports & Analytics</h4>
    <ul class="space-y-gr-xs">
        <li>
            <a href="{{ route('admin.analytics.index') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.analytics.index') ? 'active' : '' }}">
                <i data-lucide="layout-grid" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Reports Hub</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.analytics.booking-statistics') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.analytics.booking-statistics') ? 'active' : '' }}">
                <i data-lucide="bar-chart-2" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Booking Statistics</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.analytics.facility-utilization') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.analytics.facility-utilization') ? 'active' : '' }}">
                <i data-lucide="pie-chart" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Facility Utilization</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.analytics.citizen-analytics') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.analytics.citizen-analytics') ? 'active' : '' }}">
                <i data-lucide="users-2" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Citizen Analytics</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.analytics.operational-metrics') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.analytics.operational-metrics') ? 'active' : '' }}">
                <i data-lucide="activity" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Operational Metrics</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.budget.index') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.budget.*') ? 'active' : '' }}">
                <i data-lucide="wallet" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Budget Management</span>
            </a>
        </li>
        <li>
            <a href="#" onclick="showComingSoon('Audit Logs'); return false;" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 opacity-60">
                <i data-lucide="shield-check" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Audit Trail</span>
                <span class="ml-auto text-xs bg-gray-500 text-white px-2 py-0.5 rounded-full">Soon</span>
            </a>
        </li>
    </ul>
</div>

<!-- SYSTEM SETTINGS SUBMODULE (Coming Soon) -->
<div class="px-gr-md mb-gr-lg">
    <h4 class="text-gray-400 text-caption font-semibold uppercase tracking-wider mb-gr-xs">System</h4>
    <ul class="space-y-gr-xs">
        <li>
            <a href="#" onclick="showComingSoon('System Settings'); return false;" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 opacity-60">
                <i data-lucide="settings" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Settings</span>
                <span class="ml-auto text-xs bg-gray-500 text-white px-2 py-0.5 rounded-full">Soon</span>
            </a>
        </li>
        <li>
            <a href="#" onclick="showComingSoon('Backup & Restore'); return false;" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 opacity-60">
                <i data-lucide="database" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Backup</span>
                <span class="ml-auto text-xs bg-gray-500 text-white px-2 py-0.5 rounded-full">Soon</span>
            </a>
        </li>
    </ul>
</div>
