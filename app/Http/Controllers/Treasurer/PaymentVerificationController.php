<?php

namespace App\Http\Controllers\Treasurer;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PaymentSlip;
use App\Models\Booking;
use Carbon\Carbon;

class PaymentVerificationController extends Controller
{
    /**
     * Display the payment verification queue.
     */
    public function index(Request $request)
    {
        try {
            $query = DB::connection('facilities_db')
                ->table('payment_slips')
                ->join('bookings', 'payment_slips.booking_id', '=', 'bookings.id')
                ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
                ->select(
                    'payment_slips.*',
                    'bookings.applicant_name',
                    'bookings.applicant_email',
                    'bookings.applicant_phone',
                    'bookings.user_id',
                    'bookings.start_time',
                    'facilities.name as facility_name'
                );
            
            // Filter by status (default: unpaid)
            $status = $request->get('status', 'unpaid');
            if ($status !== 'all') {
                $query->where('payment_slips.status', $status);
            }
            
            // Search functionality
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('payment_slips.slip_number', 'like', "%{$search}%")
                      ->orWhere('bookings.applicant_name', 'like', "%{$search}%")
                      ->orWhere('bookings.applicant_email', 'like', "%{$search}%");
                });
            }
            
            // Sort by payment deadline (urgent first)
            $query->orderBy('payment_slips.payment_deadline', 'asc');
            
            $paymentSlips = $query->paginate(20);
            
            // Fetch user names from auth_db for bookings with user_id but no applicant_name
            $paymentSlips->getCollection()->transform(function ($slip) {
                if (empty($slip->applicant_name) && $slip->user_id) {
                    $user = DB::connection('auth_db')->table('users')
                        ->where('id', $slip->user_id)
                        ->first(['full_name', 'email']);
                    if ($user) {
                        $slip->applicant_name = $user->full_name;
                        if (empty($slip->applicant_email)) {
                            $slip->applicant_email = $user->email;
                        }
                    }
                }
                return $slip;
            });
            
        } catch (\Exception $e) {
            // If query fails, return empty collection
            $paymentSlips = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
            $status = $request->get('status', 'unpaid');
        }
        
        return view('treasurer.payment-verification.index', [
            'paymentSlips' => $paymentSlips,
            'status' => $status ?? 'unpaid',
        ]);
    }
    
    /**
     * Show individual payment slip details.
     */
    public function show($id)
    {
        $paymentSlip = DB::connection('facilities_db')
            ->table('payment_slips')
            ->join('bookings', 'payment_slips.booking_id', '=', 'bookings.id')
            ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
            ->where('payment_slips.id', $id)
            ->select(
                'payment_slips.*',
                'bookings.applicant_name',
                'bookings.applicant_email',
                'bookings.applicant_phone',
                'bookings.user_id',
                'bookings.start_time',
                'bookings.end_time',
                'bookings.purpose',
                'bookings.expected_attendees as attendees',
                'facilities.name as facility_name',
                'facilities.address as facility_address'
            )
            ->first();
        
        if (!$paymentSlip) {
            return redirect()->route('treasurer.payment-verification')
                ->with('error', 'Payment slip not found.');
        }
        
        // Fetch user details from auth_db if applicant_name is empty
        if (empty($paymentSlip->applicant_name) && $paymentSlip->user_id) {
            $user = DB::connection('auth_db')->table('users')
                ->where('id', $paymentSlip->user_id)
                ->first(['full_name', 'email', 'mobile_number']);
            if ($user) {
                $paymentSlip->applicant_name = $user->full_name;
                if (empty($paymentSlip->applicant_email)) {
                    $paymentSlip->applicant_email = $user->email;
                }
                if (empty($paymentSlip->applicant_phone)) {
                    $paymentSlip->applicant_phone = $user->mobile_number;
                }
            }
        }
        
        return view('treasurer.payment-verification.show', [
            'paymentSlip' => $paymentSlip,
        ]);
    }
    
    /**
     * Verify cash payment and generate Official Receipt.
     */
    public function verifyPayment(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,gcash,paymaya,bank_transfer,credit_card',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        $paymentSlip = PaymentSlip::find($id);
        
        if (!$paymentSlip) {
            return response()->json([
                'success' => false,
                'message' => 'Payment slip not found.'
            ], 404);
        }
        
        if ($paymentSlip->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'This payment has already been verified.'
            ], 400);
        }
        
        try {
            DB::connection('facilities_db')->beginTransaction();
            
            // Auto-generate Official Receipt number
            $orNumber = $this->generateOfficialReceiptNumber();
            
            // Update payment slip status
            $paymentSlip->status = 'paid';
            $paymentSlip->payment_method = $request->payment_method;
            $paymentSlip->or_number = $orNumber; // Store OR number in or_number column
            // Keep transaction_reference for cashless payments (GCash/Maya reference)
            $paymentSlip->notes = $request->notes;
            $paymentSlip->paid_at = now();
            $paymentSlip->verified_by = session('user_id');
            $paymentSlip->save();
            
            // Update booking status to 'paid' (Admin will confirm later)
            $booking = Booking::find($paymentSlip->booking_id);
            if ($booking) {
                $booking->status = 'paid';
                $booking->save();
            }
            
            // Send notification to citizen about payment verification
            try {
                $user = User::find($booking->user_id);
                $bookingWithFacility = DB::connection('facilities_db')
                    ->table('bookings')
                    ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
                    ->where('bookings.id', $paymentSlip->booking_id)
                    ->selectRaw('bookings.*, facilities.name as facility_name, CONCAT("BK", LPAD(bookings.id, 6, "0")) as booking_reference')
                    ->first();
                
                $paymentSlipFresh = DB::connection('facilities_db')
                    ->table('payment_slips')
                    ->where('id', $id)
                    ->first();
                
                if ($user && $bookingWithFacility && $paymentSlipFresh) {
                    $user->notify(new \App\Notifications\PaymentVerified($bookingWithFacility, $paymentSlipFresh));
                }
                
                // Notify all admins that payment is verified and booking is ready for confirmation
                $adminUsers = User::where('subsystem_role_id', 1) // 1 is the role_id for admin
                                    ->where('subsystem_id', 4) // 4 is the subsystem_id for facilities
                                    ->get();
                foreach ($adminUsers as $admin) {
                    $admin->notify(new \App\Notifications\PaymentVerified($bookingWithFacility, $paymentSlipFresh));
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send payment verification notification: ' . $e->getMessage());
            }
            
            // Official Receipt PDF is available via the Official Receipts menu
            
            DB::connection('facilities_db')->commit();
            
            return response()->json([
                'success' => true,
                'message' => "Payment verified successfully! Official Receipt #{$orNumber} has been generated.",
                'or_number' => $orNumber,
                'redirect' => route('treasurer.payment-verification')
            ]);
            
        } catch (\Exception $e) {
            DB::connection('facilities_db')->rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify payment: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Display payment history (all verified payments).
     */
    public function history(Request $request)
    {
        try {
            $query = DB::connection('facilities_db')
                ->table('payment_slips')
                ->join('bookings', 'payment_slips.booking_id', '=', 'bookings.id')
                ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
                ->select(
                    'payment_slips.*',
                    'bookings.applicant_name',
                    'bookings.applicant_email',
                    'bookings.applicant_phone',
                    'bookings.user_id',
                    'bookings.start_time',
                    'bookings.end_time',
                    'facilities.name as facility_name'
                )
                ->where('payment_slips.status', 'paid'); // Only verified payments
            
            // Search functionality
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('payment_slips.slip_number', 'like', "%{$search}%")
                      ->orWhere('payment_slips.transaction_reference', 'like', "%{$search}%")
                      ->orWhere('bookings.applicant_name', 'like', "%{$search}%")
                      ->orWhere('bookings.applicant_email', 'like', "%{$search}%");
                });
            }
            
            // Filter by payment method
            if ($request->filled('payment_method') && $request->payment_method !== 'all') {
                $query->where('payment_slips.payment_method', $request->payment_method);
            }
            
            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('payment_slips.paid_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('payment_slips.paid_at', '<=', $request->date_to);
            }
            
            // Sort by payment date (most recent first)
            $paymentSlips = $query->orderBy('payment_slips.paid_at', 'desc')->paginate(15);
            
            // Fetch user data for bookings without applicant_name
            $userIds = $paymentSlips->filter(function($slip) {
                return empty($slip->applicant_name) && !empty($slip->user_id);
            })->pluck('user_id')->unique();
            
            if ($userIds->isNotEmpty()) {
                $users = DB::connection('auth_db')
                    ->table('users')
                    ->whereIn('id', $userIds)
                    ->get()
                    ->keyBy('id');
                
                // Merge user data into payment slips
                $paymentSlips->transform(function($slip) use ($users) {
                    if (empty($slip->applicant_name) && !empty($slip->user_id) && isset($users[$slip->user_id])) {
                        $user = $users[$slip->user_id];
                        $slip->applicant_name = $user->full_name ?? 'N/A';
                        $slip->applicant_email = $user->email ?? 'N/A';
                        $slip->applicant_phone = $user->mobile_number ?? 'N/A';
                    }
                    return $slip;
                });
            }
            
            // Get statistics
            $stats = [
                'total_verified' => DB::connection('facilities_db')
                    ->table('payment_slips')
                    ->where('status', 'paid')
                    ->count(),
                'total_amount' => DB::connection('facilities_db')
                    ->table('payment_slips')
                    ->where('status', 'paid')
                    ->sum('amount_due'),
                'today_verified' => DB::connection('facilities_db')
                    ->table('payment_slips')
                    ->where('status', 'paid')
                    ->whereDate('paid_at', today())
                    ->count(),
                'today_amount' => DB::connection('facilities_db')
                    ->table('payment_slips')
                    ->where('status', 'paid')
                    ->whereDate('paid_at', today())
                    ->sum('amount_due'),
            ];
            
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Payment History Query Error: ' . $e->getMessage());
            
            // Return empty collection with proper pagination
            $paymentSlips = new \Illuminate\Pagination\LengthAwarePaginator(
                [],
                0,
                15,
                1,
                ['path' => request()->url(), 'query' => request()->query()]
            );
            $stats = [
                'total_verified' => 0,
                'total_amount' => 0,
                'today_verified' => 0,
                'today_amount' => 0,
            ];
        }
        
        return view('treasurer.payment-history.index', compact('paymentSlips', 'stats'));
    }
    
    /**
     * Auto-generate Official Receipt number.
     * Format: OR-YYYY-NNNN (e.g., OR-2025-0001)
     */
    private function generateOfficialReceiptNumber()
    {
        $year = now()->year;
        
        // Find the last OR number for this year (look in or_number column, not transaction_reference)
        $lastPayment = PaymentSlip::where('or_number', 'like', "OR-{$year}-%")
            ->orderBy('or_number', 'desc')
            ->first();
        
        if ($lastPayment && $lastPayment->or_number) {
            // Extract the sequence number from the last OR
            $lastNumber = intval(substr($lastPayment->or_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            // First OR of the year
            $newNumber = '0001';
        }
        
        return "OR-{$year}-{$newNumber}";
    }
}

