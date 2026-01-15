<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Facility model for facilities_db database
 * This is used for booking relationships since bookings are in facilities_db
 */
class FacilityDb extends Facility
{
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
    protected $table = 'facilities';
    
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'facility_id';
    
    /**
     * Get the city that owns the facility (in facilities_db)
     */
    public function lguCity(): BelongsTo
    {
        return $this->belongsTo(LguCity::class, 'lgu_city_id', 'id');
    }
}

