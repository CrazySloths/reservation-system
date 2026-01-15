<?php

namespace App\Http\Controllers\CBD;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the CBD dashboard with revenue overview
     */
    public function index()
    {
        // Get current month revenue
        $currentMonth = Carbon::now();
        $currentMonthRevenue = $this->getMonthlyRevenue($currentMonth);
        
        // Get last month revenue
        $lastMonth = Carbon::now()->subMonth();
        $lastMonthRevenue = $this->getMonthlyRevenue($lastMonth);
        
        // Calculate growth
        $revenueGrowth = $lastMonthRevenue > 0 
            ? (($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 
            : 0;
        
        // Get year-to-date revenue
        $ytdRevenue = $this->getYearToDateRevenue();
        
        // Get total bookings count
        $totalBookings = DB::connection('facilities_db')
            ->table('bookings')
            ->whereIn('status', ['paid', 'confirmed', 'completed'])
            ->count();
        
        // Get active facilities count
        $activeFacilities = DB::connection('facilities_db')
            ->table('facilities')
            ->where('is_available', true)
            ->count();
        
        // Get recent payments (last 10)
        $recentPayments = DB::connection('facilities_db')
            ->table('payment_slips')
            ->join('bookings', 'payment_slips.booking_id', '=', 'bookings.id')
            ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
            ->selectRaw('
                payment_slips.*,
                bookings.user_name,
                facilities.name as facility_name,
                CONCAT("BK", LPAD(bookings.id, 6, "0")) as booking_reference
            ')
            ->where('payment_slips.status', 'paid')
            ->orderBy('payment_slips.paid_at', 'desc')
            ->limit(10)
            ->get();
        
        // Get top revenue facilities
        $topFacilities = DB::connection('facilities_db')
            ->table('bookings')
            ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
            ->join('lgu_cities', 'facilities.lgu_city_id', '=', 'lgu_cities.id')
            ->select([
                'facilities.name',
                'lgu_cities.city_name as city',
                DB::raw('COUNT(bookings.id) as booking_count'),
                DB::raw('SUM(bookings.total_amount) as total_revenue')
            ])
            ->whereIn('bookings.status', ['paid', 'confirmed', 'completed'])
            ->groupBy('facilities.name', 'lgu_cities.city_name')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();
        
        // Get revenue by payment method
        $revenueByPaymentMethod = DB::connection('facilities_db')
            ->table('payment_slips')
            ->selectRaw('
                payment_method,
                COUNT(*) as transaction_count,
                SUM(amount_due) as total_amount
            ')
            ->where('status', 'paid')
            ->groupBy('payment_method')
            ->get();
        
        // Get monthly revenue trend (last 6 months)
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $revenue = $this->getMonthlyRevenue($month);
            $monthlyTrend[] = [
                'month' => $month->format('M Y'),
                'revenue' => $revenue
            ];
        }
        
        return view('cbd.dashboard', compact(
            'currentMonthRevenue',
            'lastMonthRevenue',
            'revenueGrowth',
            'ytdRevenue',
            'totalBookings',
            'activeFacilities',
            'recentPayments',
            'topFacilities',
            'revenueByPaymentMethod',
            'monthlyTrend'
        ));
    }
    
    /**
     * Get monthly revenue for a specific month
     */
    private function getMonthlyRevenue(Carbon $month)
    {
        return DB::connection('facilities_db')
            ->table('payment_slips')
            ->whereYear('paid_at', $month->year)
            ->whereMonth('paid_at', $month->month)
            ->where('status', 'paid')
            ->sum('amount_due') ?? 0;
    }
    
    /**
     * Get year-to-date revenue
     */
    private function getYearToDateRevenue()
    {
        return DB::connection('facilities_db')
            ->table('payment_slips')
            ->whereYear('paid_at', Carbon::now()->year)
            ->where('status', 'paid')
            ->sum('amount_due') ?? 0;
    }
}

