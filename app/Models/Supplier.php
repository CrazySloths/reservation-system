<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'supplier_name',
        'supplier_type',
        'contact_person',
        'contact_phone',
        'contact_email',
        'business_address',
        'business_permit_number',
        'tin_number',
        'bir_registration',
        'is_active',
        'is_verified',
        'is_preferred_supplier',
        'created_by_user_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'is_preferred_supplier' => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(SupplierProduct::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}

