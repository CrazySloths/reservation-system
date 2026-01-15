# 🗄️ DATABASE SCHEMA - PUBLIC FACILITIES RESERVATION SYSTEM

**Project:** LGU1 Public Facilities Reservation System  
**Database:** MySQL 8.0+  
**Created:** December 10, 2025  
**Last Updated:** December 10, 2025  
**Version:** 1.0

---

## 📋 TABLE OF CONTENTS

1. [Schema Overview](#schema-overview)
2. [Core Tables](#core-tables)
3. [Relationship Diagram](#relationship-diagram)
4. [Table Definitions](#table-definitions)
5. [Indexes](#indexes)
6. [Soft Deletes](#soft-deletes)
7. [Migration Order](#migration-order)

---

## 🎯 SCHEMA OVERVIEW

### **Total Tables: 23**

```
├─ USERS & AUTHENTICATION (3 tables)
│  ├─ users
│  ├─ otps
│  └─ password_resets
│
├─ LOCATIONS & FACILITIES (7 tables)
│  ├─ locations
│  ├─ facilities
│  ├─ facility_photos
│  ├─ facility_schedules
│  ├─ maintenance_schedules
│  ├─ equipment
│  └─ equipment_photos
│
├─ BOOKINGS & APPROVALS (7 tables)
│  ├─ bookings
│  ├─ booking_documents
│  ├─ booking_approvals
│  ├─ booking_notes
│  ├─ booking_equipment
│  ├─ booking_reminders
│  └─ cancellations
│
├─ PAYMENTS & RECEIPTS (3 tables)
│  ├─ payments
│  ├─ official_receipts
│  └─ discount_validations
│
├─ NOTIFICATIONS & LOGS (3 tables)
│  ├─ notifications
│  ├─ audit_logs
│  └─ activity_logs
```

---

## 🔗 RELATIONSHIP DIAGRAM

```
┌──────────────────────────────────────────────────────────────┐
│                    DATABASE RELATIONSHIPS                     │
└──────────────────────────────────────────────────────────────┘

USERS (1) ────────── (∞) BOOKINGS
  │                       │
  │                       ├─ (∞) BOOKING_DOCUMENTS
  │                       ├─ (∞) BOOKING_APPROVALS
  │                       ├─ (∞) BOOKING_NOTES
  │                       ├─ (∞) BOOKING_EQUIPMENT
  │                       ├─ (1) PAYMENTS
  │                       └─ (1) OFFICIAL_RECEIPTS
  │
  ├─ (∞) NOTIFICATIONS
  ├─ (∞) AUDIT_LOGS
  └─ (1) OTPS

LOCATIONS (1) ────── (∞) FACILITIES
                         │
                         ├─ (∞) FACILITY_PHOTOS
                         ├─ (∞) FACILITY_SCHEDULES
                         ├─ (∞) MAINTENANCE_SCHEDULES
                         ├─ (∞) EQUIPMENT
                         └─ (∞) BOOKINGS

EQUIPMENT (1) ────── (∞) EQUIPMENT_PHOTOS
              └────── (∞) BOOKING_EQUIPMENT

PAYMENTS (1) ────── (1) OFFICIAL_RECEIPTS
         └────── (1) DISCOUNT_VALIDATIONS
```

---

## 📊 TABLE DEFINITIONS

### **1. USERS TABLE**

**Purpose:** Stores all user accounts (Super Admin, Admin, Staff, Citizen)

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    
    -- Basic Information
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100) NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    
    -- Contact Information
    phone VARCHAR(20) NULL,
    address TEXT NULL,
    city VARCHAR(100) NULL,
    province VARCHAR(100) NULL,
    zip_code VARCHAR(10) NULL,
    
    -- Government IDs
    valid_id_type ENUM('national_id', 'drivers_license', 'passport', 'voters_id', 'postal_id', 'philsys_id') NULL,
    valid_id_number VARCHAR(100) NULL,
    valid_id_photo VARCHAR(255) NULL,
    
    -- Discount Eligibility
    is_pwd BOOLEAN DEFAULT FALSE,
    pwd_id_number VARCHAR(100) NULL,
    pwd_id_photo VARCHAR(255) NULL,
    
    is_senior_citizen BOOLEAN DEFAULT FALSE,
    senior_id_number VARCHAR(100) NULL,
    senior_id_photo VARCHAR(255) NULL,
    
    is_student BOOLEAN DEFAULT FALSE,
    student_id_number VARCHAR(100) NULL,
    student_id_photo VARCHAR(255) NULL,
    school_name VARCHAR(255) NULL,
    
    -- Role & Status
    role ENUM('superadmin', 'admin', 'staff', 'citizen') NOT NULL DEFAULT 'citizen',
    status ENUM('active', 'inactive', 'suspended', 'pending_verification') DEFAULT 'pending_verification',
    
    -- Location Assignment (for Admin/Staff)
    location_id BIGINT UNSIGNED NULL,
    
    -- Verification
    email_verified_at TIMESTAMP NULL,
    phone_verified_at TIMESTAMP NULL,
    
    -- Security
    remember_token VARCHAR(100) NULL,
    last_login_at TIMESTAMP NULL,
    last_login_ip VARCHAR(45) NULL,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    -- Indexes
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_status (status),
    INDEX idx_location_id (location_id),
    INDEX idx_deleted_at (deleted_at),
    
    -- Foreign Keys
    FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### **2. OTPS TABLE**

**Purpose:** Stores one-time passwords for login verification

```sql
CREATE TABLE otps (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    
    email VARCHAR(255) NOT NULL,
    otp_code VARCHAR(6) NOT NULL,
    
    -- Security
    attempts INT DEFAULT 0,
    is_used BOOLEAN DEFAULT FALSE,
    
    -- Expiry
    expires_at TIMESTAMP NOT NULL,
    used_at TIMESTAMP NULL,
    
    -- IP Tracking
    ip_address VARCHAR(45) NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX idx_email (email),
    INDEX idx_otp_code (otp_code),
    INDEX idx_expires_at (expires_at),
    INDEX idx_is_used (is_used)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### **3. PASSWORD_RESETS TABLE**

**Purpose:** Stores password reset tokens

```sql
CREATE TABLE password_resets (
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_token (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### **4. LOCATIONS TABLE**

**Purpose:** Multi-city support (Caloocan, Quezon City, etc.)

```sql
CREATE TABLE locations (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    
    -- Location Details
    location_name VARCHAR(255) NOT NULL,
    location_code VARCHAR(10) NOT NULL UNIQUE,
    
    -- Contact Information
    address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    province VARCHAR(100) NOT NULL,
    zip_code VARCHAR(10) NULL,
    phone VARCHAR(20) NULL,
    email VARCHAR(255) NULL,
    
    -- Configuration (JSON)
    config JSON NULL COMMENT 'Location-specific settings',
    /*
    Example config:
    {
      "payment_mode": "hourly",
      "base_rate": 1000,
      "currency": "PHP",
      "operating_hours": {"start": "06:00", "end": "22:00"},
      "advance_booking_days": 180,
      "cancellation_deadline_hours": 48,
      "approval_levels": ["staff", "admin"],
      "discount_tiers": {"pwd": 20, "senior": 20, "student": 10},
      "requires_deposit": true,
      "deposit_percentage": 30
    }
    */
    
    -- Status
    is_active BOOLEAN DEFAULT TRUE,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_location_code (location_code),
    INDEX idx_is_active (is_active),
    INDEX idx_deleted_at (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### **5. FACILITIES TABLE**

**Purpose:** Stores facility master data

```sql
CREATE TABLE facilities (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    
    -- Location
    location_id BIGINT UNSIGNED NOT NULL,
    
    -- Basic Information
    facility_name VARCHAR(255) NOT NULL,
    facility_type ENUM('gymnasium', 'convention_center', 'function_hall', 'sports_complex', 'auditorium', 'meeting_room', 'other') NOT NULL,
    description TEXT NULL,
    
    -- Capacity
    capacity INT NOT NULL COMMENT 'Maximum number of people',
    
    -- Pricing
    hourly_rate DECIMAL(10, 2) NULL,
    per_person_rate DECIMAL(10, 2) NULL,
    deposit_amount DECIMAL(10, 2) NULL,
    
    -- Amenities (JSON array)
    amenities JSON NULL COMMENT 'List of available amenities',
    /*
    Example: ["air_conditioning", "sound_system", "projector", "wifi", "parking", "kitchen"]
    */
    
    -- Rules & Guidelines
    rules TEXT NULL,
    terms_and_conditions TEXT NULL,
    
    -- Availability
    is_available BOOLEAN DEFAULT TRUE,
    advance_booking_days INT DEFAULT 180 COMMENT 'How many days in advance can book',
    min_booking_hours INT DEFAULT 2 COMMENT 'Minimum booking duration',
    max_booking_hours INT DEFAULT 12 COMMENT 'Maximum booking duration',
    
    -- Operating Hours (JSON)
    operating_hours JSON NULL,
    /*
    Example:
    {
      "monday": {"open": "06:00", "close": "22:00"},
      "tuesday": {"open": "06:00", "close": "22:00"},
      ...
      "sunday": {"open": "08:00", "close": "20:00"}
    }
    */
    
    -- Address
    address TEXT NULL,
    google_maps_url TEXT NULL,
    
    -- Status
    status ENUM('active', 'under_construction', 'under_maintenance', 'inactive') DEFAULT 'active',
    
    -- Display Order
    display_order INT DEFAULT 0,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_location_id (location_id),
    INDEX idx_facility_type (facility_type),
    INDEX idx_status (status),
    INDEX idx_is_available (is_available),
    INDEX idx_display_order (display_order),
    INDEX idx_deleted_at (deleted_at),
    
    FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### **6. FACILITY_PHOTOS TABLE**

**Purpose:** Multiple photos per facility

```sql
CREATE TABLE facility_photos (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    
    facility_id BIGINT UNSIGNED NOT NULL,
    
    photo_path VARCHAR(255) NOT NULL,
    photo_caption VARCHAR(255) NULL,
    
    is_primary BOOLEAN DEFAULT FALSE COMMENT 'Main photo for listing',
    display_order INT DEFAULT 0,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_facility_id (facility_id),
    INDEX idx_is_primary (is_primary),
    INDEX idx_display_order (display_order),
    
    FOREIGN KEY (facility_id) REFERENCES facilities(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### **7. FACILITY_SCHEDULES TABLE**

**Purpose:** Define availability blocks (e.g., closed on holidays)

```sql
CREATE TABLE facility_schedules (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    
    facility_id BIGINT UNSIGNED NOT NULL,
    
    schedule_type ENUM('available', 'blocked', 'holiday', 'special_event') NOT NULL,
    
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    
    start_time TIME NULL,
    end_time TIME NULL,
    
    reason VARCHAR(255) NULL COMMENT 'Why blocked',
    notes TEXT NULL,
    
    created_by BIGINT UNSIGNED NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_facility_id (facility_id),
    INDEX idx_schedule_type (schedule_type),
    INDEX idx_start_date (start_date),
    INDEX idx_end_date (end_date),
    INDEX idx_deleted_at (deleted_at),
    
    FOREIGN KEY (facility_id) REFERENCES facilities(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### **8. MAINTENANCE_SCHEDULES TABLE**

**Purpose:** Track facility maintenance and downtime

```sql
CREATE TABLE maintenance_schedules (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    
    facility_id BIGINT UNSIGNED NOT NULL,
    
    maintenance_type ENUM('routine', 'repair', 'inspection', 'cleaning', 'renovation') NOT NULL,
    
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    
    description TEXT NULL,
    contractor_name VARCHAR(255) NULL,
    estimated_cost DECIMAL(10, 2) NULL,
    
    status ENUM('scheduled', 'in_progress', 'completed', 'cancelled') DEFAULT 'scheduled',
    
    scheduled_by BIGINT UNSIGNED NULL,
    completed_at TIMESTAMP NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_facility_id (facility_id),
    INDEX idx_maintenance_type (maintenance_type),
    INDEX idx_status (status),
    INDEX idx_start_date (start_date),
    INDEX idx_end_date (end_date),
    INDEX idx_deleted_at (deleted_at),
    
    FOREIGN KEY (facility_id) REFERENCES facilities(id) ON DELETE CASCADE,
    FOREIGN KEY (scheduled_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### **9. EQUIPMENT TABLE**

**Purpose:** Equipment available for rent with facilities

```sql
CREATE TABLE equipment (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    
    facility_id BIGINT UNSIGNED NOT NULL,
    
    equipment_name VARCHAR(255) NOT NULL,
    equipment_type ENUM('audio', 'visual', 'furniture', 'decoration', 'kitchen', 'sports', 'other') NOT NULL,
    
    description TEXT NULL,
    
    quantity_total INT NOT NULL DEFAULT 1,
    quantity_available INT NOT NULL DEFAULT 1,
    
    hourly_rate DECIMAL(10, 2) NULL,
    daily_rate DECIMAL(10, 2) NULL,
    
    is_free BOOLEAN DEFAULT FALSE COMMENT 'Included in facility booking',
    is_available BOOLEAN DEFAULT TRUE,
    
    condition_status ENUM('excellent', 'good', 'fair', 'needs_repair') DEFAULT 'good',
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_facility_id (facility_id),
    INDEX idx_equipment_type (equipment_type),
    INDEX idx_is_available (is_available),
    INDEX idx_deleted_at (deleted_at),
    
    FOREIGN KEY (facility_id) REFERENCES facilities(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### **10. EQUIPMENT_PHOTOS TABLE**

**Purpose:** Photos of equipment

```sql
CREATE TABLE equipment_photos (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    
    equipment_id BIGINT UNSIGNED NOT NULL,
    
    photo_path VARCHAR(255) NOT NULL,
    photo_caption VARCHAR(255) NULL,
    
    is_primary BOOLEAN DEFAULT FALSE,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_equipment_id (equipment_id),
    INDEX idx_is_primary (is_primary),
    
    FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### **11. BOOKINGS TABLE**

**Purpose:** Main booking records

```sql
CREATE TABLE bookings (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    
    -- Booking Reference
    booking_reference VARCHAR(20) NOT NULL UNIQUE COMMENT 'e.g., LGU-CAL-2025-001234',
    
    -- Relationships
    facility_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL COMMENT 'Citizen who booked',
    
    -- Event Details
    event_name VARCHAR(255) NOT NULL,
    event_type ENUM('wedding', 'birthday', 'corporate', 'seminar', 'sports', 'government', 'other') NOT NULL,
    event_description TEXT NULL,
    
    number_of_attendees INT NOT NULL,
    
    -- Schedule
    booking_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    duration_hours DECIMAL(4, 2) NOT NULL,
    
    -- Contact Person
    contact_person_name VARCHAR(255) NOT NULL,
    contact_person_phone VARCHAR(20) NOT NULL,
    contact_person_email VARCHAR(255) NULL,
    
    -- Pricing
    base_price DECIMAL(10, 2) NOT NULL,
    equipment_charges DECIMAL(10, 2) DEFAULT 0,
    deposit_amount DECIMAL(10, 2) DEFAULT 0,
    discount_percentage DECIMAL(5, 2) DEFAULT 0,
    discount_amount DECIMAL(10, 2) DEFAULT 0,
    total_amount DECIMAL(10, 2) NOT NULL,
    
    -- Discount Details
    discount_type ENUM('none', 'pwd', 'senior', 'student') DEFAULT 'none',
    discount_id_number VARCHAR(100) NULL,
    
    -- Status Workflow
    status ENUM(
        'pending_staff_verification',
        'pending_admin_approval',
        'approved',
        'rejected',
        'payment_pending',
        'confirmed',
        'completed',
        'cancelled',
        'no_show'
    ) DEFAULT 'pending_staff_verification',
    
    -- Payment
    payment_status ENUM('unpaid', 'partial', 'paid', 'refunded') DEFAULT 'unpaid',
    payment_deadline TIMESTAMP NULL,
    
    -- Cancellation
    cancelled_at TIMESTAMP NULL,
    cancellation_reason TEXT NULL,
    cancelled_by BIGINT UNSIGNED NULL,
    
    -- Completion
    completed_at TIMESTAMP NULL,
    completion_notes TEXT NULL,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    -- Indexes
    INDEX idx_booking_reference (booking_reference),
    INDEX idx_facility_id (facility_id),
    INDEX idx_user_id (user_id),
    INDEX idx_booking_date (booking_date),
    INDEX idx_status (status),
    INDEX idx_payment_status (payment_status),
    INDEX idx_event_type (event_type),
    INDEX idx_deleted_at (deleted_at),
    
    -- Foreign Keys
    FOREIGN KEY (facility_id) REFERENCES facilities(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (cancelled_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### **12. BOOKING_DOCUMENTS TABLE**

**Purpose:** Uploaded documents for verification

```sql
CREATE TABLE booking_documents (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    
    booking_id BIGINT UNSIGNED NOT NULL,
    
    document_type ENUM(
        'valid_id',
        'pwd_id',
        'senior_id',
        'student_id',
        'event_permit',
        'barangay_clearance',
        'business_permit',
        'other'
    ) NOT NULL,
    
    document_path VARCHAR(255) NOT NULL,
    original_filename VARCHAR(255) NULL,
    file_size INT NULL COMMENT 'Bytes',
    mime_type VARCHAR(100) NULL,
    
    uploaded_by BIGINT UNSIGNED NOT NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_booking_id (booking_id),
    INDEX idx_document_type (document_type),
    INDEX idx_uploaded_by (uploaded_by),
    INDEX idx_deleted_at (deleted_at),
    
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### **13. BOOKING_APPROVALS TABLE**

**Purpose:** Multi-level approval chain

```sql
CREATE TABLE booking_approvals (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    
    booking_id BIGINT UNSIGNED NOT NULL,
    
    approver_role ENUM('staff', 'admin', 'superadmin') NOT NULL,
    approver_id BIGINT UNSIGNED NOT NULL,
    
    action ENUM('approved', 'rejected', 'requested_info') NOT NULL,
    comments TEXT NULL,
    
    approval_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_booking_id (booking_id),
    INDEX idx_approver_id (approver_id),
    INDEX idx_approver_role (approver_role),
    INDEX idx_action (action),
    
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (approver_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### **14. BOOKING_NOTES TABLE**

**Purpose:** Internal notes by staff/admin

```sql
CREATE TABLE booking_notes (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    
    booking_id BIGINT UNSIGNED NOT NULL,
    
    note_text TEXT NOT NULL,
    
    is_internal BOOLEAN DEFAULT TRUE COMMENT 'Visible to staff/admin only',
    
    created_by BIGINT UNSIGNED NOT NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_booking_id (booking_id),
    INDEX idx_created_by (created_by),
    INDEX idx_is_internal (is_internal),
    INDEX idx_deleted_at (deleted_at),
    
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### **15. BOOKING_EQUIPMENT TABLE**

**Purpose:** Junction table for booking-equipment relationship

```sql
CREATE TABLE booking_equipment (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    
    booking_id BIGINT UNSIGNED NOT NULL,
    equipment_id BIGINT UNSIGNED NOT NULL,
    
    quantity INT NOT NULL DEFAULT 1,
    rate DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_booking_id (booking_id),
    INDEX idx_equipment_id (equipment_id),
    
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_booking_equipment (booking_id, equipment_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### **16. BOOKING_REMINDERS TABLE**

**Purpose:** Auto-reminders for upcoming bookings

```sql
CREATE TABLE booking_reminders (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    
    booking_id BIGINT UNSIGNED NOT NULL,
    
    reminder_type ENUM('7_days_before', '3_days_before', '1_day_before', 'same_day') NOT NULL,
    
    sent_at TIMESTAMP NULL,
    is_sent BOOLEAN DEFAULT FALSE,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_booking_id (booking_id),
    INDEX idx_is_sent (is_sent),
    INDEX idx_reminder_type (reminder_type),
    
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### **17. CANCELLATIONS TABLE**

**Purpose:** Track cancellation history

```sql
CREATE TABLE cancellations (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    
    booking_id BIGINT UNSIGNED NOT NULL,
    
    cancelled_by BIGINT UNSIGNED NOT NULL,
    cancellation_type ENUM('by_citizen', 'by_admin', 'no_show', 'payment_timeout') NOT NULL,
    
    reason TEXT NOT NULL,
    refund_amount DECIMAL(10, 2) DEFAULT 0,
    refund_status ENUM('none', 'pending', 'processed') DEFAULT 'none',
    
    cancelled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_booking_id (booking_id),
    INDEX idx_cancelled_by (cancelled_by),
    INDEX idx_cancellation_type (cancellation_type),
    
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (cancelled_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### **18. PAYMENTS TABLE**

**Purpose:** Payment records

```sql
CREATE TABLE payments (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    
    booking_id BIGINT UNSIGNED NOT NULL,
    
    payment_reference VARCHAR(50) NOT NULL UNIQUE,
    
    amount DECIMAL(10, 2) NOT NULL,
    payment_method ENUM('cash', 'gcash', 'paymaya', 'bank_transfer', 'check') NOT NULL,
    
    payment_status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    
    -- Payment Details
    transaction_id VARCHAR(255) NULL COMMENT 'External payment gateway ID',
    receipt_number VARCHAR(50) NULL,
    
    -- Payer Information
    paid_by BIGINT UNSIGNED NOT NULL,
    payer_name VARCHAR(255) NULL,
    
    -- Verification
    verified_by BIGINT UNSIGNED NULL COMMENT 'Staff who verified payment',
    verified_at TIMESTAMP NULL,
    
    -- Timestamps
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_booking_id (booking_id),
    INDEX idx_payment_reference (payment_reference),
    INDEX idx_payment_status (payment_status),
    INDEX idx_paid_by (paid_by),
    INDEX idx_payment_method (payment_method),
    INDEX idx_deleted_at (deleted_at),
    
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (paid_by) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### **19. OFFICIAL_RECEIPTS TABLE**

**Purpose:** Official receipt generation

```sql
CREATE TABLE official_receipts (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    
    payment_id BIGINT UNSIGNED NOT NULL UNIQUE,
    booking_id BIGINT UNSIGNED NOT NULL,
    
    or_number VARCHAR(50) NOT NULL UNIQUE,
    
    amount_paid DECIMAL(10, 2) NOT NULL,
    
    issued_by BIGINT UNSIGNED NOT NULL,
    issued_to VARCHAR(255) NOT NULL,
    
    receipt_path VARCHAR(255) NULL COMMENT 'PDF file path',
    
    issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_payment_id (payment_id),
    INDEX idx_booking_id (booking_id),
    INDEX idx_or_number (or_number),
    INDEX idx_issued_by (issued_by),
    
    FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (issued_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### **20. DISCOUNT_VALIDATIONS TABLE**

**Purpose:** Track discount verification by staff

```sql
CREATE TABLE discount_validations (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    
    booking_id BIGINT UNSIGNED NOT NULL,
    
    discount_type ENUM('pwd', 'senior', 'student') NOT NULL,
    id_number VARCHAR(100) NOT NULL,
    
    is_valid BOOLEAN DEFAULT FALSE,
    validation_notes TEXT NULL,
    
    validated_by BIGINT UNSIGNED NOT NULL,
    validated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_booking_id (booking_id),
    INDEX idx_discount_type (discount_type),
    INDEX idx_validated_by (validated_by),
    
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (validated_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### **21. NOTIFICATIONS TABLE**

**Purpose:** User notifications

```sql
CREATE TABLE notifications (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    
    user_id BIGINT UNSIGNED NOT NULL,
    
    notification_type ENUM(
        'booking_submitted',
        'booking_approved',
        'booking_rejected',
        'payment_received',
        'reminder',
        'cancellation',
        'system'
    ) NOT NULL,
    
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    
    related_booking_id BIGINT UNSIGNED NULL,
    
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_user_id (user_id),
    INDEX idx_notification_type (notification_type),
    INDEX idx_is_read (is_read),
    INDEX idx_related_booking_id (related_booking_id),
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (related_booking_id) REFERENCES bookings(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### **22. AUDIT_LOGS TABLE**

**Purpose:** Complete audit trail of all actions

```sql
CREATE TABLE audit_logs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    
    user_id BIGINT UNSIGNED NULL COMMENT 'Who performed the action',
    
    action VARCHAR(50) NOT NULL COMMENT 'created, updated, deleted, restored, etc.',
    
    model VARCHAR(100) NOT NULL COMMENT 'Model name (Booking, Facility, etc.)',
    model_id BIGINT UNSIGNED NOT NULL COMMENT 'Record ID',
    
    old_values JSON NULL COMMENT 'Values before change',
    new_values JSON NULL COMMENT 'Values after change',
    
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_model (model),
    INDEX idx_model_id (model_id),
    INDEX idx_created_at (created_at),
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### **23. ACTIVITY_LOGS TABLE**

**Purpose:** User activity tracking (login, logout, page views)

```sql
CREATE TABLE activity_logs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    
    user_id BIGINT UNSIGNED NULL,
    
    activity_type ENUM('login', 'logout', 'view', 'action', 'error') NOT NULL,
    description TEXT NULL,
    
    url TEXT NULL,
    method VARCHAR(10) NULL,
    
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_user_id (user_id),
    INDEX idx_activity_type (activity_type),
    INDEX idx_created_at (created_at),
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 🔍 INDEXES

### **Performance Indexes**

All primary search fields are indexed:
- Email, phone (for user lookup)
- Booking dates, status (for queries)
- Foreign keys (for joins)
- Soft delete columns (for filtering)

### **Composite Indexes (Future Optimization)**

```sql
-- Frequently queried together
CREATE INDEX idx_booking_facility_date ON bookings(facility_id, booking_date, status);
CREATE INDEX idx_user_role_status ON users(role, status, deleted_at);
CREATE INDEX idx_facility_location_status ON facilities(location_id, status, deleted_at);
```

---

## 🗑️ SOFT DELETES

### **All Tables Use Soft Deletes**

Every table (except junction tables and logs) has `deleted_at TIMESTAMP NULL`:
- Users
- Facilities
- Equipment
- Bookings
- Documents
- etc.

### **Laravel Implementation**

```php
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model {
    use SoftDeletes;
    
    // Query without deleted
    $bookings = Booking::all(); // Excludes soft deleted
    
    // Include deleted
    $all = Booking::withTrashed()->get();
    
    // Only deleted
    $deleted = Booking::onlyTrashed()->get();
    
    // Restore
    $booking->restore();
    
    // Permanent delete (avoid!)
    $booking->forceDelete();
}
```

---

## 🔢 MIGRATION ORDER

**Run migrations in this exact order to avoid foreign key errors:**

```bash
# 1. Base tables (no dependencies)
php artisan migrate --path=database/migrations/001_create_locations_table.php
php artisan migrate --path=database/migrations/002_create_users_table.php

# 2. Auth tables
php artisan migrate --path=database/migrations/003_create_otps_table.php
php artisan migrate --path=database/migrations/004_create_password_resets_table.php

# 3. Facilities
php artisan migrate --path=database/migrations/005_create_facilities_table.php
php artisan migrate --path=database/migrations/006_create_facility_photos_table.php
php artisan migrate --path=database/migrations/007_create_facility_schedules_table.php
php artisan migrate --path=database/migrations/008_create_maintenance_schedules_table.php

# 4. Equipment
php artisan migrate --path=database/migrations/009_create_equipment_table.php
php artisan migrate --path=database/migrations/010_create_equipment_photos_table.php

# 5. Bookings
php artisan migrate --path=database/migrations/011_create_bookings_table.php
php artisan migrate --path=database/migrations/012_create_booking_documents_table.php
php artisan migrate --path=database/migrations/013_create_booking_approvals_table.php
php artisan migrate --path=database/migrations/014_create_booking_notes_table.php
php artisan migrate --path=database/migrations/015_create_booking_equipment_table.php
php artisan migrate --path=database/migrations/016_create_booking_reminders_table.php
php artisan migrate --path=database/migrations/017_create_cancellations_table.php

# 6. Payments
php artisan migrate --path=database/migrations/018_create_payments_table.php
php artisan migrate --path=database/migrations/019_create_official_receipts_table.php
php artisan migrate --path=database/migrations/020_create_discount_validations_table.php

# 7. Notifications & Logs
php artisan migrate --path=database/migrations/021_create_notifications_table.php
php artisan migrate --path=database/migrations/022_create_audit_logs_table.php
php artisan migrate --path=database/migrations/023_create_activity_logs_table.php
```

---

## 📊 SAMPLE DATA

### **Seed Data Priority**

1. **Locations** - Caloocan City, Quezon City
2. **Users** - Super Admin, Admin, Staff, Citizens
3. **Facilities** - 5-10 facilities per location
4. **Equipment** - 10-20 equipment items per facility
5. **Bookings** - 100+ historical bookings (for AI training)
6. **Payments** - Corresponding payment records

---

## ✅ VALIDATION RULES

### **Database-Level Validation**
- NOT NULL constraints on required fields
- UNIQUE constraints on email, booking_reference, or_number
- ENUM constraints for status fields
- Foreign key constraints for relationships
- CHECK constraints for logical validation

### **Application-Level Validation (Laravel)**
```php
// Example: Booking validation
$request->validate([
    'facility_id' => 'required|exists:facilities,id',
    'booking_date' => 'required|date|after:today',
    'start_time' => 'required|date_format:H:i',
    'end_time' => 'required|date_format:H:i|after:start_time',
    'number_of_attendees' => 'required|integer|min:1',
    'contact_person_phone' => 'required|regex:/^[0-9]{10,}$/',
]);
```

---

## 🔐 SECURITY CONSIDERATIONS

### **Sensitive Data**
- Passwords: bcrypt hashed
- Credit card info: NOT stored (use payment gateway tokens)
- ID photos: Stored with restricted access
- Audit logs: Immutable (no delete)

### **Data Retention**
- Soft deleted records: Retain for 1 year
- Audit logs: Retain indefinitely
- Failed OTPs: Auto-clean after 24 hours
- Expired sessions: Auto-clean after 7 days

---

## 📞 MAINTENANCE

### **Regular Tasks**
- **Daily:** Clean expired OTPs
- **Weekly:** Archive old notifications
- **Monthly:** Optimize database tables
- **Yearly:** Archive old bookings (soft delete)

### **Backup Strategy**
- **Frequency:** Daily (automated)
- **Retention:** 30 days
- **Storage:** Off-site backup
- **Testing:** Monthly restore test

---

**Last Updated:** December 10, 2025  
**Version:** 1.0  
**Status:** 🔒 LOCKED FOR DEVELOPMENT

---

*This schema ensures data integrity, performance, and scalability for the final defense and production deployment.*

