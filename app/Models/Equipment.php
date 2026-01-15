<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'facility_id',
        'equipment_name',
        'equipment_type',
        'description',
        'quantity_total',
        'quantity_available',
        'hourly_rate',
        'daily_rate',
        'is_free',
        'is_available',
        'condition_status',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'daily_rate' => 'decimal:2',
        'quantity_total' => 'integer',
        'quantity_available' => 'integer',
        'is_free' => 'boolean',
        'is_available' => 'boolean',
    ];

    /**
     * Relationship: Equipment belongs to a facility
     */
    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }
}
