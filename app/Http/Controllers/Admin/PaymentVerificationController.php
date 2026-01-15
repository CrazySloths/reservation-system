<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PaymentVerificationController extends Controller
{
    /**
     * Display payment verification queue
     * Shows all staff_verified bookings awaiting payment
     */
    public function index(Request $request)
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        // Build query for payment queue
        $query = Booking::with(['facility.lguCity', 'user'])
            ->where('status', 'staff_verified'); // Awaiting payment

        // Search by booking ID or user name
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filter by facility
        if ($request->filled('facility_id') && $request->input('facility_id') !== 'all') {
            $query->where('facility_id', $request->input('facility_id'));
        }

        // Order by created date (oldest first - most urgent)
        $bookings = $query->orderBy('created_at', 'asc')->paginate(20);

        // Get facilities for filter
        $facilities = \App\Models\FacilityDb::select('facility_id', 'name')
            ->where('is_available', true)
            ->orderBy('name')
            ->get();

        // Calculate time remaining for each booking (48-hour deadline from staff approval)
        foreach ($bookings as $booking) {
            // Use updated_at as the approval time (when status changed to staff_verified)
            $approvedAt = Carbon::parse($booking->updated_at);
            $deadline = $approvedAt->copy()->addHours(48);
            $now = Carbon::now();
            
            $booking->hours_remaining = max(0, $now->diffInHours($deadline, false));
            $booking->deadline = $deadline;
            $booking->is_overdue = $now->greaterThan($deadline);
        }

        return view('admin.payment-queue', compact('bookings', 'facilities'));
    }

    /**
     * Confirm payment for a booking
     * Changes status from staff_verified to paid
     */
    public function confirmPayment(Request $request, $bookingId)
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        $booking = Booking::findOrFail($bookingId);

        // Verify booking is in correct status
        if ($booking->status !== 'staff_verified') {
            return back()->with('error', 'Booking is not awaiting payment verification.');
        }

        // Update booking status
        $booking->status = 'paid';
        
        // Add admin notes if provided
        if ($request->filled('admin_notes')) {
            $booking->admin_notes = $request->input('admin_notes');
        }

        $booking->save();

        // TODO: Send notification to citizen (Priority 4)
        
        return redirect()
            ->route('admin.bookings.review', $bookingId)
            ->with('success', 'Payment confirmed successfully! Booking is now paid and awaiting final confirmation.');
    }

    /**
     * Reject payment for a booking
     * Keeps status as staff_verified but adds rejection notes
     */
    public function rejectPayment(Request $request, $bookingId)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $userId = session('user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        $booking = Booking::findOrFail($bookingId);

        // Verify booking is in correct status
        if ($booking->status !== 'staff_verified') {
            return back()->with('error', 'Booking is not awaiting payment verification.');
        }

        // Add rejection notes (keep status as staff_verified so citizen can resubmit)
        $booking->payment_rejection_reason = $request->input('rejection_reason');
        $booking->payment_rejected_at = Carbon::now();
        $booking->payment_rejected_by = $userId;
        $booking->save();

        // TODO: Send notification to citizen (Priority 4)
        
        return redirect()
            ->route('admin.bookings.review', $bookingId)
            ->with('warning', 'Payment rejected. Citizen will be notified to resubmit payment proof.');
    }
}

