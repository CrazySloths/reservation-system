<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $facility->facility_name }} - LGU1 Public Facilities</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-50">
    <!-- Header / Navigation -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-gr-md py-gr-md flex items-center justify-between">
            <div class="flex items-center gap-gr-md">
                <a href="{{ route('facilities.index') }}" class="flex items-center gap-2 text-gray-600 hover:text-gray-900 transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                    <span class="text-small font-medium">Back to Facilities</span>
                </a>
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
                @endif
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-gr-md py-gr-lg">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-gr-lg">
            <!-- Left Column: Facility Details -->
            <div class="lg:col-span-2 space-y-gr-lg">
                <!-- Image Gallery -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    @if($facility->photos->count() > 0)
                        <div class="relative h-96">
                            <img 
                                id="mainImage"
                                src="{{ asset($facility->photos->first()->photo_path) }}" 
                                alt="{{ $facility->facility_name }}"
                                class="w-full h-full object-cover"
                            >
                        </div>
                        
                        @if($facility->photos->count() > 1)
                            <!-- Thumbnail Gallery -->
                            <div class="p-gr-md grid grid-cols-4 gap-gr-sm">
                                @foreach($facility->photos as $photo)
                                    <button 
                                        onclick="changeMainImage('{{ asset($photo->photo_path) }}')"
                                        class="relative h-20 rounded-lg overflow-hidden hover:ring-2 hover:ring-primary-500 transition-all"
                                    >
                                        <img 
                                            src="{{ asset($photo->photo_path) }}" 
                                            alt="{{ $photo->photo_caption }}"
                                            class="w-full h-full object-cover"
                                        >
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div class="h-96 bg-primary-100 flex items-center justify-center">
                            <i data-lucide="building-2" class="w-32 h-32 text-primary-400"></i>
                        </div>
                    @endif
                </div>

                <!-- Facility Information -->
                <div class="bg-white rounded-xl shadow-sm p-gr-lg">
                    <h1 class="text-h1 text-gray-900 mb-gr-sm">{{ $facility->facility_name }}</h1>
                    
                    <div class="flex items-center gap-gr-md mb-gr-lg text-body text-gray-600">
                        <span class="flex items-center gap-2">
                            <i data-lucide="map-pin" class="w-5 h-5"></i>
                            {{ $facility->location->location_name }}
                        </span>
                        <span class="flex items-center gap-2">
                            <i data-lucide="tag" class="w-5 h-5"></i>
                            {{ ucwords(str_replace('_', ' ', $facility->facility_type)) }}
                        </span>
                    </div>

                    <!-- Description -->
                    @if($facility->description)
                        <div class="mb-gr-lg">
                            <h2 class="text-h3 text-gray-900 mb-gr-sm">About This Facility</h2>
                            <p class="text-body text-gray-700 leading-relaxed">{{ $facility->description }}</p>
                        </div>
                    @endif

                    <!-- Amenities -->
                    @if($facility->amenities && count($facility->amenities) > 0)
                        <div class="mb-gr-lg">
                            <h2 class="text-h3 text-gray-900 mb-gr-sm">Amenities</h2>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-gr-sm">
                                @foreach($facility->amenities as $amenity)
                                    <div class="flex items-center gap-2 text-body text-gray-700">
                                        <i data-lucide="check-circle" class="w-5 h-5 text-green-500"></i>
                                        <span>{{ ucwords(str_replace('_', ' ', $amenity)) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Rules & Guidelines -->
                    @if($facility->rules)
                        <div class="mb-gr-lg">
                            <h2 class="text-h3 text-gray-900 mb-gr-sm">Rules & Guidelines</h2>
                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-gr-md">
                                <p class="text-body text-gray-700 whitespace-pre-line">{{ $facility->rules }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Terms & Conditions -->
                    @if($facility->terms_and_conditions)
                        <div>
                            <h2 class="text-h3 text-gray-900 mb-gr-sm">Terms & Conditions</h2>
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-gr-md">
                                <p class="text-small text-gray-700 whitespace-pre-line">{{ $facility->terms_and_conditions }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Equipment Available for Rent -->
                @if($facility->equipment->count() > 0)
                    <div class="bg-white rounded-xl shadow-sm p-gr-lg">
                        <h2 class="text-h3 text-gray-900 mb-gr-md">Available Equipment</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-gr-md">
                            @foreach($facility->equipment as $equipment)
                                <div class="border border-gray-200 rounded-lg p-gr-md">
                                    <div class="flex items-start justify-between mb-gr-sm">
                                        <div>
                                            <h4 class="text-body font-semibold text-gray-900">{{ $equipment->equipment_name }}</h4>
                                            <p class="text-small text-gray-600">{{ ucwords(str_replace('_', ' ', $equipment->equipment_type)) }}</p>
                                        </div>
                                        @if($equipment->is_free)
                                            <span class="px-gr-sm py-1 bg-green-100 text-green-700 text-caption rounded-full">Free</span>
                                        @endif
                                    </div>
                                    
                                    @if($equipment->description)
                                        <p class="text-small text-gray-600 mb-gr-sm">{{ $equipment->description }}</p>
                                    @endif
                                    
                                    <div class="space-y-1">
                                        @if($equipment->hourly_rate && !$equipment->is_free)
                                            <div class="flex items-center justify-between text-small">
                                                <span class="text-gray-600">Hourly Rate</span>
                                                <span class="font-semibold text-gray-900">₱{{ number_format($equipment->hourly_rate, 2) }}</span>
                                            </div>
                                        @endif
                                        @if($equipment->daily_rate && !$equipment->is_free)
                                            <div class="flex items-center justify-between text-small">
                                                <span class="text-gray-600">Daily Rate</span>
                                                <span class="font-semibold text-gray-900">₱{{ number_format($equipment->daily_rate, 2) }}</span>
                                            </div>
                                        @endif
                                        <div class="flex items-center justify-between text-small">
                                            <span class="text-gray-600">Available</span>
                                            <span class="font-semibold text-gray-900">{{ $equipment->quantity_available }} / {{ $equipment->quantity_total }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column: Booking Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-gr-lg sticky top-24">
                    <h2 class="text-h3 text-gray-900 mb-gr-md">Booking Information</h2>
                    
                    <!-- Capacity -->
                    <div class="mb-gr-md pb-gr-md border-b border-gray-200">
                        <div class="flex items-center justify-between text-body">
                            <span class="text-gray-600 flex items-center gap-2">
                                <i data-lucide="users" class="w-5 h-5"></i>
                                Maximum Capacity
                            </span>
                            <span class="font-bold text-gray-900">{{ number_format($facility->capacity) }}</span>
                        </div>
                    </div>

                    <!-- Pricing -->
                    <div class="mb-gr-md pb-gr-md border-b border-gray-200">
                        <h3 class="text-body font-semibold text-gray-900 mb-gr-sm">Pricing</h3>
                        <div class="space-y-gr-sm">
                            @if($facility->hourly_rate)
                                <div class="flex items-center justify-between">
                                    <span class="text-small text-gray-600">Hourly Rate</span>
                                    <span class="text-body font-bold text-primary-600">₱{{ number_format($facility->hourly_rate, 2) }}</span>
                                </div>
                            @endif
                            @if($facility->per_person_rate)
                                <div class="flex items-center justify-between">
                                    <span class="text-small text-gray-600">Per Person Rate</span>
                                    <span class="text-body font-bold text-primary-600">₱{{ number_format($facility->per_person_rate, 2) }}</span>
                                </div>
                            @endif
                            @if($facility->deposit_amount)
                                <div class="flex items-center justify-between">
                                    <span class="text-small text-gray-600">Deposit Required</span>
                                    <span class="text-body font-semibold text-gray-900">₱{{ number_format($facility->deposit_amount, 2) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Booking Duration -->
                    <div class="mb-gr-md pb-gr-md border-b border-gray-200">
                        <h3 class="text-body font-semibold text-gray-900 mb-gr-sm">Booking Duration</h3>
                        <div class="space-y-gr-xs text-small text-gray-600">
                            <div class="flex items-center justify-between">
                                <span>Minimum</span>
                                <span class="font-medium text-gray-900">{{ $facility->min_booking_hours }} hours</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Maximum</span>
                                <span class="font-medium text-gray-900">{{ $facility->max_booking_hours }} hours</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Advance Booking</span>
                                <span class="font-medium text-gray-900">{{ $facility->advance_booking_days }} days</span>
                            </div>
                        </div>
                    </div>

                    <!-- Operating Hours -->
                    @if($facility->operating_hours)
                        <div class="mb-gr-lg pb-gr-md border-b border-gray-200">
                            <h3 class="text-body font-semibold text-gray-900 mb-gr-sm">Operating Hours</h3>
                            <div class="space-y-1 text-small text-gray-700">
                                @foreach($facility->operating_hours as $day => $hours)
                                    <div class="flex items-center justify-between">
                                        <span class="capitalize">{{ $day }}</span>
                                        <span class="font-medium">{{ $hours['open'] ?? 'Closed' }} - {{ $hours['close'] ?? '' }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Location Info -->
                    @if($facility->address)
                        <div class="mb-gr-lg pb-gr-md border-b border-gray-200">
                            <h3 class="text-body font-semibold text-gray-900 mb-gr-sm">Address</h3>
                            <p class="text-small text-gray-700">{{ $facility->address }}</p>
                            @if($facility->google_maps_url)
                                <a href="{{ $facility->google_maps_url }}" target="_blank" class="inline-flex items-center gap-1 text-small text-primary-600 hover:text-primary-700 mt-gr-xs">
                                    <i data-lucide="map" class="w-4 h-4"></i>
                                    View on Google Maps
                                </a>
                            @endif
                        </div>
                    @endif

                    <!-- Book Now Button -->
                    @if(session('user_id'))
                        <a href="{{ route('citizen.booking.create', $facility->id) }}" class="block w-full px-gr-md py-gr-md bg-primary-600 text-white text-center rounded-lg hover:bg-primary-700 transition-colors text-body font-bold">
                            Book This Facility
                        </a>
                    @else
                        <div class="space-y-gr-sm">
                            <a href="{{ route('login') }}" class="block w-full px-gr-md py-gr-md bg-primary-600 text-white text-center rounded-lg hover:bg-primary-700 transition-colors text-body font-bold">
                                Login to Book
                            </a>
                            <p class="text-caption text-center text-gray-600">
                                Don't have an account? <a href="{{ route('register') }}" class="text-primary-600 hover:text-primary-700 font-medium">Register here</a>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Similar Facilities -->
        @if($similarFacilities->count() > 0)
            <div class="mt-gr-3xl">
                <h2 class="text-h2 text-gray-900 mb-gr-lg">Similar Facilities</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-gr-lg">
                    @foreach($similarFacilities as $similar)
                        <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                            <!-- Facility Image -->
                            <div class="relative h-40 bg-gray-200">
                                @if($similar->primaryPhoto)
                                    <img 
                                        src="{{ asset($similar->primaryPhoto->photo_path) }}" 
                                        alt="{{ $similar->facility_name }}"
                                        class="w-full h-full object-cover"
                                    >
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-primary-100">
                                        <i data-lucide="building-2" class="w-12 h-12 text-primary-400"></i>
                                    </div>
                                @endif
                            </div>

                            <!-- Facility Info -->
                            <div class="p-gr-md">
                                <h3 class="text-body font-semibold text-gray-900 mb-gr-xs">{{ $similar->facility_name }}</h3>
                                <p class="text-small text-gray-600 mb-gr-sm flex items-center gap-1">
                                    <i data-lucide="users" class="w-4 h-4"></i>
                                    Capacity: {{ number_format($similar->capacity) }}
                                </p>
                                <a href="{{ route('facilities.show', $similar->id) }}" class="block w-full px-gr-sm py-gr-xs bg-gray-100 text-gray-700 text-center rounded-lg hover:bg-gray-200 transition-colors text-small font-medium">
                                    View Details
                                </a>
                            </div>
                        </div>
                    @endforeach
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
        
        // Image gallery functionality
        function changeMainImage(imageSrc) {
            document.getElementById('mainImage').src = imageSrc;
        }
    </script>
</body>
</html>

