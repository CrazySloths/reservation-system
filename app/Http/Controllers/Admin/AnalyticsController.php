<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Display analytics hub index page
     */
    public function index()
    {
        // Total Revenue (Year to Date)
        $totalRevenue = DB::connection('facilities_db')
            ->table('payment_slips')
            ->where('status', 'paid')
            ->whereYear('paid_at', now()->year)
            ->sum('amount_due');

        // Total Bookings (All time)
        $totalBookings = DB::connection('facilities_db')
            ->table('bookings')
            ->count();

        // Active Citizens (users with at least 1 booking)
        $activeCitizens = DB::connection('facilities_db')
            ->table('bookings')
            ->distinct('user_id')
            ->count('user_id');

        // Facility Utilization Rate (last 30 days)
        $totalFacilities = DB::connection('facilities_db')
            ->table('facilities')
            ->where('is_available', 1)
            ->count();

        $bookedFacilities = DB::connection('facilities_db')
            ->table('bookings')
            ->distinct('facility_id')
            ->where('created_at', '>=', now()->subDays(30))
            ->whereIn('status', ['approved', 'completed', 'paid'])
            ->count('facility_id');

        $facilityUtilization = $totalFacilities > 0 ? ($bookedFacilities / $totalFacilities) * 100 : 0;

        return view('admin.analytics.index', compact(
            'totalRevenue',
            'totalBookings',
            'activeCitizens',
            'facilityUtilization'
        ));
    }

    /**
     * Display booking statistics dashboard
     */
    public function bookingStatistics(Request $request)
    {
        // Date range filter (default to last 30 days)
        $startDate = $request->input('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        
        // Total bookings count
        $totalBookings = DB::connection('facilities_db')
            ->table('bookings')
            ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
            ->count();
        
        // Bookings by status
        $bookingsByStatus = DB::connection('facilities_db')
            ->table('bookings')
            ->selectRaw('status, COUNT(*) as count')
            ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
            ->groupBy('status')
            ->get();
        
        // Daily booking trend (last 30 days)
        $dailyTrend = DB::connection('facilities_db')
            ->table('bookings')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween(DB::raw('DATE(created_at)'), [now()->subDays(30), now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Fill missing dates with zero
        $trendData = [];
        for ($i = 30; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $found = $dailyTrend->firstWhere('date', $date);
            $trendData[] = [
                'date' => Carbon::parse($date)->format('M d'),
                'count' => $found ? $found->count : 0
            ];
        }
        
        // Popular facilities
        $popularFacilities = DB::connection('facilities_db')
            ->table('bookings')
            ->selectRaw('facilities.name as facility_name, lgu_cities.city_name, COUNT(bookings.id) as booking_count, SUM(bookings.total_amount) as total_revenue')
            ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
            ->leftJoin('lgu_cities', 'facilities.lgu_city_id', '=', 'lgu_cities.id')
            ->whereBetween(DB::raw('DATE(bookings.created_at)'), [$startDate, $endDate])
            ->groupBy('facilities.facility_id', 'facilities.name', 'lgu_cities.city_name')
            ->orderByDesc('booking_count')
            ->limit(10)
            ->get();
        
        // Average booking value
        $avgBookingValue = DB::connection('facilities_db')
            ->table('bookings')
            ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
            ->avg('total_amount') ?? 0;
        
        // Conversion rate (paid bookings / total bookings)
        $paidBookings = DB::connection('facilities_db')
            ->table('bookings')
            ->whereIn('status', ['paid', 'confirmed', 'completed'])
            ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
            ->count();
        
        $conversionRate = $totalBookings > 0 ? ($paidBookings / $totalBookings) * 100 : 0;
        
        // Cancelled bookings rate
        $cancelledBookings = DB::connection('facilities_db')
            ->table('bookings')
            ->where('status', 'cancelled')
            ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
            ->count();
        
        $cancellationRate = $totalBookings > 0 ? ($cancelledBookings / $totalBookings) * 100 : 0;
        
        // Peak booking hours (top 5)
        $peakHours = DB::connection('facilities_db')
            ->table('bookings')
            ->selectRaw('HOUR(start_time) as hour, COUNT(*) as count')
            ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
            ->whereNotNull('start_time')
            ->groupBy('hour')
            ->orderByDesc('count')
            ->limit(5)
            ->get();
        
        // Peak booking days of week (when events are scheduled, not when booking was created)
        $peakDays = DB::connection('facilities_db')
            ->table('bookings')
            ->selectRaw('DAYNAME(start_time) as day_name, COUNT(*) as count')
            ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
            ->whereNotNull('start_time')
            ->groupBy('day_name')
            ->orderByDesc('count')
            ->get();
        
        return view('admin.analytics.booking-statistics', compact(
            'totalBookings',
            'bookingsByStatus',
            'trendData',
            'popularFacilities',
            'avgBookingValue',
            'conversionRate',
            'cancellationRate',
            'peakHours',
            'peakDays',
            'paidBookings',
            'cancelledBookings',
            'startDate',
            'endDate'
        ));
    }
    
    /**
     * Display facility utilization report
     */
    public function facilityUtilization(Request $request)
    {
        // Date range filter (default to last 3 months)
        $startDate = $request->input('start_date', now()->subMonths(3)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        
        // Get all facilities with booking counts
        $facilities = DB::connection('facilities_db')
            ->table('facilities')
            ->selectRaw('
                facilities.facility_id,
                facilities.name,
                lgu_cities.city_name,
                facilities.capacity,
                COUNT(bookings.id) as total_bookings,
                SUM(CASE WHEN bookings.status IN ("paid", "confirmed", "completed") THEN 1 ELSE 0 END) as confirmed_bookings,
                SUM(CASE WHEN bookings.status = "cancelled" THEN 1 ELSE 0 END) as cancelled_bookings,
                SUM(bookings.total_amount) as total_revenue
            ')
            ->leftJoin('lgu_cities', 'facilities.lgu_city_id', '=', 'lgu_cities.id')
            ->leftJoin('bookings', function($join) use ($startDate, $endDate) {
                $join->on('facilities.facility_id', '=', 'bookings.facility_id')
                     ->whereBetween(DB::raw('DATE(bookings.created_at)'), [$startDate, $endDate]);
            })
            ->where('facilities.is_available', 1)
            ->groupBy('facilities.facility_id', 'facilities.name', 'lgu_cities.city_name', 'facilities.capacity')
            ->orderByDesc('total_bookings')
            ->get();
        
        // Calculate utilization percentage (assuming 8 hours per day as available time)
        $totalDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
        foreach ($facilities as $facility) {
            $maxPossibleBookings = $totalDays; // Simplified: 1 booking per day
            $facility->utilization_rate = $maxPossibleBookings > 0 
                ? ($facility->confirmed_bookings / $maxPossibleBookings) * 100 
                : 0;
        }
        
        // Underutilized facilities (< 30% utilization)
        $underutilized = $facilities->filter(function($facility) {
            return $facility->utilization_rate < 30;
        });
        
        // High-performing facilities (> 70% utilization)
        $highPerforming = $facilities->filter(function($facility) {
            return $facility->utilization_rate > 70;
        });
        
        return view('admin.analytics.facility-utilization', compact(
            'facilities',
            'underutilized',
            'highPerforming',
            'startDate',
            'endDate'
        ));
    }
    
    /**
     * Display revenue report
     */
    public function revenueReport(Request $request)
    {
        // Date range filter (default to current month)
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());
        
        // Total revenue
        $totalRevenue = DB::connection('facilities_db')
            ->table('payment_slips')
            ->whereBetween(DB::raw('DATE(paid_at)'), [$startDate, $endDate])
            ->where('status', 'paid')
            ->sum('amount_due');
        
        // Revenue by facility
        $revenueByFacility = DB::connection('facilities_db')
            ->table('bookings')
            ->selectRaw('facilities.name as facility_name, lgu_cities.city_name, COUNT(bookings.id) as total_bookings, SUM(bookings.total_amount) as total_revenue')
            ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
            ->leftJoin('lgu_cities', 'facilities.lgu_city_id', '=', 'lgu_cities.id')
            ->join('payment_slips', 'bookings.id', '=', 'payment_slips.booking_id')
            ->whereBetween(DB::raw('DATE(payment_slips.paid_at)'), [$startDate, $endDate])
            ->where('payment_slips.status', 'paid')
            ->groupBy('facilities.facility_id', 'facilities.name', 'lgu_cities.city_name')
            ->orderByDesc('total_revenue')
            ->get();
        
        // Revenue by payment method
        $revenueByPaymentMethod = DB::connection('facilities_db')
            ->table('payment_slips')
            ->selectRaw('payment_method, COUNT(*) as transaction_count, SUM(amount_due) as total_amount')
            ->whereBetween(DB::raw('DATE(paid_at)'), [$startDate, $endDate])
            ->where('status', 'paid')
            ->groupBy('payment_method')
            ->orderByDesc('total_amount')
            ->get();
        
        // Monthly revenue trend (last 6 months)
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $revenue = DB::connection('facilities_db')
                ->table('payment_slips')
                ->whereYear('paid_at', $month->year)
                ->whereMonth('paid_at', $month->month)
                ->where('status', 'paid')
                ->sum('amount_due') ?? 0;
            
            $monthlyRevenue[] = [
                'month' => $month->format('M Y'),
                'revenue' => $revenue
            ];
        }
        
        return view('admin.analytics.revenue-report', compact(
            'totalRevenue',
            'revenueByFacility',
            'revenueByPaymentMethod',
            'monthlyRevenue',
            'startDate',
            'endDate'
        ));
    }
    
    /**
     * Display citizen analytics
     */
    public function citizenAnalytics(Request $request)
    {
        // Date range filter
        $startDate = $request->input('start_date', now()->startOfYear()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        
        // Total unique citizens who made bookings
        $totalCitizens = DB::connection('facilities_db')
            ->table('bookings')
            ->distinct('user_id')
            ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
            ->count('user_id');
        
        // New citizens (first-time bookers in date range)
        $newCitizens = DB::connection('facilities_db')
            ->table('bookings')
            ->selectRaw('user_id, MIN(DATE(created_at)) as first_booking_date')
            ->groupBy('user_id')
            ->havingBetween('first_booking_date', [$startDate, $endDate])
            ->get()
            ->count();
        
        // Repeat customers (made more than 1 booking)
        $repeatCustomers = DB::connection('facilities_db')
            ->table('bookings')
            ->selectRaw('user_id, COUNT(*) as booking_count')
            ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
            ->groupBy('user_id')
            ->having('booking_count', '>', 1)
            ->count();
        
        // Top citizens by bookings (fetch separately and merge)
        $topBookers = DB::connection('facilities_db')
            ->table('bookings')
            ->selectRaw('
                user_id,
                COUNT(*) as total_bookings,
                SUM(total_amount) as total_spent
            ')
            ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
            ->groupBy('user_id')
            ->orderByDesc('total_bookings')
            ->limit(10)
            ->get();
        
        // Get user details from auth_db
        $userIds = $topBookers->pluck('user_id')->toArray();
        $users = DB::connection('auth_db')
            ->table('users')
            ->whereIn('id', $userIds)
            ->get()
            ->keyBy('id');
        
        // Merge booking data with user data
        $topCitizens = $topBookers->map(function($booking) use ($users) {
            $user = $users->get($booking->user_id);
            return (object)[
                'full_name' => $user->full_name ?? 'Unknown User',
                'email' => $user->email ?? 'N/A',
                'total_bookings' => $booking->total_bookings,
                'total_spent' => $booking->total_spent
            ];
        });
        
        // Average bookings per citizen
        $avgBookingsPerCitizen = $totalCitizens > 0 
            ? DB::connection('facilities_db')->table('bookings')
                ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
                ->count() / $totalCitizens 
            : 0;
        
        // Citizen growth trend (monthly)
        $monthlyGrowth = DB::connection('facilities_db')
            ->table('bookings')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(DISTINCT user_id) as citizen_count')
            ->whereBetween(DB::raw('DATE(created_at)'), [now()->subMonths(12), now()])
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        return view('admin.analytics.citizen-analytics', compact(
            'totalCitizens',
            'newCitizens',
            'repeatCustomers',
            'topCitizens',
            'avgBookingsPerCitizen',
            'monthlyGrowth',
            'startDate',
            'endDate'
        ));
    }
    
    /**
     * Export facility utilization report as CSV
     */
    public function exportFacilityUtilization(Request $request)
    {
        $startDate = $request->input('start_date', now()->subMonths(3)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        
        $facilities = DB::connection('facilities_db')
            ->table('facilities')
            ->selectRaw('
                facilities.facility_id,
                facilities.name,
                lgu_cities.city_name,
                facilities.capacity,
                COUNT(bookings.id) as total_bookings,
                SUM(CASE WHEN bookings.status IN ("paid", "confirmed", "completed") THEN 1 ELSE 0 END) as confirmed_bookings,
                SUM(CASE WHEN bookings.status = "cancelled" THEN 1 ELSE 0 END) as cancelled_bookings,
                SUM(bookings.total_amount) as total_revenue
            ')
            ->leftJoin('lgu_cities', 'facilities.lgu_city_id', '=', 'lgu_cities.id')
            ->leftJoin('bookings', function($join) use ($startDate, $endDate) {
                $join->on('facilities.facility_id', '=', 'bookings.facility_id')
                     ->whereBetween(DB::raw('DATE(bookings.created_at)'), [$startDate, $endDate]);
            })
            ->where('facilities.is_available', 1)
            ->groupBy('facilities.facility_id', 'facilities.name', 'lgu_cities.city_name', 'facilities.capacity')
            ->orderByDesc('total_bookings')
            ->get();
        
        $filename = "facility_utilization_{$startDate}_to_{$endDate}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($facilities, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, ['Facility Utilization Report']);
            fputcsv($file, ['Period: ' . $startDate . ' to ' . $endDate]);
            fputcsv($file, []);
            fputcsv($file, ['Facility', 'City', 'Capacity', 'Total Bookings', 'Confirmed', 'Cancelled', 'Revenue (₱)']);
            
            // Data rows
            foreach ($facilities as $facility) {
                fputcsv($file, [
                    $facility->name,
                    $facility->city_name ?? 'N/A',
                    $facility->capacity,
                    $facility->total_bookings,
                    $facility->confirmed_bookings,
                    $facility->cancelled_bookings,
                    number_format($facility->total_revenue ?? 0, 2)
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Export citizen analytics report as CSV
     */
    public function exportCitizenAnalytics(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfYear()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        
        $topBookers = DB::connection('facilities_db')
            ->table('bookings')
            ->selectRaw('
                user_id,
                COUNT(*) as total_bookings,
                SUM(total_amount) as total_spent
            ')
            ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
            ->groupBy('user_id')
            ->orderByDesc('total_bookings')
            ->limit(50)
            ->get();
        
        $userIds = $topBookers->pluck('user_id')->toArray();
        $users = DB::connection('auth_db')
            ->table('users')
            ->whereIn('id', $userIds)
            ->get()
            ->keyBy('id');
        
        $filename = "citizen_analytics_{$startDate}_to_{$endDate}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($topBookers, $users, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, ['Citizen Analytics Report']);
            fputcsv($file, ['Period: ' . $startDate . ' to ' . $endDate]);
            fputcsv($file, []);
            fputcsv($file, ['Rank', 'Citizen Name', 'Email', 'Total Bookings', 'Total Spent (₱)']);
            
            // Data rows
            $rank = 1;
            foreach ($topBookers as $booking) {
                $user = $users->get($booking->user_id);
                fputcsv($file, [
                    $rank++,
                    $user->full_name ?? 'Unknown User',
                    $user->email ?? 'N/A',
                    $booking->total_bookings,
                    number_format($booking->total_spent ?? 0, 2)
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Display operational metrics dashboard
     */
    public function operationalMetrics(Request $request)
    {
        // Date range filter (default to last 3 months)
        $startDate = $request->input('start_date', now()->subMonths(3)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        
        // Average processing times
        $avgStaffVerificationTime = DB::connection('facilities_db')
            ->table('bookings')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, staff_verified_at)) as avg_hours')
            ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
            ->whereNotNull('staff_verified_at')
            ->value('avg_hours');
        
        $avgPaymentTime = DB::connection('facilities_db')
            ->table('bookings')
            ->join('payment_slips', 'bookings.id', '=', 'payment_slips.booking_id')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, bookings.staff_verified_at, payment_slips.paid_at)) as avg_hours')
            ->whereBetween(DB::raw('DATE(bookings.created_at)'), [$startDate, $endDate])
            ->whereNotNull('payment_slips.paid_at')
            ->value('avg_hours');
        
        $avgTotalProcessingTime = DB::connection('facilities_db')
            ->table('bookings')
            ->join('payment_slips', 'bookings.id', '=', 'payment_slips.booking_id')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, bookings.created_at, payment_slips.paid_at)) as avg_hours')
            ->whereBetween(DB::raw('DATE(bookings.created_at)'), [$startDate, $endDate])
            ->whereNotNull('payment_slips.paid_at')
            ->value('avg_hours');
        
        // Staff performance metrics
        $staffPerformance = DB::connection('facilities_db')
            ->table('bookings')
            ->selectRaw('
                staff_verified_by,
                COUNT(*) as total_verified,
                SUM(CASE WHEN status IN ("paid", "confirmed", "completed") THEN 1 ELSE 0 END) as approved_count,
                SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected_count,
                AVG(TIMESTAMPDIFF(HOUR, created_at, staff_verified_at)) as avg_verification_hours
            ')
            ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
            ->whereNotNull('staff_verified_by')
            ->groupBy('staff_verified_by')
            ->orderByDesc('total_verified')
            ->get();
        
        // Get staff names
        $staffIds = $staffPerformance->pluck('staff_verified_by')->filter()->unique()->toArray();
        $staffNames = DB::connection('auth_db')
            ->table('users')
            ->whereIn('id', $staffIds)
            ->get()
            ->keyBy('id');
        
        // Rejection reasons breakdown
        $rejectionReasons = DB::connection('facilities_db')
            ->table('bookings')
            ->selectRaw('
                rejected_reason,
                COUNT(*) as count
            ')
            ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
            ->where('status', 'rejected')
            ->whereNotNull('rejected_reason')
            ->groupBy('rejected_reason')
            ->orderByDesc('count')
            ->get();
        
        // Expiration and cancellation rates
        $totalBookings = DB::connection('facilities_db')
            ->table('bookings')
            ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
            ->count();
        
        $expiredBookings = DB::connection('facilities_db')
            ->table('bookings')
            ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
            ->where('status', 'expired')
            ->count();
        
        $cancelledBookings = DB::connection('facilities_db')
            ->table('bookings')
            ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
            ->where('status', 'cancelled')
            ->count();
        
        $completedBookings = DB::connection('facilities_db')
            ->table('bookings')
            ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
            ->where('status', 'completed')
            ->count();
        
        // Calculate rates
        $expirationRate = $totalBookings > 0 ? ($expiredBookings / $totalBookings) * 100 : 0;
        $cancellationRate = $totalBookings > 0 ? ($cancelledBookings / $totalBookings) * 100 : 0;
        $completionRate = $totalBookings > 0 ? ($completedBookings / $totalBookings) * 100 : 0;
        
        // Workflow bottleneck identification
        $bottlenecks = [];
        
        if ($avgStaffVerificationTime > 48) {
            $bottlenecks[] = [
                'stage' => 'Staff Verification',
                'avg_hours' => round($avgStaffVerificationTime, 1),
                'severity' => 'high',
                'recommendation' => 'Consider hiring additional staff or streamlining verification process'
            ];
        }
        
        if ($avgPaymentTime > 24) {
            $bottlenecks[] = [
                'stage' => 'Payment Processing',
                'avg_hours' => round($avgPaymentTime, 1),
                'severity' => 'medium',
                'recommendation' => 'Improve payment reminder system or simplify payment methods'
            ];
        }
        
        return view('admin.analytics.operational-metrics', compact(
            'startDate',
            'endDate',
            'avgStaffVerificationTime',
            'avgPaymentTime',
            'avgTotalProcessingTime',
            'staffPerformance',
            'staffNames',
            'rejectionReasons',
            'totalBookings',
            'expiredBookings',
            'cancelledBookings',
            'completedBookings',
            'expirationRate',
            'cancellationRate',
            'completionRate',
            'bottlenecks'
        ));
    }

    /**
     * Export Booking Statistics as Excel
     */
    public function exportBookingStatisticsExcel(Request $request)
    {
        $startDate = $request->input('start_date', now()->subMonths(3)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        
        $filename = "booking_statistics_{$startDate}_to_{$endDate}.xlsx";
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\BookingStatisticsExport($startDate, $endDate),
            $filename
        );
    }

    /**
     * Export Booking Statistics as PDF
     */
    public function exportBookingStatisticsPDF(Request $request)
    {
        $startDate = $request->input('start_date', now()->subMonths(3)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        
        $bookings = DB::connection('facilities_db')
            ->table('bookings')
            ->leftJoin('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
            ->selectRaw('
                bookings.id,
                bookings.user_id,
                facilities.name as facility_name,
                bookings.status,
                bookings.start_time,
                bookings.end_time,
                bookings.total_amount,
                bookings.created_at
            ')
            ->whereBetween(DB::raw('DATE(bookings.created_at)'), [$startDate, $endDate])
            ->orderBy('bookings.created_at', 'desc')
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.analytics.exports.booking-statistics-pdf', [
            'bookings' => $bookings,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);

        return $pdf->download("booking_statistics_{$startDate}_to_{$endDate}.pdf");
    }

    /**
     * Export Facility Utilization as Excel
     */
    public function exportFacilityUtilizationExcel(Request $request)
    {
        $startDate = $request->input('start_date', now()->subMonths(3)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        
        $filename = "facility_utilization_{$startDate}_to_{$endDate}.xlsx";
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\FacilityUtilizationExport($startDate, $endDate),
            $filename
        );
    }

    /**
     * Export Citizen Analytics as Excel
     */
    public function exportCitizenAnalyticsExcel(Request $request)
    {
        $startDate = $request->input('start_date', now()->subMonths(3)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        
        $filename = "citizen_analytics_{$startDate}_to_{$endDate}.xlsx";
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\CitizenAnalyticsExport($startDate, $endDate),
            $filename
        );
    }
}

