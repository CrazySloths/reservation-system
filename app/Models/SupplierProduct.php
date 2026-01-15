<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'supplier_id',
        'product_code',
        'product_name',
        'product_description',
        'product_category',
        'specifications',
        'unit_of_measure',
        'current_price',
        'price_effective_date',
        'is_available',
        'product_photo_url',
        'price_list_document_url',
    ];

    protected $casts = [
        'specifications' => 'array',
        'price_effective_date' => 'date',
        'is_available' => 'boolean',
        'current_price' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}

