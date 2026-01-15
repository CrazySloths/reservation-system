# ⚡ ENERGY EFFICIENCY INTEGRATION - FEATURE LIST

**Document Version:** 1.0  
**Date:** December 3, 2025  
**System:** Public Facilities Reservation System  
**External Integration:** Energy Efficiency and Conservation Management System

---

## 📋 INTEGRATION OVERVIEW

### **Integration Purpose:**
Enable Energy Efficiency department to book public facilities for community awareness seminars/events with complete transparency and accountability.

### **Integration Flow:**
```
Phase 1: Energy Efficiency → Request Facility (for community seminar)
Phase 2: Public Facilities → Coordinate (organizer, speaker, finance, facility)
Phase 3: Finance System → Approve Funds (for event consumables)
Phase 4: Public Facilities → Confirm Booking (approved schedule)
Phase 5: Event Execution → Post-Event Transparency (itemized reporting)
```

### **Key Principle:**
- **Citizens attend FREE** (no admission fee)
- **Facility provided FREE** (government asset, waived fees)
- **Funds requested for CONSUMABLES ONLY** (food, materials, supplies)
- **Complete transparency** (pre-event and post-event reporting)
- **Real, verifiable prices** (citizens can verify market rates)
- **Database-driven** (no hardcoded values)

---

## 🔗 SUBMODULE INTEGRATION MAPPING

| Public Facilities Submodule | ↔️ | Energy Efficiency Submodule | Data Flow |
|----------------------------|---|----------------------------|-----------|
| **Facility Directory and Calendar** | → | **Community Awareness Campaign Management** | ONE-WAY: Browse facilities (READ only) |
| **Online Booking and Approval** | ↔️ | **Community Awareness Campaign Management** | BI-DIRECTIONAL: Request facility + Confirm booking |
| **Usage Fee Calculation and Payment** | → | **Community Awareness Campaign Management** | ONE-WAY: Notify fee waived (₱0.00) |
| **Schedule Conflicts Alert** | ↔️ | **Community Awareness Campaign Management** | BI-DIRECTIONAL: Query availability + Respond status |
| **Usage Reports and Feedback** | ↔️ | **Community Awareness Campaign Management** | BI-DIRECTIONAL: Liquidation data + Transparency report |

---

## ⚠️ IMPORTANT: ROLE DEFINITIONS

### **EIS SUPER ADMIN** (Lead Programmer - Technical Role)
- **Created by:** EIS Lead Programmer (centralized in lgu1_auth)
- **Access:** All 10 subsystems (technical oversight)
- **Focus:** System configuration, database management, supplier setup
- **Time commitment:** Occasional (setup, maintenance, troubleshooting)

**Handles TECHNICAL tasks only - NOT day-to-day operations.**

### **ADMIN** (Operations Manager - Primary Operational Role)
- **Created in:** Public Facilities subsystem
- **Access:** Public Facilities Reservation System only
- **Focus:** Daily operations, event coordination, transparency reporting
- **Time commitment:** Full-time (main working role)

**This is the PRIMARY role that handles all operational features below.**

---

## 🎯 FEATURES BY ROLE

### **1. ADMIN** ⭐ (Primary Operational Role)

**A. Request Management** (`/admin/government-programs/requests`)
- ✅ Receive facility requests from Energy Efficiency system
- ✅ Review event details (name, date, attendees, budget)
- ✅ View organizer contact information
- ✅ Assign requests to staff
- ✅ Set request priority

**B. Organizer Coordination** (`/admin/government-programs/requests/{id}/coordination`)
- ✅ Call organizer (with call log tracking)
- ✅ Discuss fund breakdown in detail
- ✅ Record call notes and agreements
- ✅ Request additional information
- ✅ Negotiate budget items
- ✅ Confirm event requirements

**C. Speaker Coordination** (`/admin/government-programs/requests/{id}/speaker-coordination`)
- ✅ Contact DOE/external speakers (offline)
- ✅ Check speaker availability
- ✅ Confirm speakers for event dates
- ✅ Record coordination notes
- ✅ Track speaker travel requirements

**D. Quotation Management** (`/admin/government-programs/requests/{id}/quotations`)
- ✅ Request quotations from suppliers (database-driven)
- ✅ Generate quotation request forms
- ✅ Upload received quotations
- ✅ Compare supplier prices
- ✅ Verify prices against database
- ✅ Flag overpriced items

