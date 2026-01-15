<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LguCity extends Model
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
    protected $table = 'lgu_cities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'city_name',
        'city_code',
        'description',
        'status',
        'has_external_integration',
        'integration_config',
        'facility_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'has_external_integration' => 'boolean',
        'integration_config' => 'array',
        'facility_count' => 'integer',
    ];

    /**
     * Get the facilities for this city
     */
    public function facilities(): HasMany
    {
        return $this->hasMany(FacilityDb::class, 'lgu_city_id');
    }
}

