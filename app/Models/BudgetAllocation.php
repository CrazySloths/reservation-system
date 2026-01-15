<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetAllocation extends Model
{
    protected $connection = 'facilities_db';
    protected $table = 'budget_allocations';

    protected $fillable = [
        'fiscal_year',
        'category',
        'category_name',
        'allocated_amount',
        'spent_amount',
        'remaining_amount',
        'notes',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'allocated_amount' => 'decimal:2',
        'spent_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    /**
     * Get expenditures for this budget allocation
     */
    public function expenditures()
    {
        return $this->hasMany(BudgetExpenditure::class);
    }

    /**
     * Update spent and remaining amounts
     */
    public function updateAmounts()
    {
        $this->spent_amount = $this->expenditures()->sum('amount');
        $this->remaining_amount = $this->allocated_amount - $this->spent_amount;
        $this->save();
    }

    /**
     * Get utilization percentage
     */
    public function getUtilizationPercentageAttribute()
    {
        if ($this->allocated_amount == 0) {
            return 0;
        }
        return ($this->spent_amount / $this->allocated_amount) * 100;
    }

    /**
     * Check if budget is over-utilized
     */
    public function isOverBudget()
    {
        return $this->spent_amount > $this->allocated_amount;
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute()
    {
        $percentage = $this->utilization_percentage;
        
        if ($percentage >= 100) {
            return 'red'; // Over budget
        } elseif ($percentage >= 80) {
            return 'yellow'; // Warning
        } else {
            return 'green'; // Normal
        }
    }
}
