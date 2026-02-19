<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facility extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'location_id',
        'facility_name',
        'facility_type',
        'description',
        'capacity',
        'hourly_rate',
        'base_rate_3hrs',
        'deposit_amount',
        'amenities',
        'rules',
        'terms_and_conditions',
        'is_available',
        'advance_booking_days',
        'min_booking_hours',
        'max_booking_hours',
        'operating_hours',
        'address',
        'google_maps_url',
        'status',
        'display_order',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amenities' => 'array',
        'operating_hours' => 'array',
        'hourly_rate' => 'decimal:2',
        'base_rate_3hrs' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'capacity' => 'integer',
        'is_available' => 'boolean',
        'advance_booking_days' => 'integer',
        'min_booking_hours' => 'integer',
        'max_booking_hours' => 'integer',
        'display_order' => 'integer',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * Relationship: Facility belongs to a location
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Relationship: Facility has many photos
     */
    public function photos(): HasMany
    {
        return $this->hasMany(FacilityPhoto::class);
    }

    /**
     * Relationship: Facility has many schedules
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(FacilitySchedule::class);
    }

    /**
     * Relationship: Facility has many maintenance schedules
     */
    public function maintenanceSchedules(): HasMany
    {
        return $this->hasMany(MaintenanceSchedule::class);
    }

    /**
     * Relationship: Facility has many equipment
     */
    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class);
    }

    /**
     * Relationship: Facility has many bookings
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Scope: Only active facilities
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('is_available', true);
    }

    /**
     * Scope: Order by display order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('facility_name');
    }

    /**
     * Get primary photo for the facility
     */
    public function getPrimaryPhotoAttribute()
    {
        return $this->photos()->where('is_primary', true)->first();
    }

    /**
     * Check if facility is available on a specific date and time
     */
    public function isAvailableAt($date, $startTime, $endTime)
    {
        // Check if facility is generally available
        if (!$this->is_available || $this->status !== 'active') {
            return false;
        }

        // Check for blocked schedules
        $blocked = $this->schedules()
            ->where('schedule_type', '!=', 'available')
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->exists();

        if ($blocked) {
            return false;
        }

        // Check for existing bookings
        $conflictingBooking = $this->bookings()
            ->where('booking_date', $date)
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'rejected')
            ->where(function($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<=', $startTime)
                          ->where('end_time', '>=', $endTime);
                    });
            })
            ->exists();

        return !$conflictingBooking;
    }

    public function favoritedByUsers()
    {
        return $this->hasMany(UserFavorite::class);
    }

    public function usersFavorited()
    {
        return $this->belongsToMany(User::class, 'user_favorites')
            ->withTimestamps()
            ->withPivot('favorited_at');
    }

    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    public function getFavoritesCountAttribute()
    {
        return $this->favoritedByUsers()->count();
    }
}
