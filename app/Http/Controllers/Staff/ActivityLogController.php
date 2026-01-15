<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ActivityLogController extends Controller
{
    /**
     * Display staff member's activity log.
     */
    public function index(Request $request)
    {
        $userId = session('user_id');
        
        // Get filters
        $search = $request->input('search');
        $action = $request->input('action');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        // Build query
        $query = DB::connection('auth_db')
            ->table('audit_logs')
            ->where('user_id', $userId);

        // Search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('model', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('changes', 'like', "%{$search}%");
            });
        }

        // Action filter
        if ($action) {
            $query->where('action', $action);
        }

        // Date range filter
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // Get paginated results
        $activities = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        // Enrich activities with related data
        foreach ($activities as $activity) {
            // Decode changes JSON
            $activity->changes_array = json_decode($activity->changes, true);
            
            // Get related booking if applicable
            if ($activity->model === 'Booking' && $activity->model_id) {
                $booking = DB::connection('facilities_db')
                    ->table('bookings')
                    ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
                    ->where('bookings.id', $activity->model_id)
                    ->select(
                        'bookings.id as booking_id',
                        'bookings.event_date',
                        'bookings.status',
                        'facilities.name as facility_name'
                    )
                    ->first();
                
                if ($booking) {
                    $booking->booking_reference = 'BK-' . str_pad($booking->booking_id, 6, '0', STR_PAD_LEFT);
                }
                
                $activity->booking = $booking;
            }
        }

        // Get available actions for filter
        $availableActions = DB::connection('auth_db')
            ->table('audit_logs')
            ->where('user_id', $userId)
            ->distinct()
            ->pluck('action')
            ->sort()
            ->values();

        // Get activity summary
        $totalActivities = DB::connection('auth_db')
            ->table('audit_logs')
            ->where('user_id', $userId)
            ->count();

        $todayActivities = DB::connection('auth_db')
            ->table('audit_logs')
            ->where('user_id', $userId)
            ->whereDate('created_at', Carbon::today())
            ->count();

        return view('staff.activity-log.index', compact(
            'activities',
            'availableActions',
            'totalActivities',
            'todayActivities',
            'search',
            'action',
            'dateFrom',
            'dateTo'
        ));
    }
}

