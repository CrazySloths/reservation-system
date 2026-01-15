-- ========================================================================
-- TABLES TO ADD TO ENERGY EFFICIENCY DATABASE (ener_nova_capri)
-- For receiving facility booking confirmations from Public Facilities
-- ========================================================================
-- 
-- PURPOSE: These tables store the responses/confirmations sent by 
-- Public Facilities system back to Energy Efficiency system
-- 
-- TESTING: We write to these tables during local testing
-- PRODUCTION: Same data structure sent via API webhook
-- ========================================================================

USE ener_nova_capri;

-- ========================================================================
-- Table 1: FACILITY BOOKING CONFIRMATIONS (Main Response Table)
-- ========================================================================

CREATE TABLE IF NOT EXISTS facility_booking_confirmations (
    confirmation_id INT PRIMARY KEY AUTO_INCREMENT,
    
    -- Link to their seminar
    seminar_id INT NOT NULL,
    
    -- Tracking IDs
    public_facilities_tracking_id VARCHAR(50) COMMENT 'GPR-2025-XXX',
    request_status ENUM(
        'received',           -- We received their request
        'under_review',       -- Admin reviewing
        'coordinating',       -- Admin calling organizer
        'confirmed',          -- Facility assigned and confirmed
        'rejected',           -- Cannot accommodate
        'cancelled'           -- Event cancelled
    ) DEFAULT 'received',
    
    -- ===========================
    -- FACILITY ASSIGNMENT
    -- ===========================
    assigned_facility_id INT COMMENT 'ID from our facilities table',
    assigned_facility_name VARCHAR(255),
    assigned_facility_address TEXT,
    assigned_facility_capacity INT,
    assigned_facility_amenities JSON COMMENT '["Projector", "Sound System", "AC"]',
    
    facility_fee_original DECIMAL(10,2) COMMENT 'Normal rental fee',
    facility_fee_charged DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Always 0 - waived for gov programs',
    facility_fee_waived BOOLEAN DEFAULT TRUE,
    
    -- ===========================
    -- SCHEDULE CONFIRMATION
    -- ===========================
    confirmed_date DATE,
    confirmed_start_time TIME,
    confirmed_end_time TIME,
    setup_time_minutes INT DEFAULT 30,
    
    -- ===========================
    -- SPEAKERS & TOPICS
    -- ===========================
    confirmed_speakers JSON COMMENT '[
        {
            "name": "Dr. Juan Dela Cruz",
            "title": "DOE Energy Specialist",
            "topic": "Home Energy Conservation Techniques",
            "duration_minutes": 45
        },
        {
            "name": "Eng. Maria Santos", 
            "title": "Meralco Senior Engineer",
            "topic": "Understanding Your Electric Bill",
            "duration_minutes": 30
        }
    ]',
    
    seminar_agenda JSON COMMENT '[
        {"time": "14:00", "activity": "Registration"},
        {"time": "14:30", "activity": "Opening Remarks"},
        {"time": "14:45", "activity": "Speaker 1"},
        {"time": "15:30", "activity": "Q&A"},
        {"time": "16:00", "activity": "Speaker 2"},
        {"time": "16:30", "activity": "Closing"}
    ]',
    
    -- ===========================
    -- FUND APPROVAL
    -- ===========================
    requested_amount DECIMAL(10,2),
    approved_amount DECIMAL(10,2),
    fund_approval_status ENUM('pending', 'approved', 'rejected', 'partial') DEFAULT 'pending',
    finance_check_number VARCHAR(50),
    finance_release_date DATE,
    
    -- Pre-Event Budget Breakdown (TRANSPARENCY - PLANNED)
    pre_event_budget_breakdown JSON COMMENT '{
        "food_refreshments": {
            "amount": 2500.00,
            "details": "150 Jollibee C1 meals @ 89.00 each",
            "supplier": "Jollibee Caloocan Branch"
        },
        "training_materials": {
            "amount": 2000.00,
            "details": "Handbooks, pens, certificates",
            "supplier": "PrintHub Caloocan"
        },
        "transportation": {
            "amount": 300.00,
            "details": "Speaker travel expenses"
        },
        "miscellaneous": {
            "amount": 200.00,
            "details": "Signage, name tags"
        }
    }',
    
    -- Post-Event Actual Spending (TRANSPARENCY - ACTUAL)
    post_event_liquidation JSON COMMENT '{
        "actual_attendees": 142,
        "total_spent": 4880.00,
        "savings": 120.00,
        "items": [
            {
                "category": "food_refreshments",
                "item": "Jollibee Chickenjoy C1",
                "quantity": 142,
                "unit_price": 89.00,
                "total": 2418.00,
                "supplier": "Jollibee Caloocan Branch",
                "or_number": "123456789",
                "receipt_url": "https://facilities.caloocan.gov.ph/receipts/123.pdf"
            }
        ]
    }',
    
    -- ===========================
    -- TRANSPARENCY & PUBLIC DISPLAY
    -- ===========================
    transparency_report_url VARCHAR(500) COMMENT 'https://facilities.caloocan.gov.ph/transparency/GPR-2025-456',
    is_published_publicly BOOLEAN DEFAULT TRUE COMMENT 'Citizens can view transparency',
    
    -- ===========================
    -- COORDINATION NOTES
    -- ===========================
    admin_contact_name VARCHAR(255),
    admin_contact_phone VARCHAR(20),
    admin_contact_email VARCHAR(255),
    
    coordination_notes TEXT COMMENT 'Internal notes from Public Facilities admin',
    organizer_call_log TEXT COMMENT 'Record of phone calls/discussions with organizer',
    
    -- ===========================
    -- IMPORTANT REMINDERS
    -- ===========================
    reminders JSON COMMENT '[
        "Setup starts at 1:30 PM (30 mins before event)",
        "All equipment (projector, sound system) included FREE",
        "Facility cleaning required after event",
        "Liquidation report due within 3 days after event"
    ]',
    
    -- ===========================
    -- TIMESTAMPS
    -- ===========================
    received_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'When we received their request',
    confirmed_at TIMESTAMP NULL COMMENT 'When admin confirmed',
    liquidation_submitted_at TIMESTAMP NULL COMMENT 'When they submitted actual spending',
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- ===========================
    -- INDEXES
    -- ===========================
    INDEX idx_seminar_id (seminar_id),
    INDEX idx_status (request_status),
    INDEX idx_confirmed_date (confirmed_date),
    
    -- ===========================
    -- FOREIGN KEY
    -- ===========================
    FOREIGN KEY (seminar_id) REFERENCES seminars(seminar_id) ON DELETE CASCADE
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Stores facility booking confirmations from Public Facilities system';


