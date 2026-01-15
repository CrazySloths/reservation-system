@extends('layouts.citizen')

@section('title', 'Browse Facilities')
@section('page-title', 'Browse Facilities')
@section('page-subtitle', 'Discover and book public facilities')

@section('page-content')
<div class="space-y-gr-lg" id="facilitiesContainer">
    <!-- Search and Filters -->
    <div class="bg-white shadow rounded-xl p-gr-lg transition-all duration-300 hover:shadow-lg">
        <form method="GET" action="{{ route('citizen.browse-facilities') }}" class="space-y-gr-md">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-gr-md">
                <!-- Search -->
                <div class="md:col-span-3 lg:col-span-2">
                    <label for="search" class="block text-small font-medium text-gray-700 mb-gr-xs">Search Facilities</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-gr-sm flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" 
                               placeholder="Search by name, location, or description..." 
                               class="block w-full pl-10 pr-gr-sm py-gr-sm border-2 border-lgu-stroke rounded-lg focus:ring-2 focus:ring-lgu-highlight focus:border-lgu-highlight transition-all duration-200 text-sm {{ request('search') ? 'border-lgu-highlight bg-lgu-bg' : '' }}">
                    </div>
                </div>

                <!-- City Filter -->
                <div>
                    <label for="city_id" class="block text-small font-medium text-gray-700 mb-gr-xs">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin inline-block mr-1">
                            <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        City/Municipality
                    </label>
                    <select name="city_id" id="city_id" 
                            class="block w-full px-gr-sm py-gr-sm border-2 border-lgu-stroke rounded-lg focus:ring-2 focus:ring-lgu-highlight focus:border-lgu-highlight transition-all duration-200 text-sm {{ request('city_id') ? 'border-lgu-highlight bg-lgu-bg font-medium' : '' }}">
                        <option value="">All Cities</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>
                                {{ $city->location_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Capacity Filter -->
                <div>
                    <label for="capacity" class="block text-small font-medium text-gray-700 mb-gr-xs">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users inline-block mr-1">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        Min. Capacity
                    </label>
                    <input type="number" name="capacity" id="capacity" value="{{ request('capacity') }}" 
                           placeholder="e.g., 50" min="0"
                           class="block w-full px-gr-sm py-gr-sm border-2 border-lgu-stroke rounded-lg focus:ring-2 focus:ring-lgu-highlight focus:border-lgu-highlight transition-all duration-200 text-sm {{ request('capacity') ? 'border-lgu-highlight bg-lgu-bg font-medium' : '' }}">
                </div>
            </div>

            <div class="flex items-center justify-between pt-gr-sm border-t border-lgu-stroke">
                <div class="text-sm text-lgu-paragraph">
                    Showing <span class="font-semibold text-lgu-headline">{{ $facilities->total() }}</span> {{ $facilities->total() == 1 ? 'facility' : 'facilities' }}
                </div>
                <div class="flex gap-gr-sm">
                    <a href="{{ route('citizen.browse-facilities') }}" 
                       class="px-gr-md py-gr-sm border-2 border-lgu-stroke text-lgu-headline rounded-lg hover:bg-lgu-bg transition-colors text-sm font-semibold">
                        Clear Filters
                    </a>
                    <button type="submit" 
                            class="px-gr-md py-gr-sm bg-lgu-button text-lgu-button-text rounded-lg hover:opacity-90 transition-all text-sm font-semibold shadow-sm">
                        Apply Filters
                    </button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <style>
        /* Fade-in animation for facility cards */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }

        /* Smooth transitions */
        .facility-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .facility-card:hover {
            transform: translateY(-8px) scale(1.02);
        }

    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form[action="{{ route('citizen.browse-facilities') }}"]');
        const searchInput = document.getElementById('search');
        const citySelect = document.getElementById('city_id');
        const capacityInput = document.getElementById('capacity');
        
        let searchTimeout;

        // Auto-submit for city dropdown (instant)
        if (citySelect) {
            citySelect.addEventListener('change', function() {
                form.submit();
            });
        }

        // Auto-submit for capacity (instant when user changes value)
        if (capacityInput) {
            capacityInput.addEventListener('change', function() {
                form.submit();
            });
        }

        // Auto-submit for search with debounce (wait 500ms after user stops typing)
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    form.submit();
                }, 500); // Wait 500ms after user stops typing
            });
        }
    });
    </script>
    @endpush

    <!-- Facilities Grid -->
    @if($facilities->isEmpty())
        <div class="bg-white shadow rounded-xl p-gr-3xl text-center">
            <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-gr-md">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <h3 class="text-h3 text-gray-900 mb-gr-sm">No facilities found</h3>
            <p class="text-body text-gray-600 mb-gr-lg">Try adjusting your search or filters</p>
            <a href="{{ route('citizen.browse-facilities') }}" 
               class="inline-flex items-center px-gr-md py-gr-sm bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors text-small font-medium">
                View All Facilities
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-gr-lg" id="facilitiesGrid">
            @foreach($facilities as $facility)
                <div class="facility-card bg-white shadow-sm rounded-xl overflow-hidden hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 opacity-0 animate-fade-in" style="animation-delay: {{ $loop->index * 0.1 }}s">
                    <!-- Facility Image -->
                    <div class="relative h-48 bg-gray-200">
                        <div class="w-full h-full flex items-center justify-center bg-primary-100">
                            <svg class="w-16 h-16 text-primary-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm3 1h6v4H7V5zm6 6H7v2h6v-2z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <!-- Status Badge -->
                        <div class="absolute top-3 right-3">
                            @if(!$facility->is_available)
                                <span class="px-gr-sm py-1 bg-lgu-button text-lgu-button-text text-caption font-semibold rounded-full shadow-md">Coming Soon</span>
                            @else
                                <span class="px-gr-sm py-1 bg-green-500 text-white text-caption font-semibold rounded-full">Available</span>
                            @endif
                        </div>
                    </div>

                    <!-- Facility Info -->
                    <div class="p-gr-md">
                        <div class="mb-gr-sm">
                            <h3 class="text-h3 text-gray-900 mb-1">{{ $facility->name }}</h3>
                            @if($facility->lguCity)
                                <p class="text-small text-gray-600 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                                        <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/>
                                        <circle cx="12" cy="10" r="3"/>
                                    </svg>
                                    {{ $facility->lguCity->city_name }}
                                </p>
                            @endif
                        </div>

                        <!-- Details -->
                        <div class="space-y-gr-xs mb-gr-md">
                            <div class="flex items-center justify-between text-small">
                                <span class="text-gray-600 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                    Capacity
                                </span>
                                <span class="font-semibold text-gray-900">{{ number_format($facility->min_capacity ?? 1) }}-{{ number_format($facility->capacity) }} people</span>
                            </div>

                        </div>

                        <!-- Price & Action -->
                        <div class="border-t border-gray-200 pt-gr-md">
                            @if($facility->per_person_rate)
                                <div class="mb-gr-sm">
                                    <div class="text-h2 font-bold text-primary-600">₱{{ number_format($facility->per_person_rate, 2) }}</div>
                                    <div class="text-caption text-gray-500">Per person rate</div>
                                </div>
                            @elseif($facility->hourly_rate)
                                <div class="mb-gr-sm">
                                    <div class="text-h2 font-bold text-primary-600">₱{{ number_format($facility->hourly_rate, 2) }}</div>
                                    <div class="text-caption text-gray-500">Per hour rate</div>
                                </div>
                            @else
                                <div class="mb-gr-sm">
                                    <div class="text-body-lg font-bold text-gray-900">Contact for pricing</div>
                                    <div class="text-caption text-gray-500">Rates vary</div>
                                </div>
                            @endif
                            
                            <div class="grid grid-cols-2 gap-gr-sm">
                                <a href="{{ route('citizen.facility-details', $facility->facility_id) }}" 
                                   class="px-gr-sm py-gr-sm border-2 border-lgu-stroke text-lgu-headline font-semibold rounded-lg hover:bg-lgu-bg transition-colors text-center text-sm">
                                    Details
                                </a>
                                @if($facility->is_available)
                                    <a href="{{ route('citizen.booking.create', $facility->facility_id) }}" 
                                       class="px-gr-sm py-gr-sm bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:opacity-90 transition-all shadow-sm text-center text-sm">
                                        Book Now
                                    </a>
                                @else
                                    <button disabled 
                                            class="px-gr-sm py-gr-sm bg-gray-300 text-gray-500 font-semibold rounded-lg cursor-not-allowed text-center text-sm">
                                        Coming Soon
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-gr-lg">
            {{ $facilities->links() }}
        </div>
    @endif

    <!-- Info Box -->
    <div class="bg-lgu-bg border-2 border-lgu-stroke rounded-xl p-gr-lg">
        <div class="flex gap-gr-sm">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-lgu-headline" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-base font-semibold text-lgu-headline mb-gr-sm">Booking Information</h3>
                <div class="text-sm text-lgu-paragraph space-y-gr-xs">
                    <div class="flex items-start gap-2">
                        <span class="font-semibold">Pricing:</span>
                        <span>Rates are calculated per person based on number of attendees</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="font-semibold">Senior/PWD:</span>
                        <span>20% discount with valid ID</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="font-semibold">Student:</span>
                        <span>20% discount with valid school ID</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="font-semibold">Advance Booking:</span>
                        <span>Reserve at least 7 business days in advance</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="font-semibold">Payment:</span>
                        <span>Full payment required before reservation confirmation (no partial payments)</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="font-semibold">Coming Soon:</span>
                        <span>QC M.I.C.E. facilities pending ordinance approval for public bookings</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

