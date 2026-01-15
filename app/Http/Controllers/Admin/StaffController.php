<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StaffController extends Controller
{
    /**
     * Display a listing of staff members
     */
    public function index(Request $request)
    {
        // Get all staff from auth_db (subsystem_role_id = 3 for Reservations Staff, subsystem_id = 4 for Facilities)
        $query = DB::connection('auth_db')->table('users')
            ->where('subsystem_role_id', 3)
            ->where('subsystem_id', 4)
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

        // Order by most recently added
        $query->orderBy('created_at', 'desc');

        $staff = $query->paginate(15)->withQueryString();

        // Get cities from facilities_db and map to staff
        $cities = DB::connection('facilities_db')->table('lgu_cities')
            ->where('status', 'active')
            ->orderBy('city_name')
            ->get();
        
        $citiesById = $cities->keyBy('id');
        
        // Enrich staff with city information
        foreach ($staff->items() as $member) {
            $city = $citiesById->get($member->city_id);
            $member->city_name = $city ? $city->city_name : 'N/A';
        }

        // Get staff performance metrics
        $staffIds = collect($staff->items())->pluck('id');
        $stats = [];
        
        foreach ($staffIds as $staffId) {
            $verifications = DB::connection('auth_db')->table('audit_logs')
                ->where('user_id', $staffId)
                ->where('action', 'verify')
                ->where('model', 'Booking')
                ->count();
            
            $stats[$staffId] = [
                'total_verifications' => $verifications,
            ];
        }

        return view('admin.staff.index', compact('staff', 'cities', 'stats'));
    }

    /**
     * Show the form for creating a new staff member
     */
    public function create()
    {
        $cities = DB::connection('facilities_db')->table('lgu_cities')
            ->where('status', 'active')
            ->orderBy('city_name')
            ->get();

        return view('admin.staff.create', compact('cities'));
    }

    /**
     * Store a newly created staff member
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:lgu1_auth.users,email',
            'phone' => 'required|string|max:20',
            'city_id' => 'required|exists:lgu1_facilities.lgu_cities,id',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::connection('auth_db')->table('users')->insert([
                'username' => $request->email, // Use email as username
                'email' => $request->email,
                'full_name' => $request->name,
                'mobile_number' => $request->phone,
                'city_id' => $request->city_id,
                'subsystem_role_id' => 3, // Reservations Staff
                'subsystem_id' => 4, // Public Facilities
                'password_hash' => Hash::make($request->password),
                'status' => 'active',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('admin.staff.index')
                ->with('success', 'Staff member added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to add staff member: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing a staff member
     */
    public function edit($id)
    {
        $staff = DB::connection('auth_db')->table('users')
            ->where('id', $id)
            ->where('subsystem_role_id', 3)
            ->where('subsystem_id', 4)
            ->first();

        if (!$staff) {
            return redirect()->route('admin.staff.index')
                ->with('error', 'Staff member not found.');
        }

        $cities = DB::connection('facilities_db')->table('lgu_cities')
            ->where('status', 'active')
            ->orderBy('city_name')
            ->get();

        return view('admin.staff.edit', compact('staff', 'cities'));
    }

    /**
     * Update the specified staff member
     */
    public function update(Request $request, $id)
    {
        $staff = DB::connection('auth_db')->table('users')
            ->where('id', $id)
            ->where('subsystem_role_id', 3)
            ->where('subsystem_id', 4)
            ->first();

        if (!$staff) {
            return redirect()->route('admin.staff.index')
                ->with('error', 'Staff member not found.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:lgu1_auth.users,email,' . $id,
            'phone' => 'required|string|max:20',
            'city_id' => 'required|exists:lgu1_facilities.lgu_cities,id',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $updateData = [
                'full_name' => $request->name,
                'email' => $request->email,
                'mobile_number' => $request->phone,
                'city_id' => $request->city_id,
                'updated_at' => now(),
            ];

            // Only update password if provided
            if ($request->filled('password')) {
                $updateData['password_hash'] = Hash::make($request->password);
            }

            DB::connection('auth_db')->table('users')
                ->where('id', $id)
                ->update($updateData);

            return redirect()->route('admin.staff.index')
                ->with('success', 'Staff member updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update staff member: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Toggle staff member status (active/inactive)
     */
    public function toggleStatus($id)
    {
        $staff = DB::connection('auth_db')->table('users')
            ->where('id', $id)
            ->where('subsystem_role_id', 3)
            ->where('subsystem_id', 4)
            ->first();

        if (!$staff) {
            return response()->json([
                'success' => false,
                'message' => 'Staff member not found.'
            ], 404);
        }

        $newStatus = $staff->status === 'active' ? 'inactive' : 'active';

        DB::connection('auth_db')->table('users')
            ->where('id', $id)
            ->update([
                'status' => $newStatus,
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'status' => $newStatus,
            'message' => 'Staff member ' . ($newStatus === 'active' ? 'activated' : 'deactivated') . ' successfully!'
        ]);
    }
}

