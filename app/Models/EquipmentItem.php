<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EquipmentItem extends Model
{
    protected $connection = 'facilities_db';
    protected $table = 'equipment_items';
    
    protected $fillable = [
        'name',
        'category',
        'description',
        'price_per_unit',
        'quantity_available',
        'is_available',
        'image_path'
    ];
    
    protected $casts = [
        'price_per_unit' => 'decimal:2',
        'quantity_available' => 'integer',
        'is_available' => 'boolean'
    ];
    
    /**
     * Get bookings that use this equipment
     */
    public function bookings(): BelongsToMany
    {
        return $this->belongsToMany(Booking::class, 'booking_equipment')
                    ->withPivot('quantity', 'price_per_unit', 'subtotal')
                    ->withTimestamps();
    }
    
    /**
     * Check if equipment is in stock
     */
    public function isInStock(int $requestedQuantity = 1): bool
    {
        return $this->is_available && $this->quantity_available >= $requestedQuantity;
    }
    
    /**
     * Scope for available equipment only
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)
                    ->where('quantity_available', '>', 0);
    }
    
    /**
     * Scope by category
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}

