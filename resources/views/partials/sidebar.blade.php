<!-- Admin Sidebar with Golden Ratio Width (~23.6% of viewport) -->
    <div id="admin-sidebar" class="fixed left-0 top-0 h-full w-72 bg-lgu-headline shadow-2xl transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out z-50 overflow-hidden flex flex-col">
        <!-- Sidebar Header -->
        <div class="flex items-center justify-between p-4 border-b border-lgu-stroke">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-lgu-highlight">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="LGU Logo" class="w-full h-full object-cover">
                </div>
                <div>
                    <h2 class="text-white font-bold text-sm">Local Government Unit</h2>
                    <p class="text-gray-300 text-xs">LGU1</p>
                </div>
            </div>
            <div class="relative">
                <button id="settings-button" class="p-2 text-lgu-paragraph text-white">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
                    </svg>
                </button>
                <div id="settings-dropdown" class="hidden absolute right-0 mt-3 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                    <a href="#" class="block px-4 py-2 text-sm text-lgu-paragraph hover:bg-lgu-bg">Help & Support</a>
                    <a href="#" class="block px-4 py-2 text-sm text-lgu-paragraph hover:bg-lgu-bg">Account & Settings</a>
                    <div class="border-t border-gray-200 my-1"></div>
                    <form method="POST" action="{{ route('logout') }}" class="block" id="adminLogoutForm">
                        @csrf
                        <button type="button" onclick="confirmAdminLogout()" class="w-full text-left px-4 py-2 text-sm text-lgu-tertiary hover:bg-lgu-bg">Logout</button>
                    </form>
                </div>
            </div>
            <!-- Close button for mobile -->
            <button id="sidebar-close" class="lg:hidden text-white hover:text-lgu-highlight">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Admin Profile Section with Expandable Details -->
        <div class="border-b border-lgu-stroke">
            @php
                // Static Admin Authentication (Bypass database issues)
                $admin = null;
                
                // Try Laravel Auth first (if working)
                try {
                    if (class_exists('Illuminate\Support\Facades\Auth')) {
                        $admin = Auth::user();
                    }
                } catch (Exception $e) {
                    // Laravel Auth failed, continue to static auth
                }
                
                // If Laravel Auth fails, use static authentication from session
                if (!$admin) {
                    // Check session for static admin (set by AdminAuthMiddleware)
                    if (isset($_SESSION['static_admin_user'])) {
                        $adminData = $_SESSION['static_admin_user'];
                        $admin = (object) $adminData;
                    }
                    // Fallback: Create admin from URL parameters or session
                    elseif (request()->has('user_id') || request()->has('username') || session('user_id')) {
                        $userId = request()->get('user_id', session('user_id', 1));
                        $username = request()->get('username', session('user_name', 'Administrator'));
                        
                        // Extract clean username (remove extra chars)
                        $cleanUsername = str_replace(['Admin-facilities123', '-facilities123'], '', $username);
                        $cleanUsername = ucfirst(trim($cleanUsername, '-'));
                        if (empty($cleanUsername)) {
                            $cleanUsername = session('user_name', 'Administrator');
                        }
                        
                        $admin = (object) [
                            'id' => $userId,
                            'name' => $cleanUsername,
                            'email' => session('user_email', 'admin@lgu1.com'),
                            'role' => session('user_role', 'admin')
                        ];
                    }
                    // Final fallback: Use session data or default admin for admin routes
                    elseif (str_contains(request()->url(), '/admin/')) {
                        $admin = (object) [
                            'id' => session('user_id', 1),
                            'name' => session('user_name', 'Administrator'),
                            'email' => session('user_email', 'admin@lgu1.com'),
                            'role' => session('user_role', 'admin')
                        ];
                    }
                }
            @endphp
            
            @if($admin && (strtolower($admin->role) === 'admin' || str_contains(request()->url(), '/admin/')))
                @php
                    // Generate admin initials
                    $nameParts = explode(' ', $admin->name);
                    $firstName = $nameParts[0] ?? 'A';
                    $lastName = end($nameParts);
                    $adminInitials = strtoupper(
                        substr($firstName, 0, 1) . 
                        (($lastName !== $firstName) ? substr($lastName, 0, 1) : 'D')
                    );
                @endphp
                
                <!-- Compact Profile Header (Collapsed State) -->
                <div id="profile-compact" class="transition-all duration-300">
                    <button onclick="toggleProfileExpanded()" class="w-full p-4 flex items-center justify-between hover:bg-lgu-stroke/30 transition-all duration-300 group">
                        <div class="flex items-center space-x-3">
                            <!-- Small Avatar -->
                            <div class="w-10 h-10 bg-lgu-highlight rounded-full flex items-center justify-center shadow-md border-2 border-lgu-button transition-transform duration-300 group-hover:scale-110">
                                <span class="text-lgu-button-text font-bold text-base">{{ $adminInitials }}</span>
                            </div>
                            
                        <!-- Name and Email Label -->
                        <div class="text-left">
                            <h3 class="text-white font-semibold text-sm leading-tight">{{ $admin->name }}</h3>
                            <p class="text-gray-400 text-xs">{{ $admin->email }}</p>
                        </div>
                        </div>
                        
                        <!-- Dropdown Arrow -->
                        <svg class="w-5 h-5 text-gray-400 transition-transform duration-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
                
                <!-- Expandable Full Profile Details (Maximized State) -->
                <div id="profile-expanded-details" class="hidden transition-all duration-500 ease-in-out">
                    <button onclick="toggleProfileExpanded()" class="w-full px-6 pb-6 pt-4 text-center hover:bg-lgu-stroke/20 transition-all duration-300 rounded-lg">
                        <!-- Large Centered Admin Avatar -->
                        <div class="w-24 h-24 bg-lgu-highlight rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg border-4 border-lgu-button">
                            <span class="text-lgu-button-text font-bold text-3xl">{{ $adminInitials }}</span>
                        </div>
                        
                        <!-- Full Profile Information -->
                        <div class="space-y-2 mb-4">
                            <h3 class="text-white font-bold text-lg leading-tight">{{ $admin->name }}</h3>
                            <p class="text-gray-300 text-sm break-all">{{ $admin->email }}</p>
                            
                            <!-- Admin Role Badge -->
                            <div class="flex items-center justify-center mt-3">
                                <div class="flex items-center px-4 py-2 rounded-full bg-blue-900/40">
                                    <svg class="w-4 h-4 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-blue-400 text-xs font-semibold">{{ ucfirst($admin->role) }} Administrator</span>
                                </div>
                            </div>
                        </div>
                    </button>
                </div>
            @endif
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 overflow-y-auto overflow-x-hidden py-4">
            <!-- Dashboard -->
            <div class="px-4 mb-6">
                <h4 class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-3">Main</h4>
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="sidebar-link flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                            </svg>
                            Dashboard
                        </a>
                    </li>
                </ul>
            </div>

            <!-- SUBMODULE 1: Facility Directory and Calendar -->
            <div class="px-4 mb-6">
                <h4 class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-3">Facility Directory & Calendar</h4>
                <ul class="space-y-1">
                    <li>
                        <button class="sidebar-dropdown w-full flex items-center justify-between px-3 py-2 text-sm font-medium text-gray-300 hover:text-white hover:bg-lgu-stroke rounded-lg transition-colors duration-200">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm3 1h6v4H7V5zm6 6H7v2h6v-2z" clip-rule="evenodd"/>
                                </svg>
                                Facility Management
                            </div>
                            <svg class="w-4 h-4 transform transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                        <ul class="sidebar-submenu hidden ml-8 mt-2 space-y-1">
                            <li><a href="#browse-facilities" class="sidebar-link flex items-center px-3 py-2 text-sm text-gray-400 hover:text-white rounded-lg"><svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>Browse Facilities</a></li>
                            <li><a href="#calendar-view" class="sidebar-link flex items-center px-3 py-2 text-sm text-gray-400 hover:text-white rounded-lg"><svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/></svg>Calendar View</a></li>
                            <li><a href="#add-facility" class="sidebar-link flex items-center px-3 py-2 text-sm text-gray-400 hover:text-white rounded-lg"><svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/></svg>Add/Edit Facility</a></li>
                            <li><a href="#maintenance-logs" class="sidebar-link flex items-center px-3 py-2 text-sm text-gray-400 hover:text-white rounded-lg"><svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/></svg>Maintenance Logs</a></li>
                        </ul>
                    </li>
                </ul>
            </div>

            <!-- SUBMODULE 2: Online Booking and Approval -->
            <div class="px-4 mb-6">
                <h4 class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-3">Booking & Approval</h4>
                <ul class="space-y-1">
                    <li>
                        <button class="sidebar-dropdown w-full flex items-center justify-between px-3 py-2 text-sm font-medium text-gray-300 hover:text-white hover:bg-lgu-stroke rounded-lg transition-colors duration-200">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Approval Workflow
                            </div>
                            <svg class="w-4 h-4 transform transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                        <ul class="sidebar-submenu hidden ml-8 mt-2 space-y-1">
                            <li><a href="#pending-staff-verification" class="sidebar-link flex items-center px-3 py-2 text-sm text-gray-400 hover:text-white rounded-lg"><svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>Pending Staff Verification</a></li>
                            <li><a href="#pending-admin-approval" class="sidebar-link flex items-center px-3 py-2 text-sm text-gray-400 hover:text-white rounded-lg"><svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>Pending Admin Approval</a></li>
                            <li><a href="#approved-bookings" class="sidebar-link flex items-center px-3 py-2 text-sm text-gray-400 hover:text-white rounded-lg"><svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>Approved Bookings</a></li>
                            <li><a href="#all-reservations" class="sidebar-link flex items-center px-3 py-2 text-sm text-gray-400 hover:text-white rounded-lg"><svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/></svg>All Reservations</a></li>
                            <li><a href="#booking-history" class="sidebar-link flex items-center px-3 py-2 text-sm text-gray-400 hover:text-white rounded-lg"><svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>Booking History</a></li>
                        </ul>
                    </li>
                </ul>
            </div>

            <!-- SUBMODULE 3: Usage Fee Calculation and Payment -->
            <div class="px-4 mb-6">
                <h4 class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-3">Fee & Payment</h4>
                <ul class="space-y-1">
                    <li>
                        <button class="sidebar-dropdown w-full flex items-center justify-between px-3 py-2 text-sm font-medium text-gray-300 hover:text-white hover:bg-lgu-stroke rounded-lg transition-colors duration-200">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                                </svg>
                                Payment Management
                            </div>
                            <svg class="w-4 h-4 transform transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                        <ul class="sidebar-submenu hidden ml-8 mt-2 space-y-1">
                            <li><a href="#payment-verification" class="sidebar-link flex items-center px-3 py-2 text-sm text-gray-400 hover:text-white rounded-lg"><svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>Payment Verification</a></li>
                            <li><a href="#pricing-discounts" class="sidebar-link flex items-center px-3 py-2 text-sm text-gray-400 hover:text-white rounded-lg"><svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>Pricing & Discounts</a></li>
                            <li><a href="#revenue-reports" class="sidebar-link flex items-center px-3 py-2 text-sm text-gray-400 hover:text-white rounded-lg"><svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/></svg>Revenue Reports</a></li>
                        </ul>
                    </li>
                </ul>
            </div>

            <!-- SUBMODULE 4: Schedule Conflicts Alert -->
            <div class="px-4 mb-6">
                <h4 class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-3">Conflict Detection</h4>
                <ul class="space-y-1">
                    <li>
                        <button class="sidebar-dropdown w-full flex items-center justify-between px-3 py-2 text-sm font-medium text-gray-300 hover:text-white hover:bg-lgu-stroke rounded-lg transition-colors duration-200">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                Schedule Conflicts
                            </div>
                            <svg class="w-4 h-4 transform transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                        <ul class="sidebar-submenu hidden ml-8 mt-2 space-y-1">
                            <li><a href="#active-conflicts" class="sidebar-link flex items-center px-3 py-2 text-sm text-gray-400 hover:text-white rounded-lg"><svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>Active Conflicts</a></li>
                            <li><a href="#conflict-history" class="sidebar-link flex items-center px-3 py-2 text-sm text-gray-400 hover:text-white rounded-lg"><svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>Conflict History</a></li>
                            <li><a href="#conflict-settings" class="sidebar-link flex items-center px-3 py-2 text-sm text-gray-400 hover:text-white rounded-lg"><svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>Conflict Settings</a></li>
                        </ul>
                    </li>
                </ul>
            </div>

            <!-- SUBMODULE 5: Usage Reports and Feedback -->
            <div class="px-4 mb-6">
                <h4 class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-3">Reports & Feedback</h4>
                <ul class="space-y-1">
                    <li>
                        <button class="sidebar-dropdown w-full flex items-center justify-between px-3 py-2 text-sm font-medium text-gray-300 hover:text-white hover:bg-lgu-stroke rounded-lg transition-colors duration-200">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                                </svg>
                                Analytics & Reports
                            </div>
                            <svg class="w-4 h-4 transform transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                        <ul class="sidebar-submenu hidden ml-8 mt-2 space-y-1">
                            <li><a href="{{ route('admin.analytics') }}" class="sidebar-link flex items-center px-3 py-2 text-sm text-gray-400 hover:text-white rounded-lg"><svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"/><path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"/></svg>Analytics & Insights (AI)</a></li>
                            <li><a href="#citizen-feedback" class="sidebar-link flex items-center px-3 py-2 text-sm text-gray-400 hover:text-white rounded-lg"><svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"/><path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"/></svg>Citizen Feedback</a></li>
                            <li><a href="#usage-statistics" class="sidebar-link flex items-center px-3 py-2 text-sm text-gray-400 hover:text-white rounded-lg"><svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/></svg>Usage Statistics</a></li>
                            <li><a href="#export-reports" class="sidebar-link flex items-center px-3 py-2 text-sm text-gray-400 hover:text-white rounded-lg"><svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>Export Reports (CSV/PDF)</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden"></div>

    <!-- Mobile Sidebar Toggle Button -->
    <button id="sidebar-toggle" class="fixed top-4 left-4 z-50 lg:hidden bg-lgu-headline text-white p-2 rounded-lg shadow-lg hover:bg-lgu-stroke transition-colors duration-200">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>

    <!-- Sidebar JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('admin-sidebar');
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const sidebarClose = document.getElementById('sidebar-close');
            const sidebarOverlay = document.getElementById('sidebar-overlay');
            const dropdownButtons = document.querySelectorAll('.sidebar-dropdown');
            const sidebarLinks = document.querySelectorAll('.sidebar-link');

            // Set active link based on current URL
            function setActiveLink() {
                const currentPath = window.location.pathname;
                const currentUrl = window.location.href;
                
                // Remove active from all links first
                sidebarLinks.forEach(link => {
                    link.classList.remove('active');
                });
                
                let activeFound = false;
                
                // Check links in order of specificity (most specific first)
                sidebarLinks.forEach(link => {
                    if (activeFound) return; // Skip if we already found an active link
                    
                    const linkHref = link.getAttribute('href');
                    if (!linkHref) return;
                    
                    // Most specific matches first
                    if (linkHref.includes('reservations') && currentPath.includes('reservations')) {
                        link.classList.add('active');
                        activeFound = true;
                    }
                    else if (linkHref.includes('payment-slips') && currentPath.includes('payment')) {
                        link.classList.add('active');
                        activeFound = true;
                    }
                    else if (linkHref.includes('calendar') && currentPath.includes('calendar')) {
                        link.classList.add('active');
                        activeFound = true;
                    }
                    else if (linkHref.includes('facility') && currentPath.includes('facility')) {
                        link.classList.add('active');
                        activeFound = true;
                    }
                    else if (linkHref.includes('analytics') && currentPath.includes('analytics')) {
                        link.classList.add('active');
                        activeFound = true;
                    }
                    else if (linkHref.includes('booking') && currentPath.includes('booking')) {
                        link.classList.add('active');
                        activeFound = true;
                    }
                    else if (currentUrl === linkHref || currentPath === linkHref) {
                        link.classList.add('active');
                        activeFound = true;
                    }
                    else if ((linkHref.includes('/admin/dashboard') || linkHref.includes('admin.dashboard')) && currentPath === '/admin/dashboard') {
                        // Admin Dashboard should only be active if no other specific match was found
                        if (!activeFound) {
                            link.classList.add('active');
                            activeFound = true;
                        }
                    }
                });
            }

            // Set active link on page load
            setActiveLink();

            // Mobile sidebar toggle functionality
            function toggleSidebar() {
                sidebar.classList.toggle('-translate-x-full');
                sidebarOverlay.classList.add('hidden');
            }

            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                sidebarOverlay.classList.add('hidden');
            }

            // Event listeners for mobile sidebar
            sidebarToggle.addEventListener('click', toggleSidebar);
            sidebarClose.addEventListener('click', closeSidebar);
            sidebarOverlay.addEventListener('click', closeSidebar);

            // Dropdown functionality
            dropdownButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const submenu = this.nextElementSibling;
                    const arrow = this.querySelector('svg:last-child');
                    
                    // Toggle submenu
                    submenu.classList.toggle('hidden');
                    
                    // Rotate arrow
                    arrow.classList.toggle('rotate-180');
                    
                    // Close other dropdowns
                    dropdownButtons.forEach(otherButton => {
                        if (otherButton !== this) {
                            const otherSubmenu = otherButton.nextElementSibling;
                            const otherArrow = otherButton.querySelector('svg:last-child');
                            otherSubmenu.classList.add('hidden');
                            otherArrow.classList.remove('rotate-180');
                        }
                    });
                });
            });

            // Active link functionality
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    
                    // Don't prevent default for actual routes - let Laravel handle navigation
                    if (href && !href.startsWith('#')) {
                        // Remove active class from all links
                        sidebarLinks.forEach(l => l.classList.remove('active'));
                        
                        // Add active class to clicked link
                        this.classList.add('active');
                        
                        // Close sidebar on mobile after clicking a link
                        if (window.innerWidth < 1024) {
                            closeSidebar();
                        }
                        
                        // Let the browser handle the navigation
                        return true;
                    } else {
                        // For hash links or non-route links, prevent default
                        e.preventDefault();
                    
                    // Remove active class from all links
                    sidebarLinks.forEach(l => l.classList.remove('active'));
                    
                    // Add active class to clicked link
                    this.classList.add('active');
                    
                    // Close sidebar on mobile after clicking a link
                    if (window.innerWidth < 1024) {
                        closeSidebar();
                    }
                    
                        console.log('Non-route link clicked:', href);
                    }
                });
            });

            // Admin SweetAlert2 logout confirmation
            window.confirmAdminLogout = function() {
                Swal.fire({
                    title: 'Sign Out?',
                    text: "You will be logged out of the administrative system.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#fa5246',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, sign me out',
                    cancelButtonText: 'Cancel',
                    background: '#ffffff',
                    customClass: {
                        title: 'text-gray-900',
                        content: 'text-gray-600',
                        confirmButton: 'bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg',
                        cancelButton: 'bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show logout success message
                        Swal.fire({
                            title: 'Signing out...',
                            text: 'Thank you for using the LGU1 Admin Portal!',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            // Submit the logout form
                            document.getElementById('adminLogoutForm').submit();
                        });
                    }
                });
            };



            // Responsive sidebar behavior
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) {
                    sidebar.classList.remove('-translate-x-full');
                    sidebarOverlay.classList.add('hidden');
                } else {
                    sidebar.classList.add('-translate-x-full');
                }
            });

            // Notification badge updates (simulate real-time updates)
            function updateNotificationBadges() {
                const badges = document.querySelectorAll('.sidebar-link span');
                badges.forEach(badge => {
                    if (badge.textContent && !isNaN(badge.textContent)) {
                        // Simulate random updates for demo purposes
                        if (Math.random() > 0.8) {
                            const currentCount = parseInt(badge.textContent);
                            badge.textContent = currentCount + Math.floor(Math.random() * 3);
                        }
                    }
                });
            }

            // Update badges every 30 seconds (for demo purposes)
            setInterval(updateNotificationBadges, 30000);

            // Smooth scrolling for anchor links
            document.addEventListener('click', function(e) {
                if (e.target.matches('a[href^="#"]')) {
                    e.preventDefault();
                    const targetId = e.target.getAttribute('href');
                    const targetElement = document.querySelector(targetId);
                    
                    if (targetElement) {
                        targetElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }
            });
        });

        // --- Profile Expand/Collapse Toggle with Animations ---
        let profileExpanded = false;
        let isAnimating = false;
        
        function toggleProfileExpanded() {
            if (isAnimating) return; // Prevent multiple clicks during animation
            
            const compactProfile = document.getElementById('profile-compact');
            const expandedDetails = document.getElementById('profile-expanded-details');
            
            if (!compactProfile || !expandedDetails) return;
            
            isAnimating = true;
            profileExpanded = !profileExpanded;
            
            if (profileExpanded) {
                // Fade out compact, then fade in expanded
                compactProfile.style.opacity = '1';
                compactProfile.style.transform = 'translateY(0)';
                
                // Start fade out
                requestAnimationFrame(() => {
                    compactProfile.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
                    compactProfile.style.opacity = '0';
                    compactProfile.style.transform = 'translateY(-10px)';
                });
                
                // After compact fades out, show expanded
                setTimeout(() => {
                    compactProfile.classList.add('hidden');
                    expandedDetails.classList.remove('hidden');
                    expandedDetails.style.opacity = '0';
                    expandedDetails.style.transform = 'translateY(10px)';
                    
                    requestAnimationFrame(() => {
                        expandedDetails.style.transition = 'opacity 0.4s ease-in, transform 0.4s ease-in';
                        expandedDetails.style.opacity = '1';
                        expandedDetails.style.transform = 'translateY(0)';
                    });
                    
                    setTimeout(() => {
                        isAnimating = false;
                    }, 400);
                }, 300);
                
            } else {
                // Fade out expanded, then fade in compact
                expandedDetails.style.opacity = '1';
                expandedDetails.style.transform = 'translateY(0)';
                
                // Start fade out
                requestAnimationFrame(() => {
                    expandedDetails.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
                    expandedDetails.style.opacity = '0';
                    expandedDetails.style.transform = 'translateY(10px)';
                });
                
                // After expanded fades out, show compact
                setTimeout(() => {
                    expandedDetails.classList.add('hidden');
                    compactProfile.classList.remove('hidden');
                    compactProfile.style.opacity = '0';
                    compactProfile.style.transform = 'translateY(-10px)';
                    
                    requestAnimationFrame(() => {
                        compactProfile.style.transition = 'opacity 0.4s ease-in, transform 0.4s ease-in';
                        compactProfile.style.opacity = '1';
                        compactProfile.style.transform = 'translateY(0)';
                    });
                    
                    setTimeout(() => {
                        isAnimating = false;
                    }, 400);
                }, 300);
            }
        }
        
        // Make function globally accessible
        window.toggleProfileExpanded = toggleProfileExpanded;

        // --- Settings Dropdown ---
        const settingsButton = document.getElementById('settings-button');
        const settingsDropdown = document.getElementById('settings-dropdown');

        if (settingsButton) {
            settingsButton.addEventListener('click', function(event) {
                event.stopPropagation();
                settingsDropdown.classList.toggle('hidden');
            });
        }

        // Close dropdown settings
        window.addEventListener('click', function(event) {
            if (settingsDropdown && !settingsDropdown.classList.contains('hidden') && !settingsButton.contains(event.target)) {
                settingsDropdown.classList.add('hidden');
            }
        });

        // CSS for active states and transitions
        const style = document.createElement('style');
        style.textContent = `
            .sidebar-link {
                color: #9CA3AF;
            }
            
            .sidebar-link:hover {
                color: #FFFFFF;
                background-color: #00332c;
            }
            
            .sidebar-link.active {
                color: #faae2b;
                background-color: #00332c;
                border-left: 3px solid #faae2b;
            }
            
            .sidebar-submenu {
                transition: all 0.3s ease-in-out;
            }
            
            .rotate-180 {
                transform: rotate(180deg);
            }
            
            /* Profile Expand Animation */
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .animate-fade-in {
                animation: fadeIn 0.5s ease-out;
            }
            
            /* Smooth transitions for profile section */
            #profile-expanded-details {
                transition: max-height 0.5s cubic-bezier(0.4, 0, 0.2, 1), 
                            opacity 0.4s ease-in-out;
            }
            
            #profile-expand-icon,
            #profile-expand-icon-compact {
                transition: transform 0.3s ease-in-out;
            }
            
            /* Hover effects for profile options */
            #profile-expanded-details a {
                transition: all 0.2s ease;
            }
            
            #profile-expanded-details a:hover {
                transform: translateX(5px);
            }
            
            #profile-expanded-details a:hover svg {
                transform: scale(1.15) rotate(5deg);
                transition: transform 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            }
            
            /* Profile sections animations */
            #profile-compact,
            #profile-expanded-details {
                will-change: opacity, transform;
            }
            
            /* Avatar pulse effect on hover */
            #profile-compact:hover .bg-lgu-highlight,
            #profile-expanded-details .bg-lgu-highlight:hover {
                animation: avatarPulse 0.6s ease-in-out;
            }
            
            @keyframes avatarPulse {
                0%, 100% {
                    transform: scale(1);
                }
                50% {
                    transform: scale(1.05);
                    box-shadow: 0 0 20px rgba(250, 174, 43, 0.6);
                }
            }
            
            /* Custom scrollbar for sidebar */
            #admin-sidebar nav::-webkit-scrollbar {
                width: 4px;
            }
            
            #admin-sidebar nav::-webkit-scrollbar-track {
                background: #00332c;
            }
            
            #admin-sidebar nav::-webkit-scrollbar-thumb {
                background: #faae2b;
                border-radius: 2px;
            }
            
            #admin-sidebar nav::-webkit-scrollbar-thumb:hover {
                background: #e09900;
            }
        `;
        document.head.appendChild(style);
    </script>

