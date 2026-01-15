<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
    /**
     * Display all payment slips for the logged-in citizen.
     */
    public function index(Request $request)
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        $status = $request->get('status', 'all');
        $search = $request->get('search', '');

        // Base query
        $query = DB::connection('facilities_db')
            ->table('payment_slips')
            ->join('bookings', 'payment_slips.booking_id', '=', 'bookings.id')
            ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
            ->select(
                'payment_slips.*',
                'bookings.start_time',
                'bookings.end_time',
                'bookings.purpose',
                'facilities.name as facility_name',
                'facilities.address as facility_address'
            )
            ->where('bookings.user_id', $userId)
            ->orderBy('payment_slips.created_at', 'desc');

        // Filter by status
        if ($status !== 'all') {
            $query->where('payment_slips.status', $status);
        }

        // Live search
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('payment_slips.slip_number', 'like', "%{$search}%")
                  ->orWhere('facilities.name', 'like', "%{$search}%")
                  ->orWhere('bookings.purpose', 'like', "%{$search}%");
            });
        }

        $paymentSlips = $query->paginate(10);

        // Get counts for filter badges
        $statusCounts = [
            'all' => DB::connection('facilities_db')->table('payment_slips')
                ->join('bookings', 'payment_slips.booking_id', '=', 'bookings.id')
                ->where('bookings.user_id', $userId)->count(),
            'unpaid' => DB::connection('facilities_db')->table('payment_slips')
                ->join('bookings', 'payment_slips.booking_id', '=', 'bookings.id')
                ->where('bookings.user_id', $userId)->where('payment_slips.status', 'unpaid')->count(),
            'paid' => DB::connection('facilities_db')->table('payment_slips')
                ->join('bookings', 'payment_slips.booking_id', '=', 'bookings.id')
                ->where('bookings.user_id', $userId)->where('payment_slips.status', 'paid')->count(),
            'expired' => DB::connection('facilities_db')->table('payment_slips')
                ->join('bookings', 'payment_slips.booking_id', '=', 'bookings.id')
                ->where('bookings.user_id', $userId)->where('payment_slips.status', 'expired')->count(),
        ];

        return view('citizen.payments.index', compact('paymentSlips', 'status', 'statusCounts'));
    }

    /**
     * Display details of a specific payment slip.
     */
    public function show($id)
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        $paymentSlip = DB::connection('facilities_db')
            ->table('payment_slips')
            ->join('bookings', 'payment_slips.booking_id', '=', 'bookings.id')
            ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
            ->leftJoin('lgu_cities', 'facilities.lgu_city_id', '=', 'lgu_cities.id')
            ->select(
                'payment_slips.*',
                'bookings.start_time',
                'bookings.end_time',
                'bookings.purpose',
                'bookings.expected_attendees',
                'bookings.base_rate',
                'bookings.extension_rate',
                'bookings.equipment_total',
                'bookings.subtotal',
                'bookings.resident_discount_amount',
                'bookings.special_discount_amount',
                'bookings.total_discount',
                'facilities.name as facility_name',
                'facilities.address as facility_address',
                'facilities.image_path as facility_image',
                'lgu_cities.city_name',
                'lgu_cities.city_code'
            )
            ->where('payment_slips.id', $id)
            ->where('bookings.user_id', $userId)
            ->first();

        if (!$paymentSlip) {
            return redirect()->route('citizen.payment-slips')->with('error', 'Payment slip not found.');
        }

        // Get selected equipment for the booking
        $equipment = DB::connection('facilities_db')
            ->table('booking_equipment')
            ->join('equipment_items', 'booking_equipment.equipment_item_id', '=', 'equipment_items.id')
            ->select('booking_equipment.*', 'equipment_items.name as equipment_name', 'equipment_items.category')
            ->where('booking_equipment.booking_id', $paymentSlip->booking_id)
            ->get();

        return view('citizen.payments.show', compact('paymentSlip', 'equipment'));
    }

    /**
     * Upload payment proof/receipt.
     */
    public function uploadProof(Request $request, $id)
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Please login to continue.'], 401);
        }

        $paymentSlip = DB::connection('facilities_db')
            ->table('payment_slips')
            ->join('bookings', 'payment_slips.booking_id', '=', 'bookings.id')
            ->where('payment_slips.id', $id)
            ->where('bookings.user_id', $userId)
            ->select('payment_slips.*')
            ->first();

        if (!$paymentSlip) {
            return response()->json(['success' => false, 'message' => 'Payment slip not found.'], 404);
        }

        if ($paymentSlip->status !== 'unpaid') {
            return response()->json(['success' => false, 'message' => 'This payment slip has already been processed.'], 400);
        }

        $validator = Validator::make($request->all(), [
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'payment_method' => 'required|in:cash,gcash,paymaya,bank_transfer,check',
            'reference_number' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }

        // Handle file upload
        $paymentProofPath = null;
        if ($request->hasFile('payment_proof')) {
            $paymentProofPath = $request->file('payment_proof')->store('payment_proofs', 'public');
        }

        // Update payment slip
        DB::connection('facilities_db')
            ->table('payment_slips')
            ->where('id', $id)
            ->update([
                'payment_receipt_url' => $paymentProofPath,
                'payment_method' => $request->payment_method,
                'payment_gateway' => $request->payment_method,
                'gateway_reference_number' => $request->reference_number,
                'updated_at' => Carbon::now(),
            ]);

        return response()->json(['success' => true, 'message' => 'Payment proof uploaded successfully. Please wait for staff verification.']);
    }
    
    /**
     * Display cashless payment page.
     */
    public function showCashless($id)
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        $paymentSlip = DB::connection('facilities_db')
            ->table('payment_slips')
            ->join('bookings', 'payment_slips.booking_id', '=', 'bookings.id')
            ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
            ->select(
                'payment_slips.*',
                'bookings.start_time',
                'bookings.user_id',
                'bookings.applicant_name',
                'facilities.name as facility_name'
            )
            ->where('payment_slips.id', $id)
            ->where('bookings.user_id', $userId)
            ->first();

        if (!$paymentSlip) {
            return redirect()->route('citizen.payment-slips')->with('error', 'Payment slip not found.');
        }

        if ($paymentSlip->status !== 'unpaid') {
            return redirect()->route('citizen.payment-slips.show', $id)->with('error', 'This payment slip has already been processed.');
        }

        // Create booking object for the view
        $booking = (object)[
            'facility_name' => $paymentSlip->facility_name,
            'booking_date' => Carbon::parse($paymentSlip->start_time)->format('F d, Y'),
        ];

        return view('citizen.payments.cashless', compact('paymentSlip', 'booking'));
    }

    /**
     * Submit cashless payment with reference number.
     */
    public function submitCashless(Request $request, $id)
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        $paymentSlip = DB::connection('facilities_db')
            ->table('payment_slips')
            ->join('bookings', 'payment_slips.booking_id', '=', 'bookings.id')
            ->where('payment_slips.id', $id)
            ->where('bookings.user_id', $userId)
            ->select('payment_slips.*', 'bookings.user_id')
            ->first();

        if (!$paymentSlip) {
            return redirect()->route('citizen.payment-slips')->with('error', 'Payment slip not found.');
        }

        if ($paymentSlip->status !== 'unpaid') {
            return redirect()->route('citizen.payment-slips.show', $id)->with('error', 'This payment slip has already been processed.');
        }

        $validator = Validator::make($request->all(), [
            'payment_channel' => 'required|string|max:50',
            'reference_number' => 'required|string|max:20',
            'account_name' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $referenceNumber = strtoupper(trim($request->reference_number));
        $isTestMode = config('payment.test_mode', false);
        $isTestTransaction = $isTestMode && str_starts_with($referenceNumber, 'TEST-');

        // Map payment channel to database payment_method enum
        $paymentChannel = $request->payment_channel;
        $paymentMethod = $paymentChannel;
        
        // Convert channel names to database enum values
        if ($paymentChannel === 'maya') {
            $paymentMethod = 'paymaya';
        } elseif (in_array($paymentChannel, ['bpi', 'bdo', 'metrobank', 'unionbank', 'landbank', 'other_bank'])) {
            $paymentMethod = 'bank_transfer';
        }

        // Check for duplicate reference number (only for non-test transactions)
        if (!$isTestTransaction) {
            $duplicate = DB::connection('facilities_db')
                ->table('payment_slips')
                ->where('transaction_reference', $referenceNumber)
                ->where('payment_channel', $request->payment_channel)
                ->where('id', '!=', $id)
                ->exists();

            if ($duplicate) {
                return redirect()->back()
                    ->with('error', 'This reference number has already been used. Please check your transaction.')
                    ->withInput();
            }
        }

        // Update payment slip with reference number
        DB::connection('facilities_db')
            ->table('payment_slips')
            ->where('id', $id)
            ->update([
                'payment_method' => $paymentMethod,
                'payment_channel' => $paymentChannel,
                'transaction_reference' => $referenceNumber,
                'account_name' => $request->account_name,
                'is_test_transaction' => $isTestTransaction,
                'gateway_reference_number' => $referenceNumber, // Also store in gateway field
                'sent_to_treasurer_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

        // Update booking status to awaiting_payment_verification
        DB::connection('facilities_db')
            ->table('bookings')
            ->where('id', $paymentSlip->booking_id)
            ->update([
                'status' => 'staff_verified',
                'updated_at' => Carbon::now(),
            ]);

        // Send notification to citizen confirming payment submission
        try {
            $user = User::find($userId);
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
                // Notify the citizen
                $user->notify(new \App\Notifications\PaymentSubmitted($bookingWithFacility, $paymentSlipFresh));
                
                // Notify ALL treasurers about the new payment submission
                $treasurers = User::where('subsystem_role_id', 5)
                    ->where('subsystem_id', 4) // Facilities subsystem
                    ->get();
                
                foreach ($treasurers as $treasurer) {
                    $treasurer->notify(new \App\Notifications\PaymentSubmitted($bookingWithFacility, $paymentSlipFresh));
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send payment submission notification: ' . $e->getMessage());
        }

        $message = $isTestTransaction 
            ? 'Test payment submitted successfully! (Test Mode)' 
            : 'Payment submitted successfully! Our Treasurer will verify your payment within 24 hours.';

        return redirect()->route('citizen.payment-slips.show', $id)
            ->with('success', $message);
    }

    /**
     * Download official receipt PDF for paid payment slip.
     */
    public function downloadReceipt($id)
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }
        
        try {
            $paymentSlip = DB::connection('facilities_db')
                ->table('payment_slips')
                ->join('bookings', 'payment_slips.booking_id', '=', 'bookings.id')
                ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
                ->leftJoin('auth_db.users as verified_users', 'payment_slips.verified_by', '=', 'verified_users.id')
                ->select(
                    'payment_slips.*',
                    'bookings.applicant_name',
                    'bookings.applicant_email',
                    'bookings.applicant_phone',
                    'bookings.user_id',
                    'bookings.start_time',
                    'bookings.end_time',
                    'bookings.purpose',
                    'bookings.expected_attendees',
                    'facilities.name as facility_name',
                    'facilities.address as facility_address',
                    'verified_users.full_name as verified_by_name'
                )
                ->where('payment_slips.id', $id)
                ->where('bookings.user_id', $userId) // Ensure it belongs to this citizen
                ->first();
            
            if (!$paymentSlip || $paymentSlip->status !== 'paid') {
                return redirect()->back()->with('error', 'Official receipt not available. Payment must be verified first.');
            }
            
            // Fetch user data if applicant_name is empty
            if (empty($paymentSlip->applicant_name)) {
                $user = DB::connection('auth_db')
                    ->table('users')
                    ->where('id', $userId)
                    ->first();
                
                if ($user) {
                    $paymentSlip->applicant_name = $user->full_name ?? 'N/A';
                    $paymentSlip->applicant_email = $user->email ?? 'N/A';
                    $paymentSlip->applicant_phone = $user->mobile_number ?? 'N/A';
                }
            }
            
            // Generate PDF
            $pdf = Pdf::loadView('treasurer.official-receipts.pdf', compact('paymentSlip'))
                ->setPaper('letter', 'portrait');
            
            $filename = 'OR-' . $paymentSlip->transaction_reference . '.pdf';
            
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            \Log::error('Citizen Receipt Download Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to download receipt.');
        }
    }
}

