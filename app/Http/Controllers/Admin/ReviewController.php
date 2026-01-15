<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReviewController extends Controller
{
    /**
     * Display list of all reviews with moderation controls.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $facilityId = $request->input('facility_id');
        $rating = $request->input('rating');

        $query = DB::connection('facilities_db')
            ->table('facility_reviews')
            ->join('facilities', 'facility_reviews.facility_id', '=', 'facilities.facility_id')
            ->leftJoin('bookings', 'facility_reviews.booking_id', '=', 'bookings.id')
            ->select(
                'facility_reviews.*',
                'facilities.name as facility_name',
                'facilities.image_path as facility_image',
                'bookings.event_date',
                'bookings.start_time',
                'bookings.end_time'
            )
            ->where('facility_reviews.is_visible', true);

        // Search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('facility_reviews.user_name', 'like', "%{$search}%")
                  ->orWhere('facility_reviews.review', 'like', "%{$search}%")
                  ->orWhere('facilities.name', 'like', "%{$search}%");
            });
        }

        // Facility filter
        if ($facilityId) {
            $query->where('facility_reviews.facility_id', $facilityId);
        }

        // Rating filter
        if ($rating) {
            $query->where('facility_reviews.rating', $rating);
        }

        $reviews = $query->orderBy('facility_reviews.created_at', 'desc')
            ->paginate(15);

        // Get facilities for filter
        $facilities = DB::connection('facilities_db')
            ->table('facilities')
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get stats
        $totalReviews = DB::connection('facilities_db')
            ->table('facility_reviews')
            ->where('is_visible', true)
            ->count();

        $avgRating = DB::connection('facilities_db')
            ->table('facility_reviews')
            ->where('is_visible', true)
            ->avg('rating');

        return view('admin.reviews.index', compact(
            'reviews',
            'facilities',
            'search',
            'facilityId',
            'rating',
            'totalReviews',
            'avgRating'
        ));
    }

    /**
     * Show detailed view of a specific review.
     */
    public function show($id)
    {
        $review = DB::connection('facilities_db')
            ->table('facility_reviews')
            ->join('facilities', 'facility_reviews.facility_id', '=', 'facilities.facility_id')
            ->leftJoin('bookings', 'facility_reviews.booking_id', '=', 'bookings.id')
            ->select(
                'facility_reviews.*',
                'facilities.name as facility_name',
                'facilities.image_path as facility_image',
                'facilities.address as facility_address',
                'bookings.event_date',
                'bookings.start_time',
                'bookings.end_time',
                'bookings.purpose'
            )
            ->where('facility_reviews.id', $id)
            ->first();

        if (!$review) {
            return redirect()->route('admin.reviews.index')
                ->with('error', 'Review not found.');
        }

        return view('admin.reviews.show', compact('review'));
    }
}

