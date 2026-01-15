<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display all transactions
     */
    public function index(Request $request)
    {
        $query = DB::connection('facilities_db')->table('payment_slips')
            ->join('bookings', 'payment_slips.booking_id', '=', 'bookings.id')
            ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
            ->select(
                'payment_slips.*',
                'bookings.event_name',
                'bookings.user_id',
                'facilities.name as facility_name'
            );

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('payment_slips.slip_number', 'LIKE', "%{$search}%")
                  ->orWhere('payment_slips.or_number', 'LIKE', "%{$search}%")
                  ->orWhere('bookings.event_name', 'LIKE', "%{$search}%")
                  ->orWhere('facilities.name', 'LIKE', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('payment_slips.status', $request->status);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_slips.payment_method', $request->payment_method);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('payment_slips.created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('payment_slips.created_at', '<=', $request->end_date);
        }

        $transactions = $query->orderBy('payment_slips.created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Get user names from auth_db
        $userIds = collect($transactions->items())->pluck('user_id')->unique()->filter();
        $users = DB::connection('auth_db')->table('users')
            ->whereIn('id', $userIds)
            ->select('id', 'full_name', 'email')
            ->get()
            ->keyBy('id');

        foreach ($transactions as $transaction) {
            $user = $users->get($transaction->user_id);
            $transaction->citizen_name = $user ? $user->full_name : 'Unknown';
            $transaction->citizen_email = $user ? $user->email : 'N/A';
            $transaction->citizen_id = $transaction->user_id;
        }

        // Calculate summary statistics
        $totalAmount = DB::connection('facilities_db')->table('payment_slips')
            ->sum('amount_due');

        $paidCount = DB::connection('facilities_db')->table('payment_slips')
            ->where('status', 'paid')
            ->count();

        $pendingCount = DB::connection('facilities_db')->table('payment_slips')
            ->where('status', 'pending')
            ->count();

        return view('admin.transactions.index', compact('transactions', 'totalAmount', 'paidCount', 'pendingCount'));
    }

    /**
     * Display transaction details
     */
    public function show($id)
    {
        $transaction = DB::connection('facilities_db')->table('payment_slips')
            ->where('payment_slips.id', $id)
            ->select('payment_slips.*')
            ->first();

        if (!$transaction) {
            return redirect()->route('admin.transactions.index')
                ->with('error', 'Transaction not found.');
        }

        // Get booking info
        $booking = DB::connection('facilities_db')->table('bookings')
            ->leftJoin('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
            ->where('bookings.id', $transaction->booking_id)
            ->select(
                'bookings.*',
                'facilities.name as facility_name'
            )
            ->first();

        // Get citizen info
        $citizen = null;
        if ($booking && $booking->user_id) {
            $citizen = DB::connection('auth_db')->table('users')
                ->where('id', $booking->user_id)
                ->select('id', 'full_name', 'email', 'mobile_number')
                ->first();
        }

        return view('admin.transactions.show', compact('transaction', 'booking', 'citizen'));
    }

    /**
     * Export transactions as CSV
     */
    public function exportCsv(Request $request)
    {
        $query = DB::connection('facilities_db')->table('payment_slips')
            ->join('bookings', 'payment_slips.booking_id', '=', 'bookings.id')
            ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
            ->select(
                'payment_slips.*',
                'bookings.event_name',
                'bookings.user_id',
                'facilities.name as facility_name'
            );

        // Apply filters
        if ($request->filled('status')) {
            $query->where('payment_slips.status', $request->status);
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_slips.payment_method', $request->payment_method);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('payment_slips.created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('payment_slips.created_at', '<=', $request->end_date);
        }

        $transactions = $query->orderBy('payment_slips.created_at', 'desc')->get();

        // Get user names
        $userIds = $transactions->pluck('user_id')->unique()->filter();
        $users = DB::connection('auth_db')->table('users')
            ->whereIn('id', $userIds)
            ->select('id', 'full_name')
            ->get()
            ->keyBy('id');

        // Generate CSV
        $filename = 'transactions_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($transactions, $users) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, ['Reference No.', 'Date', 'Citizen', 'Facility', 'Event', 'Amount', 'Payment Method', 'Status']);
            
            // Data rows
            foreach ($transactions as $transaction) {
                $user = $users->get($transaction->user_id);
                fputcsv($file, [
                    $transaction->slip_number ?? $transaction->or_number ?? 'N/A',
                    \Carbon\Carbon::parse($transaction->created_at)->format('Y-m-d H:i:s'),
                    $user ? $user->full_name : 'N/A',
                    $transaction->facility_name ?? 'N/A',
                    $transaction->event_name ?? 'N/A',
                    number_format($transaction->amount_due, 2),
                    ucfirst(str_replace('_', ' ', $transaction->payment_method ?? 'N/A')),
                    ucfirst($transaction->status)
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

