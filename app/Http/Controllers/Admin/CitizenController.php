<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CitizenController extends Controller
{
    /**
     * Display a listing of citizens
     */
    public function index(Request $request)
    {
        // Get all citizens from auth_db
        // ONLY show: Citizens with subsystem_role_id = 4 OR users with NO subsystem_role_id (regular citizens)
        // Exclude: Admin (subsystem_role_id=1), Staff (subsystem_role_id=3), Treasurer (subsystem_role_id=5)
        $query = DB::connection('auth_db')->table('users')
            ->where(function($q) {
                $q->where('subsystem_role_id', 4)
                  ->orWhere(function($q2) {
                      $q2->whereNull('subsystem_role_id')
                         ->whereNull('role_id'); // Also exclude users with system roles
                  });
            })
            ->select('*');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('users.name', 'LIKE', "%{$search}%")
                  ->orWhere('users.email', 'LIKE', "%{$search}%")
                  ->orWhere('users.phone', 'LIKE', "%{$search}%");
            });
        }

        // Filter by city
        if ($request->filled('city_id')) {
            $query->where('users.city_id', $request->city_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('users.status', $request->status);
        }

        // Order by most recently registered
        $query->orderBy('created_at', 'desc');

        $citizens = $query->paginate(20)->withQueryString();

        // Get cities from facilities_db and map to citizens
        $cities = DB::connection('facilities_db')->table('lgu_cities')
            ->where('status', 'active')
            ->orderBy('city_name')
            ->get();
        
        $citiesById = $cities->keyBy('id');
        
        // Enrich citizens with city information
        foreach ($citizens->items() as $citizen) {
            $city = $citiesById->get($citizen->city_id);
            $citizen->city_name = $city ? $city->city_name : 'N/A';
        }

        // Get citizen statistics
        $citizenIds = collect($citizens->items())->pluck('id');
        $stats = [];
        
        foreach ($citizenIds as $citizenId) {
            $bookings = DB::connection('facilities_db')->table('bookings')
                ->where('user_id', $citizenId);
            
            $stats[$citizenId] = [
                'total_bookings' => $bookings->count(),
                'completed_bookings' => (clone $bookings)->where('status', 'completed')->count(),
                'total_spent' => (clone $bookings)->whereIn('status', ['confirmed', 'completed'])->sum('total_amount'),
            ];
        }

        return view('admin.citizens.index', compact('citizens', 'cities', 'stats'));
    }

    /**
     * Display the specified citizen
     */
    public function show($id)
    {
        $citizen = DB::connection('auth_db')->table('users')
            ->where('id', $id)
            ->first();

        if (!$citizen) {
            return redirect()->route('admin.citizens.index')
                ->with('error', 'Citizen not found.');
        }
        
        // Get city information from facilities_db
        if ($citizen->city_id) {
            $city = DB::connection('facilities_db')->table('lgu_cities')
                ->where('id', $citizen->city_id)
                ->first();
            $citizen->city_name = $city ? $city->city_name : 'N/A';
        } else {
            $citizen->city_name = 'N/A';
        }

        // Get citizen statistics
        $bookings = DB::connection('facilities_db')->table('bookings')
            ->where('user_id', $id);

        $stats = [
            'total_bookings' => $bookings->count(),
            'confirmed' => (clone $bookings)->where('status', 'confirmed')->count(),
            'completed' => (clone $bookings)->where('status', 'completed')->count(),
            'cancelled' => (clone $bookings)->where('status', 'cancelled')->count(),
            'total_spent' => (clone $bookings)->whereIn('status', ['confirmed', 'completed'])->sum('total_amount'),
        ];

        // Get recent bookings
        $recentBookings = DB::connection('facilities_db')->table('bookings')
            ->leftJoin('facilities', 'bookings.facility_id', '=', 'facilities.id')
            ->where('bookings.user_id', $id)
            ->select(
                'bookings.*',
                'facilities.facility_name'
            )
            ->orderBy('bookings.created_at', 'desc')
            ->limit(5)
            ->get();

        // Get reviews
        $reviews = DB::connection('facilities_db')->table('facility_reviews')
            ->leftJoin('facilities', 'facility_reviews.facility_id', '=', 'facilities.id')
            ->where('facility_reviews.user_id', $id)
            ->whereNull('facility_reviews.deleted_at')
            ->select(
                'facility_reviews.*',
                'facilities.facility_name'
            )
            ->orderBy('facility_reviews.created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.citizens.show', compact('citizen', 'stats', 'recentBookings', 'reviews'));
    }

    /**
     * Toggle citizen status (active/inactive)
     */
    public function toggleStatus($id)
    {
        $citizen = DB::connection('auth_db')->table('users')
            ->where('id', $id)
            ->first();

        if (!$citizen) {
            return response()->json([
                'success' => false,
                'message' => 'Citizen not found.'
            ], 404);
        }

        $newStatus = $citizen->status === 'active' ? 'inactive' : 'active';

        DB::connection('auth_db')->table('users')
            ->where('id', $id)
            ->update([
                'status' => $newStatus,
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'status' => $newStatus,
            'message' => 'Citizen account ' . ($newStatus === 'active' ? 'activated' : 'deactivated') . ' successfully!'
        ]);
    }

    /**
     * Display citizen's booking history
     */
    public function bookings($id)
    {
        $citizen = DB::connection('auth_db')->table('users')
            ->where('id', $id)
            ->first();

        if (!$citizen) {
            return redirect()->route('admin.citizens.index')
                ->with('error', 'Citizen not found.');
        }

        $bookings = DB::connection('facilities_db')->table('bookings')
            ->leftJoin('facilities', 'bookings.facility_id', '=', 'facilities.id')
            ->where('bookings.user_id', $id)
            ->select(
                'bookings.*',
                'facilities.facility_name'
            )
            ->orderBy('bookings.created_at', 'desc')
            ->paginate(15);

        return view('admin.citizens.bookings', compact('citizen', 'bookings'));
    }
}

