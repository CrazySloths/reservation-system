<!-- Super Admin Sidebar with Golden Ratio Width -->
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

        <!-- Super Admin Profile Section with Expandable Details -->
        <div class="border-b border-lgu-stroke">
            @php
                $admin = (object) [
                    'id' => session('user_id', 1),
                    'name' => session('user_name', 'Super Administrator'),
                    'email' => session('user_email', 'superadmin@lgu1.com'),
                    'role' => 'super admin'
                ];
                
                // Generate admin initials
                $nameParts = explode(' ', $admin->name);
                $firstName = $nameParts[0] ?? 'S';
                $lastName = end($nameParts);
                $adminInitials = strtoupper(
                    substr($firstName, 0, 1) . 
                    (($lastName !== $firstName) ? substr($lastName, 0, 1) : 'A')
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
                        
                        <!-- Super Admin Role Badge -->
                        <div class="flex items-center justify-center mt-3">
                            <div class="flex items-center px-4 py-2 rounded-full bg-purple-900/40">
                                <svg class="w-4 h-4 text-purple-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-purple-400 text-xs font-semibold">Super Administrator</span>
                            </div>
                        </div>
                    </div>
                </button>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 overflow-y-auto overflow-x-hidden py-4">
            <!-- Dashboard -->
            <div class="px-4 mb-6">
                <h4 class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-3">Main</h4>
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('superadmin.dashboard') }}" class="sidebar-link flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                            </svg>
                            Dashboard
                        </a>
                    </li>
                </ul>
            </div>

            <!-- User Management -->
            <div class="px-4 mb-6">
                <h4 class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-3">User Management</h4>
                <ul class="space-y-1">
                    <li>
                        <a href="#users" class="sidebar-link flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                            </svg>
                            All Users
                        </a>
                    </li>
                    <li>
                        <a href="#admins" class="sidebar-link flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                            Admin Accounts
                        </a>
                    </li>
                </ul>
            </div>

            <!-- System Management -->
            <div class="px-4 mb-6">
                <h4 class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-3">System</h4>
                <ul class="space-y-1">
                    <li>
                        <a href="#system-settings" class="sidebar-link flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                            </svg>
                            System Settings
                        </a>
                    </li>
                    <li>
                        <a href="#reports" class="sidebar-link flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                            </svg>
                            Global Reports
                        </a>
                    </li>
                    <li>
                        <a href="#audit-logs" class="sidebar-link flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                            </svg>
                            Audit Logs
                        </a>
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

    <!-- Sidebar JavaScript (same as admin) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('admin-sidebar');
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const sidebarClose = document.getElementById('sidebar-close');
            const sidebarOverlay = document.getElementById('sidebar-overlay');

            // Toggle sidebar
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('-translate-x-full');
                    sidebarOverlay.classList.toggle('hidden');
                });
            }

            // Close sidebar
            if (sidebarClose) {
                sidebarClose.addEventListener('click', function() {
                    sidebar.classList.add('-translate-x-full');
                    sidebarOverlay.classList.add('hidden');
                });
            }

            // Close sidebar on overlay click
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.add('-translate-x-full');
                    this.classList.add('hidden');
                });
            }
        });

        // --- Profile Expand/Collapse Toggle with Animations ---
        let profileExpanded = false;
        let isAnimating = false;
        
        function toggleProfileExpanded() {
            if (isAnimating) return;
            
            const compactProfile = document.getElementById('profile-compact');
            const expandedDetails = document.getElementById('profile-expanded-details');
            
            if (!compactProfile || !expandedDetails) return;
            
            isAnimating = true;
            profileExpanded = !profileExpanded;
            
            if (profileExpanded) {
                compactProfile.style.opacity = '1';
                compactProfile.style.transform = 'translateY(0)';
                
                requestAnimationFrame(() => {
                    compactProfile.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
                    compactProfile.style.opacity = '0';
                    compactProfile.style.transform = 'translateY(-10px)';
                });
                
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
                expandedDetails.style.opacity = '1';
                expandedDetails.style.transform = 'translateY(0)';
                
                requestAnimationFrame(() => {
                    expandedDetails.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
                    expandedDetails.style.opacity = '0';
                    expandedDetails.style.transform = 'translateY(10px)';
                });
                
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
        
        window.toggleProfileExpanded = toggleProfileExpanded;

        // Settings Dropdown
        const settingsButton = document.getElementById('settings-button');
        const settingsDropdown = document.getElementById('settings-dropdown');

        if (settingsButton) {
            settingsButton.addEventListener('click', function(event) {
                event.stopPropagation();
                settingsDropdown.classList.toggle('hidden');
            });
        }

        window.addEventListener('click', function(event) {
            if (settingsDropdown && !settingsDropdown.classList.contains('hidden') && !settingsButton.contains(event.target)) {
                settingsDropdown.classList.add('hidden');
            }
        });

        function confirmAdminLogout() {
            if (confirm('Are you sure you want to logout?')) {
                document.getElementById('adminLogoutForm').submit();
            }
        }

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
                background: transparent;
            }
            
            #admin-sidebar nav::-webkit-scrollbar-thumb {
                background: #faae2b;
                border-radius: 4px;
            }
            
            #admin-sidebar nav::-webkit-scrollbar-thumb:hover {
                background: #e09900;
            }
        `;
        document.head.appendChild(style);

        // Set active menu item based on current URL
        function setActiveLink() {
            const currentPath = window.location.pathname;
            const currentUrl = window.location.href;
            const sidebarLinks = document.querySelectorAll('.sidebar-link');
            
            // Remove active from all links first
            sidebarLinks.forEach(link => {
                link.classList.remove('active');
            });
            
            let activeFound = false;
            
            // Check links in order of specificity (most specific first)
            sidebarLinks.forEach(link => {
                const linkHref = link.getAttribute('href');
                
                if (!linkHref || linkHref === '#' || activeFound) return;
                
                // Check for specific route matches
                if (linkHref.includes('users') && currentPath.includes('users')) {
                    link.classList.add('active');
                    activeFound = true;
                }
                else if (linkHref.includes('admins') && currentPath.includes('admins')) {
                    link.classList.add('active');
                    activeFound = true;
                }
                else if (linkHref.includes('system-settings') && currentPath.includes('system-settings')) {
                    link.classList.add('active');
                    activeFound = true;
                }
                else if (linkHref.includes('reports') && currentPath.includes('reports')) {
                    link.classList.add('active');
                    activeFound = true;
                }
                else if (linkHref.includes('audit-logs') && currentPath.includes('audit')) {
                    link.classList.add('active');
                    activeFound = true;
                }
                else if (currentUrl === linkHref || currentPath === linkHref) {
                    link.classList.add('active');
                    activeFound = true;
                }
                else if ((linkHref.includes('/superadmin/dashboard') || linkHref.includes('superadmin.dashboard')) && currentPath === '/superadmin/dashboard') {
                    // Super Admin Dashboard should only be active if no other specific match was found
                    if (!activeFound) {
                        link.classList.add('active');
                        activeFound = true;
                    }
                }
            });
        }

        // Set active link on page load
        setActiveLink();
    </script>

