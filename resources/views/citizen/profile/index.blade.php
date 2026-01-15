@extends('layouts.citizen')

@section('title', 'My Profile')
@section('page-title', 'My Profile')
@section('page-subtitle', 'Manage your account information and settings')

@section('page-content')
<div class="space-y-6">
    <!-- Profile Header Card -->
    <div class="bg-lgu-headline rounded-xl shadow-lg p-8 text-white">
        <div class="flex flex-col md:flex-row items-center gap-6">
            <!-- Avatar -->
            <div class="relative group">
                <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-white shadow-xl">
                    @if($user->avatar_path)
                        <img id="avatarPreview" src="{{ asset('storage/' . $user->avatar_path) }}" alt="Avatar" class="w-full h-full object-cover">
                    @else
                        <div id="avatarPreview" class="w-full h-full bg-lgu-highlight flex items-center justify-center text-4xl font-bold">
                            {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <button onclick="document.getElementById('avatarInput').click()" 
                        class="absolute bottom-0 right-0 w-10 h-10 bg-lgu-highlight text-white rounded-full flex items-center justify-center shadow-lg hover:bg-yellow-500 transition-all cursor-pointer group-hover:scale-110">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/>
                    </svg>
                </button>
                <input type="file" id="avatarInput" accept="image/*" class="hidden" onchange="uploadAvatar(this)">
            </div>

            <!-- User Info -->
            <div class="flex-1 text-center md:text-left">
                <h1 class="text-3xl md:text-4xl font-bold mb-2">
                    {{ $user->first_name }} {{ $user->middle_name ? substr($user->middle_name, 0, 1) . '.' : '' }} {{ $user->last_name }}
                </h1>
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mb-3">
                    @if($user->city_name)
                        <span class="px-4 py-1.5 bg-white/20 backdrop-blur-sm rounded-full text-sm font-semibold flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>
                            </svg>
                            {{ $user->city_name }} Resident
                        </span>
                    @endif
                    <span class="px-4 py-1.5 bg-white/20 backdrop-blur-sm rounded-full text-sm font-semibold flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/>
                        </svg>
                        Member since {{ \Carbon\Carbon::parse($user->created_at)->format('M Y') }}
                    </span>
                </div>
                <p class="text-white/90 flex items-center justify-center md:justify-start gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                    </svg>
                    {{ $user->email }}
                </p>
            </div>

            <!-- Stats Quick View -->
            <div class="flex gap-4">
                <div class="text-center">
                    <div class="text-3xl font-bold">{{ $stats['total_bookings'] }}</div>
                    <div class="text-sm text-white/80">Total Bookings</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold">{{ $stats['active_bookings'] }}</div>
                    <div class="text-sm text-white/80">Active</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Personal Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Personal Information Card -->
            <div class="bg-white shadow-lg rounded-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-lgu-button">
                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                        </svg>
                        Personal Information
                    </h2>
                    <button id="editProfileBtn" onclick="enableEdit()" 
                            class="px-4 py-2 bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:bg-lgu-highlight transition-all cursor-pointer flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/>
                        </svg>
                        Edit Profile
                    </button>
                </div>

                <form id="profileForm" class="space-y-6">
                    @csrf
                    
                    <!-- STEP 1: Account Information (From Registration) -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                            </svg>
                            Account Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-2">Username</label>
                                <input type="text" value="{{ $user->username }}" disabled
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-100 cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-2">Email</label>
                                <input type="email" name="email" value="{{ $user->email }}" disabled
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-lgu-button focus:border-transparent transition-all disabled:bg-gray-100 disabled:cursor-not-allowed">
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: Personal Information (From Registration) -->
                    <div class="border-t-2 border-gray-200 pt-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                            </svg>
                            Personal Information
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-2">Full Name</label>
                                <input type="text" value="{{ $user->full_name }}" disabled
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-100 cursor-not-allowed">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-2">Birthdate</label>
                                    <input type="date" value="{{ $user->birthdate }}" disabled
                                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-100 cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-2">Gender</label>
                                    <input type="text" value="{{ ucfirst($user->gender ?? 'Not specified') }}" disabled
                                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-100 cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-2">Mobile Number</label>
                                    <input type="text" name="mobile_number" value="{{ $user->mobile_number }}" disabled
                                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-lgu-button focus:border-transparent transition-all disabled:bg-gray-100 disabled:cursor-not-allowed">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-2">Civil Status</label>
                                    <input type="text" value="{{ ucfirst($user->civil_status ?? 'Not specified') }}" disabled
                                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-100 cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-2">Nationality</label>
                                    <input type="text" value="{{ $user->nationality ?? 'Filipino' }}" disabled
                                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-100 cursor-not-allowed">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 3: Address Information (From Registration) -->
                    <div class="border-t-2 border-gray-200 pt-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>
                            </svg>
                            Address Information
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-2">Current Residential Address</label>
                                <textarea rows="2" disabled
                                          class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-100 cursor-not-allowed">{{ $user->current_address ?? 'Not specified' }}</textarea>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-2">Region</label>
                                    <input type="text" value="{{ $user->region_name ?? 'Not specified' }}" disabled
                                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-100 cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-2">Province</label>
                                    <input type="text" value="{{ $user->province_name ?? 'Not specified' }}" disabled
                                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-100 cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-2">City/Municipality</label>
                                    <input type="text" value="{{ $user->city_name ?? 'Not specified' }}" disabled
                                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-100 cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-2">District</label>
                                    <input type="text" value="{{ $user->district_name ?? 'Not specified' }}" disabled
                                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-100 cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-2">Barangay</label>
                                    <input type="text" value="{{ $user->barangay_name ?? 'Not specified' }}" disabled
                                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-100 cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-2">Zip Code</label>
                                    <input type="text" value="{{ $user->zip_code ?? 'Not specified' }}" disabled
                                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-100 cursor-not-allowed">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 4: ID Verification (From Registration) -->
                    <div class="border-t-2 border-gray-200 pt-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="20" height="14" x="2" y="5" rx="2"/><path d="M2 10h20"/>
                            </svg>
                            ID Verification
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-2">Valid ID Type</label>
                                <input type="text" value="{{ $user->valid_id_type ?? 'Not specified' }}" disabled
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-100 cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-2">ID Verification Status</label>
                                <input type="text" value="{{ ucfirst($user->id_verification_status ?? 'Pending') }}" disabled
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-100 cursor-not-allowed">
                            </div>
                        </div>
                        @if($user->valid_id_front_image || $user->valid_id_back_image || $user->selfie_with_id_image)
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                            @if($user->valid_id_front_image)
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Valid ID - Front Side</label>
                                <img src="{{ asset($user->valid_id_front_image) }}" alt="ID Front" class="w-full h-48 object-cover rounded-lg border-2 border-gray-200 cursor-pointer hover:border-lgu-button transition-all" onclick="window.open('{{ asset($user->valid_id_front_image) }}', '_blank')">
                            </div>
                            @endif
                            @if($user->valid_id_back_image)
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Valid ID - Back Side</label>
                                <img src="{{ asset($user->valid_id_back_image) }}" alt="ID Back" class="w-full h-48 object-cover rounded-lg border-2 border-gray-200 cursor-pointer hover:border-lgu-button transition-all" onclick="window.open('{{ asset($user->valid_id_back_image) }}', '_blank')">
                            </div>
                            @endif
                            @if($user->selfie_with_id_image)
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Selfie with ID</label>
                                <img src="{{ asset($user->selfie_with_id_image) }}" alt="Selfie" class="w-full h-48 object-cover rounded-lg border-2 border-gray-200 cursor-pointer hover:border-lgu-button transition-all" onclick="window.open('{{ asset($user->selfie_with_id_image) }}', '_blank')">
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>

                    <div id="editActions" class="hidden flex gap-3">
                        <button type="submit" 
                                class="flex-1 px-6 py-3 bg-lgu-button text-lgu-button-text font-bold rounded-lg hover:bg-lgu-highlight transition-all cursor-pointer">
                            Save Changes
                        </button>
                        <button type="button" onclick="cancelEdit()"
                                class="px-6 py-3 bg-gray-200 text-gray-700 font-bold rounded-lg hover:bg-gray-300 transition-all cursor-pointer">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

            <!-- Change Password Card -->
            <div class="bg-white shadow-lg rounded-xl p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-lgu-button">
                        <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    Change Password
                </h2>

                <form id="passwordForm" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Current Password</label>
                        <input type="password" name="current_password" required
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-lgu-button focus:border-transparent transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">New Password</label>
                        <input type="password" name="new_password" required
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-lgu-button focus:border-transparent transition-all">
                        <p class="mt-1 text-xs text-gray-600">Must be at least 8 characters long</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" required
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-lgu-button focus:border-transparent transition-all">
                    </div>

                    <button type="submit" 
                            class="w-full px-6 py-3 bg-lgu-headline text-white font-bold rounded-lg hover:bg-opacity-90 transition-all cursor-pointer">
                        Update Password
                    </button>
                </form>
            </div>
        </div>

        <!-- Right Column - Stats & Activity -->
        <div class="space-y-6">
            <!-- Account Statistics Card -->
            <div class="bg-white shadow-lg rounded-xl p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Account Statistics</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                                    <rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Total Bookings</p>
                                <p class="text-2xl font-bold text-blue-700">{{ $stats['total_bookings'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Completed</p>
                                <p class="text-2xl font-bold text-green-700">{{ $stats['completed_bookings'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                                    <line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Total Spent</p>
                                <p class="text-2xl font-bold text-purple-700">₱{{ number_format($stats['total_spent'], 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Card -->
            <div class="bg-white shadow-lg rounded-xl p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Recent Activity</h3>
                @if($recentActivity->isEmpty())
                    <p class="text-gray-600 text-center py-8">No recent activity</p>
                @else
                    <div class="space-y-3">
                        @foreach($recentActivity as $activity)
                            <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="w-8 h-8 bg-lgu-button rounded-full flex-shrink-0 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                                        <rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $activity->facility_name }}</p>
                                    <p class="text-xs text-gray-600">{{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Profile editing
function enableEdit() {
    const form = document.getElementById('profileForm');
    const inputs = form.querySelectorAll('input[name], textarea[name]');
    inputs.forEach(input => {
        if (input.name !== 'city') {
            input.disabled = false;
        }
    });
    document.getElementById('editProfileBtn').classList.add('hidden');
    document.getElementById('editActions').classList.remove('hidden');
}

function cancelEdit() {
    location.reload();
}

// Profile update
document.getElementById('profileForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('{{ route('citizen.profile.update') }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
                confirmButtonColor: '#0f5b3a'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message,
                confirmButtonColor: '#0f5b3a'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Network Error',
            text: 'An unexpected error occurred.',
            confirmButtonColor: '#0f5b3a'
        });
    });
});

// Password change
document.getElementById('passwordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('{{ route('citizen.profile.password') }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Password Changed!',
                text: data.message,
                confirmButtonColor: '#0f5b3a'
            }).then(() => {
                this.reset();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message,
                confirmButtonColor: '#0f5b3a'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Network Error',
            text: 'An unexpected error occurred.',
            confirmButtonColor: '#0f5b3a'
        });
    });
});

// Avatar upload
function uploadAvatar(input) {
    if (!input.files || !input.files[0]) return;
    
    const file = input.files[0];
    
    // Validate file size (2MB)
    if (file.size > 2048 * 1024) {
        Swal.fire({
            icon: 'error',
            title: 'File Too Large',
            text: 'Avatar image must be less than 2MB.',
            confirmButtonColor: '#0f5b3a'
        });
        return;
    }
    
    // Preview image
    const reader = new FileReader();
    reader.onload = function(e) {
        const preview = document.getElementById('avatarPreview');
        preview.innerHTML = `<img src="${e.target.result}" alt="Avatar" class="w-full h-full object-cover">`;
    };
    reader.readAsDataURL(file);
    
    // Upload
    const formData = new FormData();
    formData.append('avatar', file);
    
    fetch('{{ route('citizen.profile.avatar') }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Avatar Updated!',
                text: data.message,
                confirmButtonColor: '#0f5b3a'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Upload Failed',
                text: data.message,
                confirmButtonColor: '#0f5b3a'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Upload Failed',
            text: 'An unexpected error occurred.',
            confirmButtonColor: '#0f5b3a'
        });
    });
}
</script>
@endpush
@endsection

