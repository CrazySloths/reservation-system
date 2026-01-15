<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BudgetAllocation;
use App\Models\BudgetExpenditure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BudgetAllocationController extends Controller
{
    /**
     * Display budget management page
     */
    public function index(Request $request)
    {
        $fiscalYear = $request->input('fiscal_year', now()->year);
        
        $budgetAllocations = BudgetAllocation::where('fiscal_year', $fiscalYear)
            ->orderBy('category')
            ->get();
        
        $fiscalYears = range(now()->year + 1, now()->year - 5);
        
        return view('admin.budget.index', compact('budgetAllocations', 'fiscalYears', 'fiscalYear'));
    }
    
    /**
     * Store budget allocation
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fiscal_year' => 'required|integer|min:2020|max:2050',
            'category' => 'required|in:maintenance,equipment,operations,staff,utilities,other',
            'category_name' => 'nullable|string|max:255',
            'allocated_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        
        // Check if this category already exists for the fiscal year
        $existing = BudgetAllocation::where('fiscal_year', $validated['fiscal_year'])
            ->where('category', $validated['category'])
            ->first();
        
        if ($existing) {
            return redirect()->back()->with('error', 'Budget allocation for this category already exists for FY ' . $validated['fiscal_year']);
        }
        
        $budgetAllocation = BudgetAllocation::create([
            'fiscal_year' => $validated['fiscal_year'],
            'category' => $validated['category'],
            'category_name' => $validated['category_name'],
            'allocated_amount' => $validated['allocated_amount'],
            'spent_amount' => 0,
            'remaining_amount' => $validated['allocated_amount'],
            'notes' => $validated['notes'],
            'approved_by' => Auth::user()->name,
            'approved_at' => now(),
        ]);
        
        return redirect()->route('admin.budget.index', ['fiscal_year' => $validated['fiscal_year']])
            ->with('success', 'Budget allocation created successfully');
    }
    
    /**
     * Update budget allocation
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'allocated_amount' => 'required|numeric|min:0',
            'category_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);
        
        $budgetAllocation = BudgetAllocation::findOrFail($id);
        
        $budgetAllocation->update([
            'allocated_amount' => $validated['allocated_amount'],
            'remaining_amount' => $validated['allocated_amount'] - $budgetAllocation->spent_amount,
            'category_name' => $validated['category_name'],
            'notes' => $validated['notes'],
        ]);
        
        return redirect()->back()->with('success', 'Budget allocation updated successfully');
    }
    
    /**
     * Delete budget allocation
     */
    public function destroy($id)
    {
        $budgetAllocation = BudgetAllocation::findOrFail($id);
        
        // Check if there are any expenditures
        if ($budgetAllocation->expenditures()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete budget allocation with existing expenditures');
        }
        
        $budgetAllocation->delete();
        
        return redirect()->back()->with('success', 'Budget allocation deleted successfully');
    }
    
    /**
     * Record expenditure
     */
    public function storeExpenditure(Request $request)
    {
        $validated = $request->validate([
            'budget_allocation_id' => 'required|exists:facilities_db.budget_allocations,id',
            'expenditure_type' => 'required|in:maintenance,equipment_purchase,operational_cost,staff_salary,utility_bill,other',
            'description' => 'required|string|max:500',
            'amount' => 'required|numeric|min:0',
            'expenditure_date' => 'required|date',
            'invoice_number' => 'nullable|string|max:100',
            'vendor_name' => 'nullable|string|max:255',
            'facility_id' => 'nullable|exists:facilities_db.facilities,facility_id',
            'notes' => 'nullable|string',
        ]);
        
        $budgetAllocation = BudgetAllocation::findOrFail($validated['budget_allocation_id']);
        
        // Check if sufficient budget
        if ($validated['amount'] > $budgetAllocation->remaining_amount) {
            return redirect()->back()->with('error', 'Insufficient budget remaining. Only ₱' . number_format($budgetAllocation->remaining_amount, 2) . ' available.');
        }
        
        $expenditure = BudgetExpenditure::create([
            'budget_allocation_id' => $validated['budget_allocation_id'],
            'expenditure_type' => $validated['expenditure_type'],
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'expenditure_date' => $validated['expenditure_date'],
            'invoice_number' => $validated['invoice_number'],
            'vendor_name' => $validated['vendor_name'],
            'facility_id' => $validated['facility_id'],
            'notes' => $validated['notes'],
            'recorded_by' => Auth::user()->name,
        ]);
        
        return redirect()->back()->with('success', 'Expenditure recorded successfully');
    }
}
