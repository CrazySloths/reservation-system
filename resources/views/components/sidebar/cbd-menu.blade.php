<!-- MAIN SECTION -->
<div class="px-gr-md mb-gr-lg">
    <h4 class="text-gray-400 text-caption font-semibold uppercase tracking-wider mb-gr-xs">Main</h4>
    <ul class="space-y-gr-xs">
        <li>
            <a href="{{ route('cbd.dashboard') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('cbd.dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Dashboard</span>
            </a>
        </li>
    </ul>
</div>

<!-- REPORTS SUBMODULE -->
<div class="px-gr-md mb-gr-lg">
    <h4 class="text-gray-400 text-caption font-semibold uppercase tracking-wider mb-gr-xs">Reports</h4>
    <ul class="space-y-gr-xs">
        <li>
            <a href="{{ route('cbd.reports.revenue') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('cbd.reports.revenue') ? 'active' : '' }}">
                <i data-lucide="trending-up" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Revenue Reports</span>
            </a>
        </li>
        <li>
            <a href="{{ route('cbd.reports.facility-utilization') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('cbd.reports.facility-utilization') ? 'active' : '' }}">
                <i data-lucide="bar-chart-3" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Facility Utilization</span>
            </a>
        </li>
        <li>
            <a href="{{ route('cbd.reports.budget-analysis') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('cbd.reports.budget-analysis') ? 'active' : '' }}">
                <i data-lucide="pie-chart" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Budget Analysis</span>
            </a>
        </li>
    </ul>
</div>

