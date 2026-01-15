<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Booking;
use App\Models\Facility;
use App\Models\FacilityDb;
use App\Models\User;
use App\Models\PaymentSlip;

class BookingVerificationController extends Controller
{
    /**
     * Display staff dashboard with booking statistics
     */
    public function dashboard()
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        // Get stats for dashboard
        // Use hybrid approach: audit logs + fallback to booking table for older verifications
        $verifiedFromAudit = DB::connection('auth_db')
            ->table('audit_logs')
            ->where('user_id', $userId)
            ->where('action', 'verify')
            ->where('model', 'Booking')
            ->whereDate('created_at', Carbon::today())
            ->count();
        
        $verifiedFromBookings = DB::connection('facilities_db')
            ->table('bookings')
            ->where('staff_verified_by', $userId)
            ->whereDate('staff_verified_at', Carbon::today())
            ->count();
        
        $rejectedFromAudit = DB::connection('auth_db')
            ->table('audit_logs')
            ->where('user_id', $userId)
            ->where('action', 'reject')
            ->where('model', 'Booking')
            ->whereDate('created_at', Carbon::today())
            ->count();
        
        $rejectedFromBookings = DB::connection('facilities_db')
            ->table('bookings')
            ->where('status', 'rejected')
            ->where('staff_verified_by', $userId)
            ->whereDate('staff_verified_at', Carbon::today())
            ->count();
        
        $stats = [
            'pending_verification' => DB::connection('facilities_db')
                ->table('bookings')
                ->where('status', 'pending')
                ->count(),
            
            // Use max of audit logs or booking table (hybrid approach)
            'verified_today' => max($verifiedFromAudit, $verifiedFromBookings),
            'rejected_today' => max($rejectedFromAudit, $rejectedFromBookings),
            'total_processed' => max($verifiedFromAudit, $verifiedFromBookings) + max($rejectedFromAudit, $rejectedFromBookings),
        ];

