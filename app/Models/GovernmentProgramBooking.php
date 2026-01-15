<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GovernmentProgramBooking extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'facilities_db';

    protected $guarded = [];

    protected $casts = [
        'event_date' => 'date',
        'finance_approved_date' => 'date',
        'liquidation_date' => 'date',
        'assigned_at' => 'datetime',
        'call_log' => 'array',
        'speaker_details' => 'array',
        'equipment_provided' => 'array',
        'fund_breakdown' => 'array',
        'attendance_data' => 'array',
        'speakers_confirmed' => 'boolean',
        'is_fee_waived' => 'boolean',
        'pre_event_transparency_published' => 'boolean',
        'post_event_transparency_published' => 'boolean',
        'is_public_display' => 'boolean',
        'liquidation_required' => 'boolean',
        'liquidation_submitted' => 'boolean',
        'requested_amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'actual_spent' => 'decimal:2',
        'event_rating' => 'decimal:2',
    ];

    // Relationships
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function assignedFacility()
    {
        return $this->belongsTo(Facility::class, 'assigned_facility_id');
    }

    public function assignedAdmin()
    {
        return $this->belongsTo(User::class, 'assigned_admin_id');
    }

    public function liquidationItems()
    {
        return $this->hasMany(LiquidationItem::class);
    }

    public function citizenRegistrations()
    {
        return $this->hasMany(CitizenProgramRegistration::class);
    }

    // Helper method to get data from Energy Efficiency database
    public function getEnergyEfficiencySeminar()
    {
        return \DB::connection('energy_efficiency')->table('seminars')
            ->where('seminar_id', $this->source_seminar_id)
            ->first();
    }

    public function getEnergyEfficiencyOrganizer()
    {
        return \DB::connection('energy_efficiency')->table('users')
            ->where('user_id', $this->organizer_user_id)
            ->first();
    }

    public function getEnergyEfficiencyAttendance()
    {
        return \DB::connection('energy_efficiency')->table('attendance')
            ->where('event_name', 'LIKE', '%' . $this->program_title . '%')
            ->count();
    }
}

