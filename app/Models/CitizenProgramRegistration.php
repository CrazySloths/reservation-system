<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CitizenProgramRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'government_program_booking_id',
        'citizen_id',
        'registration_status',
        'qr_code',
        'registered_at',
        'attended_at',
        'cancelled_at',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'attended_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function governmentProgramBooking()
    {
        return $this->belongsTo(GovernmentProgramBooking::class);
    }

    public function citizen()
    {
        return $this->belongsTo(User::class, 'citizen_id');
    }
}

