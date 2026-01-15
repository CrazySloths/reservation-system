<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingConflict;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScheduleConflictController extends Controller
{
    /**
     * Display a listing of schedule conflicts (City Event conflicts).
     */
    public function index(Request $request)
    {
        $facilityId = $request->input('facility_id');
        $dateFilter = $request->input('date_filter', 'all'); // future, past, all
        $statusFilter = $request->input('status'); // pending, resolved, all

        // Query booking_conflicts table (City Event conflicts)
        $query = BookingConflict::with([
            'booking.facility',
            'booking.user',
            'cityEvent.facility'
        ]);

        // Apply status filter
        if ($statusFilter === 'pending') {
            $query->where('status', 'pending');
        } elseif ($statusFilter === 'resolved') {
            $query->where('status', 'resolved');
        }
        // If 'all' or empty, show all

        // Apply facility filter
        if ($facilityId && $facilityId !== 'all') {
            $query->whereHas('booking', function($q) use ($facilityId) {
                $q->where('facility_id', $facilityId);
            });
        }

        // Apply date filter based on city event dates
        if ($dateFilter === 'future') {
            $query->whereHas('cityEvent', function($q) {
                $q->where('start_time', '>=', now());
            });
        } elseif ($dateFilter === 'past') {
            $query->whereHas('cityEvent', function($q) {
                $q->where('start_time', '<', now());
            });
        }

        $conflicts = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get facilities for filter
        $facilities = Facility::select('facility_id', 'name')
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();

        // Get counts
        $totalConflicts = BookingConflict::count();
        $pendingConflicts = BookingConflict::where('status', 'pending')->count();
        $resolvedConflicts = BookingConflict::where('status', 'resolved')->count();

        return view('admin.schedule-conflicts.index', compact(
            'conflicts',
            'facilities',
            'facilityId',
            'dateFilter',
            'statusFilter',
            'totalConflicts',
            'pendingConflicts',
            'resolvedConflicts'
        ));
    }

    /**
     * Show details of a specific conflict.
     */
    public function show($id)
    {
        $conflict = BookingConflict::with([
            'booking.facility',
            'booking.user',
            'cityEvent.facility'
        ])->findOrFail($id);

        return view('admin.schedule-conflicts.show', compact('conflict'));
    }
}

