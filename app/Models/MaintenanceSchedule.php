<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'facilities_db';
    protected $table = 'maintenance_schedules';

    protected $fillable = [
        'facility_id',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'maintenance_type',
        'description',
        'notes',
        'is_recurring',
        'recurring_pattern',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_recurring' => 'boolean',
    ];

    /**
     * Get the facility associated with this maintenance schedule.
     */
    public function facility()
    {
        return $this->belongsTo(Facility::class, 'facility_id', 'facility_id');
    }

    /**
     * Get the admin user who created this schedule.
     */
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by', 'user_id')
            ->setConnection('auth_db');
    }

    /**
     * Check if maintenance is active on a given date and time.
     */
    public function isActiveOn($date, $time = null): bool
    {
        // Check if date is within range
        if ($date < $this->start_date || $date > $this->end_date) {
            return false;
        }

        // If no specific time slots, affects whole day
        if (!$this->start_time || !$this->end_time) {
            return true;
        }

        // If time is provided, check time overlap
        if ($time) {
            return $time >= $this->start_time && $time < $this->end_time;
        }

        return true;
    }

    /**
     * Get all affected bookings by this maintenance schedule.
     */
    public function getAffectedBookings()
    {
        $query = Booking::where('facility_id', $this->facility_id)
            ->where('event_date', '>=', $this->start_date)
            ->where('event_date', '<=', $this->end_date)
            ->whereIn('status', ['pending', 'staff_verified', 'paid', 'confirmed']);

        // If specific time slots, check time overlap
        if ($this->start_time && $this->end_time) {
            $query->where(function($q) {
                $q->where(function($query) {
                    $query->where('start_time', '<', $this->end_time)
                        ->where('end_time', '>', $this->start_time);
                });
            });
        }

        return $query->with(['user', 'facility'])->get();
    }
}
