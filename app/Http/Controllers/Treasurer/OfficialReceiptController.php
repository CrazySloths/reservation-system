<?php

namespace App\Http\Controllers\Treasurer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PaymentSlip;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class OfficialReceiptController extends Controller
{
    /**
     * Display list of all official receipts.
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
                    'bookings.user_id',
                    'facilities.name as facility_name'
                )
                ->where('payment_slips.status', 'paid')
                ->whereNotNull('payment_slips.or_number'); // Only payments with OR numbers
            
            // Search functionality
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('payment_slips.or_number', 'like', "%{$search}%")
                      ->orWhere('payment_slips.slip_number', 'like', "%{$search}%")
                      ->orWhere('bookings.applicant_name', 'like', "%{$search}%");
                });
            }
            
            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('payment_slips.paid_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('payment_slips.paid_at', '<=', $request->date_to);
            }
            
            $receipts = $query->orderBy('payment_slips.paid_at', 'desc')->paginate(15);
            
            // Fetch user data for receipts without applicant_name
            $userIds = $receipts->filter(function($receipt) {
                return empty($receipt->applicant_name) && !empty($receipt->user_id);
            })->pluck('user_id')->unique();
            
            if ($userIds->isNotEmpty()) {
                $users = DB::connection('auth_db')
                    ->table('users')
                    ->whereIn('id', $userIds)
                    ->get()
                    ->keyBy('id');
                
                $receipts->transform(function($receipt) use ($users) {
                    if (empty($receipt->applicant_name) && !empty($receipt->user_id) && isset($users[$receipt->user_id])) {
                        $user = $users[$receipt->user_id];
                        $receipt->applicant_name = $user->full_name ?? 'N/A';
                    }
                    return $receipt;
                });
            }
            
            // Statistics
            $stats = [
                'total_receipts' => DB::connection('facilities_db')
                    ->table('payment_slips')
                    ->where('status', 'paid')
                    ->whereNotNull('or_number')
                    ->count(),
                'today_receipts' => DB::connection('facilities_db')
                    ->table('payment_slips')
                    ->where('status', 'paid')
                    ->whereNotNull('or_number')
                    ->whereDate('paid_at', today())
                    ->count(),
            ];
            
        } catch (\Exception $e) {
            \Log::error('Official Receipts Query Error: ' . $e->getMessage());
            
            $receipts = new \Illuminate\Pagination\LengthAwarePaginator(
                [],
                0,
                15,
                1,
                ['path' => request()->url(), 'query' => request()->query()]
            );
            $stats = [
                'total_receipts' => 0,
                'today_receipts' => 0,
            ];
        }
        
        return view('treasurer.official-receipts.index', compact('receipts', 'stats'));
    }
    
    /**
     * Show specific official receipt details.
     */
    public function show($id)
    {
        \Log::info("OfficialReceiptController show() called with ID: " . $id);
        
        try {
            $paymentSlip = DB::connection('facilities_db')
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
                    'bookings.purpose',
                    'bookings.expected_attendees',
                    'facilities.name as facility_name',
                    'facilities.address as facility_address'
                )
                ->where('payment_slips.id', $id)
                ->first();
            
            if (!$paymentSlip) {
                return redirect()->route('treasurer.official-receipts')
                    ->with('error', 'Official receipt not found.');
            }
            
            // Fetch user data if applicant_name is empty
            if (empty($paymentSlip->applicant_name) && !empty($paymentSlip->user_id)) {
                $user = DB::connection('auth_db')
                    ->table('users')
                    ->where('id', $paymentSlip->user_id)
                    ->first();
                
                if ($user) {
                    $paymentSlip->applicant_name = $user->full_name ?? 'N/A';
                    $paymentSlip->applicant_email = $user->email ?? 'N/A';
                    $paymentSlip->applicant_phone = $user->mobile_number ?? 'N/A';
                }
            }
            
            // Fetch verified_by user data separately
            if (!empty($paymentSlip->verified_by)) {
                $verifiedByUser = DB::connection('auth_db')
                    ->table('users')
                    ->where('id', $paymentSlip->verified_by)
                    ->first();
                
                $paymentSlip->verified_by_name = $verifiedByUser ? ($verifiedByUser->full_name ?? 'N/A') : 'N/A';
            } else {
                $paymentSlip->verified_by_name = 'N/A';
            }
            
            return view('treasurer.official-receipts.show', compact('paymentSlip'));
            
        } catch (\Exception $e) {
            \Log::error('Official Receipt Show Error: ' . $e->getMessage());
            return redirect()->route('treasurer.official-receipts')
                ->with('error', 'Failed to load official receipt.');
        }
    }
    
    /**
     * Generate and download PDF official receipt.
     */
    public function print($id)
    {
        \Log::info("OfficialReceiptController print() called with ID: " . $id);
        
        try {
            $paymentSlip = DB::connection('facilities_db')
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
                    'bookings.purpose',
                    'bookings.expected_attendees',
                    'facilities.name as facility_name',
                    'facilities.address as facility_address'
                )
                ->where('payment_slips.id', $id)
                ->first();
            
            if (!$paymentSlip || $paymentSlip->status !== 'paid') {
                return redirect()->back()->with('error', 'Official receipt not available.');
            }
            
            // Fetch user data if applicant_name is empty
            if (empty($paymentSlip->applicant_name) && !empty($paymentSlip->user_id)) {
                $user = DB::connection('auth_db')
                    ->table('users')
                    ->where('id', $paymentSlip->user_id)
                    ->first();
                
                if ($user) {
                    $paymentSlip->applicant_name = $user->full_name ?? 'N/A';
                    $paymentSlip->applicant_email = $user->email ?? 'N/A';
                    $paymentSlip->applicant_phone = $user->mobile_number ?? 'N/A';
                }
            }
            
            // Fetch verified_by user data separately
            if (!empty($paymentSlip->verified_by)) {
                $verifiedByUser = DB::connection('auth_db')
                    ->table('users')
                    ->where('id', $paymentSlip->verified_by)
                    ->first();
                
                $paymentSlip->verified_by_name = $verifiedByUser ? ($verifiedByUser->full_name ?? 'N/A') : 'N/A';
            } else {
                $paymentSlip->verified_by_name = 'N/A';
            }
            
            // Generate PDF
            $pdf = Pdf::loadView('treasurer.official-receipts.pdf', compact('paymentSlip'))
                ->setPaper('letter', 'portrait');
            
            $filename = 'OR-' . $paymentSlip->transaction_reference . '.pdf';
            
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            \Log::error('Official Receipt PDF Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate PDF receipt.');
        }
    }
}

