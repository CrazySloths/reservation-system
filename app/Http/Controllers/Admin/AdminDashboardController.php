<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Facility;
use App\Models\PaymentSlip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard with REAL database data
     */
    public function index(Request $request)
    {
        // TEMPORARY SIMPLIFIED VERSION - Using static demo data
        // Will be replaced with real database queries once reservation system is integrated
        
        // Get authenticated admin
        $admin = Auth::user();
        
        // Fallback admin data if not authenticated
        if (!$admin) {
            $admin = (object) [
                'id' => 1,
                'name' => 'Administrator',
                'email' => 'admin@lgu1.com',
                'role' => 'admin',
                'status' => 'active'
            ];
        }
        
        $admin->full_name = $admin->name;
        $admin->avatar_initials = $this->generateInitials($admin->name);
        
        // Static demo data (will show zeros until reservation system is connected)
        $pendingApprovalsCount = 0;
        $pendingApprovals = collect([]);
        $conflicts = collect([]);
        $overduePayments = collect([]);
        $monthlyStats = [
            'bookings_count' => 0,
            'approved_bookings' => 0,
            'revenue' => 0,
            'pending_revenue' => 0
        ];
        $facilityStats = collect([]);
        $upcomingReservations = collect([]);
        $recentActivity = collect([]);
        $todaysEventsCount = 0;
        
        return view('admin.dashboard', compact(
            'admin',
            'pendingApprovalsCount',
            'pendingApprovals',
            'conflicts',
            'overduePayments',
            'monthlyStats',
            'facilityStats',
            'upcomingReservations',
            'recentActivity',
            'todaysEventsCount'
        ));
    }
    
    /**
     * Detect schedule conflicts (overlapping bookings for same facility)
     */
    private function detectScheduleConflicts()
    {
        $conflicts = collect([]);
        
        // Get all approved bookings for the next 30 days
        $bookings = Booking::with('facility')
            ->where('status', 'approved')
            ->where('event_date', '>=', now()->toDateString())
            ->where('event_date', '<=', now()->addDays(30)->toDateString())
            ->orderBy('event_date')
            ->orderBy('start_time')
            ->get();
        
        // Check for overlaps
        foreach ($bookings as $booking) {
            $overlapping = Booking::where('facility_id', $booking->facility_id)
                ->where('id', '!=', $booking->id)
                ->where('status', 'approved')
                ->where('event_date', $booking->event_date)
                ->where(function($query) use ($booking) {
                    $query->whereBetween('start_time', [$booking->start_time, $booking->end_time])
                          ->orWhereBetween('end_time', [$booking->start_time, $booking->end_time])
                          ->orWhere(function($q) use ($booking) {
                              $q->where('start_time', '<=', $booking->start_time)
                                ->where('end_time', '>=', $booking->end_time);
                          });
                })
                ->first();
            
            if ($overlapping) {
                $conflicts->push([
                    'facility' => $booking->facility,
                    'date' => $booking->event_date,
                    'booking1' => $booking,
                    'booking2' => $overlapping
                ]);
            }
        }
        
        return $conflicts;
    }
    
    /**
     * Get recent activity feed
     */
    private function getRecentActivity()
    {
        $activities = collect([]);
        
        // Recent approvals
        $recentApprovals = Booking::with('facility')
            ->where('status', 'approved')
            ->orderBy('updated_at', 'desc')
            ->limit(3)
            ->get();
        
        foreach ($recentApprovals as $booking) {
            $activities->push([
                'type' => 'approval',
                'message' => 'Reservation approved for ' . $booking->facility->name,
                'details' => 'Event: ' . $booking->event_name . ' on ' . Carbon::parse($booking->event_date)->format('M d, Y'),
                'time' => $booking->updated_at,
                'icon' => 'check-circle',
                'color' => 'text-green-600'
            ]);
        }
        
        // Recent payments
        $recentPayments = PaymentSlip::with('booking')
            ->where('status', 'paid')
            ->orderBy('updated_at', 'desc')
            ->limit(2)
            ->get();
        
        foreach ($recentPayments as $payment) {
            $activities->push([
                'type' => 'payment',
                'message' => 'Payment received',
                'details' => '₱' . number_format($payment->amount, 2) . ' - ' . $payment->slip_number,
                'time' => $payment->updated_at,
                'icon' => 'currency-dollar',
                'color' => 'text-blue-600'
            ]);
        }
        
        // Sort by time
        return $activities->sortByDesc('time')->take(5);
    }
    
    /**
     * Generate initials from name
     */
    private function generateInitials($name)
    {
        $nameParts = explode(' ', trim($name));
        $firstName = $nameParts[0] ?? 'A';
        $lastName = end($nameParts);
        
        return strtoupper(
            substr($firstName, 0, 1) . 
            (($lastName !== $firstName) ? substr($lastName, 0, 1) : 'D')
        );
    }
    
    /**
     * Get quick stats for admin dashboard (REAL DATA)
     */
    public function getQuickStats(Request $request)
    {
        $stats = [
            'pending_approvals' => Booking::where('status', 'pending')->count(),
            'conflicts' => $this->detectScheduleConflicts()->count(),
            'overdue_payments' => PaymentSlip::where('status', 'unpaid')
                                           ->where('due_date', '<', now())
                                           ->count(),
            'todays_events' => Booking::where('status', 'approved')
                                     ->whereDate('event_date', now()->toDateString())
                                     ->count()
        ];
        
        return response()->json($stats);
    }
}

