<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ProfileController extends Controller
{
    /**
     * Display user profile
     */
    public function index()
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        // Get user data from auth database with ALL address information
        $user = DB::connection('auth_db')
            ->table('users')
            ->leftJoin('regions', 'users.region_id', '=', 'regions.id')
            ->leftJoin('provinces', 'users.province_id', '=', 'provinces.id')
            ->leftJoin('cities', 'users.city_id', '=', 'cities.id')
            ->leftJoin('districts', 'users.district_id', '=', 'districts.id')
            ->leftJoin('barangays', 'users.barangay_id', '=', 'barangays.id')
            ->select(
                'users.*',
                'regions.name as region_name',
                'provinces.name as province_name',
                'cities.name as city_name',
                'districts.name as district_name',
                'barangays.name as barangay_name'
            )
            ->where('users.id', $userId)
            ->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'User not found.');
        }

        // Get user statistics from facilities database
        $stats = [
            'total_bookings' => DB::connection('facilities_db')
                ->table('bookings')
                ->where('user_id', $userId)
                ->count(),
            'active_bookings' => DB::connection('facilities_db')
                ->table('bookings')
                ->where('user_id', $userId)
                ->whereIn('status', ['pending', 'staff_verified', 'payment_pending', 'confirmed'])
                ->count(),
            'completed_bookings' => DB::connection('facilities_db')
                ->table('bookings')
                ->where('user_id', $userId)
                ->where('status', 'completed')
                ->count(),
            'total_spent' => DB::connection('facilities_db')
                ->table('payment_slips')
                ->where('user_id', $userId)
                ->where('status', 'paid')
                ->sum('amount'),
        ];

        // Get recent activity (last 5 bookings)
        $recentActivity = DB::connection('facilities_db')
            ->table('bookings')
            ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
            ->select(
                'bookings.id',
                'bookings.status',
                'bookings.start_time',
                'bookings.created_at',
                'facilities.name as facility_name'
            )
            ->where('bookings.user_id', $userId)
            ->orderBy('bookings.created_at', 'desc')
            ->limit(5)
            ->get();

        return view('citizen.profile.index', compact('user', 'stats', 'recentActivity'));
    }

    /**
     * Update profile information
     */
    public function update(Request $request)
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Please login to continue.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }

        // Check if email is already taken by another user
        $existingUser = DB::connection('auth_db')
            ->table('users')
            ->where('email', $request->email)
            ->where('id', '!=', $userId)
            ->first();

        if ($existingUser) {
            return response()->json(['success' => false, 'message' => 'Email is already taken.'], 400);
        }

        // Update user information
        DB::connection('auth_db')
            ->table('users')
            ->where('id', $userId)
            ->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'middle_name' => $request->middle_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'updated_at' => Carbon::now(),
            ]);

        return response()->json(['success' => true, 'message' => 'Profile updated successfully!']);
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Please login to continue.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }

        // Get current user
        $user = DB::connection('auth_db')
            ->table('users')
            ->where('id', $userId)
            ->first();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Current password is incorrect.'], 400);
        }

        // Update password
        DB::connection('auth_db')
            ->table('users')
            ->where('id', $userId)
            ->update([
                'password' => Hash::make($request->new_password),
                'updated_at' => Carbon::now(),
            ]);

        return response()->json(['success' => true, 'message' => 'Password changed successfully!']);
    }

    /**
     * Upload avatar
     */
    public function uploadAvatar(Request $request)
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Please login to continue.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpg,jpeg,png|max:2048', // 2MB max
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }

        // Delete old avatar if exists
        $user = DB::connection('auth_db')
            ->table('users')
            ->where('id', $userId)
            ->first();

        if ($user->avatar_path && file_exists(storage_path('app/public/' . $user->avatar_path))) {
            unlink(storage_path('app/public/' . $user->avatar_path));
        }

        // Store new avatar
        $avatarPath = $request->file('avatar')->store('avatars', 'public');

        // Update user avatar path
        DB::connection('auth_db')
            ->table('users')
            ->where('id', $userId)
            ->update([
                'avatar_path' => $avatarPath,
                'updated_at' => Carbon::now(),
            ]);

        return response()->json([
            'success' => true, 
            'message' => 'Avatar updated successfully!',
            'avatar_url' => asset('storage/' . $avatarPath)
        ]);
    }
}

