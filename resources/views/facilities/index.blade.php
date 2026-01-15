<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Browse Facilities - LGU1 Public Facilities Reservation System</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-50">
    <!-- Header / Navigation -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-gr-md py-gr-md flex items-center justify-between">
            <div>
                <h1 class="text-h2 text-gray-900">LGU1 Public Facilities</h1>
                <p class="text-small text-gray-600">Browse available facilities for reservation</p>
            </div>
            <div class="flex items-center gap-gr-sm">
                @if(session('user_id'))
                    <a href="{{ route('citizen.dashboard') }}" class="px-gr-md py-gr-sm bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors text-small font-medium">
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-gr-md py-gr-sm bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors text-small font-medium">
                        Login to Book
                    </a>
                    <a href="{{ route('register') }}" class="px-gr-md py-gr-sm border border-primary-600 text-primary-600 rounded-lg hover:bg-primary-50 transition-colors text-small font-medium">
                        Register
                    </a>
                @endif
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-gr-md py-gr-lg">
        <!-- Search & Filters -->
        <div class="bg-white rounded-xl shadow-sm p-gr-lg mb-gr-lg">
            <form method="GET" action="{{ route('facilities.index') }}" class="space-y-gr-md">
                <!-- Search Bar -->
                <div>
                    <label for="search" class="block text-small font-medium text-gray-700 mb-gr-xs">Search Facilities</label>
                    <div class="relative">
                        <input 
                            type="text" 
                            id="search" 
                            name="search" 
                            value="{{ request('search') }}"
                            placeholder="Search by facility name..."
                            class="w-full px-gr-md py-gr-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 pl-10"
                        >
                        <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
                    </div>
                </div>

                <!-- Filters Grid -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-gr-md">
                    <!-- Location Filter -->
                    <div>
                        <label for="location_id" class="block text-small font-medium text-gray-700 mb-gr-xs">Location</label>
                        <select 
                            id="location_id" 
                            name="location_id" 
                            class="w-full px-gr-md py-gr-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        >
                            <option value="">All Locations</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>
                                    {{ $location->location_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Facility Type Filter -->
                    <div>
                        <label for="facility_type" class="block text-small font-medium text-gray-700 mb-gr-xs">Facility Type</label>
                        <select 
                            id="facility_type" 
                            name="facility_type" 
                            class="w-full px-gr-md py-gr-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        >
                            <option value="">All Types</option>
                            <option value="gymnasium" {{ request('facility_type') == 'gymnasium' ? 'selected' : '' }}>Gymnasium</option>
                            <option value="convention_center" {{ request('facility_type') == 'convention_center' ? 'selected' : '' }}>Convention Center</option>
                            <option value="function_hall" {{ request('facility_type') == 'function_hall' ? 'selected' : '' }}>Function Hall</option>
                            <option value="sports_complex" {{ request('facility_type') == 'sports_complex' ? 'selected' : '' }}>Sports Complex</option>
                            <option value="auditorium" {{ request('facility_type') == 'auditorium' ? 'selected' : '' }}>Auditorium</option>
                            <option value="meeting_room" {{ request('facility_type') == 'meeting_room' ? 'selected' : '' }}>Meeting Room</option>
                            <option value="other" {{ request('facility_type') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <!-- Capacity Filter -->
                    <div>
                        <label for="min_capacity" class="block text-small font-medium text-gray-700 mb-gr-xs">Minimum Capacity</label>
                        <input 
                            type="number" 
                            id="min_capacity" 
                            name="min_capacity" 
                            value="{{ request('min_capacity') }}"
                            placeholder="e.g. 100"
                            min="1"
                            class="w-full px-gr-md py-gr-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        >
                    </div>

                    <!-- Sort By -->
                    <div>
                        <label for="sort" class="block text-small font-medium text-gray-700 mb-gr-xs">Sort By</label>
                        <select 
                            id="sort" 
                            name="sort" 
                            class="w-full px-gr-md py-gr-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        >
                            <option value="">Default</option>
                            <option value="capacity_asc" {{ request('sort') == 'capacity_asc' ? 'selected' : '' }}>Capacity (Low to High)</option>
                            <option value="capacity_desc" {{ request('sort') == 'capacity_desc' ? 'selected' : '' }}>Capacity (High to Low)</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price (Low to High)</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price (High to Low)</option>
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-gr-sm pt-gr-sm">
                    <a href="{{ route('facilities.index') }}" class="px-gr-md py-gr-sm border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-small font-medium">
                        Clear Filters
                    </a>
                    <button type="submit" class="px-gr-md py-gr-sm bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors text-small font-medium">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Results Summary -->
        <div class="mb-gr-md">
            <p class="text-body text-gray-600">
                Showing <span class="font-semibold text-gray-900">{{ $facilities->total() }}</span> {{ $facilities->total() === 1 ? 'facility' : 'facilities' }}
            </p>
        </div>

        <!-- Facilities Grid -->
        @if($facilities->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-gr-lg">
                @foreach($facilities as $facility)
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                        <!-- Facility Image -->
                        <div class="relative h-48 bg-gray-200">
                            @if($facility->primaryPhoto)
                                <img 
                                    src="{{ asset($facility->primaryPhoto->photo_path) }}" 
                                    alt="{{ $facility->facility_name }}"
                                    class="w-full h-full object-cover"
                                >
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-primary-100">
                                    <i data-lucide="building-2" class="w-16 h-16 text-primary-400"></i>
                                </div>
                            @endif
                            
                            <!-- Status Badge -->
                            <div class="absolute top-3 right-3">
                                <span class="px-gr-sm py-1 bg-green-500 text-white text-caption rounded-full">
                                    Available
                                </span>
                            </div>
                        </div>

                        <!-- Facility Info -->
                        <div class="p-gr-md">
                            <div class="mb-gr-sm">
                                <h3 class="text-h3 text-gray-900 mb-1">{{ $facility->facility_name }}</h3>
                                <p class="text-small text-gray-600 flex items-center gap-1">
                                    <i data-lucide="map-pin" class="w-4 h-4"></i>
                                    {{ $facility->location->location_name }}
                                </p>
                            </div>

                            <!-- Facility Details -->
                            <div class="space-y-gr-xs mb-gr-md">
                                <div class="flex items-center justify-between text-small">
                                    <span class="text-gray-600 flex items-center gap-1">
                                        <i data-lucide="users" class="w-4 h-4"></i>
                                        Capacity
                                    </span>
                                    <span class="font-semibold text-gray-900">{{ number_format($facility->capacity) }} people</span>
                                </div>

                                @if($facility->hourly_rate)
                                    <div class="flex items-center justify-between text-small">
                                        <span class="text-gray-600 flex items-center gap-1">
                                            <i data-lucide="clock" class="w-4 h-4"></i>
                                            Hourly Rate
                                        </span>
                                        <span class="font-semibold text-primary-600">₱{{ number_format($facility->hourly_rate, 2) }}</span>
                                    </div>
                                @endif

                                @if($facility->per_person_rate)
                                    <div class="flex items-center justify-between text-small">
                                        <span class="text-gray-600 flex items-center gap-1">
                                            <i data-lucide="user" class="w-4 h-4"></i>
                                            Per Person
                                        </span>
                                        <span class="font-semibold text-primary-600">₱{{ number_format($facility->per_person_rate, 2) }}</span>
                                    </div>
                                @endif

                                <div class="flex items-center justify-between text-small">
                                    <span class="text-gray-600 flex items-center gap-1">
                                        <i data-lucide="tag" class="w-4 h-4"></i>
                                        Type
                                    </span>
                                    <span class="font-medium text-gray-900">{{ ucwords(str_replace('_', ' ', $facility->facility_type)) }}</span>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <a href="{{ route('facilities.show', $facility->id) }}" class="block w-full px-gr-md py-gr-sm bg-primary-600 text-white text-center rounded-lg hover:bg-primary-700 transition-colors text-small font-medium">
                                View Details
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-gr-lg">
                {{ $facilities->links() }}
            </div>
        @else
            <!-- No Results -->
            <div class="bg-white rounded-xl shadow-sm p-gr-2xl text-center">
                <div class="flex flex-col items-center justify-center">
                    <i data-lucide="search-x" class="w-16 h-16 text-gray-400 mb-gr-md"></i>
                    <h3 class="text-h3 text-gray-900 mb-gr-sm">No Facilities Found</h3>
                    <p class="text-body text-gray-600 mb-gr-md max-w-md">
                        We couldn't find any facilities matching your criteria. Try adjusting your filters or search terms.
                    </p>
                    <a href="{{ route('facilities.index') }}" class="px-gr-md py-gr-sm bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors text-small font-medium">
                        Clear All Filters
                    </a>
                </div>
            </div>
        @endif
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-gr-3xl py-gr-2xl">
        <div class="max-w-7xl mx-auto px-gr-md text-center">
            <p class="text-body">© {{ date('Y') }} LGU1 Public Facilities Reservation System. All rights reserved.</p>
            <p class="text-small text-gray-400 mt-gr-xs">Need assistance? Contact your local LGU office.</p>
        </div>
    </footer>

    <!-- Initialize Lucide Icons -->
    <script>
        lucide.createIcons();
    </script>
</body>
</html>

