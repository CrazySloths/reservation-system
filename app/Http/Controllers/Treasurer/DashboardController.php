<?php

namespace App\Http\Controllers\Treasurer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the Treasurer dashboard.
     */
    public function index()
    {
        // Get user data from session
        $userId = session('user_id');
        $userName = session('user_name');
        $userEmail = session('user_email');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }
        
        // Today's collections
        $todayCollections = DB::connection('facilities_db')
            ->table('payment_slips')
            ->where('status', 'paid')
            ->whereDate('paid_at', Carbon::today())
            ->sum('amount_due') ?? 0;
        
        // This month's collections
        $monthlyCollections = DB::connection('facilities_db')
            ->table('payment_slips')
            ->where('status', 'paid')
            ->whereYear('paid_at', Carbon::now()->year)
            ->whereMonth('paid_at', Carbon::now()->month)
            ->sum('amount_due') ?? 0;
        
        // Total all-time collections
        $totalCollections = DB::connection('facilities_db')
            ->table('payment_slips')
            ->where('status', 'paid')
            ->sum('amount_due') ?? 0;
        
        // Pending payments (awaiting verification)
        $pendingPayments = DB::connection('facilities_db')
            ->table('payment_slips')
            ->where('status', 'unpaid')
            ->where('payment_deadline', '>=', Carbon::now())
            ->count();
        
        // Payments verified today
        $paymentsVerifiedToday = DB::connection('facilities_db')
            ->table('payment_slips')
            ->where('status', 'paid')
            ->whereDate('paid_at', Carbon::today())
            ->count();
        
        // Expired payment slips
        $expiredPaymentSlips = DB::connection('facilities_db')
            ->table('payment_slips')
            ->where('status', 'expired')
            ->count();
        
        // Recent payments (last 10)
        try {
            $recentPayments = DB::connection('facilities_db')
                ->table('payment_slips')
                ->join('bookings', 'payment_slips.booking_id', '=', 'bookings.id')
                ->join('facilities', 'bookings.facility_id', '=', 'facilities.id')
                ->where('payment_slips.status', 'paid')
                ->orderBy('payment_slips.paid_at', 'desc')
                ->limit(10)
                ->select(
                    'payment_slips.*',
                    'bookings.applicant_name',
                    'facilities.name as facility_name'
                )
                ->get();
        } catch (\Exception $e) {
            // If query fails (no data yet), return empty collection
            $recentPayments = collect([]);
        }
        
        // Payments by method (for pie chart)
        $paymentsByMethod = DB::connection('facilities_db')
            ->table('payment_slips')
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount_due) as total'))
            ->where('status', 'paid')
            ->whereNotNull('payment_method')
            ->groupBy('payment_method')
            ->get();
        
        // Daily collections for the past 7 days (for chart)
        $dailyCollectionsChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $amount = DB::connection('facilities_db')
                ->table('payment_slips')
                ->where('status', 'paid')
                ->whereDate('paid_at', $date)
                ->sum('amount_due') ?? 0;
            
            $dailyCollectionsChart[] = [
                'date' => $date->format('M d'),
                'amount' => $amount
            ];
        }
        
        return view('treasurer.dashboard', [
            'todayCollections' => $todayCollections,
            'monthlyCollections' => $monthlyCollections,
            'totalCollections' => $totalCollections,
            'pendingPayments' => $pendingPayments,
            'paymentsVerifiedToday' => $paymentsVerifiedToday,
            'expiredPaymentSlips' => $expiredPaymentSlips,
            'recentPayments' => $recentPayments,
            'paymentsByMethod' => $paymentsByMethod,
            'dailyCollectionsChart' => $dailyCollectionsChart,
        ]);
    }
}

