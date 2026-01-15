@extends('layouts.citizen')

@section('title', 'Leave a Review')
@section('page-title', 'Leave a Review')
@section('page-subtitle', 'Share your experience with this facility')

@section('page-content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('citizen.reservations.show', $booking->id) }}" 
           class="inline-flex items-center text-lgu-button hover:text-lgu-highlight font-medium">
            <i data-lucide="arrow-left" class="w-5 h-5 mr-2"></i>
            Back to Booking Details
        </a>
    </div>

    <!-- Booking Information Card -->
    <div class="bg-white shadow rounded-lg overflow-hidden border-2 border-lgu-green">
        <div class="bg-lgu-green px-6 py-4">
            <h2 class="text-xl font-bold text-white flex items-center">
                <i data-lucide="calendar-check" class="w-6 h-6 mr-3"></i>
                Your Booking
            </h2>
        </div>
        
        <div class="p-6">
            <div class="flex gap-6">
                @if($booking->facility_image)
                    <img src="{{ asset('storage/' . $booking->facility_image) }}" 
                         alt="{{ $booking->facility_name }}" 
                         class="w-32 h-32 object-cover rounded-lg shadow-md">
                @else
                    <div class="w-32 h-32 bg-lgu-bg flex items-center justify-center rounded-lg">
                        <i data-lucide="building-2" class="w-16 h-16 text-gray-400"></i>
                    </div>
                @endif

                <div class="flex-1">
                    <h3 class="text-2xl font-bold text-lgu-headline mb-2">{{ $booking->facility_name }}</h3>
                    <p class="text-sm text-gray-600 mb-3">{{ $booking->facility_address }}</p>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Event Date</p>
                            <p class="text-sm font-semibold text-gray-900">
                                {{ \Carbon\Carbon::parse($booking->start_time)->format('F d, Y') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Event Time</p>
                            <p class="text-sm font-semibold text-gray-900">
                                {{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} - 
                                {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Review Form -->
    <form action="{{ route('citizen.reviews.store') }}" method="POST" class="bg-white shadow rounded-lg overflow-hidden">
        @csrf
        <input type="hidden" name="booking_id" value="{{ $booking->id }}">

        <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-lgu-green to-emerald-600">
            <h2 class="text-xl font-bold text-white flex items-center">
                <i data-lucide="star" class="w-6 h-6 mr-3"></i>
                Rate Your Experience
            </h2>
        </div>

        <div class="p-6 space-y-6">
            <!-- Rating -->
            <div>
                <label class="block text-sm font-bold text-gray-900 mb-3">
                    Overall Rating <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center gap-2">
                    <div id="star-rating" class="flex gap-1">
                        @for ($i = 1; $i <= 5; $i++)
                            <button type="button" 
                                    onclick="setRating({{ $i }})"
                                    class="star-btn focus:outline-none transition-transform hover:scale-110"
                                    data-rating="{{ $i }}">
                                <svg class="w-12 h-12 text-gray-300 hover:text-yellow-400 transition-colors" 
                                     fill="currentColor" 
                                     viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </button>
                        @endfor
                    </div>
                    <span id="rating-text" class="text-lg font-semibold text-gray-600 ml-2">No rating selected</span>
                </div>
                <input type="hidden" name="rating" id="rating-input" required>
                @error('rating')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Review Text -->
            <div>
                <label for="review" class="block text-sm font-bold text-gray-900 mb-2">
                    Your Review
                    <span class="text-gray-500 font-normal">(Optional)</span>
                </label>
                <textarea 
                    name="review" 
                    id="review" 
                    rows="6"
                    maxlength="1000"
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-lgu-button focus:border-lgu-button resize-none"
                    placeholder="Tell us about your experience with this facility. What did you like? What could be improved?">{{ old('review') }}</textarea>
                <div class="flex justify-between mt-2">
                    <p class="text-xs text-gray-500">
                        Share details about cleanliness, amenities, staff service, or any other aspects of your visit.
                    </p>
                    <p class="text-xs text-gray-500">
                        <span id="char-count">0</span>/1000
                    </p>
                </div>
                @error('review')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Privacy Notice -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                <div class="flex">
                    <i data-lucide="info" class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5"></i>
                    <div class="ml-3">
                        <p class="text-sm text-blue-800">
                            Your review will be visible to the public and will help other citizens make informed decisions. 
                            Please be respectful and constructive in your feedback.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
            <a href="{{ route('citizen.reservations.show', $booking->id) }}" 
               class="px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition">
                Cancel
            </a>
            <button type="submit" 
                    class="px-6 py-3 bg-lgu-highlight text-lgu-button-text font-bold rounded-lg hover:bg-lgu-hover transition shadow-md flex items-center gap-2">
                <i data-lucide="send" class="w-5 h-5"></i>
                Submit Review
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Star Rating System
let currentRating = 0;

function setRating(rating) {
    currentRating = rating;
    document.getElementById('rating-input').value = rating;
    updateStars();
    updateRatingText();
}

function updateStars() {
    const stars = document.querySelectorAll('.star-btn');
    stars.forEach((star, index) => {
        const svg = star.querySelector('svg');
        if (index < currentRating) {
            svg.classList.remove('text-gray-300');
            svg.classList.add('text-yellow-400');
        } else {
            svg.classList.remove('text-yellow-400');
            svg.classList.add('text-gray-300');
        }
    });
}

function updateRatingText() {
    const text = document.getElementById('rating-text');
    const labels = {
        1: 'Poor',
        2: 'Fair',
        3: 'Good',
        4: 'Very Good',
        5: 'Excellent'
    };
    
    if (currentRating > 0) {
        text.textContent = labels[currentRating];
        text.classList.remove('text-gray-600');
        text.classList.add('text-lgu-highlight', 'font-bold');
    } else {
        text.textContent = 'No rating selected';
        text.classList.add('text-gray-600');
        text.classList.remove('text-lgu-highlight', 'font-bold');
    }
}

// Hover effect for stars
document.querySelectorAll('.star-btn').forEach((star, index) => {
    star.addEventListener('mouseenter', () => {
        const stars = document.querySelectorAll('.star-btn svg');
        stars.forEach((s, i) => {
            if (i <= index) {
                s.classList.add('text-yellow-300');
            }
        });
    });
    
    star.addEventListener('mouseleave', () => {
        updateStars();
    });
});

// Character counter
const reviewTextarea = document.getElementById('review');
const charCount = document.getElementById('char-count');

reviewTextarea.addEventListener('input', function() {
    const count = this.value.length;
    charCount.textContent = count;
    
    if (count > 900) {
        charCount.classList.add('text-red-600', 'font-bold');
    } else {
        charCount.classList.remove('text-red-600', 'font-bold');
    }
});

// Initialize character count
charCount.textContent = reviewTextarea.value.length;

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    if (currentRating === 0) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Rating Required',
            text: 'Please select a star rating before submitting your review.',
            confirmButtonColor: '#D97706',
            confirmButtonText: 'OK'
        });
    }
});
</script>
@endpush
@endsection

