<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class BookingConflict extends Model
{
    use SoftDeletes;

    protected $connection = 'facilities_db';
    protected $table = 'booking_conflicts';

    protected $fillable = [
        'booking_id',
        'city_event_id',
        'status',
        'citizen_choice',
        'response_deadline',
        'responded_at',
        'resolved_at',
        'new_booking_id',
        'admin_notes',
        'refund_method',
        'refund_account_name',
        'refund_account_number',
        'refund_bank_name',
    ];

    protected $casts = [
        'response_deadline' => 'datetime',
        'responded_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    /**
     * Get the city event that caused this conflict
     */
    public function cityEvent()
    {
        return $this->belongsTo(CityEvent::class, 'city_event_id');
    }

    /**
     * Get the booking that is affected by this conflict
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    /**
     * Get the new booking if the citizen chose to reschedule
     */
    public function newBooking()
    {
        return $this->belongsTo(Booking::class, 'new_booking_id');
    }

    /**
     * Check if the response deadline has passed
     */
    public function isDeadlinePassed()
    {
        return now()->greaterThan($this->response_deadline);
    }

    /**
     * Process citizen's choice (reschedule or refund)
     */
    public function processCitizenChoice($choice, $newBookingId = null, $adminNotes = null)
    {
        $this->update([
            'citizen_choice' => $choice,
            'responded_at' => now(),
            'status' => 'resolved',
            'resolved_at' => now(),
            'new_booking_id' => $newBookingId,
            'admin_notes' => $adminNotes,
        ]);

        // Update original booking status
        if ($choice === 'refund') {
            DB::connection('facilities_db')
                ->table('bookings')
                ->where('id', $this->booking_id)
                ->update([
                    'status' => 'refunded',
                    'updated_at' => now(),
                ]);
        } elseif ($choice === 'reschedule' && $newBookingId) {
            DB::connection('facilities_db')
                ->table('bookings')
                ->where('id', $this->booking_id)
                ->update([
                    'status' => 'rescheduled',
                    'updated_at' => now(),
                ]);
        }

        return true;
    }

    /**
     * Auto-process expired conflicts with no response
     */
    public function processNoResponse()
    {
        $this->update([
            'citizen_choice' => 'no_response',
            'responded_at' => now(),
            'status' => 'resolved',
            'resolved_at' => now(),
            'admin_notes' => 'Auto-refunded: No response by deadline',
        ]);

        // Auto-refund the booking
        DB::connection('facilities_db')
            ->table('bookings')
            ->where('id', $this->booking_id)
            ->update([
                'status' => 'refunded',
                'updated_at' => now(),
            ]);

        return true;
    }
}
