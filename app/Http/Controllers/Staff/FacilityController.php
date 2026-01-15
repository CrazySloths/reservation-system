<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FacilityController extends Controller
{
    /**
     * Display a listing of facilities (read-only for staff).
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $facilityType = $request->input('facility_type');
        $status = $request->input('status');
        $cityId = $request->input('city_id');

        $query = DB::connection('facilities_db')
            ->table('facilities')
            ->leftJoin('lgu_cities', 'facilities.lgu_city_id', '=', 'lgu_cities.id')
            ->select(
                'facilities.*',
                'lgu_cities.city_name'
            )
            ->whereNull('facilities.deleted_at');

        // Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('facilities.name', 'LIKE', "%{$search}%")
                  ->orWhere('facilities.address', 'LIKE', "%{$search}%")
                  ->orWhere('facilities.description', 'LIKE', "%{$search}%");
            });
        }

        // Filter by facility type
        if ($facilityType) {
            $query->where('facilities.type', $facilityType);
        }

        // Filter by status
        if ($status) {
            $query->where('facilities.status', $status);
        }

        // Filter by city
        if ($cityId) {
            $query->where('facilities.lgu_city_id', $cityId);
        }

        $facilities = $query->orderBy('lgu_cities.city_name')
            ->orderBy('facilities.name')
            ->paginate(12);

        // Get cities for filter dropdown
        $cities = DB::connection('facilities_db')
            ->table('lgu_cities')
            ->where('status', 'active')
            ->orderBy('city_name')
            ->get();

        // Facility types
        $facilityTypes = [
            'gymnasium' => 'Gymnasium',
            'convention_center' => 'Convention Center',
            'function_hall' => 'Function Hall',
            'sports_complex' => 'Sports Complex',
            'covered_court' => 'Covered Court',
            'auditorium' => 'Auditorium',
            'meeting_room' => 'Meeting Room',
            'other' => 'Other'
        ];

        // Status options
        $statusOptions = [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'under_maintenance' => 'Under Maintenance',
            'coming_soon' => 'Coming Soon'
        ];

        return view('staff.facilities.index', compact(
            'facilities',
            'cities',
            'facilityTypes',
            'statusOptions',
            'search',
            'facilityType',
            'status',
            'cityId'
        ));
    }

    /**
     * Display detailed information about a specific facility.
     */
    public function show($id)
    {
        $facility = DB::connection('facilities_db')
            ->table('facilities')
            ->leftJoin('lgu_cities', 'facilities.lgu_city_id', '=', 'lgu_cities.id')
            ->select('facilities.*', 'lgu_cities.city_name')
            ->where('facilities.facility_id', $id)
            ->whereNull('facilities.deleted_at')
            ->first();

        if (!$facility) {
            return redirect()->route('staff.facilities.index')
                ->with('error', 'Facility not found.');
        }

        // Decode JSON fields
        $facility->amenities = json_decode($facility->amenities ?? '[]', true);

        // Get recent bookings count
        $recentBookingsCount = DB::connection('facilities_db')
            ->table('bookings')
            ->where('facility_id', $id)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        return view('staff.facilities.show', compact('facility', 'recentBookingsCount'));
    }
}

