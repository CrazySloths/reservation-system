<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConflictCreatedMail;

class CityEvent extends Model
{
    use SoftDeletes;

    protected $connection = 'facilities_db';
    protected $table = 'city_events';

    protected $fillable = [
        'facility_id',
        'start_time',
        'end_time',
        'event_title',
        'event_description',
        'event_type',
        'created_by',
        'status',
        'affected_bookings_count',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Get the facility that this city event belongs to
     */
    public function facility()
    {
        return $this->belongsTo(Facility::class, 'facility_id', 'facility_id');
    }

    /**
     * Get the booking conflicts associated with this city event
     */
    public function bookingConflicts()
    {
        return $this->hasMany(BookingConflict::class, 'city_event_id');
    }

    /**
     * Detect conflicting bookings for this city event
     */
    public function detectConflictingBookings()
    {
        return DB::connection('facilities_db')
            ->table('bookings')
            ->where('facility_id', $this->facility_id)
            ->whereIn('status', ['confirmed', 'paid'])
            ->where(function($query) {
                $query->whereBetween('start_time', [$this->start_time, $this->end_time])
                      ->orWhereBetween('end_time', [$this->start_time, $this->end_time])
                      ->orWhere(function($q) {
                          $q->where('start_time', '<=', $this->start_time)
                            ->where('end_time', '>=', $this->end_time);
                      });
            })
            ->get();
    }

    /**
     * Create conflict records for all affected bookings
     */
    public function createConflicts()
    {
        $conflictingBookings = $this->detectConflictingBookings();
        $conflictsCreated = 0;

        // Load facility relationship if not already loaded
        if (!$this->relationLoaded('facility')) {
            $this->load('facility');
        }

        // Get facility details
        $facilityDetails = DB::connection('facilities_db')
            ->table('facilities')
            ->where('facility_id', $this->facility_id)
            ->first();

        if (!$facilityDetails) {
            \Log::error('Facility not found for city event', [
                'facility_id' => $this->facility_id,
                'city_event_id' => $this->id
            ]);
            return 0;
        }

        foreach ($conflictingBookings as $booking) {
            // Check if conflict already exists
            $existingConflict = BookingConflict::where('booking_id', $booking->id)
                ->where('city_event_id', $this->id)
                ->first();

            if (!$existingConflict) {
                $conflict = BookingConflict::create([
                    'booking_id' => $booking->id,
                    'city_event_id' => $this->id,
                    'status' => 'pending',
                    'response_deadline' => now()->addDays(7), // 7 days to respond
                ]);
                
                // Get citizen details from auth_db
                $citizen = DB::connection('auth_db')
                    ->table('users')
                    ->where('id', $booking->user_id)
                    ->first();
                
                if ($citizen) {
                    // Send email notification
                    if ($citizen->email) {
                        try {
                            Mail::to($citizen->email)->send(
                                new ConflictCreatedMail($conflict, $booking, $this, $facilityDetails, $citizen)
                            );
                        } catch (\Exception $e) {
                            // Log error but don't stop the process
                            \Log::error('Failed to send conflict email: ' . $e->getMessage());
                        }
                    }
                    
                    // Create in-app notification
                    try {
                        DB::connection('auth_db')->table('notifications')->insert([
                            'id' => \Illuminate\Support\Str::uuid(),
                            'type' => 'App\\Notifications\\BookingConflictNotification',
                            'notifiable_type' => 'App\\Models\\User',
                            'notifiable_id' => $citizen->id,
                            'data' => json_encode([
                                'message' => "A city event has been scheduled that conflicts with your booking at {$facilityDetails->name}. Please choose to reschedule or request a refund within 7 days.",
                                'conflict_id' => $conflict->id,
                                'booking_reference' => $booking->booking_reference,
                                'facility_name' => $facilityDetails->name,
                                'event_title' => $this->event_title,
                                'action_url' => url('/citizen/booking-conflicts/' . $conflict->id),
                            ]),
                            'read_at' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    } catch (\Exception $e) {
                        \Log::error('Failed to create in-app notification: ' . $e->getMessage());
                    }
                }
                
                $conflictsCreated++;
            }
        }

        // Update affected bookings count
        $this->update(['affected_bookings_count' => $conflictsCreated]);

        return $conflictsCreated;
    }
}
