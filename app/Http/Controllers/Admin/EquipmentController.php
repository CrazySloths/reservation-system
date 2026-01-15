<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class EquipmentController extends Controller
{
    /**
     * Display a listing of equipment.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');
        $availability = $request->input('availability');
        $showDeleted = $request->input('show_deleted', false);

        $query = DB::connection('facilities_db')->table('equipment_items');

        // Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Filter by category
        if ($category) {
            $query->where('category', $category);
        }

        // Filter by availability
        if ($availability !== null && $availability !== '') {
            $query->where('is_available', $availability);
        }

        // Show archived
        if ($showDeleted) {
            $query->whereNotNull('deleted_at');
        } else {
            $query->whereNull('deleted_at');
        }

        $equipment = $query->orderBy('created_at', 'desc')
            ->paginate(15);

        // Get counts for tabs
        $activeEquipmentCount = DB::connection('facilities_db')
            ->table('equipment_items')
            ->whereNull('deleted_at')
            ->count();

        $archivedEquipmentCount = DB::connection('facilities_db')
            ->table('equipment_items')
            ->whereNotNull('deleted_at')
            ->count();

        // Categories
        $categories = [
            'chairs' => 'Chairs',
            'tables' => 'Tables',
            'sound_system' => 'Sound System',
            'lighting' => 'Lighting',
            'decoration' => 'Decoration',
            'kitchen_equipment' => 'Kitchen Equipment',
            'sports_equipment' => 'Sports Equipment',
            'stage_equipment' => 'Stage Equipment',
            'tents_canopy' => 'Tents & Canopy',
            'other' => 'Other'
        ];

        return view('admin.equipment.index', compact(
            'equipment',
            'categories',
            'search',
            'category',
            'availability',
            'showDeleted',
            'activeEquipmentCount',
            'archivedEquipmentCount'
        ));
    }

    /**
     * Show the form for creating new equipment.
     */
    public function create()
    {
        // Categories
        $categories = [
            'chairs' => 'Chairs',
            'tables' => 'Tables',
            'sound_system' => 'Sound System',
            'lighting' => 'Lighting',
            'decoration' => 'Decoration',
            'kitchen_equipment' => 'Kitchen Equipment',
            'sports_equipment' => 'Sports Equipment',
            'stage_equipment' => 'Stage Equipment',
            'tents_canopy' => 'Tents & Canopy',
            'other' => 'Other'
        ];

        return view('admin.equipment.create', compact('categories'));
    }

    /**
     * Store a newly created equipment in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price_per_unit' => 'required|numeric|min:0',
            'quantity_available' => 'required|integer|min:0',
            'is_available' => 'nullable|boolean',
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
                $imagePath = $request->file('image_path')->store('equipment', 'public');
            }

            // Insert equipment
            $equipmentId = DB::connection('facilities_db')->table('equipment_items')->insertGetId([
                'name' => $request->name,
                'category' => $request->category,
                'description' => $request->description,
                'price_per_unit' => $request->price_per_unit,
                'quantity_available' => $request->quantity_available,
                'is_available' => $request->is_available ?? 1,
                'image_path' => $imagePath,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Log activity
            DB::connection('auth_db')->table('audit_logs')->insert([
                'user_id' => session('user_id'),
                'action' => 'create',
                'model' => 'Equipment',
                'model_id' => $equipmentId,
                'changes' => json_encode($request->except(['_token', 'image_path'])),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now()
            ]);

            return redirect()->route('admin.equipment.index')
                ->with('success', 'Equipment added successfully!');

        } catch (\Exception $e) {
            \Log::error('Failed to create equipment: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to add equipment. Please try again.')
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified equipment.
     */
    public function edit($id)
    {
        $equipment = DB::connection('facilities_db')
            ->table('equipment_items')
            ->where('id', $id)
            ->first();

        if (!$equipment) {
            return redirect()->route('admin.equipment.index')
                ->with('error', 'Equipment not found.');
        }

        // Categories
        $categories = [
            'chairs' => 'Chairs',
            'tables' => 'Tables',
            'sound_system' => 'Sound System',
            'lighting' => 'Lighting',
            'decoration' => 'Decoration',
            'kitchen_equipment' => 'Kitchen Equipment',
            'sports_equipment' => 'Sports Equipment',
            'stage_equipment' => 'Stage Equipment',
            'tents_canopy' => 'Tents & Canopy',
            'other' => 'Other'
        ];

        return view('admin.equipment.edit', compact('equipment', 'categories'));
    }

    /**
     * Update the specified equipment in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price_per_unit' => 'required|numeric|min:0',
            'quantity_available' => 'required|integer|min:0',
            'is_available' => 'nullable|boolean',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Get existing equipment
            $equipment = DB::connection('facilities_db')
                ->table('equipment_items')
                ->where('id', $id)
                ->first();

            if (!$equipment) {
                return redirect()->route('admin.equipment.index')
                    ->with('error', 'Equipment not found.');
            }

            // Handle image upload
            $imagePath = $equipment->image_path;
            if ($request->hasFile('image_path')) {
                // Delete old image
                if ($equipment->image_path) {
                    Storage::disk('public')->delete($equipment->image_path);
                }
                $imagePath = $request->file('image_path')->store('equipment', 'public');
            }

            // Update equipment
            DB::connection('facilities_db')->table('equipment_items')
                ->where('id', $id)
                ->update([
                    'name' => $request->name,
                    'category' => $request->category,
                    'description' => $request->description,
                    'price_per_unit' => $request->price_per_unit,
                    'quantity_available' => $request->quantity_available,
                    'is_available' => $request->is_available ?? 0,
                    'image_path' => $imagePath,
                    'updated_at' => now()
                ]);

            // Log activity
            DB::connection('auth_db')->table('audit_logs')->insert([
                'user_id' => session('user_id'),
                'action' => 'update',
                'model' => 'Equipment',
                'model_id' => $id,
                'changes' => json_encode($request->except(['_token', '_method', 'image_path'])),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now()
            ]);

            return redirect()->route('admin.equipment.index')
                ->with('success', 'Equipment updated successfully!');

        } catch (\Exception $e) {
            \Log::error('Failed to update equipment: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update equipment. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified equipment from storage.
     */
    public function destroy($id)
    {
        try {
            // Check if equipment is in active bookings
            $activeBookings = DB::connection('facilities_db')
                ->table('booking_equipment')
                ->join('bookings', 'booking_equipment.booking_id', '=', 'bookings.id')
                ->where('booking_equipment.equipment_item_id', $id)
                ->whereIn('bookings.status', ['pending', 'staff_verified', 'paid', 'confirmed'])
                ->count();

            if ($activeBookings > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot delete equipment that is currently in active bookings.');
            }

            // Get equipment
            $equipment = DB::connection('facilities_db')
                ->table('equipment_items')
                ->where('id', $id)
                ->first();

            if (!$equipment) {
                return redirect()->route('admin.equipment.index')
                    ->with('error', 'Equipment not found.');
            }

            // Soft delete equipment (archive)
            DB::connection('facilities_db')->table('equipment_items')
                ->where('id', $id)
                ->update(['deleted_at' => now()]);

            // Log activity
            DB::connection('auth_db')->table('audit_logs')->insert([
                'user_id' => session('user_id'),
                'action' => 'delete',
                'model' => 'Equipment',
                'model_id' => $id,
                'changes' => json_encode(['deleted' => true, 'name' => $equipment->name]),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now()
            ]);

            return redirect()->route('admin.equipment.index')
                ->with('success', 'Equipment archived successfully!');

        } catch (\Exception $e) {
            \Log::error('Failed to delete equipment: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete equipment. Please try again.');
        }
    }

    /**
     * Restore archived equipment.
     */
    public function restore($id)
    {
        try {
            $equipment = DB::connection('facilities_db')
                ->table('equipment_items')
                ->where('id', $id)
                ->whereNotNull('deleted_at')
                ->first();

            if (!$equipment) {
                return redirect()->route('admin.equipment.index')
                    ->with('error', 'Equipment not found.');
            }

            // Restore equipment
            DB::connection('facilities_db')->table('equipment_items')
                ->where('id', $id)
                ->update(['deleted_at' => null]);

            // Log activity
            DB::connection('auth_db')->table('audit_logs')->insert([
                'user_id' => session('user_id'),
                'action' => 'restore',
                'model' => 'Equipment',
                'model_id' => $id,
                'changes' => json_encode(['restored' => true, 'name' => $equipment->name]),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now()
            ]);

            return redirect()->route('admin.equipment.index')
                ->with('success', 'Equipment restored successfully!');

        } catch (\Exception $e) {
            \Log::error('Failed to restore equipment: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to restore equipment. Please try again.');
        }
    }

    /**
     * Update equipment availability status.
     */
    public function toggleAvailability($id)
    {
        try {
            $equipment = DB::connection('facilities_db')
                ->table('equipment_items')
                ->where('id', $id)
                ->first();

            if (!$equipment) {
                return redirect()->route('admin.equipment.index')
                    ->with('error', 'Equipment not found.');
            }

            $newStatus = !$equipment->is_available;

            DB::connection('facilities_db')->table('equipment_items')
                ->where('id', $id)
                ->update([
                    'is_available' => $newStatus,
                    'updated_at' => now()
                ]);

            // Log activity
            DB::connection('auth_db')->table('audit_logs')->insert([
                'user_id' => session('user_id'),
                'action' => 'update',
                'model' => 'Equipment',
                'model_id' => $id,
                'changes' => json_encode(['is_available' => $newStatus]),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now()
            ]);

            $message = $newStatus ? 'Equipment is now available' : 'Equipment is now unavailable';
            return redirect()->route('admin.equipment.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Failed to toggle equipment availability: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update equipment status.');
        }
    }
}

