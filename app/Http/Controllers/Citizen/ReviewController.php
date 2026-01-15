<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * Display list of all user's reviews.
     */
    public function index(Request $request)
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        $facilityFilter = $request->input('facility_id');

        $query = DB::connection('facilities_db')
            ->table('facility_reviews')
            ->join('facilities', 'facility_reviews.facility_id', '=', 'facilities.facility_id')
            ->join('bookings', 'facility_reviews.booking_id', '=', 'bookings.id')
            ->select(
                'facility_reviews.*',
                'facilities.name as facility_name',
                'facilities.image_path as facility_image',
                'bookings.event_date',
                'bookings.start_time',
                'bookings.end_time'
            )
            ->where('facility_reviews.user_id', $userId)
            ->where('facility_reviews.is_visible', true);

        if ($facilityFilter) {
            $query->where('facility_reviews.facility_id', $facilityFilter);
        }

        $reviews = $query->orderBy('facility_reviews.created_at', 'desc')
            ->paginate(10);

        // Get facilities for filter
        $facilities = DB::connection('facilities_db')
            ->table('facilities')
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();

        return view('citizen.reviews.index', compact('reviews', 'facilities', 'facilityFilter'));
    }

    /**
     * Show form to submit a review for a completed booking.
     */
    public function create($bookingId)
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        // Check if booking exists and event has passed
        $booking = DB::connection('facilities_db')
            ->table('bookings')
            ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
            ->select(
                'bookings.*',
                'facilities.name as facility_name',
                'facilities.address as facility_address',
                'facilities.image_path as facility_image'
            )
            ->where('bookings.id', $bookingId)
            ->where('bookings.user_id', $userId)
            ->whereIn('bookings.status', ['confirmed', 'completed'])
            ->first();

        if (!$booking) {
            return redirect()->route('citizen.reservations')->with('error', 'Booking not found or not eligible for review.');
        }

        // Check if event has passed
        if (Carbon::parse($booking->end_time)->isFuture()) {
            return redirect()->route('citizen.reservations.show', $bookingId)
                ->with('error', 'You can only leave a review after your event has ended.');
        }

        // Check if review already exists
        $existingReview = DB::connection('facilities_db')
            ->table('facility_reviews')
            ->where('booking_id', $bookingId)
            ->where('user_id', $userId)
            ->first();

        if ($existingReview) {
            return redirect()->route('citizen.reviews.edit', $existingReview->id)->with('info', 'You have already submitted a review for this booking. You can edit it below.');
        }

        return view('citizen.reviews.create', compact('booking'));
    }

    /**
     * Store a new review.
     */
    public function store(Request $request)
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:facilities_db.bookings,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Verify the booking belongs to the user and event has passed
        $booking = DB::connection('facilities_db')
            ->table('bookings')
            ->where('id', $request->booking_id)
            ->where('user_id', $userId)
            ->whereIn('status', ['confirmed', 'completed'])
            ->first();

        if (!$booking) {
            return redirect()->route('citizen.reservations')->with('error', 'Invalid booking for review.');
        }

        // Verify event has passed
        if (Carbon::parse($booking->end_time)->isFuture()) {
            return redirect()->back()->with('error', 'You can only leave a review after your event has ended.');
        }

        // Check if review already exists
        $existingReview = DB::connection('facilities_db')
            ->table('facility_reviews')
            ->where('booking_id', $request->booking_id)
            ->where('user_id', $userId)
            ->first();

        if ($existingReview) {
            return redirect()->back()->with('error', 'You have already submitted a review for this booking.');
        }

        // Create the review
        DB::connection('facilities_db')->table('facility_reviews')->insert([
            'facility_id' => $booking->facility_id,
            'booking_id' => $request->booking_id,
            'user_id' => $userId,
            'user_name' => session('user_name'),
            'rating' => $request->rating,
            'review' => $request->review,
            'is_verified' => true, // Auto-verified for completed bookings
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()->route('citizen.reservations.show', $request->booking_id)->with('success', 'Thank you for your review!');
    }

    /**
     * Show form to edit an existing review.
     */
    public function edit($id)
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        $review = DB::connection('facilities_db')
            ->table('facility_reviews')
            ->join('bookings', 'facility_reviews.booking_id', '=', 'bookings.id')
            ->join('facilities', 'facility_reviews.facility_id', '=', 'facilities.facility_id')
            ->select(
                'facility_reviews.*',
                'facilities.name as facility_name',
                'facilities.address as facility_address',
                'facilities.image_path as facility_image',
                'bookings.start_time',
                'bookings.end_time'
            )
            ->where('facility_reviews.id', $id)
            ->where('facility_reviews.user_id', $userId)
            ->first();

        if (!$review) {
            return redirect()->route('citizen.reservations')->with('error', 'Review not found.');
        }

        return view('citizen.reviews.edit', compact('review'));
    }

    /**
     * Update an existing review.
     */
    public function update(Request $request, $id)
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Verify the review belongs to the user
        $review = DB::connection('facilities_db')
            ->table('facility_reviews')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$review) {
            return redirect()->route('citizen.reservations')->with('error', 'Review not found.');
        }

        // Update the review
        DB::connection('facilities_db')
            ->table('facility_reviews')
            ->where('id', $id)
            ->update([
                'rating' => $request->rating,
                'review' => $request->review,
                'updated_at' => Carbon::now(),
            ]);

        return redirect()->route('citizen.reservations.show', $review->booking_id)->with('success', 'Review updated successfully!');
    }

    /**
     * Archive a review (soft delete).
     */
    public function destroy($id)
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        $review = DB::connection('facilities_db')
            ->table('facility_reviews')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$review) {
            return redirect()->route('citizen.reservations')->with('error', 'Review not found.');
        }

        // Soft delete: Hide the review instead of permanent deletion
        DB::connection('facilities_db')
            ->table('facility_reviews')
            ->where('id', $id)
            ->update([
                'is_visible' => false,
                'updated_at' => Carbon::now(),
            ]);

        return redirect()->route('citizen.reservations.show', $review->booking_id)->with('success', 'Review archived successfully.');
    }

    /**
     * View all reviews for a facility (public view).
     */
    public function facilityReviews($facilityId)
    {
        $facility = DB::connection('facilities_db')
            ->table('facilities')
            ->where('facility_id', $facilityId)
            ->whereNull('deleted_at')
            ->first();

        if (!$facility) {
            return redirect()->route('citizen.browse-facilities')->with('error', 'Facility not found.');
        }

        $reviews = DB::connection('facilities_db')
            ->table('facility_reviews')
            ->where('facility_id', $facilityId)
            ->where('is_visible', true)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Calculate average rating
        $avgRating = DB::connection('facilities_db')
            ->table('facility_reviews')
            ->where('facility_id', $facilityId)
            ->where('is_visible', true)
            ->avg('rating');

        $totalReviews = DB::connection('facilities_db')
            ->table('facility_reviews')
            ->where('facility_id', $facilityId)
            ->where('is_visible', true)
            ->count();

        // Get rating distribution
        $ratingDistribution = DB::connection('facilities_db')
            ->table('facility_reviews')
            ->select('rating', DB::raw('count(*) as count'))
            ->where('facility_id', $facilityId)
            ->where('is_visible', true)
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get()
            ->pluck('count', 'rating')
            ->toArray();

        return view('citizen.reviews.facility', compact('facility', 'reviews', 'avgRating', 'totalReviews', 'ratingDistribution'));
    }
}

