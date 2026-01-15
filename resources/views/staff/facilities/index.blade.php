@extends('layouts.staff')

@section('title', 'View Facilities')
@section('page-title', 'View Facilities')
@section('page-subtitle', 'Browse all available public facilities')

@section('page-content')
<div class="container mx-auto px-gr-md py-gr-lg">
    {{-- Page Header --}}
    <div class="mb-gr-lg">
        <h1 class="text-h1 font-bold text-lgu-headline mb-gr-xs">Facilities Directory</h1>
        <p class="text-body text-lgu-paragraph">Browse all available facilities and their details</p>
    </div>

    {{-- Search and Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-gr-lg mb-gr-lg">
        <form method="GET" action="{{ route('staff.facilities.index') }}" class="space-y-gr-md">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-gr-md">
                {{-- Search --}}
                <div>
                    <label for="search" class="block text-small font-semibold text-lgu-headline mb-gr-xs">Search</label>
                    <div class="relative">
                        <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
                        <input type="text" id="search" name="search" value="{{ $search }}" 
                            placeholder="Search facilities..." 
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lgu-highlight focus:border-transparent">
                    </div>
                </div>

                {{-- City Filter --}}
                <div>
                    <label for="city_id" class="block text-small font-semibold text-lgu-headline mb-gr-xs">City</label>
                    <select id="city_id" name="city_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lgu-highlight focus:border-transparent">
                        <option value="">All Cities</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ $cityId == $city->id ? 'selected' : '' }}>
                                {{ $city->city_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Facility Type Filter --}}
                <div>
                    <label for="facility_type" class="block text-small font-semibold text-lgu-headline mb-gr-xs">Type</label>
                    <select id="facility_type" name="facility_type" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lgu-highlight focus:border-transparent">
                        <option value="">All Types</option>
                        @foreach($facilityTypes as $key => $label)
                            <option value="{{ $key }}" {{ $facilityType == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Status Filter --}}
                <div>
                    <label for="status" class="block text-small font-semibold text-lgu-headline mb-gr-xs">Status</label>
                    <select id="status" name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lgu-highlight focus:border-transparent">
                        <option value="">All Statuses</option>
                        @foreach($statusOptions as $key => $label)
                            <option value="{{ $key }}" {{ $status == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-gr-sm">
                <button type="submit" class="inline-flex items-center px-gr-md py-gr-sm bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:bg-opacity-90 transition-colors duration-200">
                    <i data-lucide="filter" class="w-5 h-5 mr-gr-xs"></i>
                    Apply Filters
                </button>
                <a href="{{ route('staff.facilities.index') }}" class="inline-flex items-center px-gr-md py-gr-sm bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors duration-200">
                    <i data-lucide="x" class="w-5 h-5 mr-gr-xs"></i>
                    Clear
                </a>
            </div>
        </form>
    </div>

    {{-- Facilities Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-gr-lg">
        @forelse($facilities as $facility)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
                {{-- Facility Image --}}
                @if($facility->image_path)
                    <img src="{{ Storage::url($facility->image_path) }}" alt="{{ $facility->name }}" 
                        class="w-full h-48 object-cover">
                @else
                    <div class="w-full h-48 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                        <i data-lucide="building-2" class="w-16 h-16 text-gray-400"></i>
                    </div>
                @endif

                {{-- Facility Details --}}
                <div class="p-gr-lg">
                    <div class="flex items-start justify-between mb-gr-sm">
                        <div class="flex-1">
                            <h3 class="text-h4 font-bold text-lgu-headline mb-1">{{ $facility->name }}</h3>
                            <p class="text-small text-gray-600">{{ $facility->city_name }}</p>
                        </div>
                        @if($facility->is_available)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                Available
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i>
                                Unavailable
                            </span>
                        @endif
                    </div>

                    <p class="text-small text-lgu-paragraph mb-gr-md line-clamp-2">
                        {{ $facility->description ?? 'No description available.' }}
                    </p>

                    <div class="grid grid-cols-2 gap-gr-sm mb-gr-md text-small">
                        <div class="flex items-center text-gray-600">
                            <i data-lucide="users" class="w-4 h-4 mr-1"></i>
                            <span>{{ number_format($facility->capacity) }} pax</span>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <i data-lucide="tag" class="w-4 h-4 mr-1"></i>
                            <span>₱{{ number_format($facility->per_person_rate ?? 0, 2) }}/person</span>
                        </div>
                    </div>

                    <a href="{{ route('staff.facilities.show', $facility->facility_id) }}" 
                        class="block w-full text-center px-gr-md py-gr-sm bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:bg-opacity-90 transition-colors duration-200">
                        View Details
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                    <i data-lucide="inbox" class="w-16 h-16 text-gray-300 mx-auto mb-gr-md"></i>
                    <p class="text-body font-semibold text-gray-600 mb-gr-xs">No facilities found</p>
                    <p class="text-small text-gray-500">Try adjusting your search or filters</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($facilities->hasPages())
        <div class="mt-gr-lg">
            {{ $facilities->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
// Initialize Lucide icons
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}
</script>
@endpush
@endsection

