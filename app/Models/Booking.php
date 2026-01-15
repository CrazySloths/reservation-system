<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    /**
     * The database connection that should be used by the model.
     *
     * @var string
     */
    protected $connection = 'facilities_db';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bookings';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'facility_id',
        'user_id',
        'user_name',
        'applicant_name',
        'applicant_email', 
        'applicant_phone',
        'applicant_address',
        'event_name',
        'event_description',
        'event_date',
        'start_time',
        'end_time',
        'expected_attendees',
        'purpose',
        'special_requests',
        // Pricing fields (actual column names)
        'base_rate',
        'extension_rate',
        'subtotal',
        'equipment_total',
        'city_of_residence',
        'is_resident',
        'resident_discount_rate',
        'resident_discount_amount',
        'special_discount_type',
        'special_discount_id_path',
        'special_discount_rate',
        'special_discount_amount',
        'total_discount',
        'total_amount',
        // Document file paths (actual column names)
        'valid_id_type',
        'valid_id_front_path',
        'valid_id_back_path',
        'valid_id_selfie_path',
        'supporting_doc_path',
        // Status and approval fields
        'status',
        'rejected_reason',
        'staff_verified_by',
        'staff_verified_at',
        'staff_notes',
        'admin_approved_by',
        'admin_approved_at',
        'admin_approval_notes',
        'reserved_until'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'event_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'expected_attendees' => 'integer',
        'base_rate' => 'decimal:2',
        'extension_rate' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'equipment_total' => 'decimal:2',
        'is_resident' => 'boolean',
        'resident_discount_rate' => 'decimal:2',
        'resident_discount_amount' => 'decimal:2',
        'special_discount_rate' => 'decimal:2',
        'special_discount_amount' => 'decimal:2',
        'total_discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'staff_verified_at' => 'datetime',
        'admin_approved_at' => 'datetime',
        'reserved_until' => 'datetime',
    ];

    /**
     * Get the facility that owns the booking.
     * Note: Uses FacilityDb which queries facilities from facilities_db
     */
    public function facility(): BelongsTo
    {
        return $this->belongsTo(FacilityDb::class, 'facility_id', 'facility_id');
    }

    /**
     * Get the user that owns the booking.
     * Note: Users are in the default database (auth_db), not facilities_db
     * This relationship cannot be eager loaded due to cross-database constraints
     * Load it manually when needed: User::find($booking->user_id)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the payment slip for this booking.
     */
    public function paymentSlip()
    {
        return $this->hasOne(PaymentSlip::class);
    }

    /**
     * Get the equipment items for this booking.
     */
    public function equipmentItems()
    {
        return $this->belongsToMany(EquipmentItem::class, 'booking_equipment')
                    ->withPivot('quantity', 'price_per_unit', 'subtotal')
                    ->withTimestamps();
    }

    /**
     * Get the admin who approved the booking.
     */
    public function adminApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_approved_by');
    }

    /**
     * Get the user who rejected the booking.
     */
    public function rejector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Scope to get approved bookings only
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get pending bookings only
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Check if booking is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if booking is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if booking is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if booking is in reserved status (24-hour hold)
     */
    public function isReserved(): bool
    {
        return $this->status === 'reserved';
    }

    /**
     * Check if booking is tentative (after 24-hour hold expires)
     */
    public function isTentative(): bool
    {
        return $this->status === 'tentative';
    }

    /**
     * Check if booking has expired
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired';
    }

    /**
     * Check if booking is payment pending
     */
    public function isPaymentPending(): bool
    {
        return $this->status === 'payment_pending';
    }

    /**
     * Check if booking is confirmed (fully paid)
     */
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    /**
     * Check for schedule conflicts with other bookings
     * Single source of truth for conflict detection - NO REDUNDANCY
     * 
     * @return array Array with 'hasConflict' boolean and 'conflicts' collection
     */
    public function checkScheduleConflicts(): array
    {
        // Find conflicting bookings:
        // - Same facility
        // - Same date
        // - Already approved or paid (locked slots)
        // - Overlapping time range
        $conflicts = self::where('facility_id', $this->facility_id)
            ->where('id', '!=', $this->id) // Exclude current booking
            ->where('event_date', $this->event_date) // Same date
            ->whereIn('status', ['staff_verified', 'paid', 'confirmed']) // Only check locked bookings
            ->where(function($query) {
                // Time overlap detection:
                // Overlap exists if: (start1 < end2) AND (end1 > start2)
                $query->where(function($q) {
                    // Other booking starts during this booking
                    $q->where('start_time', '>=', $this->start_time)
                      ->where('start_time', '<', $this->end_time);
                })
                ->orWhere(function($q) {
                    // Other booking ends during this booking
                    $q->where('end_time', '>', $this->start_time)
                      ->where('end_time', '<=', $this->end_time);
                })
                ->orWhere(function($q) {
                    // Other booking completely contains this booking
                    $q->where('start_time', '<=', $this->start_time)
                      ->where('end_time', '>=', $this->end_time);
                });
            })
            ->with(['facility.lguCity'])
            ->get();

        return [
            'hasConflict' => $conflicts->isNotEmpty(),
            'conflicts' => $conflicts,
            'conflictCount' => $conflicts->count(),
            'message' => $conflicts->isNotEmpty() 
                ? $conflicts->count() . ' conflicting booking(s) detected'
                : 'No schedule conflicts'
        ];
    }

    /**
     * Get the payment deadline (48 hours after staff verification)
     * 
     * @return \Carbon\Carbon|null
     */
    public function getPaymentDeadline()
    {
        if ($this->status === 'staff_verified' && $this->staff_verified_at) {
            // Deadline is 48 hours from when staff verified the booking
            return $this->staff_verified_at->copy()->addHours(48);
        }
        
        return null;
    }

    /**
     * Get remaining time until payment deadline
     * 
     * @return \Carbon\CarbonInterval|null
     */
    public function getTimeUntilDeadline()
    {
        $deadline = $this->getPaymentDeadline();
        
        if (!$deadline) {
            return null;
        }

        $now = \Carbon\Carbon::now();
        
        if ($now->greaterThan($deadline)) {
            return null; // Deadline has passed
        }

        return $now->diff($deadline);
    }

    /**
     * Get remaining hours until payment deadline
     * 
     * @return float|null
     */
    public function getHoursUntilDeadline()
    {
        $deadline = $this->getPaymentDeadline();
        
        if (!$deadline) {
            return null;
        }

        $now = \Carbon\Carbon::now();
        
        if ($now->greaterThan($deadline)) {
            return 0; // Deadline has passed
        }

        return $now->floatDiffInHours($deadline);
    }

    /**
     * Check if payment deadline is approaching (< 24 hours remaining)
     * 
     * @return bool
     */
    public function isDeadlineApproaching(): bool
    {
        $hoursRemaining = $this->getHoursUntilDeadline();
        
        if ($hoursRemaining === null) {
            return false;
        }

        return $hoursRemaining > 0 && $hoursRemaining <= 24;
    }

    /**
     * Check if payment deadline is critical (< 6 hours remaining)
     * 
     * @return bool
     */
    public function isDeadlineCritical(): bool
    {
        $hoursRemaining = $this->getHoursUntilDeadline();
        
        if ($hoursRemaining === null) {
            return false;
        }

        return $hoursRemaining > 0 && $hoursRemaining <= 6;
    }

    /**
     * Check if payment is overdue (deadline has passed)
     * 
     * @return bool
     */
    public function isPaymentOverdue(): bool
    {
        $deadline = $this->getPaymentDeadline();
        
        if (!$deadline) {
            return false;
        }

        return \Carbon\Carbon::now()->greaterThan($deadline);
    }

    /**
     * Format remaining time for display
     * 
     * @return string
     */
    public function formatTimeRemaining(): string
    {
        $interval = $this->getTimeUntilDeadline();
        
        if (!$interval) {
            return 'Expired';
        }

        $days = $interval->d;
        $hours = $interval->h;
        $minutes = $interval->i;

        if ($days > 0) {
            return "{$days}d {$hours}h {$minutes}m";
        } elseif ($hours > 0) {
            return "{$hours}h {$minutes}m";
        } else {
            return "{$minutes}m";
        }
    }
}

