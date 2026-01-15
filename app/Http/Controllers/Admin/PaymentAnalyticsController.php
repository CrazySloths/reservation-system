<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentAnalyticsController extends Controller
{
    /**
     * Display payment analytics dashboard
     */
    public function index(Request $request)
    {
        // Date range filter (default: last 30 days)
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Total Revenue
        $totalRevenue = DB::connection('facilities_db')->table('payment_slips')
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->sum('amount_due');

        // Total Transactions
        $totalTransactions = DB::connection('facilities_db')->table('payment_slips')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Payment Method Breakdown
        $paymentMethodBreakdown = DB::connection('facilities_db')->table('payment_slips')
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount_due) as total'))
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->groupBy('payment_method')
            ->get();

        // Payment Status Breakdown
        $statusBreakdown = DB::connection('facilities_db')->table('payment_slips')
            ->select('status', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('status')
            ->get();

        // Daily Revenue Trend (last 30 days)
        $dailyRevenue = DB::connection('facilities_db')->table('payment_slips')
            ->select(DB::raw('DATE(paid_at) as date'), DB::raw('SUM(amount_due) as total'))
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top Revenue Generating Facilities
        $topFacilities = DB::connection('facilities_db')->table('payment_slips')
            ->join('bookings', 'payment_slips.booking_id', '=', 'bookings.id')
            ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
            ->select('facilities.name as facility_name', DB::raw('SUM(payment_slips.amount_due) as total_revenue'), DB::raw('COUNT(payment_slips.id) as booking_count'))
            ->where('payment_slips.status', 'paid')
            ->whereBetween('payment_slips.paid_at', [$startDate, $endDate])
            ->groupBy('facilities.facility_id', 'facilities.name')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();

        // Average Payment Processing Time
        $avgProcessingTime = DB::connection('facilities_db')->table('payment_slips')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, paid_at)) as avg_hours'))
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->whereNotNull('paid_at')
            ->value('avg_hours');

        // Success Rate
        $successRate = 0;
        if ($totalTransactions > 0) {
            $paidCount = DB::connection('facilities_db')->table('payment_slips')
                ->where('status', 'paid')
                ->whereBetween('paid_at', [$startDate, $endDate])
                ->count();
            $successRate = ($paidCount / $totalTransactions) * 100;
        }

        // Pending Payments
        $pendingPayments = DB::connection('facilities_db')->table('payment_slips')
            ->where('status', 'pending')
            ->count();

        $pendingAmount = DB::connection('facilities_db')->table('payment_slips')
            ->where('status', 'pending')
            ->sum('amount_due');

        return view('admin.analytics.payments', compact(
            'startDate',
            'endDate',
            'totalRevenue',
            'totalTransactions',
            'paymentMethodBreakdown',
            'statusBreakdown',
            'dailyRevenue',
            'topFacilities',
            'avgProcessingTime',
            'successRate',
            'pendingPayments',
            'pendingAmount'
        ));
    }

    /**
     * Export payment analytics
     */
    public function export(Request $request)
    {
        // Implementation for export (CSV/Excel)
        // This can be added later if needed
        return response()->json(['message' => 'Export feature coming soon']);
    }
}

