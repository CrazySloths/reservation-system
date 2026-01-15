<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiquidationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'government_program_booking_id',
        'category',
        'supplier_name',
        'official_receipt_number',
        'receipt_date',
        'receipt_image_url',
        'item_description',
        'item_specification',
        'quantity',
        'unit_price',
        'total_price',
        'is_public_display',
    ];

    protected $casts = [
        'receipt_date' => 'date',
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'is_public_display' => 'boolean',
    ];

    public function governmentProgramBooking()
    {
        return $this->belongsTo(GovernmentProgramBooking::class);
    }
}

