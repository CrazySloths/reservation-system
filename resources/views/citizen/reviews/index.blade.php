@extends('layouts.citizen')

@section('page-title', 'My Reviews & Ratings')
@section('page-subtitle', 'View and manage your facility reviews')

@section('page-content')
<div class="space-y-gr-lg">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-h1 font-bold text-lgu-headline mb-gr-xs">My Reviews & Ratings</h1>
            <p class="text-body text-lgu-paragraph">View and manage your feedback for facilities you've used</p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-gr-lg">
        <form method="GET" action="{{ route('citizen.reviews.index') }}" class="flex items-end gap-gr-md">
            <div class="flex-1">
                <label for="facility_id" class="block text-small font-semibold text-lgu-headline mb-gr-xs">Filter by Facility</label>
                <select id="facility_id" name="facility_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lgu-highlight focus:border-transparent">
                    <option value="">All Facilities</option>
                    @foreach($facilities as $facility)
                        <option value="{{ $facility->facility_id }}" {{ $facilityFilter == $facility->facility_id ? 'selected' : '' }}>
                            {{ $facility->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="inline-flex items-center px-gr-lg py-gr-sm bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:bg-opacity-90 transition-colors duration-200">
                <i data-lucide="filter" class="w-5 h-5 mr-gr-xs"></i>
                Filter
            </button>
            @if($facilityFilter)
                <a href="{{ route('citizen.reviews.index') }}" class="inline-flex items-center px-gr-lg py-gr-sm bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors duration-200">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </a>
            @endif
        </form>
    </div>

    {{-- Reviews List --}}
    @if($reviews->count() > 0)
        <div class="space-y-gr-md">
            @foreach($reviews as $review)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-gr-lg">
                    <div class="flex items-start gap-gr-md">
                        {{-- Facility Image --}}
                        @if($review->facility_image)
                            <img src="{{ Storage::url($review->facility_image) }}" alt="{{ $review->facility_name }}" 
                                class="w-24 h-24 object-cover rounded-lg flex-shrink-0">
                        @else
                            <div class="w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i data-lucide="building-2" class="w-12 h-12 text-gray-400"></i>
                            </div>
                        @endif

                        {{-- Review Content --}}
                        <div class="flex-1">
                            {{-- Header --}}
                            <div class="flex items-start justify-between mb-gr-sm">
                                <div>
                                    <h3 class="text-h3 font-bold text-lgu-headline mb-1">{{ $review->facility_name }}</h3>
                                    <div class="flex items-center gap-gr-sm text-small text-gray-600">
                                        <span>{{ \Carbon\Carbon::parse($review->event_date)->format('F j, Y') }}</span>
                                        <span>•</span>
                                        <span>{{ \Carbon\Carbon::parse($review->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($review->end_time)->format('g:i A') }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-gr-xs">
                                    <a href="{{ route('citizen.reviews.edit', $review->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200" title="Edit Review">
                                        <i data-lucide="edit-2" class="w-5 h-5"></i>
                                    </a>
                                    <button onclick="deleteReview({{ $review->id }})" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200" title="Delete Review">
                                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Rating --}}
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

                            {{-- Review Text --}}
                            @if($review->review)
                                <p class="text-body text-gray-700 mb-gr-sm">{{ $review->review }}</p>
                            @else
                                <p class="text-body text-gray-400 italic mb-gr-sm">No written review provided.</p>
                            @endif

                            {{-- Footer --}}
                            <div class="flex items-center gap-gr-sm text-small text-gray-500">
                                <i data-lucide="clock" class="w-4 h-4"></i>
                                <span>Reviewed {{ \Carbon\Carbon::parse($review->created_at)->diffForHumans() }}</span>
                                @if($review->updated_at != $review->created_at)
                                    <span>•</span>
                                    <span>Edited {{ \Carbon\Carbon::parse($review->updated_at)->diffForHumans() }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($reviews->hasPages())
            <div class="mt-gr-lg">
                {{ $reviews->links() }}
            </div>
        @endif
    @else
        {{-- Empty State --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <div class="flex flex-col items-center justify-center">
                <i data-lucide="star" class="w-16 h-16 text-gray-300 mb-gr-md"></i>
                <h3 class="text-h3 font-bold text-lgu-headline mb-gr-xs">No Reviews Yet</h3>
                <p class="text-body text-gray-600 mb-gr-md">Complete a booking to leave your first review!</p>
                <a href="{{ route('citizen.reservations') }}" class="inline-flex items-center px-gr-lg py-gr-md bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:bg-opacity-90 transition-colors duration-200">
                    <i data-lucide="calendar" class="w-5 h-5 mr-gr-xs"></i>
                    View My Reservations
                </a>
            </div>
        </div>
    @endif
</div>

{{-- Delete Form --}}
<form id="deleteForm" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Initialize Lucide icons
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}

// Delete review function
function deleteReview(reviewId) {
    Swal.fire({
        title: 'Delete Review?',
        text: 'This will permanently remove your review. This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#fa5246',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteForm');
            form.action = `/citizen/reviews/${reviewId}`;
            form.submit();
        }
    });
}
</script>
@endpush
@endsection

