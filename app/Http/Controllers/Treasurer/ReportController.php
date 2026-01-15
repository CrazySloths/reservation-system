<?php

namespace App\Http\Controllers\Treasurer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Daily Collections Report
     */
    public function dailyCollections(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        $selectedDate = Carbon::parse($date);
        
        try {
            // Get all payments for the selected date
            $payments = DB::connection('facilities_db')
                ->table('payment_slips')
                ->join('bookings', 'payment_slips.booking_id', '=', 'bookings.id')
                ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
                ->select(
                    'payment_slips.*',
                    'bookings.applicant_name',
                    'bookings.user_id',
                    'facilities.name as facility_name'
                )
                ->where('payment_slips.status', 'paid')
                ->whereDate('payment_slips.paid_at', $selectedDate)
                ->orderBy('payment_slips.paid_at', 'asc')
                ->get();
            
            // Fetch user data for payments without applicant_name
            $payments->transform(function($payment) {
                if (empty($payment->applicant_name) && !empty($payment->user_id)) {
                    $user = DB::connection('auth_db')
                        ->table('users')
                        ->where('id', $payment->user_id)
                        ->first();
                    
                    if ($user) {
                        $payment->applicant_name = $user->full_name ?? 'N/A';
                    }
                }
                return $payment;
            });
            
            // Calculate statistics
            $stats = [
                'total_collections' => $payments->sum('amount_due'),
                'total_transactions' => $payments->count(),
                'cash' => $payments->where('payment_method', 'cash')->sum('amount_due'),
                'gcash' => $payments->where('payment_method', 'gcash')->sum('amount_due'),
                'paymaya' => $payments->where('payment_method', 'paymaya')->sum('amount_due'),
                'bank_transfer' => $payments->where('payment_method', 'bank_transfer')->sum('amount_due'),
                'credit_card' => $payments->where('payment_method', 'credit_card')->sum('amount_due'),
                'cash_count' => $payments->where('payment_method', 'cash')->count(),
                'gcash_count' => $payments->where('payment_method', 'gcash')->count(),
                'paymaya_count' => $payments->where('payment_method', 'paymaya')->count(),
                'bank_transfer_count' => $payments->where('payment_method', 'bank_transfer')->count(),
                'credit_card_count' => $payments->where('payment_method', 'credit_card')->count(),
            ];
            
        } catch (\Exception $e) {
            \Log::error('Daily Collections Report Error: ' . $e->getMessage());
            $payments = collect();
            $stats = [
                'total_collections' => 0,
                'total_transactions' => 0,
                'cash' => 0, 'gcash' => 0, 'paymaya' => 0,
                'bank_transfer' => 0, 'credit_card' => 0,
                'cash_count' => 0, 'gcash_count' => 0, 'paymaya_count' => 0,
                'bank_transfer_count' => 0, 'credit_card_count' => 0,
            ];
        }
        
        return view('treasurer.reports.daily-collections', compact('payments', 'stats', 'selectedDate'));
    }
    
    /**
     * Export Daily Collections Report to PDF
     */
    public function exportDailyCollections(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        $selectedDate = Carbon::parse($date);
        
        try {
            // Get all payments for the selected date
            $payments = DB::connection('facilities_db')
                ->table('payment_slips')
                ->join('bookings', 'payment_slips.booking_id', '=', 'bookings.id')
                ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
                ->select(
                    'payment_slips.*',
                    'bookings.applicant_name',
                    'bookings.user_id',
                    'facilities.name as facility_name'
                )
                ->where('payment_slips.status', 'paid')
                ->whereDate('payment_slips.paid_at', $selectedDate)
                ->orderBy('payment_slips.paid_at', 'asc')
                ->get();
            
            // Fetch user data
            $payments->transform(function($payment) {
                if (empty($payment->applicant_name) && !empty($payment->user_id)) {
                    $user = DB::connection('auth_db')
                        ->table('users')
                        ->where('id', $payment->user_id)
                        ->first();
                    
                    if ($user) {
                        $payment->applicant_name = $user->full_name ?? 'N/A';
                    }
                }
                return $payment;
            });
            
            // Calculate statistics
            $stats = [
                'total_collections' => $payments->sum('amount_due'),
                'total_transactions' => $payments->count(),
                'cash' => $payments->where('payment_method', 'cash')->sum('amount_due'),
                'gcash' => $payments->where('payment_method', 'gcash')->sum('amount_due'),
                'paymaya' => $payments->where('payment_method', 'paymaya')->sum('amount_due'),
                'bank_transfer' => $payments->where('payment_method', 'bank_transfer')->sum('amount_due'),
                'credit_card' => $payments->where('payment_method', 'credit_card')->sum('amount_due'),
                'cash_count' => $payments->where('payment_method', 'cash')->count(),
                'gcash_count' => $payments->where('payment_method', 'gcash')->count(),
                'paymaya_count' => $payments->where('payment_method', 'paymaya')->count(),
                'bank_transfer_count' => $payments->where('payment_method', 'bank_transfer')->count(),
                'credit_card_count' => $payments->where('payment_method', 'credit_card')->count(),
            ];
            
            $pdf = Pdf::loadView('treasurer.reports.daily-collections-pdf', compact('payments', 'stats', 'selectedDate'))
                ->setPaper('letter', 'portrait');
            
            $filename = 'Daily_Collections_' . $selectedDate->format('Y-m-d') . '.pdf';
            
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            \Log::error('Export Daily Collections Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate report PDF.');
        }
    }
    
    /**
     * Monthly Summary Report
     */
    public function monthlySummary(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $selectedMonth = Carbon::parse($month . '-01');
        
        try {
            // Get all payments for the selected month
            $payments = DB::connection('facilities_db')
                ->table('payment_slips')
                ->join('bookings', 'payment_slips.booking_id', '=', 'bookings.id')
                ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
                ->select(
                    'payment_slips.*',
                    'facilities.name as facility_name',
                    DB::raw('DATE(payment_slips.paid_at) as payment_date')
                )
                ->where('payment_slips.status', 'paid')
                ->whereYear('payment_slips.paid_at', $selectedMonth->year)
                ->whereMonth('payment_slips.paid_at', $selectedMonth->month)
                ->get();
            
            // Daily breakdown
            $dailyCollections = $payments->groupBy('payment_date')->map(function($dayPayments) {
                return [
                    'total' => $dayPayments->sum('amount_due'),
                    'count' => $dayPayments->count(),
                ];
            });
            
            // Payment method breakdown
            $methodBreakdown = [
                'cash' => [
                    'amount' => $payments->where('payment_method', 'cash')->sum('amount_due'),
                    'count' => $payments->where('payment_method', 'cash')->count(),
                ],
                'gcash' => [
                    'amount' => $payments->where('payment_method', 'gcash')->sum('amount_due'),
                    'count' => $payments->where('payment_method', 'gcash')->count(),
                ],
                'paymaya' => [
                    'amount' => $payments->where('payment_method', 'paymaya')->sum('amount_due'),
                    'count' => $payments->where('payment_method', 'paymaya')->count(),
                ],
                'bank_transfer' => [
                    'amount' => $payments->where('payment_method', 'bank_transfer')->sum('amount_due'),
                    'count' => $payments->where('payment_method', 'bank_transfer')->count(),
                ],
                'credit_card' => [
                    'amount' => $payments->where('payment_method', 'credit_card')->sum('amount_due'),
                    'count' => $payments->where('payment_method', 'credit_card')->count(),
                ],
            ];
            
            // Top facilities
            $topFacilities = $payments->groupBy('facility_name')->map(function($facilityPayments) {
                return [
                    'revenue' => $facilityPayments->sum('amount_due'),
                    'bookings' => $facilityPayments->count(),
                ];
            })->sortByDesc('revenue')->take(5);
            
            // Monthly statistics
            $stats = [
                'total_revenue' => $payments->sum('amount_due'),
                'total_transactions' => $payments->count(),
                'average_transaction' => $payments->count() > 0 ? $payments->sum('amount_due') / $payments->count() : 0,
                'days_with_collections' => $dailyCollections->count(),
            ];
            
        } catch (\Exception $e) {
            \Log::error('Monthly Summary Report Error: ' . $e->getMessage());
            $dailyCollections = collect();
            $methodBreakdown = [];
            $topFacilities = collect();
            $stats = [
                'total_revenue' => 0,
                'total_transactions' => 0,
                'average_transaction' => 0,
                'days_with_collections' => 0,
            ];
        }
        
        return view('treasurer.reports.monthly-summary', compact('dailyCollections', 'methodBreakdown', 'topFacilities', 'stats', 'selectedMonth'));
    }
    
    /**
     * Export Monthly Summary Report to PDF
     */
    public function exportMonthlySummary(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $selectedMonth = Carbon::parse($month . '-01');
        
        try {
            // Same query logic as monthlySummary()
            $payments = DB::connection('facilities_db')
                ->table('payment_slips')
                ->join('bookings', 'payment_slips.booking_id', '=', 'bookings.id')
                ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
                ->select(
                    'payment_slips.*',
                    'facilities.name as facility_name',
                    DB::raw('DATE(payment_slips.paid_at) as payment_date')
                )
                ->where('payment_slips.status', 'paid')
                ->whereYear('payment_slips.paid_at', $selectedMonth->year)
                ->whereMonth('payment_slips.paid_at', $selectedMonth->month)
                ->get();
            
            $dailyCollections = $payments->groupBy('payment_date')->map(function($dayPayments) {
                return [
                    'total' => $dayPayments->sum('amount_due'),
                    'count' => $dayPayments->count(),
                ];
            });
            
            $methodBreakdown = [
                'cash' => [
                    'amount' => $payments->where('payment_method', 'cash')->sum('amount_due'),
                    'count' => $payments->where('payment_method', 'cash')->count(),
                ],
                'gcash' => [
                    'amount' => $payments->where('payment_method', 'gcash')->sum('amount_due'),
                    'count' => $payments->where('payment_method', 'gcash')->count(),
                ],
                'paymaya' => [
                    'amount' => $payments->where('payment_method', 'paymaya')->sum('amount_due'),
                    'count' => $payments->where('payment_method', 'paymaya')->count(),
                ],
                'bank_transfer' => [
                    'amount' => $payments->where('payment_method', 'bank_transfer')->sum('amount_due'),
                    'count' => $payments->where('payment_method', 'bank_transfer')->count(),
                ],
                'credit_card' => [
                    'amount' => $payments->where('payment_method', 'credit_card')->sum('amount_due'),
                    'count' => $payments->where('payment_method', 'credit_card')->count(),
                ],
            ];
            
            $topFacilities = $payments->groupBy('facility_name')->map(function($facilityPayments) {
                return [
                    'revenue' => $facilityPayments->sum('amount_due'),
                    'bookings' => $facilityPayments->count(),
                ];
            })->sortByDesc('revenue')->take(5);
            
            $stats = [
                'total_revenue' => $payments->sum('amount_due'),
                'total_transactions' => $payments->count(),
                'average_transaction' => $payments->count() > 0 ? $payments->sum('amount_due') / $payments->count() : 0,
                'days_with_collections' => $dailyCollections->count(),
            ];
            
            $pdf = Pdf::loadView('treasurer.reports.monthly-summary-pdf', compact('dailyCollections', 'methodBreakdown', 'topFacilities', 'stats', 'selectedMonth'))
                ->setPaper('letter', 'landscape');
            
            $filename = 'Monthly_Summary_' . $selectedMonth->format('Y-m') . '.pdf';
            
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            \Log::error('Export Monthly Summary Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate report PDF.');
        }
    }
}

