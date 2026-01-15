<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class FacilityController extends Controller
{
    /**
     * Display a listing of facilities.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $facilityType = $request->input('facility_type');
        $status = $request->input('status');
        $cityId = $request->input('city_id');
        $showDeleted = $request->input('show_deleted', false);

        $query = DB::connection('facilities_db')
            ->table('facilities')
            ->leftJoin('lgu_cities', 'facilities.lgu_city_id', '=', 'lgu_cities.id')
            ->select(
                'facilities.*',
                'lgu_cities.city_name'
            );

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

        // Show deleted
        if ($showDeleted) {
            $query->whereNotNull('facilities.deleted_at');
        } else {
            $query->whereNull('facilities.deleted_at');
        }

        $facilities = $query->orderBy('facilities.created_at', 'desc')
            ->paginate(15);

        // Get counts for tabs
        $activeFacilitiesCount = DB::connection('facilities_db')
            ->table('facilities')
            ->whereNull('deleted_at')
            ->count();

        $archivedFacilitiesCount = DB::connection('facilities_db')
            ->table('facilities')
            ->whereNotNull('deleted_at')
            ->count();

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

        return view('admin.facilities.index', compact(
            'facilities',
            'cities',
            'facilityTypes',
            'statusOptions',
            'search',
            'facilityType',
            'status',
            'cityId',
            'showDeleted',
            'activeFacilitiesCount',
            'archivedFacilitiesCount'
        ));
    }

    /**
     * Show the form for creating a new facility.
     */
    public function create()
    {
        // Get cities for dropdown
        $cities = DB::connection('facilities_db')
            ->table('lgu_cities')
            ->where('is_active', 1)
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

        // Amenities list
        $amenitiesList = [
            'air_conditioning' => 'Air Conditioning',
            'sound_system' => 'Sound System',
            'projector' => 'Projector & Screen',
            'wifi' => 'WiFi Internet',
            'parking' => 'Parking Space',
            'kitchen' => 'Kitchen Facilities',
            'restrooms' => 'Restrooms',
            'stage' => 'Stage/Platform',
            'tables_chairs' => 'Tables & Chairs',
            'security' => 'Security Service',
            'generator' => 'Backup Generator',
            'elevator' => 'Elevator Access'
        ];

        return view('admin.facilities.create', compact(
            'cities',
            'facilityTypes',
            'statusOptions',
            'amenitiesList'
        ));
    }

    /**
     * Store a newly created facility in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'city_id' => 'required|exists:facilities_db.lgu_cities,city_id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:gymnasium,convention_center,function_hall,sports_complex,covered_court,auditorium,meeting_room,other',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'address' => 'required|string',
            'google_maps_url' => 'nullable|url',
            'base_rate' => 'required|numeric|min:0',
            'per_person_rate' => 'nullable|numeric|min:0',
            'minimum_hours' => 'required|integer|min:1|max:24',
            'extension_rate' => 'required|numeric|min:0',
            'amenities' => 'nullable|array',
            'status' => 'required|in:active,inactive,under_maintenance,coming_soon',
            'is_available' => 'nullable|boolean',
            'operating_hours' => 'nullable|array',
            'rules' => 'nullable|string',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image_path')) {
                $imagePath = $request->file('image_path')->store('facilities', 'public');
            }

            // Insert facility
            $facilityId = DB::connection('facilities_db')->table('facilities')->insertGetId([
                'city_id' => $request->city_id,
                'name' => $request->name,
                'type' => $request->type,
                'description' => $request->description,
                'capacity' => $request->capacity,
                'address' => $request->address,
                'google_maps_url' => $request->google_maps_url,
                'base_rate' => $request->base_rate,
                'per_person_rate' => $request->per_person_rate ?? 0,
                'minimum_hours' => $request->minimum_hours,
                'extension_rate' => $request->extension_rate,
                'amenities' => json_encode($request->amenities ?? []),
                'status' => $request->status,
                'is_available' => $request->is_available ?? ($request->status === 'active' ? 1 : 0),
                'operating_hours' => json_encode($request->operating_hours ?? []),
                'rules' => $request->rules,
                'image_path' => $imagePath,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Log activity
            DB::connection('auth_db')->table('audit_logs')->insert([
                'user_id' => session('user_id'),
                'action' => 'create',
                'model' => 'Facility',
                'model_id' => $facilityId,
                'changes' => json_encode($request->except(['_token', 'image_path'])),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now()
            ]);

            return redirect()->route('admin.facilities.index')
                ->with('success', 'Facility created successfully!');

        } catch (\Exception $e) {
            \Log::error('Failed to create facility: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create facility. Please try again.')
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified facility.
     */
    public function edit($id)
    {
        $facility = DB::connection('facilities_db')
            ->table('facilities')
            ->leftJoin('lgu_cities', 'facilities.city_id', '=', 'lgu_cities.city_id')
            ->select('facilities.*', 'lgu_cities.city_name')
            ->where('facilities.facility_id', $id)
            ->first();

        if (!$facility) {
            return redirect()->route('admin.facilities.index')
                ->with('error', 'Facility not found.');
        }

        // Decode JSON fields
        $facility->amenities = json_decode($facility->amenities ?? '[]', true);
        $facility->operating_hours = json_decode($facility->operating_hours ?? '[]', true);

        // Get cities for dropdown
        $cities = DB::connection('facilities_db')
            ->table('lgu_cities')
            ->where('is_active', 1)
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

        // Amenities list
        $amenitiesList = [
            'air_conditioning' => 'Air Conditioning',
            'sound_system' => 'Sound System',
            'projector' => 'Projector & Screen',
            'wifi' => 'WiFi Internet',
            'parking' => 'Parking Space',
            'kitchen' => 'Kitchen Facilities',
            'restrooms' => 'Restrooms',
            'stage' => 'Stage/Platform',
            'tables_chairs' => 'Tables & Chairs',
            'security' => 'Security Service',
            'generator' => 'Backup Generator',
            'elevator' => 'Elevator Access'
        ];

        return view('admin.facilities.edit', compact(
            'facility',
            'cities',
            'facilityTypes',
            'statusOptions',
            'amenitiesList'
        ));
    }

    /**
     * Update the specified facility in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'city_id' => 'required|exists:facilities_db.lgu_cities,city_id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:gymnasium,convention_center,function_hall,sports_complex,covered_court,auditorium,meeting_room,other',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'address' => 'required|string',
            'google_maps_url' => 'nullable|url',
            'base_rate' => 'required|numeric|min:0',
            'per_person_rate' => 'nullable|numeric|min:0',
            'minimum_hours' => 'required|integer|min:1|max:24',
            'extension_rate' => 'required|numeric|min:0',
            'amenities' => 'nullable|array',
            'status' => 'required|in:active,inactive,under_maintenance,coming_soon',
            'is_available' => 'nullable|boolean',
            'operating_hours' => 'nullable|array',
            'rules' => 'nullable|string',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Get existing facility
            $facility = DB::connection('facilities_db')
                ->table('facilities')
                ->where('facility_id', $id)
                ->first();

            if (!$facility) {
                return redirect()->route('admin.facilities.index')
                    ->with('error', 'Facility not found.');
            }

            // Handle image upload
            $imagePath = $facility->image_path;
            if ($request->hasFile('image_path')) {
                // Delete old image
                if ($facility->image_path) {
                    Storage::disk('public')->delete($facility->image_path);
                }
                $imagePath = $request->file('image_path')->store('facilities', 'public');
            }

            // Update facility
            DB::connection('facilities_db')->table('facilities')
                ->where('facility_id', $id)
                ->update([
                    'city_id' => $request->city_id,
                    'name' => $request->name,
                    'type' => $request->type,
                    'description' => $request->description,
                    'capacity' => $request->capacity,
                    'address' => $request->address,
                    'google_maps_url' => $request->google_maps_url,
                    'base_rate' => $request->base_rate,
                    'per_person_rate' => $request->per_person_rate ?? 0,
                    'minimum_hours' => $request->minimum_hours,
                    'extension_rate' => $request->extension_rate,
                    'amenities' => json_encode($request->amenities ?? []),
                    'status' => $request->status,
                    'is_available' => $request->is_available ?? ($request->status === 'active' ? 1 : 0),
                    'operating_hours' => json_encode($request->operating_hours ?? []),
                    'rules' => $request->rules,
                    'image_path' => $imagePath,
                    'updated_at' => now()
                ]);

            // Log activity
            DB::connection('auth_db')->table('audit_logs')->insert([
                'user_id' => session('user_id'),
                'action' => 'update',
                'model' => 'Facility',
                'model_id' => $id,
                'changes' => json_encode($request->except(['_token', '_method', 'image_path'])),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now()
            ]);

            return redirect()->route('admin.facilities.index')
                ->with('success', 'Facility updated successfully!');

        } catch (\Exception $e) {
            \Log::error('Failed to update facility: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update facility. Please try again.')
                ->withInput();
        }
    }

    /**
     * Soft delete the specified facility.
     */
    public function destroy($id)
    {
        try {
            // Check if facility has active bookings
            $activeBookings = DB::connection('facilities_db')
                ->table('bookings')
                ->where('facility_id', $id)
                ->whereIn('status', ['pending', 'staff_verified', 'paid', 'confirmed'])
                ->count();

            if ($activeBookings > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot delete facility with active bookings. Please cancel all bookings first.');
            }

            // Soft delete
            DB::connection('facilities_db')->table('facilities')
                ->where('facility_id', $id)
                ->update([
                    'deleted_at' => now(),
                    'is_available' => 0
                ]);

            // Log activity
            DB::connection('auth_db')->table('audit_logs')->insert([
                'user_id' => session('user_id'),
                'action' => 'delete',
                'model' => 'Facility',
                'model_id' => $id,
                'changes' => json_encode(['deleted' => true]),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now()
            ]);

            return redirect()->route('admin.facilities.index')
                ->with('success', 'Facility deleted successfully!');

        } catch (\Exception $e) {
            \Log::error('Failed to delete facility: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete facility. Please try again.');
        }
    }

    /**
     * Restore a soft-deleted facility.
     */
    public function restore($id)
    {
        try {
            DB::connection('facilities_db')->table('facilities')
                ->where('facility_id', $id)
                ->update([
                    'deleted_at' => null,
                    'is_available' => 1
                ]);

            // Log activity
            DB::connection('auth_db')->table('audit_logs')->insert([
                'user_id' => session('user_id'),
                'action' => 'restore',
                'model' => 'Facility',
                'model_id' => $id,
                'changes' => json_encode(['restored' => true]),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now()
            ]);

            return redirect()->route('admin.facilities.index')
                ->with('success', 'Facility restored successfully!');

        } catch (\Exception $e) {
            \Log::error('Failed to restore facility: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to restore facility. Please try again.');
        }
    }
}

