<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetExpenditure extends Model
{
    protected $connection = 'facilities_db';
    protected $table = 'budget_expenditures';

    protected $fillable = [
        'budget_allocation_id',
        'expenditure_type',
        'description',
        'amount',
        'expenditure_date',
        'invoice_number',
        'vendor_name',
        'facility_id',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expenditure_date' => 'date',
    ];

    /**
     * Get the budget allocation this expenditure belongs to
     */
    public function budgetAllocation()
    {
        return $this->belongsTo(BudgetAllocation::class);
    }

    /**
     * Get the facility if this expenditure is facility-specific
     */
    public function facility()
    {
        return $this->belongsTo(Facility::class, 'facility_id', 'facility_id');
    }

    /**
     * Boot method to update budget allocation amounts when expenditure is saved
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($expenditure) {
            $expenditure->budgetAllocation->updateAmounts();
        });

        static::deleted(function ($expenditure) {
            $expenditure->budgetAllocation->updateAmounts();
        });
    }
}
