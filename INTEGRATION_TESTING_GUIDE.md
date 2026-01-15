# 🧪 ENERGY EFFICIENCY INTEGRATION - TESTING GUIDE

**Last Updated:** January 9, 2026  
**Purpose:** Test the UI before connecting to actual Energy Efficiency database

---

## 📦 WHAT I'VE CREATED

### **1. Database Tables (In YOUR system)**
These tables store government program data in our facilities database:

- ✅ `government_program_bookings` - Main table for imported seminars
- ✅ `suppliers` - Supplier database for transparency
- ✅ `supplier_products` - Product catalog with prices
- ✅ `liquidation_items` - Post-event spending records
- ✅ `citizen_program_registrations` - Citizen registrations

### **2. Models**
- ✅ `GovernmentProgramBooking.php`
- ✅ `Supplier.php`
- ✅ `SupplierProduct.php`
- ✅ `LiquidationItem.php`
- ✅ `CitizenProgramRegistration.php`

### **3. Admin Controller**
- ✅ `Admin\GovernmentProgramController.php`
  - Import dashboard (view seminars from Energy Efficiency DB)
  - Import single seminar
  - Import bulk seminars
  - Manage programs (index, show)
  - Assign facilities
  - Update coordination status
  - Sync attendance

### **4. Admin UI (Landing Point)**
- ✅ `/admin/government-programs/import` - **Import Dashboard** (main landing page)
- ✅ `/admin/government-programs` - Manage imported programs
- ✅ `/admin/government-programs/{id}` - View/coordinate single program

### **5. Routes**
- ✅ Added 8 new routes in `routes/web.php` (lines 1208-1215)

---

## 🧪 PHASE 1: TESTING WITHOUT ENERGY EFFICIENCY DATABASE

### **Step 1: Run Migrations**

```bash
cd c:\laragon\www\local-government-unit-1-ph.com
php artisan migrate
```

This creates all the new tables in your `lgu1_facilities` database.

---

### **Step 2: Insert Test Data (Backdoor)**

Open phpMyAdmin and run this SQL to create test government program data:

```sql
-- Switch to your facilities database
USE lgu1_facilities;

-- Insert a test government program booking (simulating imported seminar)
INSERT INTO government_program_bookings (
    source_system,
    source_seminar_id,
    source_database,
    organizer_user_id,
    organizer_name,
    organizer_contact,
    organizer_email,
    organizer_area,
    program_title,
    program_type,
    program_description,
    event_date,
    start_time,
    end_time,
    expected_attendees,
    requested_location,
    coordination_status,
    assigned_admin_id,
    created_at,
    updated_at
) VALUES (
    'Energy Efficiency',                    -- source_system
    '28',                                   -- source_seminar_id (links to their seminar_id)
    'ener_nova_capri',                      -- source_database
    '82',                                   -- organizer_user_id (from their users table)
    'Christian Arnaldo Cando',              -- organizer_name
    '9085919898',                           -- organizer_contact
    'piyasigno@gmail.com',                  -- organizer_email
    'AREA 4',                               -- organizer_area
    'Energy Conservation Awareness Seminar', -- program_title
    'seminar',                              -- program_type
    'A seminar focused on teaching residents how to reduce electricity consumption and use energy-efficient appliances.', -- program_description
    '2026-01-23',                           -- event_date
    '20:00:00',                             -- start_time
    '22:00:00',                             -- end_time
    150,                                    -- expected_attendees
    'Multi-Purpose Hall',                   -- requested_location
    'pending_review',                       -- coordination_status
    NULL,                                   -- assigned_admin_id (you'll assign in UI)
    NOW(),
    NOW()
);

-- Insert another test program
INSERT INTO government_program_bookings (
    source_system, source_seminar_id, source_database, organizer_user_id,
    organizer_name, organizer_contact, organizer_email, organizer_area,
    program_title, program_type, program_description,
    event_date, start_time, end_time, expected_attendees,
    requested_location, coordination_status, created_at, updated_at
) VALUES (
    'Energy Efficiency', '29', 'ener_nova_capri', '82',
    'Christian Arnaldo Cando', '9085919898', 'piyasigno@gmail.com', 'AREA 1',
    'Renewable Energy Workshop',
    'workshop',
    'Hands-on workshop teaching residents about solar energy and renewable resources available in the Philippines.',
    '2026-02-15', '14:00:00', '17:00:00', 100,
    'Community Center', 'pending_review', NOW(), NOW()
);

-- Insert test suppliers (for transparency reports later)
INSERT INTO suppliers (
    supplier_name, supplier_type, contact_person, contact_phone,
    business_address, is_active, is_verified, created_at, updated_at
) VALUES
('Jollibee - Caloocan Branch', 'food_service', 'Store Manager', '09171234567',
 'Caloocan City, Metro Manila', 1, 1, NOW(), NOW()),
('PrintHub Caloocan', 'printing', 'Juan Dela Cruz', '09181234567',
 'Caloocan City, Metro Manila', 1, 1, NOW(), NOW());

-- Insert test products
INSERT INTO supplier_products (
    supplier_id, product_code, product_name, product_category,
    specifications, unit_of_measure, current_price, price_effective_date,
    is_available, created_at, updated_at
) VALUES
(1, 'C1', 'Chickenjoy Meal with Rice', 'meal',
 '{"includes": ["1pc Chickenjoy", "Rice"], "size": "regular"}',
 'meal', 89.00, '2026-01-01', 1, NOW(), NOW()),
(1, 'C3', 'Jolly Spaghetti with Chickenjoy', 'meal',
 '{"includes": ["Spaghetti", "1pc Chickenjoy"], "size": "regular"}',
 'meal', 115.00, '2026-01-01', 1, NOW(), NOW()),
(2, 'PRINT-COLOR-A4', 'Color Printing A4', 'printing',
 '{"paper": "bond paper", "quality": "high"}',
 'page', 15.00, '2026-01-01', 1, NOW(), NOW());
```

