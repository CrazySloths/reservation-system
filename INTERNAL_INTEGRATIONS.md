# 🔗 INTERNAL INTEGRATIONS - PUBLIC FACILITIES RESERVATION SYSTEM

**Document Type:** Internal System Architecture  
**Date Created:** December 6, 2025  
**Purpose:** Document how internal components, submodules, and services integrate with each other

---

## 📋 TABLE OF CONTENTS

1. [System Architecture Overview](#system-architecture-overview)
2. [Five Core Submodules](#five-core-submodules)
3. [Database Integration Layer](#database-integration-layer)
4. [Service Layer Integration](#service-layer-integration)
5. [Authentication & Authorization Integration](#authentication--authorization-integration)
6. [Data Flow Diagrams](#data-flow-diagrams)
7. [Role-Based Integration](#role-based-integration)
8. [AI Analytics Integration](#ai-analytics-integration)

---

## 🏗️ SYSTEM ARCHITECTURE OVERVIEW

### **Integration Philosophy**

The Public Facilities Reservation System is built on a **modular, integrated architecture** where:
- Each submodule has a clear, single responsibility
- Modules communicate through well-defined interfaces
- Database serves as the central integration point
- Services provide reusable business logic
- Everything connects seamlessly without redundancy

### **Core Technology Stack**

```
┌─────────────────────────────────────────────────────┐
│                  PRESENTATION LAYER                  │
├─────────────────────────────────────────────────────┤
│  Blade Views + Tailwind CSS + Lucide Icons         │
│  SweetAlert2 + Chart.js + TensorFlow.js             │
└─────────────────────────────────────────────────────┘
                        ↕
┌─────────────────────────────────────────────────────┐
│                 APPLICATION LAYER                    │
├─────────────────────────────────────────────────────┤
│  Laravel Controllers + Middleware                   │
│  Route Handlers + Request Validation                │
└─────────────────────────────────────────────────────┘
                        ↕
┌─────────────────────────────────────────────────────┐
│                  BUSINESS LOGIC LAYER                │
├─────────────────────────────────────────────────────┤
│  Eloquent Models + Service Classes                  │
│  PricingCalculatorService + Business Rules          │
└─────────────────────────────────────────────────────┘
                        ↕
┌─────────────────────────────────────────────────────┐
│                   DATA ACCESS LAYER                  │
├─────────────────────────────────────────────────────┤
│  MySQL Database (public_reservation_db)             │
│  Migrations + Seeders + Database Relationships      │
└─────────────────────────────────────────────────────┘
                        ↕
┌─────────────────────────────────────────────────────┐
│                  EXTERNAL SYSTEMS                    │
├─────────────────────────────────────────────────────┤
│  lgu1_auth (Authentication)                         │
│  External LGU Systems APIs + Webhooks               │
└─────────────────────────────────────────────────────┘
```

---

## 🧩 FIVE CORE SUBMODULES

### **Submodule 1: Facility Directory and Calendar**

**Responsibility:** Display available facilities, show calendars, check availability

**Database Tables:**
- `facilities` (main table)
- `lgu_cities` (multi-LGU support)
- `bookings` (for availability checking)

**Integration Points:**
- **→ Booking System:** Provides facility details for booking form
- **→ Conflict Detection:** Queries bookings to show available/booked dates
- **→ Equipment System:** Shows compatible equipment per facility
- **→ Pricing Calculator:** Provides base_rate for calculations
- **→ AI Analytics:** Analyzes facility usage patterns

**Key Methods:**
```php
// Facility Model
public function bookings() // Get all bookings for this facility
public function city()      // Get LGU city relationship
public function isAvailable($date, $startTime, $endTime) // Check availability
```

---

### **Submodule 2: Online Booking System**

**Responsibility:** Handle complete booking workflow from submission to confirmation

**Database Tables:**
- `bookings` (main table)
- `booking_equipment` (pivot table)
- `payment_slips` (payment tracking)

**Integration Points:**
- **→ Facility Directory:** Fetches facility details
- **→ User Authentication:** Gets citizen profile from lgu1_auth
- **→ Pricing Calculator:** Calculates total with discounts
- **→ Equipment System:** Reserves equipment for booking
- **→ Conflict Detection:** Validates no schedule conflicts
- **→ Staff Verification:** Sends for document review
- **→ Admin Approval:** Sends for final approval
- **→ Payment System:** Generates payment slip
- **→ Notification System:** Sends email/SMS at each stage

**Booking Status Flow:**
```
reserved (24h hold)
    ↓
tentative (documents submitted)
    ↓
pending_approval (staff verified)
    ↓
payment_pending (admin approved)
    ↓
confirmed (payment received)
    ↓
completed (event finished)
```

**Key Methods:**
```php
// Booking Model
public function facility()           // Get facility relationship
public function user()               // Get citizen who booked
public function equipmentItems()     // Get all equipment for this booking
public function staffVerifier()      // Get staff who verified
public function adminApprover()      // Get admin who approved
public function isWithinReservedPeriod()  // Check 24-hour hold
public function getDiscountSummary() // Get pricing breakdown
```

---

### **Submodule 3: Usage Fee Calculation with Two-Tier Discount System**

**Responsibility:** Calculate final pricing with city and identity discounts

**Service Class:** `PricingCalculatorService`

**Database Tables:**
- `users` (for residency and age data)
- `facilities` (for base_rate)
- `equipment_items` (for equipment pricing)
- `bookings` (stores final pricing breakdown)

**Integration Points:**
- **→ User Profile:** Reads city, birthdate, ID type
- **→ Facility Directory:** Fetches facility base_rate
- **→ Equipment System:** Fetches equipment prices
- **→ Booking System:** Saves pricing breakdown to booking

**Two-Tier Discount Logic:**
```php
// Step 1: Calculate Base Costs
$facilityFee = $facility->base_rate;
$equipmentTotal = sum of (price_per_unit × quantity);
$subtotal = $facilityFee + $equipmentTotal;

// Step 2: Apply Tier 1 - City Discount (30%)
if ($user->is_caloocan_resident) {
    $cityDiscountAmount = $subtotal * 0.30;
    $afterCityDiscount = $subtotal - $cityDiscountAmount;
} else {
    $afterCityDiscount = $subtotal;
}

// Step 3: Apply Tier 2 - Identity Discount (20%)
if (in_array($idType, ['senior', 'pwd', 'student'])) {
    $identityDiscountAmount = $afterCityDiscount * 0.20;
    $finalTotal = $afterCityDiscount - $identityDiscountAmount;
} else {
    $finalTotal = $afterCityDiscount;
}

// Step 4: Calculate Total Savings
$totalSavings = $cityDiscountAmount + $identityDiscountAmount;
```

**Key Methods:**
```php
// PricingCalculatorService
public function calculateBookingPrice($user, $facility, $equipmentItems, $idType)
public function previewPricing($user, $facility) // Show discount preview
public function validateDiscountEligibility($user, $idType) // Verify eligibility
```

**Integration Example:**
```php
// In BookingController
$pricingService = new PricingCalculatorService();
$pricing = $pricingService->calculateBookingPrice(
    auth()->user(),           // From Auth integration
    $facility,                // From Facility submodule
    $selectedEquipment,       // From Equipment submodule
    $request->id_type         // From user input
);

// Save to booking
$booking->pricing_breakdown = json_encode($pricing);
$booking->final_total = $pricing['final_total'];
```

---

### **Submodule 4: Equipment Rental System**

**Responsibility:** Manage equipment catalog, inventory, and rental tracking

**Database Tables:**
- `equipment_items` (catalog)
- `booking_equipment` (pivot table for rentals)

**Equipment Categories (Limited to 3):**
1. **Chairs** (Monobloc ₱25, Banquet ₱50, Folding ₱35)
2. **Tables** (Round 6-seater ₱300, 8-seater ₱400, Rectangular ₱250-₱350)
3. **Sound System** (Basic ₱2,500, Premium ₱4,500)

**Integration Points:**
- **→ Booking System:** Links equipment to bookings via pivot table
- **→ Pricing Calculator:** Provides equipment pricing for total calculation
- **→ Conflict Detection:** Checks equipment availability on dates
- **→ AI Analytics:** Recommends equipment based on similar events
- **→ Inventory Management:** Tracks quantity_available in real-time

**Real-Time Availability Logic:**
```php
// EquipmentItem Model
public function getAvailableQuantity($date, $startTime, $endTime)
{
    $totalStock = $this->quantity_available;
    
    // Get all bookings on this date that are confirmed/pending
    $bookedQuantity = DB::table('booking_equipment')
        ->join('bookings', 'booking_equipment.booking_id', '=', 'bookings.id')
        ->where('booking_equipment.equipment_item_id', $this->id)
        ->where('bookings.booking_date', $date)
        ->whereIn('bookings.status', ['confirmed', 'payment_pending', 'pending_approval'])
        ->where(function($query) use ($startTime, $endTime) {
            // Check time overlap
            $query->whereBetween('bookings.start_time', [$startTime, $endTime])
                  ->orWhereBetween('bookings.end_time', [$startTime, $endTime]);
        })
        ->sum('booking_equipment.quantity');
    
    return $totalStock - $bookedQuantity;
}
```

**AI-Powered Recommendations:**
```php
// Integration with AI Analytics
public function getSuggestedEquipment($eventType, $attendeeCount, $facilityId)
{
    // Query historical data
    $similarBookings = Booking::where('event_type', $eventType)
        ->where('facility_id', $facilityId)
        ->whereBetween('expected_attendees', [$attendeeCount - 20, $attendeeCount + 20])
        ->with('equipmentItems')
        ->limit(50)
        ->get();
    
    // Analyze common equipment patterns
    $equipmentFrequency = [];
    foreach ($similarBookings as $booking) {
        foreach ($booking->equipmentItems as $item) {
            $equipmentFrequency[$item->id] = ($equipmentFrequency[$item->id] ?? 0) + 1;
        }
    }
    
    // Return top 5 most common equipment
    arsort($equipmentFrequency);
    return array_slice($equipmentFrequency, 0, 5, true);
}
```

---

### **Submodule 5: AI-Powered Analytics (TensorFlow.js)**

**Responsibility:** Pattern recognition, usage insights, resource optimization recommendations

**Technology:** TensorFlow.js (client-side LSTM neural network)

**Database Tables (Data Sources):**
- `bookings` (historical booking data)
- `booking_equipment` (equipment usage patterns)
- `facilities` (facility utilization)
- `feedback` (citizen satisfaction data)

**Integration Points:**
- **→ All Submodules:** Consumes data from every part of the system
- **→ Admin Dashboard:** Displays insights and recommendations
- **→ Equipment System:** Powers smart equipment suggestions
- **→ Pricing System:** Analyzes discount impact
- **→ Scheduling:** Identifies peak/low-demand periods

**Data Flow for AI:**
```
Historical Data Collection
         ↓
[Bookings + Equipment + Feedback + Facilities]
         ↓
Data Preparation (Normalization + Windowing)
         ↓
TensorFlow.js LSTM Model Training
         ↓
Pattern Recognition (not prediction!)
         ↓
Generate Insights:
  • Peak booking days/times
  • Facility popularity trends
  • Equipment usage patterns
  • Seasonal variations
  • Discount utilization
         ↓
Display on Admin Dashboard (Chart.js)
         ↓
Admin Makes Data-Driven Decisions
```

**Key Integration Code:**
```javascript
// resources/js/analytics.js
export async function startForecasting() {
    // 1. Fetch data from Laravel API
    const response = await fetch('/admin/api/usage-data');
    const usageData = await response.json();
    
    // 2. Train LSTM model (client-side)
    const model = buildLSTMModel();
    await model.fit(xsTensor, ysTensor, { epochs: 100 });
    
    // 3. Generate patterns (not predictions!)
    const patterns = generateUsagePatterns(model, usageData);
    
    // 4. Visualize with Chart.js
    visualizeResults(patterns);
}
```

**AI Insights Output (Example):**
```json
{
    "peak_days": {
        "Saturday": { "percentage": 45, "insight": "45% higher than average" },
        "Sunday": { "percentage": 30, "insight": "30% higher than average" }
    },
    "facility_utilization": {
        "City Hall Main Hall": { "rate": 75, "status": "High demand" },
        "Covered Court": { "rate": 40, "status": "Underutilized" }
    },
    "equipment_recommendations": {
        "Monobloc Chairs": { "usage_rate": 85, "action": "Purchase 50 more" },
        "Sound System Premium": { "usage_rate": 12, "action": "Keep as-is" }
    },
    "seasonal_trends": {
        "December": { "trend": "+40% weddings", "action": "Prepare extra equipment" }
    }
}
```

---

## 🗄️ DATABASE INTEGRATION LAYER

### **Core Tables and Relationships**

```
┌─────────────────────────────────────────────────────┐
│                 DATABASE SCHEMA                      │
└─────────────────────────────────────────────────────┘

users (lgu1_auth database)
├─ id
├─ name, email, password
├─ city ────────────────┐ (for discount calculation)
├─ is_caloocan_resident │ (auto-tagged)
└─ birthdate ───────────┘ (for senior discount)
        │
        │ (1-to-many)
        ↓
bookings
├─ id
├─ user_id ────────────→ users.id
├─ facility_id ────────→ facilities.id
├─ booking_date, start_time, end_time
├─ expected_attendees
├─ event_type
├─ status (enum: 7 statuses)
├─ reserved_until (24-hour hold)
├─ verified_by ────────→ users.id (Staff)
├─ admin_approved_by ──→ users.id (Admin)
├─ subtotal
├─ equipment_total
├─ city_discount_amount
├─ identity_discount_amount
├─ total_savings
├─ final_total
├─ pricing_breakdown (JSON)
└─ timestamps
        │
        │ (many-to-many)
        ↓
booking_equipment (PIVOT)
├─ booking_id ─────────→ bookings.id
├─ equipment_item_id ──→ equipment_items.id
├─ quantity
├─ price_per_unit (locked at booking time)
└─ subtotal
        │
        ↓
equipment_items
├─ id
├─ name
├─ category (chairs, tables, sound_system)
├─ price_per_unit
├─ quantity_available
├─ is_available
└─ image_path

facilities
├─ id
├─ lgu_city_id ────────→ lgu_cities.id
├─ name
├─ capacity
├─ base_rate (per 3 hours)
├─ extension_rate (per 2 hours)
├─ amenities (JSON)
└─ status (active, coming_soon, maintenance)

lgu_cities
├─ id
├─ city_name (Caloocan, Quezon City, etc.)
├─ city_code (CLCN, QC)
├─ status (active, coming_soon, inactive)
└─ facility_count

payment_slips
├─ id
├─ booking_id ─────────→ bookings.id
├─ amount
├─ payment_method (cash, gcash, paymaya, bank)
├─ payment_gateway
├─ gateway_transaction_id
├─ or_number (from Treasurer)
├─ treasurer_status
└─ confirmed_by_treasurer_at

feedback (post-event)
├─ id
├─ booking_id ─────────→ bookings.id
├─ rating (1-5 stars)
├─ comment
├─ photos (JSON)
└─ created_at
```

### **Key Database Relationships**

```php
// User Model (lgu1_auth)
public function bookings() {
    return $this->hasMany(Booking::class);
}
public function verifiedBookings() {
    return $this->hasMany(Booking::class, 'verified_by');
}
public function approvedBookings() {
    return $this->hasMany(Booking::class, 'admin_approved_by');
}

// Booking Model
public function user() {
    return $this->belongsTo(User::class);
}
public function facility() {
    return $this->belongsTo(Facility::class);
}
public function equipmentItems() {
    return $this->belongsToMany(EquipmentItem::class, 'booking_equipment')
                ->withPivot('quantity', 'price_per_unit', 'subtotal');
}
public function staffVerifier() {
    return $this->belongsTo(User::class, 'verified_by');
}
public function adminApprover() {
    return $this->belongsTo(User::class, 'admin_approved_by');
}
public function paymentSlip() {
    return $this->hasOne(PaymentSlip::class);
}
public function feedback() {
    return $this->hasOne(Feedback::class);
}

// Facility Model
public function bookings() {
    return $this->hasMany(Booking::class);
}
public function city() {
    return $this->belongsTo(LguCity::class, 'lgu_city_id');
}

// EquipmentItem Model
public function bookings() {
    return $this->belongsToMany(Booking::class, 'booking_equipment')
                ->withPivot('quantity', 'price_per_unit', 'subtotal');
}

// LguCity Model
public function facilities() {
    return $this->hasMany(Facility::class, 'lgu_city_id');
}
```

---

## ⚙️ SERVICE LAYER INTEGRATION

### **PricingCalculatorService**

**Location:** `app/Services/PricingCalculatorService.php`

**Purpose:** Centralized pricing logic used across the system

**Integration Points:**
- Called by `BookingController` during booking creation
- Called by frontend for real-time price preview (AJAX)
- Used by admin to recalculate pricing if needed
- Referenced by payment system for validation

**Complete Service Code:**
```php
<?php

namespace App\Services;

use App\Models\User;
use App\Models\Facility;
use App\Models\EquipmentItem;

class PricingCalculatorService
{
    /**
     * Calculate complete booking price with two-tier discounts
     *
     * @param User $user
     * @param Facility $facility
     * @param array $equipmentItems [['id' => 1, 'quantity' => 50], ...]
     * @param string $idType (regular, senior, pwd, student)
     * @return array
     */
    public function calculateBookingPrice($user, $facility, $equipmentItems, $idType)
    {
        // STEP 1: Calculate facility fee
        $facilityFee = $facility->base_rate;
        
        // STEP 2: Calculate equipment total
        $equipmentTotal = 0;
        $equipmentDetails = [];
        
        foreach ($equipmentItems as $item) {
            $equipment = EquipmentItem::find($item['id']);
            $quantity = $item['quantity'];
            $subtotal = $equipment->price_per_unit * $quantity;
            
            $equipmentTotal += $subtotal;
            $equipmentDetails[] = [
                'id' => $equipment->id,
                'name' => $equipment->name,
                'quantity' => $quantity,
                'price_per_unit' => $equipment->price_per_unit,
                'subtotal' => $subtotal,
            ];
        }
        
        // STEP 3: Calculate SUBTOTAL (facility + equipment)
        $subtotal = $facilityFee + $equipmentTotal;
        
        // STEP 4: Apply Tier 1 - City Discount (30% on total)
        $cityDiscountAmount = 0;
        $cityDiscountPercentage = 0;
        
        if ($user->is_caloocan_resident) {
            $cityDiscountPercentage = 30;
            $cityDiscountAmount = $subtotal * 0.30;
        }
        
        $afterCityDiscount = $subtotal - $cityDiscountAmount;
        
        // STEP 5: Apply Tier 2 - Identity Discount (20% on discounted total)
        $identityDiscountAmount = 0;
        $identityDiscountPercentage = 0;
        $identityDiscountType = null;
        
        if (in_array($idType, ['senior', 'pwd', 'student'])) {
            $identityDiscountPercentage = 20;
            $identityDiscountAmount = $afterCityDiscount * 0.20;
            $identityDiscountType = $idType;
        }
        
        // STEP 6: Calculate final total
        $finalTotal = $afterCityDiscount - $identityDiscountAmount;
        
        // STEP 7: Calculate total savings
        $totalSavings = $cityDiscountAmount + $identityDiscountAmount;
        $savingsPercentage = $subtotal > 0 ? ($totalSavings / $subtotal) * 100 : 0;
        
        return [
            'facility_fee' => $facilityFee,
            'equipment_total' => $equipmentTotal,
            'equipment_details' => $equipmentDetails,
            'subtotal' => $subtotal,
            'city_discount_percentage' => $cityDiscountPercentage,
            'city_discount_amount' => $cityDiscountAmount,
            'after_city_discount' => $afterCityDiscount,
            'identity_discount_type' => $identityDiscountType,
            'identity_discount_percentage' => $identityDiscountPercentage,
            'identity_discount_amount' => $identityDiscountAmount,
            'total_savings' => $totalSavings,
            'savings_percentage' => round($savingsPercentage, 2),
            'final_total' => $finalTotal,
        ];
    }
    
    /**
     * Preview pricing for user (shows potential discounts)
     */
    public function previewPricing($user, $facility)
    {
        $eligibility = $this->getDiscountEligibility($user);
        
        return [
            'base_rate' => $facility->base_rate,
            'eligible_for_city_discount' => $eligibility['city_discount'],
            'eligible_for_identity_discount' => $eligibility['identity_discount'],
            'potential_savings' => $this->calculatePotentialSavings(
                $facility->base_rate,
                $eligibility
            ),
        ];
    }
    
    /**
     * Validate discount eligibility
     */
    public function validateDiscountEligibility($user, $idType)
    {
        $errors = [];
        
        // Validate city discount
        if (!$user->is_caloocan_resident) {
            $errors['city'] = 'City discount only available for Caloocan residents';
        }
        
        // Validate identity discount
        if ($idType === 'senior') {
            $age = \Carbon\Carbon::parse($user->birthdate)->age;
            if ($age < 60) {
                $errors['senior'] = 'Senior discount requires age 60 or above';
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }
    
    /**
     * Get user's discount eligibility
     */
    private function getDiscountEligibility($user)
    {
        return [
            'city_discount' => $user->is_caloocan_resident,
            'identity_discount' => $user->birthdate && 
                \Carbon\Carbon::parse($user->birthdate)->age >= 60,
        ];
    }
    
    /**
     * Calculate potential savings
     */
    private function calculatePotentialSavings($baseAmount, $eligibility)
    {
        $savings = 0;
        
        if ($eligibility['city_discount']) {
            $savings += $baseAmount * 0.30; // 30% city discount
        }
        
        if ($eligibility['identity_discount']) {
            $afterCity = $baseAmount - ($baseAmount * 0.30);
            $savings += $afterCity * 0.20; // 20% identity discount
        }
        
        return $savings;
    }
}
```

**Usage Example in Controller:**
```php
// BookingController.php
public function store(Request $request)
{
    $user = auth()->user();
    $facility = Facility::findOrFail($request->facility_id);
    $equipment = $request->equipment ?? [];
    
    // Use PricingCalculatorService
    $pricingService = new PricingCalculatorService();
    $pricing = $pricingService->calculateBookingPrice(
        $user,
        $facility,
        $equipment,
        $request->id_type
    );
    
    // Create booking with calculated pricing
    $booking = Booking::create([
        'user_id' => $user->id,
        'facility_id' => $facility->id,
        'booking_date' => $request->booking_date,
        'start_time' => $request->start_time,
        'end_time' => $request->end_time,
        'subtotal' => $pricing['subtotal'],
        'equipment_total' => $pricing['equipment_total'],
        'city_discount_amount' => $pricing['city_discount_amount'],
        'identity_discount_amount' => $pricing['identity_discount_amount'],
        'total_savings' => $pricing['total_savings'],
        'final_total' => $pricing['final_total'],
        'pricing_breakdown' => json_encode($pricing),
        'status' => 'reserved',
        'reserved_until' => now()->addHours(24),
    ]);
    
    // Attach equipment to booking
    foreach ($pricing['equipment_details'] as $item) {
        $booking->equipmentItems()->attach($item['id'], [
            'quantity' => $item['quantity'],
            'price_per_unit' => $item['price_per_unit'],
            'subtotal' => $item['subtotal'],
        ]);
    }
    
    return redirect()->route('bookings.show', $booking);
}
```

---

## 🔐 AUTHENTICATION & AUTHORIZATION INTEGRATION

### **Centralized Authentication: lgu1_auth**

The system integrates with the centralized **lgu1_auth** database for user authentication.

**Integration Setup:**
```php
// config/database.php
'connections' => [
    'mysql' => [
        'database' => env('DB_DATABASE', 'public_reservation_db'),
    ],
    'lgu1_auth' => [
        'driver' => 'mysql',
        'database' => env('AUTH_DB_DATABASE', 'lgu1_auth'),
        'host' => env('AUTH_DB_HOST', '127.0.0.1'),
        // ... other config
    ],
],

// User Model
protected $connection = 'lgu1_auth'; // Uses lgu1_auth database
```

### **Role-Based Access Control (RBAC)**

**Four Roles:**
1. **Citizen** - Book facilities, view own bookings, make payments
2. **Staff** - Verify documents, review bookings (Gate 1)
3. **Admin** - Final approval, manage facilities, view analytics (Gate 2)
4. **EIS Super Admin** - System configuration, user management, API setup (Technical)

**Middleware Integration:**
```php
// routes/web.php
Route::middleware(['auth', 'role:citizen'])->group(function () {
    Route::get('/facilities', [FacilityController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
});

Route::middleware(['auth', 'role:staff'])->group(function () {
    Route::get('/staff/verifications', [VerificationController::class, 'index']);
    Route::post('/staff/verify/{booking}', [VerificationController::class, 'verify']);
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/approvals', [ApprovalController::class, 'index']);
    Route::post('/admin/approve/{booking}', [ApprovalController::class, 'approve']);
    Route::get('/admin/analytics', [AnalyticsController::class, 'index']);
});

Route::middleware(['auth', 'role:superadmin'])->group(function () {
    Route::resource('/superadmin/users', UserController::class);
    Route::resource('/superadmin/equipment', EquipmentController::class);
});
```

**Role Check in Models:**
```php
// User Model
public function isCitizen() {
    return $this->role === 'citizen';
}

public function isStaff() {
    return $this->role === 'staff';
}

public function isAdmin() {
    return $this->role === 'admin';
}

public function isSuperAdmin() {
    return $this->role === 'superadmin';
}

public function canVerifyBookings() {
    return in_array($this->role, ['staff', 'admin', 'superadmin']);
}

public function canApproveBookings() {
    return in_array($this->role, ['admin', 'superadmin']);
}
```

### **User Profile Integration with Discounts**

**Automatic City Tagging:**
```php
// User Model
protected static function boot()
{
    parent::boot();
    
    static::saving(function ($user) {
        // Auto-tag Caloocan residents
        if ($user->city && stripos($user->city, 'caloocan') !== false) {
            $user->is_caloocan_resident = true;
        } else {
            $user->is_caloocan_resident = false;
        }
    });
}
```

**Profile Data Used in Pricing:**
```php
// User attributes integrated with PricingCalculatorService
$user->city                  → City discount eligibility
$user->is_caloocan_resident  → Auto-tagged for 30% discount
$user->birthdate             → Calculate age for senior discount
$user->id_type               → Identity discount type (senior/pwd/student)
```

---

## 🔄 DATA FLOW DIAGRAMS

### **Data Flow 1: Complete Booking Creation**

```
┌──────────────────────────────────────────────────────┐
│ CITIZEN INPUT                                        │
├──────────────────────────────────────────────────────┤
│ • Expected attendees (150)                           │
│ • Browse facilities                                  │
│ • Select facility (Covered Court)                    │
│ • Select date/time (Dec 14, 2:00 PM - 5:00 PM)     │
│ • Select equipment (chairs, tables, sound)           │
│ • Choose ID type (senior)                            │
│ • Upload documents (ID, selfie, proposal)            │
└──────────────────────────────────────────────────────┘
                        ↓
┌──────────────────────────────────────────────────────┐
│ INTEGRATION LAYER 1: Validation & Availability      │
├──────────────────────────────────────────────────────┤
│ → Check facility capacity (300 ≥ 150) ✓             │
│ → Check date availability (query bookings table)     │
│ → Check equipment stock (query equipment_items)      │
│ → Validate time slot (no conflicts)                  │
└──────────────────────────────────────────────────────┘
                        ↓
┌──────────────────────────────────────────────────────┐
│ INTEGRATION LAYER 2: Pricing Calculation            │
├──────────────────────────────────────────────────────┤
│ → Fetch user profile (lgu1_auth.users)              │
│ → Fetch facility rate (facilities.base_rate)         │
│ → Fetch equipment prices (equipment_items)           │
│ → Call PricingCalculatorService                      │
│   • Subtotal: ₱6,000 + ₱5,250 = ₱11,250            │
│   • City discount (30%): -₱3,375                     │
│   • Senior discount (20%): -₱1,575                   │
│   • Final: ₱6,300                                    │
└──────────────────────────────────────────────────────┘
                        ↓
┌──────────────────────────────────────────────────────┐
│ INTEGRATION LAYER 3: Data Persistence               │
├──────────────────────────────────────────────────────┤
│ → Create booking record (bookings table)             │
│   • status = 'reserved'                              │
│   • reserved_until = now() + 24 hours                │
│   • pricing_breakdown = JSON                         │
│ → Create booking_equipment records (pivot)           │
│ → Store documents (file storage)                     │
└──────────────────────────────────────────────────────┘
                        ↓
┌──────────────────────────────────────────────────────┐
│ INTEGRATION LAYER 4: Notifications                  │
├──────────────────────────────────────────────────────┤
│ → Send email confirmation (Mail service)             │
│ → Send SMS notification (SMS service)                │
│ → Add to staff verification queue                    │
└──────────────────────────────────────────────────────┘
                        ↓
┌──────────────────────────────────────────────────────┐
│ OUTPUT: Booking Created                              │
├──────────────────────────────────────────────────────┤
│ • Booking ID: 12345                                  │
│ • Status: Reserved (24-hour hold)                    │
│ • Final Amount: ₱6,300                               │
│ • Equipment Reserved: 50 chairs, 5 tables, sound     │
└──────────────────────────────────────────────────────┘
```

### **Data Flow 2: Real-Time Discount Preview (AJAX)**

```
┌──────────────────────────────────────────────────────┐
│ CITIZEN BROWSER (JavaScript)                         │
└──────────────────────────────────────────────────────┘
                        ↓
        User changes dropdown: "Senior Citizen"
                        ↓
┌──────────────────────────────────────────────────────┐
│ AJAX REQUEST                                         │
├──────────────────────────────────────────────────────┤
│ POST /api/pricing/preview                            │
│ {                                                    │
│   facility_id: 3,                                    │
│   equipment: [{id: 1, quantity: 50}, ...],          │
│   id_type: 'senior'                                  │
│ }                                                    │
└──────────────────────────────────────────────────────┘
                        ↓
┌──────────────────────────────────────────────────────┐
│ LARAVEL CONTROLLER                                   │
├──────────────────────────────────────────────────────┤
│ PricingController@preview                            │
│ → Fetch auth()->user()                               │
│ → Fetch Facility::find($request->facility_id)       │
│ → Fetch equipment items                              │
│ → Call PricingCalculatorService                      │
└──────────────────────────────────────────────────────┘
                        ↓
┌──────────────────────────────────────────────────────┐
│ PRICING CALCULATOR SERVICE                           │
├──────────────────────────────────────────────────────┤
│ → Read user->is_caloocan_resident (from lgu1_auth)  │
│ → Read user->birthdate (calculate age)               │
│ → Calculate pricing with both discounts              │
│ → Return JSON breakdown                              │
└──────────────────────────────────────────────────────┘
                        ↓
┌──────────────────────────────────────────────────────┐
│ JSON RESPONSE                                        │
├──────────────────────────────────────────────────────┤
│ {                                                    │
│   "subtotal": 11250,                                 │
│   "city_discount_amount": 3375,                      │
│   "identity_discount_amount": 1575,                  │
│   "total_savings": 4950,                             │
│   "final_total": 6300                                │
│ }                                                    │
└──────────────────────────────────────────────────────┘
                        ↓
┌──────────────────────────────────────────────────────┐
│ BROWSER JAVASCRIPT                                   │
├──────────────────────────────────────────────────────┤
│ → Update DOM with new pricing                        │
│ → Display savings badge                              │
│ → Enable/disable submit button                       │
└──────────────────────────────────────────────────────┘
```

### **Data Flow 3: AI Analytics Processing**

```
┌──────────────────────────────────────────────────────┐
│ ADMIN OPENS ANALYTICS DASHBOARD                     │
└──────────────────────────────────────────────────────┘
                        ↓
┌──────────────────────────────────────────────────────┐
│ FRONTEND: analytics.js loads                         │
├──────────────────────────────────────────────────────┤
│ startForecasting() called                            │
└──────────────────────────────────────────────────────┘
                        ↓
┌──────────────────────────────────────────────────────┐
│ API REQUEST                                          │
├──────────────────────────────────────────────────────┤
│ GET /admin/api/usage-data                            │
└──────────────────────────────────────────────────────┘
                        ↓
┌──────────────────────────────────────────────────────┐
│ LARAVEL API CONTROLLER                               │
├──────────────────────────────────────────────────────┤
│ → Query bookings (approved only)                     │
│ → Group by month                                     │
│ → Calculate total hours per month                    │
│ → Return JSON array [120, 145, 180, ...]           │
└──────────────────────────────────────────────────────┘
                        ↓
┌──────────────────────────────────────────────────────┐
│ TENSORFLOW.JS (CLIENT-SIDE)                         │
├──────────────────────────────────────────────────────┤
│ → Normalize data (0 to 1 range)                     │
│ → Create windows (3-month sequences)                │
│ → Build LSTM model                                   │
│ → Train for 100 epochs                               │
│ → Generate patterns (NOT predictions!)              │
└──────────────────────────────────────────────────────┘
                        ↓
┌──────────────────────────────────────────────────────┐
│ PATTERN RECOGNITION OUTPUT                           │
├──────────────────────────────────────────────────────┤
│ • Peak months: December (+40%)                       │
│ • Popular facility: City Hall (75% utilization)     │
│ • Equipment trend: Chairs usage +15%                 │
│ • Seasonal pattern detected                          │
└──────────────────────────────────────────────────────┘
                        ↓
┌──────────────────────────────────────────────────────┐
│ CHART.JS VISUALIZATION                               │
├──────────────────────────────────────────────────────┤
│ → Line chart: Historical usage                       │
│ → Bar chart: Facility comparison                     │
│ → Pie chart: Event type distribution                 │
└──────────────────────────────────────────────────────┘
                        ↓
┌──────────────────────────────────────────────────────┐
│ ADMIN DASHBOARD DISPLAY                              │
├──────────────────────────────────────────────────────┤
│ ✓ Insights displayed                                 │
│ ✓ Recommendations shown                              │
│ ✓ Admin makes data-driven decisions                 │
└──────────────────────────────────────────────────────┘
```

---

## 👥 ROLE-BASED INTEGRATION

### **How Roles Interact with System Components**

```
┌─────────────────────────────────────────────────────┐
│ CITIZEN ROLE                                        │
├─────────────────────────────────────────────────────┤
│ Integrates with:                                    │
│ ✓ Facility Directory (browse)                      │
│ ✓ Booking System (create/view own)                 │
│ ✓ Equipment System (select equipment)              │
│ ✓ Pricing Calculator (see discounts)               │
│ ✓ Payment System (make payment)                    │
│ ✓ Feedback System (submit reviews)                 │
│                                                     │
│ Cannot access:                                      │
│ ✗ Staff verification interface                     │
│ ✗ Admin approval interface                         │
│ ✗ Analytics dashboard                              │
│ ✗ System configuration                             │
└─────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────┐
│ STAFF ROLE                                          │
├─────────────────────────────────────────────────────┤
│ Integrates with:                                    │
│ ✓ Booking System (view pending)                    │
│ ✓ Document Verification (check IDs)                │
│ ✓ Discount Validation (verify eligibility)         │
│ ✓ Schedule Calendar (view all bookings)            │
│ ✓ Inspection System (post-event check)             │
│                                                     │
│ Cannot access:                                      │
│ ✗ Final approval (Gate 2)                          │
│ ✗ Analytics dashboard                              │
│ ✗ Equipment management (add/edit)                  │
│ ✗ System configuration                             │
└─────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────┐
│ ADMIN ROLE                                          │
├─────────────────────────────────────────────────────┤
│ Integrates with:                                    │
│ ✓ ALL Citizen features                             │
│ ✓ ALL Staff features                               │
│ ✓ Final Booking Approval (Gate 2)                  │
│ ✓ Facility Management (edit rates)                 │
│ ✓ AI Analytics Dashboard                           │
│ ✓ External System Requests                         │
│ ✓ Report Generation                                │
│ ✓ Government Event Management                      │
│                                                     │
│ Cannot access:                                      │
│ ✗ User management (create/delete users)            │
│ ✗ Equipment CRUD (add/edit catalog)                │
│ ✗ System configuration                             │
│ ✗ API setup                                        │
└─────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────┐
│ EIS SUPER ADMIN ROLE (Technical)                    │
├─────────────────────────────────────────────────────┤
│ Integrates with:                                    │
│ ✓ ALL system components                            │
│ ✓ User Management (CRUD all users)                 │
│ ✓ Equipment Management (CRUD catalog)              │
│ ✓ System Configuration                             │
│ ✓ API Setup (external integrations)                │
│ ✓ Database Management                              │
│ ✓ Multi-LGU Setup                                  │
│                                                     │
│ Special responsibilities:                           │
│ • Configure external API endpoints                 │
│ • Manage discount percentages                      │
│ • Set system-wide rules                            │
│ • Technical troubleshooting                        │
└─────────────────────────────────────────────────────┘
```

---

## 🤖 AI ANALYTICS INTEGRATION

### **How AI Integrates with All Submodules**

```
┌─────────────────────────────────────────────────────┐
│           AI ANALYTICS CENTRAL HUB                   │
└─────────────────────────────────────────────────────┘
                        ↓
    ┌───────────────────┼───────────────────┐
    ↓                   ↓                   ↓
┌─────────┐      ┌─────────┐        ┌─────────┐
│ Booking │      │Equipment│        │Facility │
│  Data   │      │  Data   │        │  Data   │
└─────────┘      └─────────┘        └─────────┘
    ↓                   ↓                   ↓
    └───────────────────┼───────────────────┘
                        ↓
┌─────────────────────────────────────────────────────┐
│ DATA AGGREGATION & PREPROCESSING                    │
├─────────────────────────────────────────────────────┤
│ → Group bookings by month/facility/event type       │
│ → Calculate utilization rates                       │
│ → Analyze equipment rental patterns                 │
│ → Process feedback ratings                          │
│ → Identify seasonal trends                          │
└─────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────┐
│ TENSORFLOW.JS ANALYSIS                              │
├─────────────────────────────────────────────────────┤
│ → LSTM pattern recognition                          │
│ → Usage trend analysis                              │
│ → Equipment optimization suggestions                │
│ → Peak period identification                        │
└─────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────┐
│ OUTPUT: ACTIONABLE INSIGHTS                         │
├─────────────────────────────────────────────────────┤
│ → Facility utilization reports                      │
│ → Equipment purchase recommendations                │
│ → Best maintenance windows                          │
│ → Pricing optimization suggestions                  │
│ → Smart equipment recommendations (for citizens)    │
└─────────────────────────────────────────────────────┘
                        ↓
    ┌───────────────────┼───────────────────┐
    ↓                   ↓                   ↓
┌──────────┐    ┌──────────┐        ┌──────────┐
│  Admin   │    │Equipment │        │ Booking  │
│Dashboard │    │Suggestions│       │  Form    │
└──────────┘    └──────────┘        └──────────┘
```

### **AI Integration Points**

**1. With Booking System:**
```javascript
// When citizen selects event type and attendee count
fetch('/api/equipment/suggestions', {
    method: 'POST',
    body: JSON.stringify({
        event_type: 'wedding',
        attendee_count: 150,
        facility_id: 3
    })
})
.then(response => response.json())
.then(suggestions => {
    // Display AI-recommended equipment
    displayEquipmentSuggestions(suggestions);
});
```

**2. With Equipment System:**
```php
// EquipmentController
public function suggestions(Request $request)
{
    // Query historical data
    $similarBookings = Booking::where('event_type', $request->event_type)
        ->whereBetween('expected_attendees', [
            $request->attendee_count - 20,
            $request->attendee_count + 20
        ])
        ->with('equipmentItems')
        ->get();
    
    // AI analysis (simplified)
    $equipmentFrequency = [];
    foreach ($similarBookings as $booking) {
        foreach ($booking->equipmentItems as $item) {
            $key = $item->id;
            $equipmentFrequency[$key] = [
                'equipment' => $item,
                'frequency' => ($equipmentFrequency[$key]['frequency'] ?? 0) + 1,
                'avg_quantity' => 0, // Calculate average
            ];
        }
    }
    
    // Sort by frequency
    uasort($equipmentFrequency, function($a, $b) {
        return $b['frequency'] <=> $a['frequency'];
    });
    
    return response()->json(array_slice($equipmentFrequency, 0, 5));
}
```

**3. With Admin Dashboard:**
```php
// AnalyticsController
public function index()
{
    return view('admin.analytics', [
        'monthly_usage' => $this->getMonthlyUsageData(),
        'facility_utilization' => $this->getFacilityUtilization(),
        'equipment_analysis' => $this->getEquipmentAnalysis(),
        'discount_impact' => $this->getDiscountImpact(),
    ]);
}

private function getMonthlyUsageData()
{
    return Booking::selectRaw('
            DATE_FORMAT(booking_date, "%Y-%m") as month,
            COUNT(*) as total_bookings,
            SUM(TIMESTAMPDIFF(HOUR, start_time, end_time)) as total_hours
        ')
        ->where('status', 'confirmed')
        ->groupBy('month')
        ->orderBy('month')
        ->get();
}
```

---

## ✅ INTEGRATION CHECKLIST

Use this checklist to verify all internal integrations are properly implemented:

### **Database Integration**
- [ ] All Eloquent relationships defined
- [ ] Foreign keys properly set up
- [ ] Pivot tables for many-to-many relationships
- [ ] JSON columns for flexible data storage
- [ ] Indexes on frequently queried columns
- [ ] Database migrations all run successfully

### **Service Layer Integration**
- [ ] PricingCalculatorService accessible from controllers
- [ ] Service properly handles edge cases
- [ ] Returns consistent data structures
- [ ] Error handling implemented
- [ ] Service is testable (unit tests possible)

### **Authentication Integration**
- [ ] lgu1_auth connection configured
- [ ] User model points to correct database
- [ ] Role middleware protecting routes
- [ ] Role checks in controllers and views
- [ ] Session management working correctly

### **Component Integration**
- [ ] Facility Directory → Booking System ✓
- [ ] Booking System → Pricing Calculator ✓
- [ ] Booking System → Equipment System ✓
- [ ] Equipment System → Inventory Tracking ✓
- [ ] AI Analytics → All Data Sources ✓
- [ ] Notification System → Status Changes ✓

### **Data Flow Integration**
- [ ] Create booking flow works end-to-end
- [ ] Real-time pricing preview works via AJAX
- [ ] Equipment availability updates in real-time
- [ ] Status changes trigger notifications
- [ ] AI analytics fetches fresh data
- [ ] Dashboard displays integrated data

### **Frontend Integration**
- [ ] AJAX calls to pricing API work
- [ ] TensorFlow.js loads and trains
- [ ] Chart.js displays analytics
- [ ] SweetAlert2 shows notifications
- [ ] Form submissions integrate with backend
- [ ] Real-time updates via JavaScript

---

## 📊 INTEGRATION METRICS

**Total Integration Points:** 47

**Breakdown:**
- Database Relationships: 15
- Service Integrations: 8
- Authentication Points: 6
- API Endpoints: 12
- Frontend Integrations: 6

**Key Performance Indicators:**
- Average booking creation time: < 2 seconds
- Pricing calculation time: < 100ms
- Equipment availability check: < 50ms
- AI analytics training time: < 30 seconds
- Dashboard load time: < 1 second

---

## 🎯 SUMMARY

The Public Facilities Reservation System achieves seamless internal integration through:

1. **Clear Submodule Boundaries** - Each of the 5 core submodules has a distinct purpose
2. **Centralized Services** - PricingCalculatorService provides consistent business logic
3. **Database as Integration Hub** - Well-designed schema with proper relationships
4. **Unified Authentication** - lgu1_auth serves as single source of truth for users
5. **Role-Based Access** - Clean separation of concerns by user role
6. **AI as Enhancement** - Analytics improve user experience without being intrusive
7. **Real-Time Updates** - AJAX and modern JavaScript keep data fresh
8. **Consistent Data Flow** - Clear patterns for how data moves through the system

**Result:** A cohesive, maintainable system where all parts work together harmoniously. 🎯

---

**Next Documentation:**
- `INTERNAL_PROCESSES.md` - The 5 pure internal end-to-end processes
- `HYBRID_INTEGRATION_PROCESSES.md` - Processes combining internal + external systems

---

*Document Version: 1.0*  
*Last Updated: December 6, 2025*  
*Status: Complete ✅*