        return view('staff.dashboard', compact('stats'));
    }

    /**
     * Display verification queue - all pending bookings
     */
    public function verificationQueue(Request $request)
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        // Build query for pending bookings using Eloquent
        // Note: Not eager loading 'user' because it's in a different database (auth_db vs facilities_db)
        $query = Booking::with(['facility.lguCity'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc');

        // Filter by facility if provided
        if ($request->has('facility_id') && $request->facility_id != '') {
            $query->where('facility_id', $request->facility_id);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('event_date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('event_date', '<=', $request->date_to);
        }

        $bookings = $query->paginate(10);

        // Get facilities for filter dropdown (from facilities_db)
        // Note: facilities_db uses 'name' not 'facility_name', and 'facility_id' as primary key
        $facilities = FacilityDb::select('facility_id', 'name')
            ->orderBy('name')
            ->get();

        return view('staff.bookings.verification-queue', compact('bookings', 'facilities'));
    }

    /**
     * Show detailed review page for a specific booking
     */
    public function review($bookingId)
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        // Get booking details with all related data using Eloquent
        // Note: Not eager loading 'user' because it's in a different database
        $booking = Booking::with(['facility.lguCity', 'equipmentItems'])
            ->find($bookingId);

        if (!$booking) {
            return redirect()->route('staff.verification-queue')
                ->with('error', 'Booking not found.');
        }

        // Equipment is already loaded via relationship
        $equipment = $booking->equipmentItems;

        // Documents - for now, we'll handle as attributes on the booking model
        $documents = [];
        if ($booking->valid_id_front_path) {
            $documents[] = (object)[
                'type' => 'Valid ID (Front)',
                'path' => $booking->valid_id_front_path
            ];
        }
        if ($booking->valid_id_back_path) {
            $documents[] = (object)[
                'type' => 'Valid ID (Back)',
                'path' => $booking->valid_id_back_path
            ];
        }
        if ($booking->valid_id_selfie_path) {
            $documents[] = (object)[
                'type' => 'Selfie with ID',
                'path' => $booking->valid_id_selfie_path
            ];
        }
        if ($booking->supporting_doc_path) {
            $documents[] = (object)[
                'type' => 'Supporting Document',
                'path' => $booking->supporting_doc_path
            ];
        }

        // Manually load user from auth_db (different database) with relationships
        $user = $booking->user_id ? User::with(['philippineCity', 'barangay'])->find($booking->user_id) : null;

        // Check for schedule conflicts (single source of truth - NO redundancy)
        $conflictCheck = $booking->checkScheduleConflicts();

        return view('staff.bookings.review', compact('booking', 'equipment', 'documents', 'user', 'conflictCheck'));
    }

    /**
     * Verify/Approve a booking
     */
    public function verify(Request $request, $bookingId)
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'staff_notes' => 'nullable|string|max:1000'
        ]);

        try {
            // Get booking details
            $booking = Booking::findOrFail($bookingId);

            // Update booking status
            DB::connection('facilities_db')->table('bookings')
                ->where('id', $bookingId)
                ->update([
                    'status' => 'staff_verified',
                    'staff_verified_by' => $userId,
                    'staff_verified_at' => now(),
                    'staff_notes' => $validated['staff_notes'] ?? null,
                    'updated_at' => now()
                ]);

            // Auto-generate payment slip
            $paymentSlip = PaymentSlip::create([
                'slip_number' => PaymentSlip::generateSlipNumber(),
                'booking_id' => $bookingId,
                'amount_due' => $booking->total_amount,
                'payment_deadline' => now()->addHours(48), // 48-hour deadline
                'status' => 'unpaid',
            ]);

            // Log the verification action
            DB::connection('auth_db')->table('audit_logs')->insert([
                'user_id' => $userId,
                'action' => 'verify',
                'model' => 'Booking',
                'model_id' => $bookingId,
                'changes' => json_encode([
                    'status' => 'staff_verified',
                    'staff_notes' => $validated['staff_notes'] ?? null,
                    'payment_slip' => $paymentSlip->slip_number,
                ]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Send notification to citizen with payment deadline
            try {
                $user = User::find($booking->user_id);
                $bookingWithFacility = DB::connection('facilities_db')
                    ->table('bookings')
                    ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
                    ->where('bookings.id', $bookingId)
                    ->selectRaw('bookings.*, facilities.name as facility_name, CONCAT("BK", LPAD(bookings.id, 6, "0")) as booking_reference')
                    ->first();
                
                if ($user && $bookingWithFacility) {
                    $user->notify(new \App\Notifications\StaffVerified($bookingWithFacility, $paymentSlip));
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send staff verification notification: ' . $e->getMessage());
            }

            return redirect()->route('staff.verification-queue')
                ->with('success', 'Booking verified successfully. Payment slip ' . $paymentSlip->slip_number . ' generated with 48-hour deadline.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to verify booking: ' . $e->getMessage());
        }
    }

    /**
     * Reject a booking
     */
    public function reject(Request $request, $bookingId)
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000'
        ]);

        try {
            // Get booking before updating
            $booking = DB::connection('facilities_db')
                ->table('bookings')
                ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
                ->where('bookings.id', $bookingId)
                ->select('bookings.*', 'facilities.name as facility_name')
                ->first();

            // Update booking status
            DB::connection('facilities_db')->table('bookings')
                ->where('id', $bookingId)
                ->update([
                    'status' => 'rejected',
                    'staff_verified_by' => $userId,
                    'staff_verified_at' => now(),
                    'rejected_reason' => $validated['rejection_reason'],
                    'updated_at' => now()
                ]);

            // Log the rejection action
            DB::connection('auth_db')->table('audit_logs')->insert([
                'user_id' => $userId,
                'action' => 'reject',
                'model' => 'Booking',
                'model_id' => $bookingId,
                'changes' => json_encode([
                    'status' => 'rejected',
                    'rejection_reason' => $validated['rejection_reason'],
                ]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Send rejection notification to citizen
            try {
                $user = User::find($booking->user_id);
                
                // Add booking_reference to the booking object
                $booking->booking_reference = 'BK' . str_pad($booking->id, 6, '0', STR_PAD_LEFT);
                
                \Log::info('Attempting to send rejection notification', [
                    'booking_id' => $booking->id,
                    'user_id' => $booking->user_id,
                    'user_found' => $user ? 'yes' : 'no',
                    'user_email' => $user ? $user->email : 'N/A'
                ]);
                
                if ($user && $booking) {
                    $user->notify(new \App\Notifications\BookingRejected($booking, $validated['rejection_reason']));
                    \Log::info('Rejection notification sent successfully');
                } else {
                    \Log::warning('Cannot send notification: user or booking is null');
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send booking rejection notification: ' . $e->getMessage(), [
                    'exception' => $e,
                    'trace' => $e->getTraceAsString()
                ]);
            }

            return redirect()->route('staff.verification-queue')
                ->with('success', 'Booking rejected. Citizen has been notified.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to reject booking: ' . $e->getMessage());
        }
    }

    /**
     * Show all bookings with filtering (history page)
     */
    public function allBookings(Request $request)
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        // Build query for bookings with filters
        // Exclude pending bookings - they belong in Verification Queue only
        $query = Booking::with(['facility.lguCity'])
            ->where('status', '!=', 'pending')
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by facility
        if ($request->has('facility_id') && $request->facility_id != '') {
            $query->where('facility_id', $request->facility_id);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('event_date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('event_date', '<=', $request->date_to);
        }

        // Search by booking ID
        if ($request->has('search') && $request->search != '') {
            $query->where('id', 'like', '%' . $request->search . '%');
        }

        $bookings = $query->paginate(15);

        // Get facilities for filter dropdown
        $facilities = FacilityDb::select('facility_id', 'name')
            ->orderBy('name')
            ->get();

        return view('staff.bookings.index', compact('bookings', 'facilities'));
    }
}

