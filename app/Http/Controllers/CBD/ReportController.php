<?php

namespace App\Http\Controllers\CBD;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display revenue report (Monthly/Quarterly)
     */
    public function revenue(Request $request)
    {
        // Get selected month and year (default to current)
        $selectedMonth = $request->input('month', now()->month);
        $selectedYear = $request->input('year', now()->year);
        $reportType = $request->input('type', 'monthly'); // monthly or quarterly
        
        // Build the date range based on report type
        $selectedQuarter = null;
        if ($reportType === 'quarterly') {
            $selectedQuarter = $request->input('quarter', ceil(now()->month / 3));
            $startMonth = (($selectedQuarter - 1) * 3) + 1;
            $startDate = Carbon::createFromDate($selectedYear, $startMonth, 1)->startOfMonth();
            $endDate = $startDate->copy()->addMonths(2)->endOfMonth();
            
            // Previous quarter for comparison
            $prevStartDate = $startDate->copy()->subMonths(3)->startOfMonth();
            $prevEndDate = $prevStartDate->copy()->addMonths(2)->endOfMonth();
        } else {
            // Monthly report
            $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->endOfMonth();
            
            // Previous month for comparison
            $prevStartDate = $startDate->copy()->subMonth()->startOfMonth();
            $prevEndDate = $prevStartDate->copy()->endOfMonth();
        }
        
        // Current period revenue
        $currentRevenue = DB::connection('facilities_db')
            ->table('payment_slips')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->where('status', 'paid')
            ->sum('amount_due');
        
        // Previous period revenue
        $previousRevenue = DB::connection('facilities_db')
            ->table('payment_slips')
            ->whereBetween('paid_at', [$prevStartDate, $prevEndDate])
            ->where('status', 'paid')
            ->sum('amount_due');
        
        // Calculate percentage change
        $percentageChange = 0;
        if ($previousRevenue > 0) {
            $percentageChange = (($currentRevenue - $previousRevenue) / $previousRevenue) * 100;
        }
        
        // Revenue by facility
        $revenueByFacility = DB::connection('facilities_db')
            ->table('bookings')
            ->selectRaw('facilities.name as facility_name, lgu_cities.city_name, COUNT(bookings.id) as total_bookings, SUM(bookings.total_amount) as total_revenue')
            ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
            ->leftJoin('lgu_cities', 'facilities.lgu_city_id', '=', 'lgu_cities.id')
            ->join('payment_slips', 'bookings.id', '=', 'payment_slips.booking_id')
            ->whereBetween('payment_slips.paid_at', [$startDate, $endDate])
            ->where('payment_slips.status', 'paid')
            ->groupBy('facilities.facility_id', 'facilities.name', 'lgu_cities.city_name')
            ->orderByDesc('total_revenue')
            ->get();
        
        // Revenue by payment method
        $revenueByPaymentMethod = DB::connection('facilities_db')
            ->table('payment_slips')
            ->selectRaw('payment_method, COUNT(*) as transaction_count, SUM(amount_due) as total_amount')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->where('status', 'paid')
            ->groupBy('payment_method')
            ->orderByDesc('total_amount')
            ->get();
        
        // Recent transactions
        $transactions = DB::connection('facilities_db')
            ->table('payment_slips')
            ->selectRaw('
                payment_slips.id,
                payment_slips.slip_number as payment_slip_number,
                payment_slips.amount_due,
                payment_slips.payment_method,
                payment_slips.paid_at,
                bookings.id as booking_id,
                CONCAT("BK", LPAD(bookings.id, 6, "0")) as booking_reference,
                facilities.name as facility_name,
                lgu_cities.city_name
            ')
            ->join('bookings', 'payment_slips.booking_id', '=', 'bookings.id')
            ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
            ->leftJoin('lgu_cities', 'facilities.lgu_city_id', '=', 'lgu_cities.id')
            ->whereBetween('payment_slips.paid_at', [$startDate, $endDate])
            ->where('payment_slips.status', 'paid')
            ->orderByDesc('payment_slips.paid_at')
            ->paginate(20);
        
        // Generate month options for selector
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = Carbon::createFromDate(null, $i, 1)->format('F');
        }
        
        // Generate year options (current year and 5 years back)
        $years = range(now()->year, now()->year - 5);
        
        // Quarter options
        $quarters = [1 => 'Q1 (Jan-Mar)', 2 => 'Q2 (Apr-Jun)', 3 => 'Q3 (Jul-Sep)', 4 => 'Q4 (Oct-Dec)'];
        
        return view('cbd.reports.revenue', compact(
            'currentRevenue',
            'previousRevenue',
            'percentageChange',
            'revenueByFacility',
            'revenueByPaymentMethod',
            'transactions',
            'months',
            'years',
            'quarters',
            'selectedMonth',
            'selectedYear',
            'selectedQuarter',
            'reportType',
            'startDate',
            'endDate'
        ));
    }
    
    /**
     * Export revenue report
     */
    public function exportRevenue(Request $request)
    {
        // Get same data as revenue report
        $selectedMonth = $request->input('month', now()->month);
        $selectedYear = $request->input('year', now()->year);
        $reportType = $request->input('type', 'monthly');
        $format = $request->input('format', 'csv'); // csv, excel, pdf
        
        // Build date range
        if ($reportType === 'quarterly') {
            $quarter = $request->input('quarter', ceil(now()->month / 3));
            $startMonth = (($quarter - 1) * 3) + 1;
            $startDate = Carbon::createFromDate($selectedYear, $startMonth, 1)->startOfMonth();
            $endDate = $startDate->copy()->addMonths(2)->endOfMonth();
        } else {
            $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->endOfMonth();
        }
        
        // Get revenue data
        $revenueByFacility = DB::connection('facilities_db')
            ->table('bookings')
            ->selectRaw('facilities.name as facility_name, lgu_cities.city_name, COUNT(bookings.id) as total_bookings, SUM(bookings.total_amount) as total_revenue')
            ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
            ->leftJoin('lgu_cities', 'facilities.lgu_city_id', '=', 'lgu_cities.id')
            ->join('payment_slips', 'bookings.id', '=', 'payment_slips.booking_id')
            ->whereBetween('payment_slips.paid_at', [$startDate, $endDate])
            ->where('payment_slips.status', 'paid')
            ->groupBy('facilities.facility_id', 'facilities.name', 'lgu_cities.city_name')
            ->orderByDesc('total_revenue')
            ->get();
        
        // Export as CSV
        if ($format === 'csv') {
            $filename = "revenue_report_{$reportType}_{$selectedYear}_{$selectedMonth}.csv";
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];
            
            $callback = function() use ($revenueByFacility, $reportType, $startDate, $endDate) {
                $file = fopen('php://output', 'w');
                
                // Header
                fputcsv($file, ['Revenue Report - ' . ucfirst($reportType)]);
                fputcsv($file, ['Period: ' . $startDate->format('M d, Y') . ' to ' . $endDate->format('M d, Y')]);
                fputcsv($file, []); // Empty row
                
                // Column headers
                fputcsv($file, ['Facility', 'City', 'Total Bookings', 'Total Revenue']);
                
                // Data rows
                foreach ($revenueByFacility as $facility) {
                    fputcsv($file, [
                        $facility->facility_name,
                        $facility->city_name ?? 'N/A',
                        $facility->total_bookings,
                        number_format($facility->total_revenue, 2)
                    ]);
                }
                
                // Total
                $totalRevenue = $revenueByFacility->sum('total_revenue');
                $totalBookings = $revenueByFacility->sum('total_bookings');
                fputcsv($file, []); // Empty row
                fputcsv($file, ['TOTAL', '', $totalBookings, number_format($totalRevenue, 2)]);
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
        }
        
        return response()->json(['message' => 'Only CSV export is currently supported. Excel and PDF coming soon.']);
    }
    
    /**
     * Display facility utilization report
     */
    public function facilityUtilization(Request $request)
    {
        return view('cbd.reports.facility-utilization');
    }
    
    /**
     * Display budget analysis report
     */
    public function budgetAnalysis(Request $request)
    {
        $fiscalYear = $request->input('fiscal_year', now()->year);
        
        // Get all budget allocations for the fiscal year
        $budgetAllocations = DB::connection('facilities_db')
            ->table('budget_allocations')
            ->where('fiscal_year', $fiscalYear)
            ->get();
        
        // Calculate totals
        $totalAllocated = $budgetAllocations->sum('allocated_amount');
        $totalSpent = $budgetAllocations->sum('spent_amount');
        $totalRemaining = $budgetAllocations->sum('remaining_amount');
        $utilizationPercentage = $totalAllocated > 0 ? ($totalSpent / $totalAllocated) * 100 : 0;
        
        // Get revenue for the fiscal year
        $fiscalYearStart = Carbon::createFromDate($fiscalYear, 1, 1)->startOfYear();
        $fiscalYearEnd = Carbon::createFromDate($fiscalYear, 12, 31)->endOfYear();
        
        $totalRevenue = DB::connection('facilities_db')
            ->table('payment_slips')
            ->whereBetween('paid_at', [$fiscalYearStart, $fiscalYearEnd])
            ->where('status', 'paid')
            ->sum('amount_due');
        
        // Get recent expenditures
        $recentExpenditures = DB::connection('facilities_db')
            ->table('budget_expenditures')
            ->join('budget_allocations', 'budget_expenditures.budget_allocation_id', '=', 'budget_allocations.id')
            ->select(
                'budget_expenditures.*',
                'budget_allocations.category',
                'budget_allocations.category_name'
            )
            ->where('budget_allocations.fiscal_year', $fiscalYear)
            ->orderByDesc('budget_expenditures.expenditure_date')
            ->limit(10)
            ->get();
        
        // Fiscal year options (current year and 5 years back)
        $fiscalYears = range(now()->year, now()->year - 5);
        
        // Category labels
        $categoryLabels = [
            'maintenance' => 'Facility Maintenance',
            'equipment' => 'Equipment Purchase',
            'operations' => 'Operational Costs',
            'staff' => 'Staff Salaries',
            'utilities' => 'Utility Bills',
            'other' => 'Other Expenses'
        ];
        
        return view('cbd.reports.budget-analysis', compact(
            'budgetAllocations',
            'totalAllocated',
            'totalSpent',
            'totalRemaining',
            'utilizationPercentage',
            'totalRevenue',
            'recentExpenditures',
            'fiscalYears',
            'fiscalYear',
            'categoryLabels'
        ));
    }
}

