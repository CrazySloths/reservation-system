<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Location;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    /**
     * Display a listing of facilities (Public Access)
     */
    public function index(Request $request)
    {
        $query = Facility::with(['location']) // Photos disabled - table not created yet
            ->active()
            ->ordered();

        // Filter by location
        if ($request->has('location_id') && $request->location_id) {
            $query->where('location_id', $request->location_id);
        }

        // Filter by facility type
        if ($request->has('facility_type') && $request->facility_type) {
            $query->where('facility_type', $request->facility_type);
        }

        // Filter by capacity
        if ($request->has('min_capacity') && $request->min_capacity) {
            $query->where('capacity', '>=', $request->min_capacity);
        }

        // Search by name
        if ($request->has('search') && $request->search) {
            $query->where('facility_name', 'like', '%' . $request->search . '%');
        }

        // Sort options
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'capacity_asc':
                    $query->orderBy('capacity', 'asc');
                    break;
                case 'capacity_desc':
                    $query->orderBy('capacity', 'desc');
                    break;
                case 'price_asc':
                    $query->orderBy('hourly_rate', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('hourly_rate', 'desc');
                    break;
            }
        }

        $facilities = $query->paginate(12);
        $locations = Location::where('is_active', true)->get();

        return view('facilities.index', compact('facilities', 'locations'));
    }

    /**
     * Display the specified facility (Public Access)
     */
    public function show($id)
    {
        $facility = Facility::with([
            'location',
            // 'photos' => function($query) {
            //     $query->orderBy('display_order');
            // },
            // 'equipment' => function($query) {
            //     $query->where('is_available', true);
            // }
        ])
        ->where('status', 'active')
        ->findOrFail($id);

        // Get similar facilities (same type, same location, excluding current)
        $similarFacilities = Facility::with(['location', 'photos'])
            ->active()
            ->where('facility_type', $facility->facility_type)
            ->where('location_id', $facility->location_id)
            ->where('id', '!=', $facility->id)
            ->limit(3)
            ->get();

        return view('facilities.show', compact('facility', 'similarFacilities'));
    }

    /**
     * Check facility availability (AJAX)
     */
    public function checkAvailability(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $facility = Facility::findOrFail($id);

        $isAvailable = $facility->isAvailableAt(
            $request->date,
            $request->start_time,
            $request->end_time
        );

        return response()->json([
            'available' => $isAvailable,
            'message' => $isAvailable 
                ? 'Facility is available for the selected date and time.' 
                : 'Facility is not available for the selected date and time. Please choose another time slot.',
        ]);
    }
}
