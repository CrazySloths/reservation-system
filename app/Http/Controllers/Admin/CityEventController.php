<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CityEvent;
use App\Models\BookingConflict;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CityEventController extends Controller
{
    /**
     * Display a listing of city events
     */
    public function index(Request $request)
    {
        $query = CityEvent::query()->with('facility');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('event_title', 'like', "%{$search}%")
                  ->orWhere('event_description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by event type
        if ($request->filled('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('start_time', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('end_time', '<=', $request->date_to);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'start_time');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $cityEvents = $query->paginate(15);

        // Get facilities for dropdown
        $facilities = DB::connection('facilities_db')
            ->table('facilities')
            ->select('facility_id as id', 'name')
            ->get();

        return view('admin.city-events.index', compact('cityEvents', 'facilities'));
    }

    /**
     * Show the form for creating a new city event
     */
    public function create()
    {
        $facilities = DB::connection('facilities_db')
            ->table('facilities')
            ->select('facility_id as id', 'name')
            ->get();

        return view('admin.city-events.create', compact('facilities'));
    }

    /**
     * Preview conflicting bookings before creating city event
     */
    public function previewConflicts(Request $request)
    {
        $request->validate([
            'facility_id' => 'required|exists:facilities_db.facilities,facility_id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $conflictingBookings = DB::connection('facilities_db')
            ->table('bookings')
            ->where('facility_id', $request->facility_id)
            ->whereIn('status', ['confirmed', 'paid'])
            ->where(function($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                      ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                      ->orWhere(function($q) use ($request) {
                          $q->where('start_time', '<=', $request->start_time)
                            ->where('end_time', '>=', $request->end_time);
                      });
            })
            ->get();

        // Get user details from auth_db for each booking
        $bookingIds = $conflictingBookings->pluck('user_id')->unique();
        $users = DB::connection('auth_db')
            ->table('users')
            ->whereIn('id', $bookingIds)
            ->get()
            ->keyBy('id');

        // Merge user data with bookings
        foreach ($conflictingBookings as $booking) {
            $booking->citizen = $users->get($booking->user_id);
        }

        return response()->json([
            'count' => $conflictingBookings->count(),
            'bookings' => $conflictingBookings
        ]);
    }

    /**
     * Store a newly created city event
     */
    public function store(Request $request)
    {
        $request->validate([
            'facility_id' => 'required|exists:facilities_db.facilities,facility_id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'event_title' => 'required|string|max:255',
            'event_description' => 'nullable|string',
            'event_type' => 'required|in:government,emergency,maintenance',
        ]);

        try {
            DB::connection('facilities_db')->beginTransaction();

            // Create city event
            $cityEvent = CityEvent::create([
                'facility_id' => $request->facility_id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'event_title' => $request->event_title,
                'event_description' => $request->event_description,
                'event_type' => $request->event_type,
                'created_by' => session('user_id'),
                'status' => 'scheduled',
            ]);

            // Detect and create conflicts
            $conflictsCreated = $cityEvent->createConflicts();

            DB::connection('facilities_db')->commit();

            return redirect()
                ->route('admin.city-events.index')
                ->with('success', "City event created successfully. {$conflictsCreated} booking(s) affected and citizens will be notified.");

        } catch (\Exception $e) {
            DB::connection('facilities_db')->rollBack();
            \Log::error('City event creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()
                ->withInput()
                ->with('error', 'Failed to create city event: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified city event
     */
    public function show(CityEvent $cityEvent)
    {
        $cityEvent->load('facility', 'bookingConflicts');

        // Get affected bookings with user details
        $affectedBookings = [];
        foreach ($cityEvent->bookingConflicts as $conflict) {
            $booking = $conflict->booking();
            if ($booking) {
                $user = DB::connection('auth_db')
                    ->table('users')
                    ->where('id', $booking->user_id)
                    ->first();
                
                $booking->citizen = $user;
                $booking->conflict = $conflict;
                $affectedBookings[] = $booking;
            }
        }

        return view('admin.city-events.show', compact('cityEvent', 'affectedBookings'));
    }

    /**
     * Show the form for editing the specified city event
     */
    public function edit(CityEvent $cityEvent)
    {
        // Only allow editing if status is scheduled
        if ($cityEvent->status !== 'scheduled') {
            return redirect()
                ->route('admin.city-events.show', $cityEvent)
                ->withErrors(['error' => 'Can only edit scheduled city events.']);
        }

        $facilities = DB::connection('facilities_db')
            ->table('facilities')
            ->select('facility_id as id', 'name')
            ->get();

        return view('admin.city-events.edit', compact('cityEvent', 'facilities'));
    }

    /**
     * Update the specified city event
     */
    public function update(Request $request, CityEvent $cityEvent)
    {
        $request->validate([
            'facility_id' => 'required|exists:facilities_db.facilities,facility_id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'event_title' => 'required|string|max:255',
            'event_description' => 'nullable|string',
            'event_type' => 'required|in:government,emergency,maintenance',
            'status' => 'required|in:scheduled,ongoing,completed,cancelled',
        ]);

        try {
            $cityEvent->update([
                'facility_id' => $request->facility_id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'event_title' => $request->event_title,
                'event_description' => $request->event_description,
                'event_type' => $request->event_type,
                'status' => $request->status,
            ]);

            return redirect()
                ->route('admin.city-events.show', $cityEvent)
                ->with('success', 'City event updated successfully.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update city event: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified city event (soft delete)
     */
    public function destroy(CityEvent $cityEvent)
    {
        try {
            // Only allow deleting scheduled events
            if ($cityEvent->status !== 'scheduled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Can only delete scheduled city events.'
                ], 400);
            }

            // Check if there are unresolved conflicts
            $unresolvedConflicts = $cityEvent->bookingConflicts()
                ->where('status', 'pending')
                ->count();

            if ($unresolvedConflicts > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete city event with unresolved booking conflicts.'
                ], 400);
            }

            $cityEvent->delete();

            return response()->json([
                'success' => true,
                'message' => 'City event archived successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete city event: ' . $e->getMessage()
            ], 500);
        }
    }
}
