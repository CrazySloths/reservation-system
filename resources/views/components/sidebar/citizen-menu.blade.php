<!-- MAIN SECTION -->
<div class="px-gr-md mb-gr-lg">
    <h4 class="text-gray-400 text-caption font-semibold uppercase tracking-wider mb-gr-xs">Main</h4>
    <ul class="space-y-gr-xs">
        <li>
            <a href="{{ route('citizen.dashboard') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('citizen.dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Dashboard</span>
            </a>
        </li>
    </ul>
</div>

<!-- BOOKING MANAGEMENT SUBMODULE -->
<div class="px-gr-md mb-gr-lg">
    <h4 class="text-gray-400 text-caption font-semibold uppercase tracking-wider mb-gr-xs">Bookings</h4>
    <ul class="space-y-gr-xs">
        <li>
            <a href="{{ route('citizen.browse-facilities') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('citizen.browse-facilities') ? 'active' : '' }}">
                <i data-lucide="calendar-plus" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Book Facility</span>
            </a>
        </li>
        <li>
            <a href="{{ route('citizen.reservations') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('citizen.reservations') ? 'active' : '' }}">
                <i data-lucide="calendar-check" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>My Reservations</span>
            </a>
        </li>
        <li>
            <a href="{{ route('citizen.reservation.history') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('citizen.reservation.history') ? 'active' : '' }}">
                <i data-lucide="history" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Booking History</span>
            </a>
        </li>
        <li>
            <a href="{{ route('citizen.conflicts.index') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('citizen.conflicts.*') ? 'active' : '' }}">
                <i data-lucide="alert-triangle" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Booking Conflicts</span>
            </a>
        </li>
    </ul>
</div>

<!-- FACILITIES BROWSE SUBMODULE -->
<div class="px-gr-md mb-gr-lg">
    <h4 class="text-gray-400 text-caption font-semibold uppercase tracking-wider mb-gr-xs">Facilities</h4>
    <ul class="space-y-gr-xs">
        <li>
            <a href="{{ route('citizen.facility-calendar') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('citizen.facility-calendar') ? 'active' : '' }}">
                <i data-lucide="calendar-days" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Availability Calendar</span>
            </a>
        </li>
        <li>
            <a href="{{ route('citizen.reviews.index') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('citizen.reviews.*') ? 'active' : '' }}">
                <i data-lucide="star" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>My Reviews</span>
            </a>
        </li>
        <li>
            <a href="#" onclick="showComingSoon('Browse All Facilities'); return false;" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 opacity-60">
                <i data-lucide="building-2" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Browse All</span>
                <span class="ml-auto text-xs bg-gray-500 text-white px-2 py-0.5 rounded-full">Soon</span>
            </a>
        </li>
        <li>
            <a href="#" onclick="showComingSoon('Favorite Facilities'); return false;" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 opacity-60">
                <i data-lucide="heart" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Favorites</span>
                <span class="ml-auto text-xs bg-gray-500 text-white px-2 py-0.5 rounded-full">Soon</span>
            </a>
        </li>
    </ul>
</div>

<!-- PAYMENTS SUBMODULE -->
<div class="px-gr-md mb-gr-lg">
    <h4 class="text-gray-400 text-caption font-semibold uppercase tracking-wider mb-gr-xs">Payments</h4>
    <ul class="space-y-gr-xs">
        <li>
            <a href="{{ route('citizen.payment-slips') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('citizen.payment-slips') ? 'active' : '' }}">
                <i data-lucide="file-text" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Payment Slips</span>
            </a>
        </li>
        <li>
            <a href="{{ route('citizen.payment-methods.index') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('citizen.payment-methods.*') ? 'active' : '' }}">
                <i data-lucide="credit-card" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Payment Methods</span>
            </a>
        </li>
        <li>
            <a href="{{ route('citizen.transactions.index') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('citizen.transactions.*') ? 'active' : '' }}">
                <i data-lucide="receipt" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Transaction History</span>
            </a>
        </li>
    </ul>
</div>

<!-- COMMUNITY SUBMODULE -->
<div class="px-gr-md mb-gr-lg">
    <h4 class="text-gray-400 text-caption font-semibold uppercase tracking-wider mb-gr-xs">Community</h4>
    <ul class="space-y-gr-xs">
        <li>
            <a href="{{ route('citizen.bulletin') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('citizen.bulletin') ? 'active' : '' }}">
                <i data-lucide="layout-grid" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Bulletin Board</span>
            </a>
        </li>
        <li>
            <a href="#" onclick="showComingSoon('Events & News'); return false;" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 opacity-60">
                <i data-lucide="megaphone" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Events & News</span>
                <span class="ml-auto text-xs bg-gray-500 text-white px-2 py-0.5 rounded-full">Soon</span>
            </a>
        </li>
    </ul>
</div>

<!-- SUPPORT SUBMODULE -->
<div class="px-gr-md mb-gr-lg">
    <h4 class="text-gray-400 text-caption font-semibold uppercase tracking-wider mb-gr-xs">Support</h4>
    <ul class="space-y-gr-xs">
        <li>
            <a href="#" onclick="showComingSoon('Help Center'); return false;" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 opacity-60">
                <i data-lucide="help-circle" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Help Center</span>
                <span class="ml-auto text-xs bg-gray-500 text-white px-2 py-0.5 rounded-full">Soon</span>
            </a>
        </li>
        <li>
            <a href="#" onclick="showComingSoon('Contact Us'); return false;" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 opacity-60">
                <i data-lucide="message-circle" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Contact Us</span>
                <span class="ml-auto text-xs bg-gray-500 text-white px-2 py-0.5 rounded-full">Soon</span>
            </a>
        </li>
    </ul>
</div>

<!-- ACCOUNT SUBMODULE -->
<div class="px-gr-md mb-gr-lg">
    <h4 class="text-gray-400 text-caption font-semibold uppercase tracking-wider mb-gr-xs">Account</h4>
    <ul class="space-y-gr-xs">
        <li>
            <a href="{{ route('citizen.profile') }}" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('citizen.profile') ? 'active' : '' }}">
                <i data-lucide="user-circle" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Profile Settings</span>
            </a>
        </li>
        <li>
            <a href="#" onclick="showComingSoon('Security Settings'); return false;" class="sidebar-link flex items-center px-gr-sm py-gr-xs text-small font-medium rounded-lg transition-colors duration-200 opacity-60">
                <i data-lucide="shield" class="w-5 h-5 mr-gr-xs flex-shrink-0"></i>
                <span>Security</span>
                <span class="ml-auto text-xs bg-gray-500 text-white px-2 py-0.5 rounded-full">Soon</span>
            </a>
        </li>
    </ul>
</div>
