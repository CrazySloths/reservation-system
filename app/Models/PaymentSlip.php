<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PaymentSlip extends Model
{
    /**
     * The database connection that should be used by the model.
     *
     * @var string
     */
    protected $connection = 'facilities_db';

    protected $fillable = [
        'slip_number',
        'booking_id',
        'amount_due',
        'payment_deadline',
        'status',
        'payment_method',
        'paid_at',
        'verified_by',
        'transaction_reference',
        'notes'
    ];

    protected $casts = [
        'amount_due' => 'decimal:2',
        'payment_deadline' => 'datetime',
        'paid_at' => 'datetime',
    ];

    /**
     * Generate a unique slip number (Format: PS-2025-001234)
     */
    public static function generateSlipNumber()
    {
        $year = Carbon::now()->year;
        $lastSlip = self::where('slip_number', 'like', "PS-{$year}-%")
                       ->orderBy('slip_number', 'desc')
                       ->first();
        
        if ($lastSlip) {
            $lastNumber = intval(substr($lastSlip->slip_number, -6));
            $newNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '000001';
        }
        
        return "PS-{$year}-{$newNumber}";
    }

    /**
     * Relationships
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Treasurer who verified the payment (from auth_db)
     */
    public function verifiedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'verified_by');
    }

    /**
     * Check if payment slip is expired
     */
    public function getIsExpiredAttribute()
    {
        return $this->status === 'unpaid' && $this->payment_deadline->isPast();
    }

    /**
     * Get hours until deadline
     */
    public function getHoursUntilDeadlineAttribute()
    {
        if ($this->status === 'paid') {
            return 0;
        }
        
        return max(0, Carbon::now()->diffInHours($this->payment_deadline, false));
    }

    /**
     * Mark payment slip as paid
     */
    public function markAsPaid($paymentMethod, $verifiedBy, $transactionReference = null)
    {
        $this->update([
            'status' => 'paid',
            'payment_method' => $paymentMethod,
            'paid_at' => now(),
            'verified_by' => $verifiedBy,
            'transaction_reference' => $transactionReference,
        ]);
    }

    /**
     * Mark payment slip as expired
     */
    public function markAsExpired()
    {
        $this->update(['status' => 'expired']);
    }
}

