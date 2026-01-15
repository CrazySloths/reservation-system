@extends('layouts.master')

@section('content')
<!-- CBD Sidebar -->
<div id="cbd-sidebar" class="fixed left-0 top-0 h-full w-72 bg-lgu-headline shadow-2xl transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out z-50 overflow-hidden flex flex-col no-print">
    <!-- Sidebar Header -->
    <div class="flex items-center justify-between p-gr-md border-b border-lgu-stroke">
        <div class="flex items-center gap-gr-sm">
            <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-lgu-highlight">
                <img src="{{ asset('assets/images/logo.png') }}" alt="LGU Logo" class="w-full h-full object-cover">
            </div>
            <div>
                <h2 class="text-white font-bold text-small">Local Government Unit</h2>
                <p class="text-gray-300 text-caption">LGU1</p>
            </div>
        </div>
        <div class="relative">
            <button id="cbd-settings-button" class="p-2 text-white">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
                </svg>
            </button>
            <div id="cbd-settings-dropdown" class="hidden absolute right-0 mt-3 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                <a href="#" class="block px-4 py-2 text-sm text-lgu-paragraph hover:bg-lgu-bg">Help & Support</a>
                <div class="border-t border-gray-200 my-1"></div>
                <form method="POST" action="{{ route('logout') }}" class="block" id="cbdLogoutForm">
                    @csrf
                    <button type="button" onclick="confirmCbdLogout()" class="w-full text-left px-4 py-2 text-sm text-lgu-tertiary hover:bg-lgu-bg">Logout</button>
                </form>
            </div>
        </div>
        <!-- Close button for mobile -->
        <button id="cbd-sidebar-close" class="lg:hidden text-white hover:text-lgu-highlight">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- CBD Profile Section with Expandable Details -->
    <div class="border-b border-lgu-stroke">
        @php
            $cbd = (object) [
                'id' => session('user_id', 1),
                'name' => session('user_name', 'CBD User'),
                'email' => session('user_email', 'cbd@lgu1.com'),
                'role' => 'cbd'
            ];
            
            // Generate initials
            $nameParts = explode(' ', $cbd->name);
            $firstName = $nameParts[0] ?? 'C';
            $lastName = end($nameParts);
            $cbdInitials = strtoupper(
                substr($firstName, 0, 1) . 
                (($lastName !== $firstName) ? substr($lastName, 0, 1) : 'B')
            );
        @endphp
        
        <!-- Compact Profile Header (Collapsed State) -->
        <div id="profile-compact" class="transition-all duration-300">
            <button onclick="toggleProfileExpanded()" class="w-full p-gr-md flex items-center justify-between hover:bg-lgu-stroke/30 transition-all duration-300 group">
                <div class="flex items-center gap-gr-sm">
                    <!-- Small Avatar -->
                    <div class="w-10 h-10 bg-lgu-highlight rounded-full flex items-center justify-center shadow-md border-2 border-lgu-button transition-transform duration-300 group-hover:scale-110">
                        <span class="text-lgu-button-text font-bold text-body">{{ $cbdInitials }}</span>
                    </div>
                    
                    <!-- Name and Email Label -->
                    <div class="text-left">
                        <h3 class="text-white font-semibold text-small leading-tight">{{ $cbd->name }}</h3>
                        <p class="text-gray-400 text-caption">{{ $cbd->email }}</p>
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
                <!-- Large Centered CBD Avatar -->
                <div class="w-24 h-24 bg-lgu-highlight rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg border-4 border-lgu-button">
                    <span class="text-lgu-button-text font-bold text-3xl">{{ $cbdInitials }}</span>
                </div>
                
                <!-- Full Profile Information -->
                <div class="space-y-2 mb-4">
                    <h3 class="text-white font-bold text-lg leading-tight">{{ $cbd->name }}</h3>
                    <p class="text-gray-300 text-sm break-all">{{ $cbd->email }}</p>
                    
                    <!-- CBD Role Badge -->
                    <div class="flex items-center justify-center mt-3">
                        <div class="flex items-center px-4 py-2 rounded-full bg-purple-900/40">
                            <svg class="w-4 h-4 text-purple-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-purple-400 text-xs font-semibold">CBD Staff</span>
                        </div>
                    </div>
                </div>
            </button>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 overflow-y-auto overflow-x-hidden py-gr-md">
        @include('components.sidebar.cbd-menu')
    </nav>
</div>

<!-- Mobile Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden no-print"></div>

<!-- Mobile Sidebar Toggle Button -->
<button id="sidebar-toggle" class="fixed top-4 left-4 z-50 lg:hidden bg-lgu-headline text-white p-2 rounded-lg shadow-lg hover:bg-lgu-stroke transition-colors duration-200 no-print">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
    </svg>
</button>

<!-- Main Content -->
<div class="lg:ml-72">
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40 no-print">
        @include('components.header.cbd-header')
    </header>

    <main class="min-h-screen bg-gray-50">
        <div class="container mx-auto px-gr-lg py-gr-xl">
            @yield('page-content')
        </div>
    </main>

    <footer class="bg-white border-t border-gray-200 py-gr-md px-gr-lg no-print">
        <div class="flex justify-between items-center text-small text-gray-600">
            <p>&copy; {{ date('Y') }} LGU Facility Reservation System. All rights reserved.</p>
            <p>City Budget Department Portal</p>
        </div>
    </footer>
</div>

@include('components.sidebar.cbd-script')

@stack('scripts')

@endsection

