@extends('layouts.admin')

@section('page-title', 'Reviews Moderation')
@section('page-subtitle', 'Manage facility reviews and ratings')

@section('page-content')
<div class="space-y-gr-lg">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-h1 font-bold text-lgu-headline mb-gr-xs">Reviews Moderation</h1>
            <p class="text-body text-lgu-paragraph">Monitor and manage citizen feedback</p>
        </div>
        <div class="flex items-center gap-gr-sm">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 px-gr-md py-gr-sm">
                <div class="text-caption text-gray-600 uppercase mb-1">Total Reviews</div>
                <div class="text-h2 font-bold text-lgu-headline">{{ $totalReviews }}</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 px-gr-md py-gr-sm">
                <div class="text-caption text-gray-600 uppercase mb-1">Avg Rating</div>
                <div class="text-h2 font-bold text-lgu-button">{{ number_format($avgRating ?? 0, 1) }}</div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-gr-lg">
        <form method="GET" action="{{ route('admin.reviews.index') }}" class="space-y-gr-md">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-gr-md">
                <div>
                    <label for="search" class="block text-small font-semibold text-lgu-headline mb-gr-xs">Search</label>
                    <input type="text" id="search" name="search" value="{{ $search }}" placeholder="Search reviews..."
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lgu-highlight focus:border-transparent">
                </div>
                <div>
                    <label for="facility_id" class="block text-small font-semibold text-lgu-headline mb-gr-xs">Facility</label>
                    <select id="facility_id" name="facility_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lgu-highlight focus:border-transparent">
                        <option value="">All Facilities</option>
                        @foreach($facilities as $facility)
                            <option value="{{ $facility->facility_id }}" {{ $facilityId == $facility->facility_id ? 'selected' : '' }}>
                                {{ $facility->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="rating" class="block text-small font-semibold text-lgu-headline mb-gr-xs">Rating</label>
                    <select id="rating" name="rating" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lgu-highlight focus:border-transparent">
                        <option value="">All Ratings</option>
                        <option value="5" {{ $rating == '5' ? 'selected' : '' }}>5 Stars</option>
                        <option value="4" {{ $rating == '4' ? 'selected' : '' }}>4 Stars</option>
                        <option value="3" {{ $rating == '3' ? 'selected' : '' }}>3 Stars</option>
                        <option value="2" {{ $rating == '2' ? 'selected' : '' }}>2 Stars</option>
                        <option value="1" {{ $rating == '1' ? 'selected' : '' }}>1 Star</option>
                    </select>
                </div>
            </div>
            <div class="flex items-center gap-gr-sm">
                <button type="submit" class="inline-flex items-center px-gr-md py-gr-sm bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:bg-opacity-90 transition-colors duration-200">
                    <i data-lucide="filter" class="w-5 h-5 mr-gr-xs"></i>
                    Apply Filters
                </button>
                @if($search || $facilityId || $rating)
                    <a href="{{ route('admin.reviews.index') }}" class="inline-flex items-center px-gr-md py-gr-sm bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors duration-200">
                        <i data-lucide="x" class="w-5 h-5 mr-gr-xs"></i>
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Reviews List --}}
    @if($reviews->count() > 0)
        <div class="space-y-gr-md">
            @foreach($reviews as $review)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-gr-lg">
                    <div class="flex items-start gap-gr-md">
                        @if($review->facility_image)
                            <img src="{{ Storage::url($review->facility_image) }}" alt="{{ $review->facility_name }}" 
                                class="w-20 h-20 object-cover rounded-lg flex-shrink-0">
                        @else
                            <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i data-lucide="building-2" class="w-10 h-10 text-gray-400"></i>
                            </div>
                        @endif

                        <div class="flex-1">
                            <div class="flex items-start justify-between mb-gr-sm">
                                <div>
                                    <h3 class="text-h3 font-bold text-lgu-headline mb-1">{{ $review->facility_name }}</h3>
                                    <div class="flex items-center gap-gr-sm text-small text-gray-600">
                                        <span class="font-semibold">{{ $review->user_name }}</span>
                                        <span>•</span>
                                        <span>{{ \Carbon\Carbon::parse($review->created_at)->format('M j, Y') }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-gr-xs">
                                    <a href="{{ route('admin.reviews.show', $review->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200" title="View Details">
                                        <i data-lucide="eye" class="w-5 h-5"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="flex items-center gap-gr-xs mb-gr-sm">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        <i data-lucide="star" class="w-5 h-5 text-lgu-button fill-current"></i>
                                    @else
                                        <i data-lucide="star" class="w-5 h-5 text-gray-300"></i>
                                    @endif
                                @endfor
                                <span class="text-body font-semibold text-lgu-headline ml-gr-xs">{{ $review->rating }}.0</span>
                            </div>

                            @if($review->review)
                                <p class="text-body text-gray-700">{{ $review->review }}</p>
                            @else
                                <p class="text-body text-gray-400 italic">No written review provided.</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($reviews->hasPages())
            <div class="mt-gr-lg">
                {{ $reviews->links() }}
            </div>
        @endif
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <i data-lucide="star" class="w-16 h-16 text-gray-300 mb-gr-md mx-auto"></i>
            <h3 class="text-h3 font-bold text-lgu-headline mb-gr-xs">No Reviews Found</h3>
            <p class="text-body text-gray-600">No citizen reviews match your filters.</p>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}
</script>
@endpush
@endsection

