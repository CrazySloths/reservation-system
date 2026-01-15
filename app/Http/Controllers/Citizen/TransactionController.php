<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display citizen's transaction history
     */
    public function index(Request $request)
    {
        $userId = session('user_id');

        // Get transactions
        $query = DB::connection('facilities_db')->table('payment_slips')
            ->join('bookings', 'payment_slips.booking_id', '=', 'bookings.id')
            ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
            ->where('bookings.user_id', $userId)
            ->select(
                'payment_slips.*',
                'bookings.event_name',
                'bookings.start_time',
                'bookings.end_time',
                'facilities.name as facility_name'
            );

        // Filter by status
        if ($request->filled('status')) {
            $query->where('payment_slips.status', $request->status);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('payment_slips.created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('payment_slips.created_at', '<=', $request->end_date);
        }

        $transactions = $query->orderBy('payment_slips.created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Calculate summary statistics
        $totalPaid = DB::connection('facilities_db')->table('payment_slips')
            ->join('bookings', 'payment_slips.booking_id', '=', 'bookings.id')
            ->where('bookings.user_id', $userId)
            ->where('payment_slips.status', 'paid')
            ->sum('payment_slips.amount_due');

        $totalTransactions = DB::connection('facilities_db')->table('payment_slips')
            ->join('bookings', 'payment_slips.booking_id', '=', 'bookings.id')
            ->where('bookings.user_id', $userId)
            ->count();

        $pendingAmount = DB::connection('facilities_db')->table('payment_slips')
            ->join('bookings', 'payment_slips.booking_id', '=', 'bookings.id')
            ->where('bookings.user_id', $userId)
            ->where('payment_slips.status', 'pending')
            ->sum('payment_slips.amount_due');

        return view('citizen.transactions.index', compact('transactions', 'totalPaid', 'totalTransactions', 'pendingAmount'));
    }

    /**
     * Display transaction details
     */
    public function show($id)
    {
        $userId = session('user_id');

        $transaction = DB::connection('facilities_db')->table('payment_slips')
            ->where('payment_slips.id', $id)
            ->select('payment_slips.*')
            ->first();

        if (!$transaction) {
            return redirect()->route('citizen.transactions.index')
                ->with('error', 'Transaction not found.');
        }

        // Get booking info
        $booking = DB::connection('facilities_db')->table('bookings')
            ->leftJoin('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
            ->where('bookings.id', $transaction->booking_id)
            ->where('bookings.user_id', $userId) // Verify ownership
            ->select(
                'bookings.*',
                'facilities.name as facility_name',
                'facilities.address as facility_address'
            )
            ->first();

        if (!$booking) {
            return redirect()->route('citizen.transactions.index')
                ->with('error', 'Transaction not found or access denied.');
        }

        return view('citizen.transactions.show', compact('transaction', 'booking'));
    }
}

