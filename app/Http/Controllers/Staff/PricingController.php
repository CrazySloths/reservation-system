<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PricingController extends Controller
{
    /**
     * Display pricing information (read-only for staff).
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $cityId = $request->input('city_id');

        $query = DB::connection('facilities_db')
            ->table('facilities')
            ->leftJoin('lgu_cities', 'facilities.lgu_city_id', '=', 'lgu_cities.id')
            ->select(
                'facilities.facility_id',
                'facilities.name',
                'facilities.lgu_city_id',
                'lgu_cities.city_name',
                'facilities.per_person_rate'
            )
            ->whereNull('facilities.deleted_at')
            ->where('facilities.is_available', 1);

        // Search
        if ($search) {
            $query->where('facilities.name', 'LIKE', "%{$search}%");
        }

        // Filter by city
        if ($cityId) {
            $query->where('facilities.lgu_city_id', $cityId);
        }

        $facilities = $query->orderBy('lgu_cities.city_name')
            ->orderBy('facilities.name')
            ->get();

        // Get cities for filter dropdown
        $cities = DB::connection('facilities_db')
            ->table('lgu_cities')
            ->where('status', 'active')
            ->orderBy('city_name')
            ->get();

        return view('staff.pricing.index', compact(
            'facilities',
            'cities',
            'search',
            'cityId'
        ));
    }
}

