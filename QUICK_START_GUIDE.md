# 🚀 Quick Start Guide - LGU1 Reservation System

## ⚡ GET STARTED IN 3 MINUTES

### Step 1: Start Your Database (30 seconds)
1. Open **Laragon**
2. Click **Start All**
3. Verify MySQL is running (green indicator)

### Step 2: Run Migrations (1 minute)
Open PowerShell/Terminal in your project:

```bash
cd C:\laragon\www\local-government-unit-1-ph.com\lgu1-reservation-system
php artisan migrate
```

✅ **Expected Output**: `Migration completed successfully`

### Step 3: Seed Equipment Data (30 seconds)
```bash
php artisan db:seed --class=EquipmentItemsSeeder
```

✅ **Expected Output**: `✅ Equipment items seeded successfully! Total: 25 items`

---

## 🧪 TESTING THE NEW FEATURES

### Test 1: Pricing Calculator

```bash
php artisan tinker
```

```php
// Create test user
$user = new \App\Models\User();
$user->name = 'Juan Dela Cruz';
$user->email = 'juan@test.com';
$user->city = 'Caloocan City'; // Will auto-tag as Caloocan resident
$user->save();

// Get a facility
$facility = \App\Models\Facility::first();

// Test discount calculation
$calculator = new \App\Services\PricingCalculatorService();

// Test 1: Caloocan Senior Citizen
$pricing = $calculator->calculateBookingPrice($user, $facility, [], 'senior');
print_r($pricing);

// You should see:
// - City Discount: 30%
// - Identity Discount: 20%
// - Total Savings: around ₱2,200 on ₱5,000 facility

// Test 2: Non-resident Student
$user2 = new \App\Models\User();
$user2->name = 'Maria Santos';
$user2->email = 'maria@test.com';
$user2->city = 'Manila'; // NOT Caloocan
$user2->save();

$pricing2 = $calculator->calculateBookingPrice($user2, $facility, [], 'student');
print_r($pricing2);

// You should see:
// - City Discount: 0%
// - Identity Discount: 20%
// - Total Savings: ₱1,000 on ₱5,000 facility
```

### Test 2: Equipment Items

```php
// Check equipment count
\App\Models\EquipmentItem::count(); // Should be 25

// Get chairs
$chairs = \App\Models\EquipmentItem::category('chairs')->get();
print_r($chairs->toArray());

// Get sound systems
$sound = \App\Models\EquipmentItem::category('sound_system')->get();
print_r($sound->toArray());

// Check pricing
$chair = \App\Models\EquipmentItem::where('name', 'LIKE', '%Monobloc%')->first();
echo "Monobloc Chair Price: ₱" . $chair->price_per_unit;
```

### Test 3: New Booking Statuses

```php
// Get a booking
$booking = \App\Models\Booking::first();

// Test new status methods
$booking->status = 'reserved';
$booking->save();

// Check status methods
echo $booking->isReserved(); // true
echo $booking->isTentative(); // false
echo $booking->isConfirmed(); // false

// Set reserved_until
$booking->reserved_until = now()->addHours(24);
$booking->save();

// Check if within reserved period
echo $booking->isWithinReservedPeriod(); // true
```

### Test 4: City Residency Auto-Tagging

```php
// Test various city formats
$test = new \App\Models\User();
$test->name = 'Test User';
$test->email = 'test@test.com';

// Should be true
$test->city = 'Caloocan City';
$test->save();
echo $test->is_caloocan_resident; // 1 (true)

// Should still be true (case-insensitive)
$test->city = 'CALOOCAN';
$test->save();
echo $test->is_caloocan_resident; // 1 (true)

// Should be false
$test->city = 'Quezon City';
$test->save();
echo $test->is_caloocan_resident; // 0 (false)
```

---

## 📊 DATABASE VERIFICATION

### Check Created Tables

```bash
php artisan tinker
```

```php
// List all tables
DB::select('SHOW TABLES');

// You should see these NEW tables:
// - equipment_items
// - booking_equipment
// - lgu_cities
// - maintenance_requests_sent
// - maintenance_schedules_received
// - usage_reports_sent
// - energy_reports_received
// - external_projects
// - event_schedules_sent
// - road_maintenance_received
// - treasurer_webhooks
// - treasurer_sync_log
```