**E. Finance Coordination** (`/admin/government-programs/requests/{id}/finance`)
- ✅ Submit fund request to Finance system (via API)
- ✅ Track Finance approval status
- ✅ Receive approval notifications
- ✅ View approved fund details (check #, release date)
- ✅ Follow up on pending approvals

**F. Facility Assignment** (`/admin/government-programs/requests/{id}/facility`)
- ✅ View suggested facilities (based on requirements)
- ✅ Check facility availability
- ✅ Assign facility to event
- ✅ Waive facility fees (government program)
- ✅ Include equipment (tables, chairs, sound system - FREE)
- ✅ Generate booking confirmation

**G. Event Finalization** (`/admin/government-programs/requests/{id}/finalize`)
- ✅ Verify all requirements met (speaker, funds, facility)
- ✅ Set final event date
- ✅ Send confirmation to Energy Efficiency (API)
- ✅ Send SMS to organizer
- ✅ Publish event on public calendar

**H. Pre-Event Transparency** (`/admin/government-programs/{id}/transparency/preview`)
- ✅ Create pre-event transparency report
- ✅ Input planned budget breakdown
- ✅ Add supplier information
- ✅ Publish for citizens BEFORE event

**I. Post-Event Liquidation** (`/admin/government-programs/{id}/liquidation`)
- ✅ Create liquidation report
- ✅ Upload official receipts (itemized)
- ✅ Enter actual purchases with specifications
- ✅ Link to suppliers in database
- ✅ Verify prices match market rates
- ✅ Upload product photos
- ✅ Record actual attendance
- ✅ Calculate savings (if under budget)
- ✅ Publish post-event transparency report

**J. Supplier Database (View & Request)** (`/admin/suppliers`)
- ✅ View all suppliers
- ✅ View supplier products and prices
- ✅ Request quotations from existing suppliers
- ✅ Suggest new suppliers to EIS Super Admin
- ❌ Cannot add/edit/delete suppliers (EIS Super Admin only)
- ❌ Cannot update prices in catalog (EIS Super Admin only)

---

### **2. EIS SUPER ADMIN** ⭐ (Technical Role Only)

**A. Supplier Management** (`/superadmin/suppliers`)
- ✅ Add/edit/delete suppliers (Jollibee, printing shops, etc.)
- ✅ Verify supplier credentials (TIN, business permit, BIR)
- ✅ Manage supplier product catalog
- ✅ Update product prices with documentation
- ✅ View price history for audit trail
- ✅ Mark preferred suppliers
- ✅ Export supplier reports

**B. System Configuration** (`/superadmin/settings/government-programs`)
- ✅ Configure API integration with Energy Efficiency system
- ✅ Manage external system API keys
- ✅ Configure webhook endpoints
- ✅ Monitor integration health and logs
- ✅ Handle technical issues

**C. Technical Oversight** (`/superadmin/government-programs/oversight`)
- ✅ View all government program requests (monitoring only)
- ✅ Override admin decisions (emergency only)
- ✅ Access complete system audit trail
- ✅ Review integration logs
- ✅ Generate technical reports

**Note:** EIS Super Admin focuses on TECHNICAL setup and monitoring, not operational coordination.

---

### **3. STAFF** ⭐

**A. View Access** (`/staff/government-programs`)
- ✅ View upcoming government programs
- ✅ View event details and schedules
- ✅ View facility assignments
- ✅ View contact information
- ❌ Cannot modify requests
- ❌ Cannot approve bookings

**B. Facility Preparation** (`/staff/government-programs/{id}/facility-prep`)
- ✅ View facility setup requirements checklist:
  - ☐ Tables and chairs arranged
  - ☐ Sound system tested
  - ☐ Projector working
  - ☐ AC turned on
  - ☐ Signage posted
- ✅ Mark tasks complete
- ✅ Report facility issues to Admin

**C. Event Day Support** (`/staff/government-programs/{id}/event-day`)
- ✅ View event schedule
- ✅ Record actual attendance count
- ✅ Take event photos
- ✅ Report issues during event
- ✅ Submit event completion notes

**D. Public Calendar** (`/staff/calendar/government-programs`)
- ✅ View government programs on calendar
- ✅ Help citizens find events
- ✅ Print event schedules

---

### **4. CITIZEN** 💚

**A. Browse Programs** (`/citizen/government-programs`)
- ✅ View all upcoming government programs
- ✅ Filter by:
  - Program type (seminar, training, workshop)
  - Location (city, district, barangay)
  - Date range
- ✅ Search programs
- ✅ See what's FREE

**B. Event Registration** (`/citizen/government-programs/{id}/register`)
- ✅ Register for free events
- ✅ Receive confirmation SMS/email
- ✅ Add to personal calendar
- ✅ View registration QR code
- ✅ Cancel registration
- ✅ View registration history

**C. Pre-Event Transparency** (`/citizen/government-programs/{id}/transparency`)
- ✅ View PLANNED budget BEFORE event:
  - What food will be provided (e.g., "Jollibee C1 meal")
  - What materials will be given (e.g., "Handbook, pen")
  - How much per item (e.g., "₱89 per meal")
  - Speaker information
  - Total government investment
- ✅ See facility provided FREE by City Hall
- ✅ See cost per citizen
- ✅ Share on social media

**D. Post-Event Transparency** (`/citizen/government-programs/{id}/completed`)
- ✅ View ACTUAL spending AFTER event
- ✅ Compare planned vs actual:
  - Budget comparison
  - Attendees (expected vs actual)
  - Spending by category
- ✅ View itemized receipts:
  - Exact items purchased
  - Quantities and prices (e.g., "142 Chickenjoy C1 @ ₱89 = ₱12,638")
  - Supplier information
- ✅ View photos of food and materials provided
- ✅ Download receipts (PDF)
- ✅ See savings returned to City budget

**E. Price Verification** (`/citizen/government-programs/verify-prices`)
- ✅ Report price discrepancies:
  - If government price seems wrong
  - Submit actual market price found
  - Upload proof (receipt/menu photo)
- ✅ Track report status
- ✅ Become "citizen auditor"

**F. Feedback** (`/citizen/government-programs/{id}/feedback`)
- ✅ Rate event (1-5 stars)
- ✅ Submit feedback
- ✅ Rate transparency level
- ✅ View other attendees' feedback

**G. Notifications** (`/citizen/profile/notifications`)
- ✅ Subscribe to government program alerts:
  - By barangay
  - By program type
  - By topic (energy, health, education)
- ✅ Choose method (SMS, email, in-app)
- ✅ Unsubscribe anytime

---

## 📊 DATABASE SCHEMA (Essential Tables)

### **1. Government Program Bookings**
```sql
CREATE TABLE government_program_bookings (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    booking_id BIGINT UNSIGNED NOT NULL,
    
    -- Source System
    source_system VARCHAR(100) NOT NULL, -- 'Energy Efficiency'
    source_request_id VARCHAR(50) NOT NULL,
    requesting_department VARCHAR(255),
    
    -- Organizer Details
    organizer_name VARCHAR(255) NOT NULL,
    organizer_title VARCHAR(255),
    organizer_office VARCHAR(255),
    organizer_contact VARCHAR(20) NOT NULL,
    organizer_email VARCHAR(255),
    
    -- Event Details
    program_title VARCHAR(255) NOT NULL,
    program_type ENUM('seminar', 'training', 'workshop', 'community_event', 'other'),
    target_audience VARCHAR(255),
    expected_attendees INT,
    actual_attendees INT,
    number_of_speakers INT,
    speaker_details JSON,
    
    -- Budget & Funding
    funding_source VARCHAR(255),
    budget_code VARCHAR(100),
    requested_amount DECIMAL(15,2),
    approved_amount DECIMAL(15,2),
    actual_spent DECIMAL(15,2),
    is_fee_waived BOOLEAN DEFAULT TRUE,
    
    -- Coordination Notes
    call_log JSON,
    fund_discussion_notes TEXT,
    speaker_coordination_notes TEXT,
    
    -- Finance Integration
    finance_request_id VARCHAR(50),
    finance_approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    finance_approved_date DATE,
    finance_check_number VARCHAR(50),
    finance_release_date DATE,
    
    -- Event Outcome
    event_rating DECIMAL(3,2),
    feedback_summary TEXT,
    
    -- Liquidation
    liquidation_required BOOLEAN DEFAULT TRUE,
    liquidation_submitted BOOLEAN DEFAULT FALSE,
    liquidation_date DATE,
    
    -- Transparency
    fund_breakdown JSON,
    is_public_display BOOLEAN DEFAULT TRUE,
    pre_event_transparency_published BOOLEAN DEFAULT FALSE,
    post_event_transparency_published BOOLEAN DEFAULT FALSE,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    INDEX idx_source_system (source_system),
    INDEX idx_finance_status (finance_approval_status)
);
```

### **2. Suppliers**
```sql
CREATE TABLE suppliers (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    supplier_name VARCHAR(255) NOT NULL,
    supplier_type ENUM('food_service', 'printing', 'transportation', 'supplies', 'other') NOT NULL,
    
    contact_person VARCHAR(255),
    contact_phone VARCHAR(20),
    contact_email VARCHAR(255),
    business_address TEXT,
    
    business_permit_number VARCHAR(100),
    tin_number VARCHAR(50),
    bir_registration VARCHAR(100),
    
    is_active BOOLEAN DEFAULT TRUE,
    is_verified BOOLEAN DEFAULT FALSE,
    is_preferred_supplier BOOLEAN DEFAULT FALSE,
    
    created_by_user_id BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_type (supplier_type),
    INDEX idx_active (is_active)
);
```

### **3. Supplier Products**
```sql
CREATE TABLE supplier_products (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    supplier_id BIGINT UNSIGNED NOT NULL,
    
    product_code VARCHAR(50), -- e.g., "C1" for Jollibee
    product_name VARCHAR(255) NOT NULL,
    product_description TEXT,
    product_category ENUM('meal', 'beverage', 'printing', 'material', 'service', 'other'),
    
    specifications JSON, -- {"includes": ["1pc chicken", "rice"], "size": "regular"}
    unit_of_measure VARCHAR(50), -- 'piece', 'set', 'page', etc.
    
    current_price DECIMAL(10,2) NOT NULL,
    price_effective_date DATE NOT NULL,
    
    is_available BOOLEAN DEFAULT TRUE,
    
    product_photo_url VARCHAR(500),
    price_list_document_url VARCHAR(500),
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE CASCADE,
    INDEX idx_supplier (supplier_id),
    INDEX idx_available (is_available)
);
```

### **4. Supplier Price History**
```sql
CREATE TABLE supplier_price_history (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    supplier_product_id BIGINT UNSIGNED NOT NULL,
    
    old_price DECIMAL(10,2),
    new_price DECIMAL(10,2) NOT NULL,
    effective_date DATE NOT NULL,
    
    updated_by_user_id BIGINT UNSIGNED,
    reason_for_change TEXT,
    verified_by_document_url VARCHAR(500),
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (supplier_product_id) REFERENCES supplier_products(id) ON DELETE CASCADE,
    INDEX idx_product (supplier_product_id)
);
```

### **5. Liquidation Items**
```sql
CREATE TABLE liquidation_items (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    government_program_booking_id BIGINT UNSIGNED NOT NULL,
    
    category ENUM('refreshments', 'materials', 'transportation', 'miscellaneous') NOT NULL,
    
    supplier_name VARCHAR(255),
    official_receipt_number VARCHAR(100),
    receipt_date DATE,
    receipt_image_url VARCHAR(500),
    
    item_description VARCHAR(255) NOT NULL,
    item_specification TEXT, -- e.g., "Jollibee Chickenjoy C1 (1pc chicken + rice)"
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    
    is_public_display BOOLEAN DEFAULT TRUE,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (government_program_booking_id) REFERENCES government_program_bookings(id) ON DELETE CASCADE,
    INDEX idx_category (category)
);
```

### **6. Price Verifications**
```sql
CREATE TABLE price_verifications (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    liquidation_item_id BIGINT UNSIGNED NOT NULL,
    
    market_price_min DECIMAL(10,2),
    market_price_max DECIMAL(10,2),
    market_price_average DECIMAL(10,2),
    
    government_paid_price DECIMAL(10,2) NOT NULL,
    
    is_within_market_range BOOLEAN,
    price_variance_percentage DECIMAL(5,2),
    verification_status ENUM('verified', 'questionable', 'flagged') DEFAULT 'verified',
    
    citizen_reports_count INT DEFAULT 0,
    
    verified_by_user_id BIGINT UNSIGNED,
    verified_at TIMESTAMP,
    
    FOREIGN KEY (liquidation_item_id) REFERENCES liquidation_items(id) ON DELETE CASCADE
);
```

### **7. Citizen Price Reports**
```sql
CREATE TABLE citizen_price_reports (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    liquidation_item_id BIGINT UNSIGNED NOT NULL,
    citizen_id BIGINT UNSIGNED NOT NULL,
    
    reported_government_price DECIMAL(10,2),
    reported_market_price DECIMAL(10,2),
    
    verification_location VARCHAR(255), -- Where citizen checked
    verification_date DATE,
    proof_image_url VARCHAR(500),
    
    notes TEXT,
    
    status ENUM('pending', 'reviewed', 'verified', 'dismissed') DEFAULT 'pending',
    admin_response TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (liquidation_item_id) REFERENCES liquidation_items(id) ON DELETE CASCADE,
    INDEX idx_status (status)
);
```

### **8. SMS Notifications**
```sql
CREATE TABLE sms_notifications (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    
    recipient_phone VARCHAR(20) NOT NULL,
    recipient_type ENUM('citizen', 'staff', 'admin', 'organizer'),
    
    message_content TEXT NOT NULL,
    message_type ENUM('event_announcement', 'reminder', 'update', 'transparency_report'),
    
    related_program_id BIGINT UNSIGNED,
    
    status ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
    sent_at TIMESTAMP NULL,
    
    sms_provider VARCHAR(50),
    sms_cost DECIMAL(10,4),
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_status (status)
);
```

### **9. Event Feedback**
```sql
CREATE TABLE event_feedback (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    government_program_booking_id BIGINT UNSIGNED NOT NULL,
    
    -- Collection Method
    feedback_method ENUM('qr_code', 'paper_form') NOT NULL,
    
    -- Ratings (1-5 stars)
    overall_rating TINYINT CHECK (overall_rating BETWEEN 1 AND 5),
    food_rating TINYINT CHECK (food_rating BETWEEN 1 AND 5),
    materials_rating TINYINT CHECK (materials_rating BETWEEN 1 AND 5),
    
    -- Open-ended feedback
    liked_most TEXT,
    needs_improvement TEXT,
    
    -- Would attend again?
    would_attend_again ENUM('yes', 'maybe', 'no'),
    
    -- Metadata
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (government_program_booking_id) REFERENCES government_program_bookings(id) ON DELETE CASCADE,
    INDEX idx_rating (overall_rating),
    INDEX idx_method (feedback_method)
);
```

---

## 📝 FEEDBACK COLLECTION SYSTEM

### **Purpose:**
Collect attendee feedback to improve future government programs and measure event success.

### **Two Simple Methods:**

#### **METHOD 1: QR CODE** 📱 (Digital, Instant)

**Strategic Placement:**

1. **Event Presentation (Last Slide)**
   ```
   ┌─────────────────────────────────────┐
   │  THANK YOU FOR ATTENDING!           │
   │                                     │
   │       [Large QR Code]              │
   │                                     │
   │  Please share your feedback         │
   │  It helps us improve!               │
   │                                     │
   │  Or use the paper form              │
   └─────────────────────────────────────┘
   ```
   - Display during last 5 minutes of event
   - Keep on screen while attendees exit
   - Speaker reminds people to scan

2. **On Certificate**
   ```
   ┌─────────────────────────────────────┐
   │  CERTIFICATE OF PARTICIPATION       │
   │                                     │
   │  JUAN DELA CRUZ                     │
   │                                     │
   │  Energy Conservation Seminar        │
   │  December 16, 2025                 │
   │                                     │
   │  [QR Code] → Give feedback         │
   └─────────────────────────────────────┘
   ```

3. **In Handout Materials (Last Page)**
   ```
   Page 13: FEEDBACK PAGE
   ┌─────────────────────────────────────┐
   │  Thank you for attending!           │
   │                                     │
   │  YOUR FEEDBACK:                     │
   │                                     │
   │  Option 1: Scan QR code            │
   │      [QR Code]                     │
   │                                     │
   │  Option 2: Fill form on next page  │
   └─────────────────────────────────────┘
   ```

**QR Code Links To:**
Mobile-optimized web form at:
`https://facilities.caloocan.gov.ph/feedback/GPR-2025-456`

**Mobile Form Design:**
```
┌─────────────────────────────┐
│ Event Feedback              │
├─────────────────────────────┤
│                             │
│ Overall Experience:         │
│ ⭐⭐⭐⭐⭐                    │
│ (Tap to rate)               │
│                             │
│ Food Quality:               │
│ ⭐⭐⭐⭐⭐                    │
│                             │
│ Materials Quality:          │
│ ⭐⭐⭐⭐⭐                    │
│                             │
│ What did you like?          │
│ [text box]                  │
│                             │
│ What can improve?           │
│ [text box]                  │
│                             │
│ Attend again?               │
│ ○ Yes  ○ Maybe  ○ No       │
│                             │
│ [SUBMIT]                    │
│                             │
│ Anonymous submission        │
└─────────────────────────────┘
```

---

#### **METHOD 2: PAPER FORM** 📄 (Inclusive, Works for Everyone)

**Included in Handout Packet:**

```
Page 14: FEEDBACK FORM
┌─────────────────────────────────────────────────┐
│ FEEDBACK FORM                                   │
│ Energy Conservation Seminar - Dec 16, 2025     │
├─────────────────────────────────────────────────┤
│                                                 │
│ Your feedback helps us improve!                 │
│                                                 │
│ 1. Overall Experience (circle):                 │
│    1    2    3    4    5                        │
│    Poor      OK      Excellent                  │
│                                                 │
│ 2. Food Quality:                                │
│    1    2    3    4    5                        │
│                                                 │
│ 3. Materials Quality:                           │
│    1    2    3    4    5                        │
│                                                 │
│ 4. What did you like most?                      │
│    ___________________________________          │
│    ___________________________________          │
│                                                 │
│ 5. What can be improved?                        │
│    ___________________________________          │
│    ___________________________________          │
│                                                 │
│ 6. Would you attend similar events?             │
│    ☐ Yes    ☐ Maybe    ☐ No                   │
│                                                 │
│ TEAR THIS PAGE & DROP IN BOX AT EXIT           │
│ Thank you! 💚                                   │
└─────────────────────────────────────────────────┘
```

**Handout Structure:**
```
COMPLETE HANDOUT PACKET (stapled):
- Pages 1-10: Energy conservation materials
- Pages 11-12: Additional resources
- Page 13: Feedback instructions + QR code
- Page 14: Paper feedback form (perforated/tear-off)
```

---

### **Implementation Workflow:**

#### **BEFORE EVENT:**
```
Staff Preparation:
✅ Print handouts including feedback pages (13-14)
✅ Print certificates with QR code
✅ Add feedback slide to presentation
✅ Set up simple drop box at exit (labeled "Feedback")
✅ Test QR code link
```

#### **DURING EVENT:**
```
Distribution:
✅ Hand out complete packet at registration
   (Attendees already have both feedback options)

At Closing (Last 5 minutes):
Speaker announces:
"Please give us feedback! You have two options:
 1. Scan the QR code on screen or in your materials
 2. Fill the paper form (last page) and drop at exit
 
 Your feedback helps us improve. Thank you!"

✅ Display QR code on projector screen
✅ Keep displayed while people exit
```

#### **AT EXIT:**
```
Setup:
┌──────────────────────┐
│   FEEDBACK FORMS     │
│   [Drop slot]        │
└──────────────────────┘

Staff reminder:
"If you filled the feedback form, 
 please tear and drop here. Thank you!"
```

#### **AFTER EVENT:**
```
Within 24 hours:
✅ Collect paper forms from drop box
✅ Staff enters paper data into system
✅ Scan paper forms and upload as backup
✅ Review all feedback (QR + paper)

Within 3 days:
✅ Calculate average ratings
✅ Identify common themes in comments
✅ Generate feedback summary report

Within 1 week:
✅ Publish feedback summary on transparency page
✅ Use insights to improve next event
✅ Share feedback with Energy Efficiency team
```

---

### **Cost Analysis (Extremely Low):**

```
Per Event (150 attendees):

QR Code:
- Generate QR code: FREE (online tools)
- Add to presentation slide: FREE (already using projector)
- Print on certificates: FREE (already printing)
- Add to handouts: FREE (already printing)
Subtotal: ₱0

Paper Forms:
- 2 extra pages per handout × 150: ₱30
  (Page 13: QR code info, Page 14: Paper form)
Subtotal: ₱30

Drop Box:
- Simple cardboard box: FREE (reusable)
Subtotal: ₱0

TOTAL COST PER EVENT: ₱30
(Just 2 extra pages in existing handouts!)
```

---

### **Why This Approach Works:**

**Cost-Effective:**
- ✅ Piggybacks on existing materials (handouts, certificates)
- ✅ No separate printing runs needed
- ✅ No extra posters or signage
- ✅ Minimal additional cost (₱30)

**User-Friendly:**
- ✅ Both options already in attendee's hand
- ✅ No hunting for forms
- ✅ Choose preferred method (digital or paper)
- ✅ Can fill during event or after

**Inclusive:**
- ✅ QR code for tech-savvy attendees
- ✅ Paper form for elderly or non-smartphone users
- ✅ Anonymous submissions (no pressure)
- ✅ Multiple touchpoints (presentation, certificate, handouts)

**Staff-Efficient:**
- ✅ No extra distribution work
- ✅ Just place drop box at exit
- ✅ Collect forms after event
- ✅ Simple data entry

**Professional:**
- ✅ Not intrusive or pushy
- ✅ Voluntary participation
- ✅ Respects attendees' time
- ✅ Shows government values feedback

---

### **Expected Response Rate:**

```
With this dual approach:

QR Code submissions: 30-35% (45-53 responses)
- Tech-savvy attendees
- Quick and convenient
- Instant data entry

Paper Form submissions: 20-25% (30-38 responses)
- Elderly or non-smartphone users
- Prefer traditional method
- Can fill during event

TOTAL EXPECTED: 75-90 responses (50-60% rate)
This is EXCELLENT for voluntary, non-incentivized feedback!
```

---

### **Using Feedback Data:**

**For Transparency Reports:**
- ✅ Display average ratings on public transparency page
- ✅ Show attendee satisfaction level
- ✅ Include representative comments (positive and constructive)
- ✅ Demonstrate accountability

**For Improvement:**
- ✅ Identify what worked well (repeat in future)
- ✅ Identify areas needing improvement
- ✅ Adjust food/material choices based on feedback
- ✅ Improve event organization

**For Reporting:**
- ✅ Include in post-event reports to Energy Efficiency
- ✅ Share with Finance to show program impact
- ✅ Use in budget justification for future events
- ✅ Demonstrate citizen satisfaction

---

## 🔗 API ENDPOINTS

### **INBOUND (Receive from Energy Efficiency System)**

#### **1. Request Facility for Government Program**
```
POST /api/facilities/government-program/request
Content-Type: application/json
Authorization: Bearer {api_key}

Request Body:
{
  "request_id": "EEC-2025-001",
  "requested_by_system": "Energy Efficiency and Conservation",
  "requesting_department": "Barangay 188",
  "organizer_name": "Juan dela Cruz",
  "organizer_title": "Barangay Energy Officer",
  "organizer_contact": "09171234567",
  "organizer_email": "juan@caloocan.gov.ph",
  
  "event_details": {
    "event_name": "Energy Conservation Seminar",
    "event_type": "seminar",
    "event_description": "Community education on electricity saving",
    "expected_attendees": 150,
    "number_of_speakers": 3,
    "speaker_details": ["DOE Representative", "Meralco Specialist", "Local Expert"]
  },
  
  "schedule_requirements": {
    "preferred_date": "2025-12-15",
    "alternative_dates": ["2025-12-16", "2025-12-17"],
    "start_time": "14:00:00",
    "end_time": "17:00:00",
    "setup_time_needed": 30
  },
  
  "facility_requirements": {
    "preferred_location": "Barangay 188 or nearby",
    "capacity_needed": 150,
    "required_amenities": ["Projector", "Sound System", "AC"]
  },
  
  "fund_request": {
    "total_amount_requested": 5000.00,
    "purpose": "Event consumables (food, materials, supplies)",
    "funding_source": "City Budget / DOE Program Fund"
  }
}

Response:
{
  "success": true,
  "request_id": "EEC-2025-001",
  "internal_tracking_id": "GPR-2025-456",
  "status": "pending_review",
  "estimated_response_time": "24-48 hours",
  "message": "Request received. Admin will coordinate with you."
}
```

#### **2. Post-Event Completion Report**
```
POST /api/facilities/government-program/completion
Content-Type: application/json
Authorization: Bearer {api_key}

Request Body:
{
  "request_id": "EEC-2025-001",
  "booking_id": "PFR-BOOK-2025-456",
  
  "event_outcome": {
    "actual_attendees": 142,
    "event_rating": 4.8,
    "feedback_summary": "Very informative seminar",
    "community_impact": "142 residents trained on energy conservation"
  },
  
  "program_metrics": {
    "knowledge_improvement": "85% pass post-test",
    "behavioral_indicators": "Committed to reduce electricity usage",
    "follow_up_requests": 15
  }
}

Response:
{
  "success": true,
  "message": "Completion report received and recorded"
}
```

---

### **OUTBOUND (Send to Energy Efficiency System)**

#### **1. Booking Approval Notification**
```
POST {energy_system_webhook}/facility-booking/approved
Content-Type: application/json
Authorization: Bearer {api_key}

Request Body:
{
  "request_id": "EEC-2025-001",
  "booking_id": "PFR-BOOK-2025-456",
  "approval_status": "approved",
  "approved_date": "2025-12-04",
  
  "facility_booking": {
    "facility_id": 123,
    "facility_name": "Barangay 188 Community Center",
    "facility_address": "Camarin Road, Barangay 188, Caloocan City",
    "booking_date": "2025-12-15",
    "start_time": "14:00:00",
    "end_time": "17:00:00",
    "setup_from": "13:30:00",
    "capacity": 200,
    
    "included_amenities": [
      "Projector and Screen",
      "Sound System with 2 mics",
      "Air Conditioning",
      "150 Chairs",
      "20 Tables"
    ],
    
    "facility_fee": 0.00,
    "waiver_reason": "Government program exemption",
    "booking_qr_code": "https://facilities.caloocan.gov.ph/qr/456"
  },
  
  "fund_approval": {
    "approved_amount": 5000.00,
    "check_number": "CH-2025-789",
    "release_date": "2025-12-10",
    "claimant": "Juan dela Cruz",
    "claim_location": "City Treasury Office"
  },
  
  "contact_information": {
    "facility_manager": "Maria Santos",
    "facility_contact": "09191234567",
    "admin_contact": "Public Facilities Office - 09201234567"
  },
  
  "transparency_url": "https://facilities.caloocan.gov.ph/transparency/GPR-2025-456"
}

Response (from Energy Efficiency):
{
  "success": true,
  "message": "Booking confirmation received"
}
```

#### **2. Booking Status Update**
```
POST {energy_system_webhook}/facility-booking/status-update
Content-Type: application/json
Authorization: Bearer {api_key}

Request Body:
{
  "request_id": "EEC-2025-001",
  "booking_id": "PFR-BOOK-2025-456",
  "status": "rescheduled",
  "new_date": "2025-12-16",
  "reason": "Facility maintenance on original date",
  "alternative_facilities": [...]
}
```

---

### **EXTERNAL (To Finance System)**

#### **1. Submit Fund Request**
```
POST /api/finance/fund-request
Content-Type: application/json
Authorization: Bearer {api_key}

Request Body:
{
  "finance_request_id": "PFR-FIN-2025-001",
  "originating_system": "Public Facilities Reservation",
  "requesting_department": "Barangay 188 (via Energy Efficiency)",
  
  "fund_details": {
    "total_amount": 5000.00,
    "purpose": "Energy Conservation Seminar",
    "event_date": "2025-12-15",
    "beneficiaries": "150 Barangay 188 residents",
    
    "breakdown": [
      {
        "item": "Refreshments (Jollibee meals)",
        "amount": 2500.00,
        "justification": "Free meals for 150 attendees"
      },
      {
        "item": "Training Materials",
        "amount": 2000.00,
        "justification": "Handouts, workbooks for 150 attendees"
      },
      {
        "item": "Speaker Transportation",
        "amount": 300.00,
        "justification": "DOE speaker travel from Manila"
      },
      {
        "item": "Miscellaneous",
        "amount": 200.00,
        "justification": "Certificates, supplies"
      }
    ]
  },
  
  "organizer_info": {
    "name": "Juan dela Cruz",
    "title": "Barangay Energy Officer",
    "contact": "09171234567"
  }
}

Response (from Finance):
{
  "success": true,
  "finance_request_id": "PFR-FIN-2025-001",
  "status": "pending_review",
  "estimated_approval_time": "24-48 hours"
}
```

---

## 🔄 KEY WORKFLOWS

### **WORKFLOW 1: Complete Booking Process**

```
Step 1: Energy Efficiency submits request
        ↓
Step 2: Public Facilities Admin receives
        → Reviews request details
        ↓
Step 3: Admin calls organizer
        → Discusses fund breakdown
        → Gets itemized list
        → Records call notes
        ↓
Step 4: Admin coordinates DOE speaker (offline)
        → Checks availability
        → Confirms speaker
        ↓
Step 5: Admin requests fund from Finance (API)
        → Submits detailed breakdown
        → Waits for approval
        ↓
Step 6: Finance approves (webhook notification)
        → Receives check number
        → Receives release date
        ↓
Step 7: Admin verifies all confirmed:
        ✅ Speaker available
        ✅ Funds approved
        ✅ Facility available
        ↓
Step 8: Admin sets final date & assigns facility
        → Facility fee = ₱0.00 (waived)
        → Equipment included (free)
        ↓
Step 9: System sends confirmation to Energy Efficiency (API)
        → Booking details
        → Fund details
        → Transparency URL
        ↓
Step 10: Admin publishes pre-event transparency
         → Citizens see planned budget
         → Citizens see what's free for them
```

---

### **WORKFLOW 2: Post-Event Transparency**

```
Step 1: Event happens
        ↓
Step 2: Energy Efficiency submits liquidation
        → Uploads official receipts
        → Itemizes purchases (Jollibee C1 @ ₱89 each)
        → Provides supplier info
        → Uploads product photos
        ↓
Step 3: Admin creates liquidation report
        → Links items to suppliers in database
        → Verifies prices match market rates
        → System flags if prices seem high
        ↓
Step 4: Admin inputs actual data
        → Actual attendees: 142 (expected 150)
        → Actual spent: ₱4,850 (budget ₱5,000)
        → Savings: ₱150
        ↓
Step 5: System publishes post-event transparency
        → Citizens see actual spending
        → Citizens compare planned vs actual
        → Citizens view receipts
        → Citizens can verify prices
        ↓
Step 6: Citizens verify prices (optional)
        → Check Jollibee: "Yes, C1 is ₱89" ✅
        → Report if mismatch found
        ↓
Step 7: Citizens provide feedback
        → Rate event
        → Comment on transparency
        → Thank government
```

---

### **WORKFLOW 3: Price Verification by Citizens**

```
Step 1: Citizen views transparency report
        "Government paid ₱89 for Chickenjoy C1"
        ↓
Step 2: Citizen checks actual Jollibee store
        "Menu shows C1 = ₱89" ✅
        ↓
Step 3a: IF price matches
         → Citizen trusts government
         → No action needed
         
Step 3b: IF price doesn't match
         → Citizen submits discrepancy report
         → Uploads proof (menu photo)
         ↓
Step 4: Admin reviews citizen report
        → Investigates discrepancy
        → Checks with supplier
        ↓
Step 5: Admin responds to citizen
        → Explains reason for difference (if valid)
        → Or acknowledges error and takes action
```

---

## ✅ IMPLEMENTATION PRIORITY

### **Phase 1 - Core Functionality (MVP)**
1. ✅ Database tables: `government_program_bookings`, `suppliers`, `supplier_products`
2. ✅ API: Receive facility requests from Energy Efficiency
3. ✅ Admin: Request management dashboard
4. ✅ Admin: Organizer coordination (call log)
5. ✅ Admin: Finance integration (submit fund request)
6. ✅ Admin: Facility assignment with fee waiver
7. ✅ API: Send booking confirmation to Energy Efficiency
8. ✅ Citizen: View upcoming programs (basic)

### **Phase 2 - Transparency & Supplier Management**
9. ✅ Super Admin: Supplier management
10. ✅ Super Admin: Product catalog management
11. ✅ Database table: `supplier_price_history`
12. ✅ Admin: Pre-event transparency creation
13. ✅ Admin: Post-event liquidation report
14. ✅ Database table: `liquidation_items`
15. ✅ Citizen: View pre-event transparency
16. ✅ Citizen: View post-event transparency (planned vs actual)

### **Phase 3 - Advanced Features**
17. ✅ Citizen: Price verification & discrepancy reporting
18. ✅ Database tables: `price_verifications`, `citizen_price_reports`
19. ✅ Admin: Quotation management
20. ✅ SMS notifications for all stakeholders
21. ✅ Database table: `sms_notifications`
22. ✅ Citizen: Event registration
23. ✅ Staff: Event day support features
24. ✅ Analytics and reporting dashboard

---

## 💡 KEY PRINCIPLES TO REMEMBER

### **1. DATABASE-DRIVEN (NO HARDCODING)**
- ❌ Don't hardcode: `$chickenjoyPrice = 89;`
- ✅ Get from database: `$product->current_price`
- ✅ All suppliers in database
- ✅ All products/prices in database
- ✅ Price history tracked for audit

### **2. REAL, VERIFIABLE PRICES**
- ✅ Government shows: "Jollibee C1 @ ₱89"
- ✅ Citizen checks Jollibee: "Menu = ₱89" ✅
- ✅ Prices match = Trust increases
- ✅ Citizens can report mismatches
- ✅ Complete accountability

### **3. COMPLETE TRANSPARENCY**
- ✅ PRE-EVENT: Show planned budget
- ✅ POST-EVENT: Show actual spending
- ✅ ITEMIZED: Exact items, quantities, prices
- ✅ RECEIPTS: Upload all official receipts
- ✅ PHOTOS: Show what was provided
- ✅ COMPARISON: Planned vs Actual

### **4. CITIZENS PAY NOTHING**
- ✅ Facility rental: FREE (waived)
- ✅ Equipment: FREE (included)
- ✅ Admission: FREE (no ticket)
- ✅ Food: FREE (government provides)
- ✅ Materials: FREE (government provides)
- ❌ Citizen only pays: Transportation (their own)

### **5. FUNDS FOR CONSUMABLES ONLY**
- ✅ ₱5,000 request is for:
  - Refreshments (food for attendees)
  - Training materials (handouts, pens)
  - Transportation (speaker travel)
  - Supplies (certificates, name tags)
- ❌ NOT for:
  - Facility rental (waived)
  - Equipment (included)
  - City Hall staff (on payroll)
  - DOE staff (on payroll)

### **6. OFFLINE + ONLINE COORDINATION**
- ✅ ONLINE: System-to-system API
- ✅ OFFLINE: Admin calls organizer
- ✅ OFFLINE: Admin contacts DOE speaker
- ✅ ONLINE: Submit to Finance via API
- ✅ HYBRID: Best of both worlds

---

## 📞 COORDINATION WITH ENERGY EFFICIENCY TEAM

### **Technical Coordination Needed:**
1. ✅ API authentication method (Bearer token, OAuth2?)
2. ✅ Webhook URLs for notifications
3. ✅ Data format agreements (JSON schemas)
4. ✅ Error handling procedures
5. ✅ Rate limiting (how many requests/minute?)
6. ✅ Test environment availability
7. ✅ Technical contact for integration issues
8. ✅ API documentation exchange
9. ✅ Security protocols (encryption, HTTPS)
10. ✅ Downtime notification procedures

---

## 📊 SUCCESS METRICS

### **System Performance:**
- ⏱️ Average request processing time < 48 hours
- ✅ 99% uptime for API integration
- 📈 Zero data loss in transactions
- 🔒 Zero security breaches

### **Transparency Impact:**
- 👀 100% of citizens can view pre-event plans
- 👀 100% of citizens can view post-event reports
- ✅ 100% of receipts uploaded and visible
- 📊 < 1% price discrepancy reports

### **User Satisfaction:**
- ⭐ Event ratings > 4.5/5
- ⭐ Transparency ratings > 4.5/5
- 💬 Positive feedback > 90%
- 🎯 Repeat attendance rates increase

### **Government Accountability:**
- 💰 100% of funds accounted for
- 📄 100% liquidation reports submitted on time
- ✅ Zero COA audit findings
- 🏆 Transparency award eligibility

---

**END OF DOCUMENT**

**Last Updated:** December 3, 2025  
**Prepared By:** AI Assistant  
**Approved By:** [Pending User Approval]

---

## 📋 CHANGE LOG

| Version | Date | Changes |
|---------|------|---------|
| 1.0 | Dec 3, 2025 | Initial document created with complete integration specifications |

