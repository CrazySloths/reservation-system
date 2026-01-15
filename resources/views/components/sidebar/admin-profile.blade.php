@php
    $admin = (object) [
        'id' => session('user_id', 1),
        'name' => session('user_name', 'Admin User'),
        'email' => session('user_email', 'admin@lgu1.com'),
        'role' => 'admin'
    ];
    
    // Generate initials
    $nameParts = explode(' ', $admin->name);
    $firstName = $nameParts[0] ?? 'A';
    $lastName = end($nameParts);
    $adminInitials = strtoupper(
        substr($firstName, 0, 1) . 
        (($lastName !== $firstName) ? substr($lastName, 0, 1) : 'D')
    );
@endphp

<!-- Admin Profile Section with Expandable Details -->
<div class="border-b border-lgu-stroke">
    <!-- Compact Profile Header (Collapsed State) -->
    <div id="profile-compact" class="transition-all duration-300">
        <button onclick="toggleProfileExpanded()" class="w-full p-gr-md flex items-center justify-between hover:bg-lgu-stroke/30 transition-all duration-300 group">
            <div class="flex items-center gap-gr-sm">
                <!-- Small Avatar -->
                <div class="w-10 h-10 bg-lgu-highlight rounded-full flex items-center justify-center shadow-md border-2 border-lgu-button transition-transform duration-300 group-hover:scale-110 flex-shrink-0">
                    <span class="text-lgu-button-text font-bold text-base">{{ $adminInitials }}</span>
                </div>
                
                <!-- Name and Email Label -->
                <div class="text-left min-w-0">
                    <h3 class="text-white font-semibold text-small leading-tight truncate">{{ $admin->name }}</h3>
                    <p class="text-gray-400 text-caption leading-tight truncate">{{ $admin->email }}</p>
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
        <button onclick="toggleProfileExpanded()" class="w-full px-gr-lg pb-gr-lg pt-gr-md text-center hover:bg-lgu-stroke/20 transition-all duration-300 rounded-lg">
            <!-- Large Centered Admin Avatar -->
            <div class="w-24 h-24 bg-lgu-highlight rounded-full flex items-center justify-center mx-auto mb-gr-md shadow-lg border-4 border-lgu-button">
                <span class="text-lgu-button-text font-bold text-3xl">{{ $adminInitials }}</span>
            </div>
            
            <!-- Full Profile Information -->
            <div class="space-y-gr-xs mb-gr-md">
                <h3 class="text-white font-bold text-body leading-tight">{{ $admin->name }}</h3>
                <p class="text-gray-300 text-small break-all">{{ $admin->email }}</p>
                
                <!-- Admin Role Badge -->
                <div class="flex items-center justify-center mt-gr-xs">
                    <div class="flex items-center px-gr-sm py-gr-xs rounded-full bg-red-900/40">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-check text-red-400 mr-2">
                            <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/>
                            <path d="m9 12 2 2 4-4"/>
                        </svg>
                        <span class="text-red-400 text-caption font-semibold">Administrator</span>
                    </div>
                </div>
            </div>
        </button>
    </div>
</div>

