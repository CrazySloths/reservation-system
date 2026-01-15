<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PricingController extends Controller
{
    /**
     * Display pricing management page.
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
            ->whereNull('facilities.deleted_at');

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

        return view('admin.pricing.index', compact(
            'facilities',
            'cities',
            'search',
            'cityId'
        ));
    }

    /**
     * Update facility pricing.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'per_person_rate' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get facility name for logging
            $facility = DB::connection('facilities_db')
                ->table('facilities')
                ->where('facility_id', $id)
                ->first();

            if (!$facility) {
                return response()->json([
                    'success' => false,
                    'message' => 'Facility not found.'
                ], 404);
            }

            // Update pricing (only per-person rate)
            DB::connection('facilities_db')->table('facilities')
                ->where('facility_id', $id)
                ->update([
                    'per_person_rate' => $request->per_person_rate,
                    'updated_at' => now()
                ]);

            // Log activity
            DB::connection('auth_db')->table('audit_logs')->insert([
                'user_id' => session('user_id'),
                'action' => 'update_pricing',
                'model' => 'Facility',
                'model_id' => $id,
                'changes' => json_encode([
                    'facility_name' => $facility->name,
                    'per_person_rate' => $request->per_person_rate
                ]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pricing updated successfully!'
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to update pricing: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update pricing. Please try again.'
            ], 500);
        }
    }

    /**
     * Bulk update pricing (apply percentage increase/decrease).
     */
    public function bulkUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'adjustment_type' => 'required|in:increase,decrease',
            'adjustment_percentage' => 'required|numeric|min:0|max:100',
            'apply_to' => 'required|in:all,city,selected',
            'city_id' => 'required_if:apply_to,city|exists:facilities_db.lgu_cities,city_id',
            'facility_ids' => 'required_if:apply_to,selected|array',
            'facility_ids.*' => 'exists:facilities_db.facilities,facility_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $multiplier = $request->adjustment_type === 'increase' 
                ? (1 + ($request->adjustment_percentage / 100))
                : (1 - ($request->adjustment_percentage / 100));

            $query = DB::connection('facilities_db')->table('facilities')
                ->whereNull('deleted_at');

            // Apply filters
            if ($request->apply_to === 'city') {
                $query->where('city_id', $request->city_id);
            } elseif ($request->apply_to === 'selected') {
                $query->whereIn('facility_id', $request->facility_ids);
            }

            // Update pricing
            $query->update([
                'base_rate' => DB::raw("ROUND(base_rate * {$multiplier}, 2)"),
                'extension_rate' => DB::raw("ROUND(extension_rate * {$multiplier}, 2)"),
                'per_person_rate' => DB::raw("ROUND(per_person_rate * {$multiplier}, 2)"),
                'updated_at' => now()
            ]);

            $affectedCount = $query->count();

            // Log activity
            DB::connection('auth_db')->table('audit_logs')->insert([
                'user_id' => session('user_id'),
                'action' => 'bulk_update_pricing',
                'model' => 'Facility',
                'model_id' => null,
                'changes' => json_encode([
                    'adjustment_type' => $request->adjustment_type,
                    'adjustment_percentage' => $request->adjustment_percentage,
                    'apply_to' => $request->apply_to,
                    'affected_count' => $affectedCount
                ]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Pricing updated for {$affectedCount} facilities successfully!"
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to bulk update pricing: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update pricing. Please try again.'
            ], 500);
        }
    }
}