-- ========================================================================
-- Table 2: FACILITY CONFIRMATION PHOTOS (Supporting Documents)
-- ========================================================================

CREATE TABLE IF NOT EXISTS facility_confirmation_photos (
    photo_id INT PRIMARY KEY AUTO_INCREMENT,
    confirmation_id INT NOT NULL,
    
    photo_type ENUM(
        'facility_exterior',
        'facility_interior', 
        'event_setup',
        'event_ongoing',
        'food_provided',
        'materials_provided',
        'receipt',
        'attendance'
    ),
    
    photo_url VARCHAR(500),
    photo_caption VARCHAR(255),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (confirmation_id) REFERENCES facility_booking_confirmations(confirmation_id) ON DELETE CASCADE,
    INDEX idx_confirmation_id (confirmation_id),
    INDEX idx_photo_type (photo_type)
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Photos and documents related to facility confirmations';


-- ========================================================================
-- Table 3: FACILITY CONFIRMATION RECEIPTS (Detailed Spending Proof)
-- ========================================================================

CREATE TABLE IF NOT EXISTS facility_confirmation_receipts (
    receipt_id INT PRIMARY KEY AUTO_INCREMENT,
    confirmation_id INT NOT NULL,
    
    -- Receipt Details
    receipt_category ENUM('food', 'materials', 'transportation', 'miscellaneous'),
    supplier_name VARCHAR(255),
    official_receipt_number VARCHAR(100),
    receipt_date DATE,
    
    -- Item Details
    item_description VARCHAR(255),
    quantity INT,
    unit_price DECIMAL(10,2),
    total_amount DECIMAL(10,2),
    
    -- Verification
    receipt_image_url VARCHAR(500),
    is_price_verified BOOLEAN DEFAULT FALSE COMMENT 'Verified against market prices',
    price_variance_percentage DECIMAL(5,2) COMMENT 'Difference from market price',
    
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (confirmation_id) REFERENCES facility_booking_confirmations(confirmation_id) ON DELETE CASCADE,
    INDEX idx_confirmation_id (confirmation_id),
    INDEX idx_category (receipt_category)
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Itemized receipts for transparency and verification';


-- ========================================================================
-- NO SAMPLE DATA
-- ========================================================================
-- 
-- Following project architecture rules: NO HARDCODED DATA
-- All data must be:
-- ✅ Database-driven (from facilities, suppliers, products tables)
-- ✅ Dynamically generated by the system
-- ✅ Pulled from actual records
-- 
-- Data will be inserted by the application when:
-- 1. Admin assigns a facility (pulls from lgu1_facilities.facilities table)
-- 2. Admin enters speakers (user input, stored dynamically)
-- 3. Admin approves budget (calculated from supplier_products table)
-- 4. System generates tracking ID (auto-generated, not hardcoded)
-- 
-- ========================================================================


-- ========================================================================
-- VIEWS FOR EASY QUERYING
-- ========================================================================

-- View: Active Confirmed Bookings
-- This view joins confirmations with original seminar requests
-- All data is pulled dynamically from related tables (no hardcoded values)
CREATE OR REPLACE VIEW active_facility_confirmations AS
SELECT 
    fbc.confirmation_id,
    fbc.public_facilities_tracking_id,
    fbc.seminar_id,
    s.seminar_title,
    s.seminar_date,
    s.start_time,
    s.end_time,
    fbc.assigned_facility_name,
    fbc.assigned_facility_address,
    fbc.request_status,
    fbc.approved_amount,
    fbc.confirmed_at,
    fbc.transparency_report_url
FROM facility_booking_confirmations fbc
JOIN seminars s ON fbc.seminar_id = s.seminar_id
WHERE fbc.request_status IN ('confirmed', 'coordinating')
AND s.seminar_date >= CURDATE()
ORDER BY s.seminar_date ASC;


-- ========================================================================
-- NOTES FOR ENERGY EFFICIENCY TEAM
-- ========================================================================

/*
INSTRUCTIONS FOR ENERGY EFFICIENCY TEAM:
=========================================

1. Run this SQL script in your ener_nova_capri database

2. After event completion, you can query the transparency data:
   SELECT * FROM facility_booking_confirmations WHERE seminar_id = YOUR_SEMINAR_ID;

3. Display transparency report in your citizen portal using:
   - pre_event_budget_breakdown (what was planned)
   - post_event_liquidation (what was actually spent)

4. Access photos: facility_confirmation_photos table

5. Access detailed receipts: facility_confirmation_receipts table

6. Use the view for dashboard:
   SELECT * FROM active_facility_confirmations;

7. In PRODUCTION, instead of us writing to your database, 
   you will receive this same data via webhook POST request
   to your endpoint with the exact same JSON structure.
*/

