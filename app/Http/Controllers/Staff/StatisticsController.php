<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    /**
     * Display staff member's personal statistics.
     */
    public function index()
    {
        $userId = session('user_id');
        
        // Get date range for stats (last 30 days by default)
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        // Total verifications performed
        $totalVerifications = DB::connection('auth_db')
            ->table('audit_logs')
            ->where('user_id', $userId)
            ->where('action', 'verify')
            ->where('model', 'Booking')
            ->where('created_at', '>=', $startDate)
            ->count();

        // Total approvals
        $totalApprovals = DB::connection('auth_db')
            ->table('audit_logs')
            ->where('user_id', $userId)
            ->where('action', 'approve')
            ->where('model', 'Booking')
            ->where('created_at', '>=', $startDate)
            ->count();

        // Total rejections
        $totalRejections = DB::connection('auth_db')
            ->table('audit_logs')
            ->where('user_id', $userId)
            ->where('action', 'reject')
            ->where('model', 'Booking')
            ->where('created_at', '>=', $startDate)
            ->count();

        // Pending verifications (assigned to this user or unassigned)
        $pendingVerifications = DB::connection('facilities_db')
            ->table('bookings')
            ->where('status', 'pending')
            ->where('event_date', '>=', Carbon::now()->toDateString())
            ->count();

        // Today's activity count
        $todayActivity = DB::connection('auth_db')
            ->table('audit_logs')
            ->where('user_id', $userId)
            ->whereDate('created_at', Carbon::today())
            ->count();

        // This week's activity count
        $weekActivity = DB::connection('auth_db')
            ->table('audit_logs')
            ->where('user_id', $userId)
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->count();

        // Activity by day (last 7 days)
        $dailyActivity = DB::connection('auth_db')
            ->table('audit_logs')
            ->where('user_id', $userId)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Fill in missing days with zero
        $activityByDay = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->toDateString();
            $found = $dailyActivity->firstWhere('date', $date);
            $activityByDay[] = [
                'date' => $date,
                'day' => Carbon::parse($date)->format('D'),
                'count' => $found ? $found->count : 0
            ];
        }

        // Recent bookings verified by this staff
        $recentAuditLogs = DB::connection('auth_db')
            ->table('audit_logs')
            ->where('user_id', $userId)
            ->whereIn('action', ['verify', 'approve', 'reject'])
            ->where('model', 'Booking')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Enrich with booking data
        $recentVerifications = collect();
        foreach ($recentAuditLogs as $log) {
            if ($log->model_id) {
                $booking = DB::connection('facilities_db')
                    ->table('bookings')
                    ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
                    ->where('bookings.id', $log->model_id)
                    ->select(
                        'bookings.id as booking_id',
                        'bookings.event_date',
                        'bookings.status',
                        'facilities.name as facility_name'
                    )
                    ->first();
                
                if ($booking) {
                    $log->booking_id = $booking->booking_id;
                    $log->booking_reference = 'BK-' . str_pad($booking->booking_id, 6, '0', STR_PAD_LEFT);
                    $log->event_date = $booking->event_date;
                    $log->status = $booking->status;
                    $log->facility_name = $booking->facility_name;
                    $recentVerifications->push($log);
                }
            }
        }

        // Most verified facilities
        $verifyLogs = DB::connection('auth_db')
            ->table('audit_logs')
            ->where('user_id', $userId)
            ->where('action', 'verify')
            ->where('model', 'Booking')
            ->where('created_at', '>=', $startDate)
            ->pluck('model_id');

        $topFacilities = collect();
        if ($verifyLogs->count() > 0) {
            $topFacilities = DB::connection('facilities_db')
                ->table('bookings')
                ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
                ->whereIn('bookings.id', $verifyLogs)
                ->selectRaw('facilities.name as facility_name, COUNT(*) as verification_count')
                ->groupBy('facilities.facility_id', 'facilities.name')
                ->orderByDesc('verification_count')
                ->limit(5)
                ->get();
        }

        // Average response time (hours between booking submission and verification)
        $verifyLogsForAvg = DB::connection('auth_db')
            ->table('audit_logs')
            ->where('user_id', $userId)
            ->where('action', 'verify')
            ->where('model', 'Booking')
            ->where('created_at', '>=', $startDate)
            ->get();

        $totalHours = 0;
        $count = 0;
        foreach ($verifyLogsForAvg as $log) {
            if ($log->model_id) {
                $booking = DB::connection('facilities_db')
                    ->table('bookings')
                    ->where('id', $log->model_id)
                    ->select('created_at')
                    ->first();
                
                if ($booking) {
                    $bookingTime = Carbon::parse($booking->created_at);
                    $verifyTime = Carbon::parse($log->created_at);
                    $totalHours += $bookingTime->diffInHours($verifyTime);
                    $count++;
                }
            }
        }

        $avgResponseTime = (object)['avg_hours' => $count > 0 ? $totalHours / $count : 0];

        return view('staff.statistics.index', compact(
            'totalVerifications',
            'totalApprovals',
            'totalRejections',
            'pendingVerifications',
            'todayActivity',
            'weekActivity',
            'activityByDay',
            'recentVerifications',
            'topFacilities',
            'avgResponseTime',
            'startDate',
            'endDate'
        ));
    }
}