---

### **Step 3: Test the UI**

Now visit these URLs in your browser:

#### **A. Import Dashboard (Main Landing Page)**
```
http://localhost/local-government-unit-1-ph.com/admin/government-programs/import
```

**What you should see:**
- Connection status (will show "Connection Failed" since we haven't set up the Energy Efficiency DB yet)
- This is EXPECTED for now - we're testing the UI structure first

#### **B. Manage Programs Dashboard**
```
http://localhost/local-government-unit-1-ph.com/admin/government-programs
```

**What you should see:**
- List of 2 test programs you inserted
- Status badges (Pending Review)
- Event dates and times
- Organizer information
- "View Details" buttons

#### **C. View Single Program**
```
http://localhost/local-government-unit-1-ph.com/admin/government-programs/1
```

**What you should see:**
- Full program details
- Organizer information
- Requested location
- Expected attendees
- Dropdown to assign facility
- Status update form
- Coordination workflow tabs

---

### **Step 4: Test Admin Features**

**Test assigning a facility:**
1. Go to program detail page
2. Select a facility from dropdown
3. Click "Assign Facility"
4. Should update status to "facility_assigned"
5. Fee should be automatically waived (₱0.00)

**Test updating coordination status:**
1. Use status dropdown
2. Add coordination notes
3. Click "Update Status"
4. Should reflect new status

---

## 🔌 PHASE 2: CONNECTING TO ENERGY EFFICIENCY DATABASE

### **Step 1: Import Their Database**

1. Open phpMyAdmin
2. Create new database: `ener_nova_capri`
3. Import the SQL file they provided: `database/ener_nova_capri.sql`
4. Verify tables exist: `seminars`, `users`, `attendance`, `seminar_joins`

---

### **Step 2: Configure Database Connection**

Add to your `.env` file:

```env
# Energy Efficiency Database Connection
EE_DB_HOST=127.0.0.1
EE_DB_PORT=3306
EE_DB_DATABASE=ener_nova_capri
EE_DB_USERNAME=root
EE_DB_PASSWORD=
```

Add to `config/database.php` in the `connections` array:

```php
'energy_efficiency' => [
    'driver' => 'mysql',
    'host' => env('EE_DB_HOST', '127.0.0.1'),
    'port' => env('EE_DB_PORT', '3306'),
    'database' => env('EE_DB_DATABASE', 'ener_nova_capri'),
    'username' => env('EE_DB_USERNAME', 'root'),
    'password' => env('EE_DB_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
    'engine' => null,
],
```

---

### **Step 3: Test Connection**

Visit the import dashboard again:
```
http://localhost/local-government-unit-1-ph.com/admin/government-programs/import
```

**Now you should see:**
- ✅ "Connected to ener_nova_capri" status
- List of seminars from THEIR database
- "Energy Conservation Awareness" seminar (seminar_id = 28)
- Import buttons for each seminar
- "Import All" bulk button

---

### **Step 4: Test Import**

1. Click "Import" on a seminar
2. System fetches data from `ener_nova_capri.seminars`
3. System fetches organizer from `ener_nova_capri.users`
4. System creates record in YOUR `government_program_bookings` table
5. Redirects to program detail page
6. Now you can assign facility, coordinate, etc.

---

## 📊 DATA FLOW DIAGRAM

```
┌─────────────────────────────────────────────────────────┐
│                 Energy Efficiency System                 │
│                 (ener_nova_capri DB)                    │
└─────────────────────────────────────────────────────────┘
                         │
                         │ READ ONLY
                         │ (We fetch their data)
                         ▼
┌─────────────────────────────────────────────────────────┐
│           Admin Import Dashboard (Landing Page)          │
│     /admin/government-programs/import                   │
│                                                          │
│  ✅ Shows seminars from their DB                         │
│  ✅ Shows organizer info from their users table          │
│  ✅ Shows registration count from seminar_joins          │
└─────────────────────────────────────────────────────────┘
                         │
                         │ IMPORT (Copy to our DB)
                         ▼
┌─────────────────────────────────────────────────────────┐
│           Public Facilities System                       │
│           (lgu1_facilities DB)                          │
│                                                          │
│  government_program_bookings table                      │
│  ├─ source_seminar_id (links to their seminar_id)      │
│  ├─ organizer info (copied)                             │
│  ├─ event details (copied)                              │
│  └─ assigned_facility_id (WE assign)                    │
└─────────────────────────────────────────────────────────┘
                         │
                         │ MANAGE & COORDINATE
                         ▼
┌─────────────────────────────────────────────────────────┐
│       Admin Coordination Workflow                        │
│     /admin/government-programs/{id}                     │
│                                                          │
│  ✅ Assign facility (free)                               │
│  ✅ Coordinate with organizer                            │
│  ✅ Request funds from Finance                           │
│  ✅ Confirm speakers                                     │
│  ✅ Publish transparency reports                         │
└─────────────────────────────────────────────────────────┘
                         │
                         │ AFTER EVENT
                         ▼
┌─────────────────────────────────────────────────────────┐
│       Sync Back Attendance                               │
│     /admin/government-programs/{id}/sync-attendance     │
│                                                          │
│  READ from their attendance table                       │
│  └─ Store actual_attendees in our table                 │
└─────────────────────────────────────────────────────────┘
```

---

## 🎯 WHAT EACH PAGE DOES

### **1. Import Dashboard** (`/admin/government-programs/import`)
**Purpose:** Landing page that shows seminars from Energy Efficiency database

**What it displays:**
- Connection status to `ener_nova_capri`
- "Available Seminars" section - Shows seminars NOT yet imported
- "Already Imported" section - Shows seminars already in our system
- Import buttons for each seminar
- Bulk import button

**Data Sources:**
- `ener_nova_capri.seminars` (their database) - for seminar list
- `lgu1_facilities.government_program_bookings` (our database) - to check what's imported

---

### **2. Manage Programs** (`/admin/government-programs`)
**Purpose:** Admin dashboard to view all imported government programs

**What it displays:**
- All records from `government_program_bookings` table
- Coordination status
- Assigned facilities
- Event dates
- Quick actions

**Data Sources:**
- `lgu1_facilities.government_program_bookings` (our database only)

---

### **3. Program Details** (`/admin/government-programs/{id}`)
**Purpose:** Full coordination workflow for a single program

**What it displays:**
- Program details (from our DB)
- Original seminar data (fetched from their DB in real-time)
- Organizer info (fetched from their DB)
- Registration list (fetched from their `seminar_joins`)
- Facility assignment form
- Status update form
- Sync attendance button

**Data Sources:**
- `lgu1_facilities.government_program_bookings` (our DB - main data)
- `ener_nova_capri.seminars` (their DB - reference)
- `ener_nova_capri.users` (their DB - organizer details)
- `ener_nova_capri.seminar_joins` (their DB - registrations)

---

## ✅ TESTING CHECKLIST

### **Phase 1: UI Testing (Without Connection)**
- [ ] Run migrations successfully
- [ ] Insert test data via SQL
- [ ] Visit `/admin/government-programs`
- [ ] See 2 test programs listed
- [ ] Click "View Details" on a program
- [ ] Assign a facility from dropdown
- [ ] Update coordination status
- [ ] Verify data persists in database

### **Phase 2: Integration Testing (With Connection)**
- [ ] Import `ener_nova_capri.sql` to phpMyAdmin
- [ ] Add database connection to `.env`
- [ ] Add connection config to `config/database.php`
- [ ] Visit `/admin/government-programs/import`
- [ ] See "Connected" status
- [ ] See seminars from their database
- [ ] Import a single seminar
- [ ] Verify it appears in "Manage Programs"
- [ ] View details and see data from both databases
- [ ] Test bulk import
- [ ] Test sync attendance button

---

## 🚨 TROUBLESHOOTING

### **"Connection Failed" Error**
✅ **Expected during Phase 1 testing** - This is normal before setting up the Energy Efficiency DB connection.

To fix for Phase 2:
1. Make sure `ener_nova_capri` database exists in phpMyAdmin
2. Check `.env` file has correct credentials
3. Run `php artisan config:clear`

### **"Table not found" Error**
Run: `php artisan migrate`

### **Routes Not Found**
Run: `php artisan route:clear`

### **Class Not Found**
Run: `php artisan optimize:clear`

---

## 📝 NEXT STEPS

After testing the basic import/display UI:

1. ✅ Build transparency report UI (pre-event and post-event)
2. ✅ Build liquidation report UI
3. ✅ Add citizen registration features
4. ✅ Add supplier management (for Super Admin)
5. ✅ Add quotation management
6. ✅ Build full coordination workflow tabs

---

**Your UI is ready for backdoor testing!** 🎉

Start with Phase 1 to test the interface with manual data, then move to Phase 2 when ready to connect to their actual database.