### Check New Columns in Existing Tables

```php
// Check bookings table columns
$columns = DB::select('DESCRIBE bookings');
print_r($columns);

// Look for these NEW columns:
// - selected_id_type
// - subtotal
// - equipment_total
// - city_discount_percentage
// - city_discount_amount
// - identity_discount_type
// - identity_discount_percentage
// - identity_discount_amount
// - total_savings
// - pricing_breakdown
// - id_verified
// - admin_approved_by
// - reserved_until

// Check users table columns
$userColumns = DB::select('DESCRIBE users');
print_r($userColumns);

// Look for these NEW columns:
// - is_caloocan_resident

// Check payment_slips table
$paymentColumns = DB::select('DESCRIBE payment_slips');
print_r($paymentColumns);

// Look for these NEW columns:
// - payment_gateway
// - gateway_transaction_id
// - treasurer_reference
// - or_number
// - treasurer_status
```

---

## 🎨 VISUAL VERIFICATION (phpMyAdmin)

1. Open **http://localhost/phpmyadmin** in your browser
2. Select database: **`public_reservation_db`**
3. Check these tables exist:

**New Tables**:
- ✅ equipment_items (should have 25 rows)
- ✅ booking_equipment (pivot table)
- ✅ lgu_cities
- ✅ maintenance_requests_sent
- ✅ maintenance_schedules_received
- ✅ usage_reports_sent
- ✅ energy_reports_received
- ✅ external_projects
- ✅ event_schedules_sent
- ✅ road_maintenance_received
- ✅ treasurer_webhooks
- ✅ treasurer_sync_log

**Updated Tables** (check Structure tab):
- ✅ bookings - should have 40+ columns now
- ✅ users - should have `city` and `is_caloocan_resident` columns
- ✅ payment_slips - should have 12 new payment integration columns

---

## 🔧 TROUBLESHOOTING

### Problem: Migration Fails

**Error**: `Syntax error or access violation`

**Solution**:
```bash
# Rollback last migration
php artisan migrate:rollback

# Try again
php artisan migrate
```

---

### Problem: "Class EquipmentItem not found"

**Solution**:
```bash
# Regenerate autoload
composer dump-autoload

# Try again
php artisan db:seed --class=EquipmentItemsSeeder
```

---

### Problem: Database Connection Refused

**Solution**:
1. Check Laragon is running
2. Check MySQL port in `.env` (should be 3306 or 3307)
3. Restart Laragon

---

### Problem: PricingCalculatorService Not Found in Tinker

**Solution**:
```bash
composer dump-autoload
php artisan tinker
```

---

## ✅ SUCCESS CHECKLIST

After following this guide, you should have:

- ✅ 12 new database tables created
- ✅ 40+ new columns added to existing tables
- ✅ 25 equipment items in database
- ✅ PricingCalculatorService working in Tinker
- ✅ Auto-tagging of Caloocan residents working
- ✅ All models updated with new fields and relationships

---

## 🎯 WHAT TO DO NEXT

Now that the foundation is complete, you can:

1. **Build Controllers** - Update booking, payment, and equipment controllers
2. **Create UI Forms** - Build booking form with discount preview
3. **Staff Interface** - Create ID verification interface
4. **Admin Dashboard** - Enhance with new approval workflow
5. **Equipment Selection** - Add equipment selector to booking flow

See `IMPLEMENTATION_SUMMARY.md` for detailed next steps!

---

## 💡 PRO TIPS

### Tip 1: Keep Testing Data Separate
```bash
# Create a test seeder for demo data
php artisan make:seeder TestBookingsWithDiscountsSeeder
```

### Tip 2: Use Database Backups
```bash
# Before major changes
mysqldump -u root public_reservation_db > backup_before_changes.sql
```

### Tip 3: Check Migration Status
```bash
# See which migrations ran
php artisan migrate:status
```

### Tip 4: Fresh Start (if needed)
```bash
# WARNING: This deletes all data!
php artisan migrate:fresh --seed
```

---

**Ready to continue? Check `IMPLEMENTATION_SUMMARY.md` for the complete feature list!** 🚀

