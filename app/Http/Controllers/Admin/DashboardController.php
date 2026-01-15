<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\FacilityDb;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the Admin dashboard with statistics
     */
    public function index()
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        // Get statistics
        $stats = $this->getStatistics();
        
        // Get recent activity (without user relationship due to cross-database issue)
        $recentBookings = Booking::with(['facility'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentBookings'));
    }

    /**
     * Get dashboard statistics
     */
    private function getStatistics()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            // Payment pending (staff_verified bookings awaiting payment)
            'payment_pending' => Booking::where('status', 'staff_verified')->count(),
            
            // Payment verification (paid bookings awaiting admin confirmation)
            'payment_verification' => Booking::where('status', 'paid')->count(),
            
            // Confirmed bookings (final approved)
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            
            // Total revenue (all paid and confirmed bookings)
            'total_revenue' => Booking::whereIn('status', ['paid', 'confirmed'])
                ->sum('total_amount'),
            
            // Revenue this month
            'monthly_revenue' => Booking::whereIn('status', ['paid', 'confirmed'])
                ->where('created_at', '>=', $thisMonth)
                ->sum('total_amount'),
            
            // Today's bookings (by event date)
            'todays_events' => Booking::where('status', 'confirmed')
                ->whereDate('start_time', $today)
                ->count(),
            
            // Pending staff verification
            'pending_verification' => Booking::where('status', 'pending')->count(),
            
            // Rejected/Cancelled
            'rejected' => Booking::whereIn('status', ['rejected', 'cancelled'])->count(),
            
            // Expired (unpaid after 48 hours)
            'expired' => Booking::where('status', 'expired')->count(),
            
            // Total facilities (including "coming soon")
            'total_facilities' => FacilityDb::count(),
            
            // Most popular facility (this month)
            'popular_facility' => $this->getMostPopularFacility($thisMonth),

            // Daily revenue for the last 7 days (for chart)
            'daily_revenue_labels' => $this->getDailyRevenueLabels(),
            'daily_revenue_data' => $this->getDailyRevenueData(),
        ];
    }

    /**
     * Get daily revenue labels for the last 7 days
     */
    private function getDailyRevenueLabels()
    {
        $labels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            if ($i == 0) {
                $labels[] = 'Today';
            } elseif ($i == 1) {
                $labels[] = 'Yesterday';
            } else {
                $labels[] = $date->format('M d');
            }
        }
        return $labels;
    }

    /**
     * Get daily revenue data for the last 7 days
     */
    private function getDailyRevenueData()
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $revenue = Booking::whereIn('status', ['paid', 'confirmed'])
                ->whereDate('created_at', $date->format('Y-m-d'))
                ->sum('total_amount');
            $data[] = (float) $revenue;
        }
        return $data;
    }

    /**
     * Get the most popular facility this month
     */
    private function getMostPopularFacility($since)
    {
        $facility = Booking::select('facility_id', DB::raw('COUNT(*) as booking_count'))
            ->where('created_at', '>=', $since)
            ->whereIn('status', ['staff_verified', 'paid', 'confirmed'])
            ->groupBy('facility_id')
            ->orderBy('booking_count', 'desc')
            ->with('facility')
            ->first();

        return $facility ? [
            'name' => $facility->facility->name ?? 'N/A',
            'count' => $facility->booking_count
        ] : [
            'name' => 'No bookings yet',
            'count' => 0
        ];
    }
}

