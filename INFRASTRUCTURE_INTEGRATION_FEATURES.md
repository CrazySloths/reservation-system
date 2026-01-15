# 🏗️ INFRASTRUCTURE PROJECT MANAGEMENT INTEGRATION - FEATURE LIST

**Document Version:** 1.1 (Database-Driven Design)  
**Date:** November 30, 2025  
**System:** Public Facilities Reservation System  
**External Integration:** Infrastructure Project Management System

⚠️ **IMPORTANT:** This system follows DATABASE-DRIVEN design principles. All configurable data MUST be stored in database tables, NOT hardcoded.

---

## 📋 INTEGRATION OVERVIEW

### **Integration Flow:**
```
Phase 1: Request New Facility → Infrastructure PM (Project Planning & Budgeting)
Phase 2: Receive Contractor Info ← Infrastructure PM (Contractor & Bidding Management)
Phase 3: Track Progress ← Infrastructure PM (Construction Progress Tracking)
Phase 4: Facility Turnover ← Infrastructure PM (Project Completion & Turnover)
Phase 5: Warranty Claims → Infrastructure PM (Project Completion & Turnover)
```

### **Integration Points:**
- **Outbound:** Request Project Plan
- **Inbound:** Provide Project with Contractor, Progress Updates, Turnover Certificate
- **Optional:** Warranty/Defect Reports

### **Submodule Mapping:**

| Public Facilities Submodule | ↔️ | Infrastructure PM Submodule | Purpose |
|------------------------------|---|----------------------------|---------|
| **Facility Directory and Calendar** | → | **Project Planning and Budgeting** | Request new facility construction |
| **Facility Directory and Calendar** | ← | **Contractor and Bidding Management** | Receive contractor assignment |
| **Facility Directory and Calendar** | ← | **Construction Progress Tracking** | Monitor construction status & milestones |
| **Facility Directory and Calendar** | ← | **Project Completion and Turnover Reports** | Receive completed facility & activate |
| **Usage Reports and Feedback** | → | **Project Completion and Turnover Reports** | Report defects during warranty period |

---

## 🎯 DATABASE-DRIVEN DESIGN PRINCIPLES

**IMPORTANT:** All configurable data MUST be stored in the database, NOT hardcoded in the application.

### **What Should Be Database-Driven:**

1. ✅ **Facility Types** - Super Admin can add/edit/delete types
2. ✅ **Amenities** - Maintained in master table
3. ✅ **Pricing Rates** - Configurable per size category
4. ✅ **Capacity Ranges** - Small/Medium/Large thresholds
5. ✅ **Cities/Districts/Barangays** - Use existing auth_db tables
6. ✅ **Defect Types** - Warranty claim categories
7. ✅ **System Settings** - All configurable parameters
8. ✅ **Priority Levels** - Request priority options
9. ✅ **Status Options** - User-friendly labels

### **What Can Be ENUMs (Not Hardcoding):**
- Core workflow states (pending, approved, rejected)
- Payment states (unpaid, paid, refunded)
- Size categories (small, medium, large) - but VALUES are in config table

### **Required Master Data Tables:**

#### **1. System Configurations**
```sql
CREATE TABLE system_configurations (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    module VARCHAR(100) NOT NULL,
    config_key VARCHAR(100) NOT NULL,
    config_value TEXT NOT NULL,
    data_type ENUM('string', 'integer', 'decimal', 'boolean', 'json') DEFAULT 'string',
    description TEXT,
    is_editable BOOLEAN DEFAULT TRUE,
    updated_by_user_id BIGINT UNSIGNED,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_config (module, config_key)
);

-- Example data:
INSERT INTO system_configurations (module, config_key, config_value, data_type, description) VALUES
('facilities', 'small_capacity_max', '150', 'integer', 'Maximum capacity for small facilities'),
('facilities', 'medium_capacity_max', '500', 'integer', 'Maximum capacity for medium facilities'),
('pricing', 'small_facility_rate', '100.00', 'decimal', 'Per-person rate for small facilities'),
('pricing', 'medium_facility_rate', '150.00', 'decimal', 'Per-person rate for medium facilities'),
('pricing', 'large_facility_rate', '200.00', 'decimal', 'Per-person rate for large facilities'),
('booking', 'max_advance_days', '90', 'integer', 'How far ahead citizens can book'),
('warranty', 'default_period_months', '12', 'integer', 'Default warranty period for new facilities'),
('session', 'timeout_minutes', '2', 'integer', 'Session timeout duration');
```

#### **2. Facility Types**
```sql
CREATE TABLE facility_types (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    type_name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(50),
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_by_user_id BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_active (is_active),
    INDEX idx_sort (sort_order)
);

-- Example data:
INSERT INTO facility_types (type_name, description, icon, sort_order) VALUES
('Sports Complex', 'Multi-purpose sports facilities', 'trophy', 1),
('Multi-Purpose Hall', 'Indoor halls for various events', 'building', 2),
('Conference Center', 'Professional meeting spaces', 'briefcase', 3),
('Community Center', 'Local community gathering places', 'users', 4),
('Auditorium', 'Large capacity event venues', 'theater', 5);
```

#### **3. Amenities Master**
```sql
CREATE TABLE amenities_master (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    amenity_name VARCHAR(100) NOT NULL UNIQUE,
    category VARCHAR(50), -- 'sports', 'utilities', 'accessibility', 'facilities'
    icon VARCHAR(50),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_active (is_active)
);

-- Example data:
INSERT INTO amenities_master (amenity_name, category, icon) VALUES
('Basketball Court', 'sports', 'basketball'),
('Volleyball Court', 'sports', 'volleyball'),
('Bleachers', 'facilities', 'seats'),
('Restrooms', 'utilities', 'restroom'),
('Parking Area', 'utilities', 'parking'),
('Air Conditioning', 'utilities', 'ac'),
('Audio System', 'facilities', 'speaker'),
('Stage', 'facilities', 'stage'),
('WiFi', 'utilities', 'wifi'),
('Wheelchair Accessible', 'accessibility', 'wheelchair');
```

#### **4. Priority Levels**
```sql
CREATE TABLE priority_levels (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    level_name VARCHAR(50) NOT NULL UNIQUE,
    level_value INT NOT NULL, -- 1=highest, 3=lowest
    color_code VARCHAR(20), -- For UI display
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_value (level_value)
);

-- Example data:
INSERT INTO priority_levels (level_name, level_value, color_code, description) VALUES
('High', 1, 'red', 'Urgent projects requiring immediate attention'),
('Medium', 2, 'yellow', 'Standard priority projects'),
('Low', 3, 'green', 'Non-urgent projects for future consideration');
```

#### **5. Defect Types**
```sql
CREATE TABLE defect_types (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    type_name VARCHAR(100) NOT NULL UNIQUE,
    category VARCHAR(50), -- 'structural', 'electrical', 'plumbing', etc.
    description TEXT,
    severity_default ENUM('low', 'medium', 'high', 'critical'),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category)
);

-- Example data:
INSERT INTO defect_types (type_name, category, severity_default) VALUES
('Wall Cracks', 'structural', 'medium'),
('Foundation Issues', 'structural', 'critical'),
('Electrical Wiring Problems', 'electrical', 'high'),
('Lighting Issues', 'electrical', 'medium'),
('Plumbing Leaks', 'plumbing', 'high'),
('Drainage Problems', 'plumbing', 'medium'),
('Paint Peeling', 'finishing', 'low'),
('Door/Window Issues', 'finishing', 'medium');
```

---

## 🎛️ SUPER ADMIN: SYSTEM MANAGEMENT FEATURES

### **New Section: System Settings Management**
**Location:** `/superadmin/settings`

This is a CRITICAL feature - Super Admin can configure ALL system parameters without developer intervention.

#### **Features:**

**A. Pricing Management**
- ✅ Edit per-person rates for each facility size
- ✅ Set effective dates for rate changes
- ✅ View pricing history
- ✅ Set minimum fees

**B. Capacity Configuration**
- ✅ Define small/medium/large capacity thresholds
- ✅ Update anytime as business needs change

**C. Facility Types Management**
- ✅ Add new facility types
- ✅ Edit existing types (name, description, icon)
- ✅ Reorder types (drag-and-drop sort)
- ✅ Activate/Deactivate types
- ✅ Delete unused types

**D. Amenities Management**
- ✅ Add new amenities
- ✅ Edit amenity details
- ✅ Categorize amenities
- ✅ Set icons for each amenity
- ✅ Activate/Deactivate amenities

**E. System Parameters**
- ✅ Edit warranty period (months)
- ✅ Edit booking advance limit (days)
- ✅ Edit session timeout (minutes)
- ✅ Edit notification settings
- ✅ Audit trail for all changes

**F. Defect Types Management**
- ✅ Add/edit defect categories
- ✅ Set default severity levels
- ✅ Organize by category

**UI Example:**
```
┌─────────────────────────────────────────────┐
│ System Settings > Pricing Management       │
├─────────────────────────────────────────────┤
│                                             │
│ Per-Person Rates:                           │
│                                             │
│ Small Facilities (1-150 people)            │
│ Current Rate: ₱100.00 [Edit]              │
│                                             │
│ Medium Facilities (151-500 people)         │
│ Current Rate: ₱150.00 [Edit]              │
│                                             │
│ Large Facilities (501+ people)             │
│ Current Rate: ₱200.00 [Edit]              │
│                                             │
│ [Save Changes]                              │
└─────────────────────────────────────────────┘
```

---

## ⚠️ IMPORTANT: ROLE DEFINITIONS

### **EIS SUPER ADMIN** (Lead Programmer - Technical Role)
- **Created by:** EIS Lead Programmer (centralized in lgu1_auth)
- **Access:** All 10 subsystems (technical oversight)
- **Focus:** System configuration, database management, API setup
- **Time commitment:** Occasional (setup, maintenance, troubleshooting)

**NOT responsible for day-to-day Public Facilities operations.**

### **ADMIN** (Operations Manager - Primary Operational Role)
- **Created in:** Public Facilities subsystem
- **Access:** Public Facilities Reservation System only
- **Focus:** Daily operations, coordination, facility management
- **Time commitment:** Full-time (main working role)

**This is the PRIMARY role that handles all operational features below.**

---

## 🆕 NEW FEATURES BY ROLE

### **1. ADMIN** ⭐ (Primary Operational Role)

#### **A. Request New Facility**
**Location:** `/superadmin/facilities/request-new`

**Features:**
- ✅ Create facility construction request form
- ✅ Select proposed land from Urban Planning data
- ✅ Specify facility details:
  - Facility name
  - Facility type (dropdown from `facility_types` table)
  - Size category (auto-calculated based on capacity + `system_configurations`)
  - Target capacity (number of people)
  - Estimated area (sqm)
  - Required amenities (multi-select from `amenities_master` table)
- ✅ Set budget estimate
- ✅ Set priority level (dropdown from `priority_levels` table)
- ✅ Add justification/demand analysis
- ✅ Submit request to Infrastructure PM via API
- ✅ Save draft requests

**Data Sent to Infrastructure PM:**
```json
{
  "request_id": "PFR-2025-001",
  "requested_by_system": "Public Facilities Reservation",
  "requested_by_user_id": 1,
  "requested_by_user_name": "Admin Name",
  "request_date": "2025-11-30 14:30:00",
  
  "facility_details": {
    "facility_name": "Nueva Caloocan Sports Complex",
    "facility_type_id": 1,
    "facility_type": "Sports Complex",
    "size_category": "large",
    "target_capacity": 1500,
    "estimated_area_sqm": 5000,
    "purpose": "To address the growing demand for large-scale sports events",
    "target_amenities": [
      {"id": 1, "name": "Basketball court"},
      {"id": 2, "name": "Volleyball court"},
      {"id": 3, "name": "Bleachers"},
      {"id": 4, "name": "Restrooms"},
      {"id": 5, "name": "Parking area"}
    ]
  },
  
  "location_details": {
    "proposed_land_id": "LAND-2025-078",
    "city": "Caloocan",
    "district": "District 1",
    "barangay": "Barangay 188",
    "address": "Camarin Road, Caloocan City",
    "latitude": 14.7231,
    "longitude": 120.9612,
    "lot_area_sqm": 8000,
    "zoning_classification": "Commercial/Recreational",
    "ownership_status": "Government-owned"
  },
  
  "budget_estimate": {
    "initial_estimate": 15000000.00,
    "currency": "PHP",
    "funding_source": "City Budget 2025",
    "priority_level": "high"
  },
  
  "timeline_requirements": {
    "target_start_date": "2026-01-15",
    "target_completion_date": "2026-12-31",
    "urgency": "high"
  },
  
  "justification": {
    "demand_analysis": "Current facilities are overbooked 85% of the time",
    "community_impact": "Will serve 50,000+ residents in District 1",
    "revenue_projection": "Expected ₱500,000 annual revenue"
  }
}
```

---

#### **B. Construction Projects Dashboard**
**Location:** `/superadmin/facilities/construction-projects`

**Features:**
- ✅ List all facility construction projects
- ✅ Filter by status:
  - Pending (awaiting Infrastructure PM response)
  - Approved (contractor assigned)
  - Under Construction (with progress %)
  - Completed (ready for turnover)
  - Rejected (with reason)
- ✅ View project details:
  - Project ID & Reference
  - Facility name & type
  - Contractor information
  - Budget (estimated vs approved)
  - Timeline (start date, completion date, duration)
  - Current progress percentage
  - Milestone status
- ✅ Track construction milestones
- ✅ View project team contacts
- ✅ Download project documents
- ✅ Real-time status updates from Infrastructure PM

**Data Received from Infrastructure PM:**
```json
{
  "project_id": "IPM-2025-045",
  "reference_request_id": "PFR-2025-001",
  "project_status": "approved",
  
  "project_details": {
    "project_name": "Construction of Nueva Caloocan Sports Complex",
    "project_code": "INFRA-2025-SPORTS-001",
    "approved_budget": 14500000.00,
    "project_category": "Construction",
    "project_description": "Construction of multi-purpose sports complex"
  },
  
  "contractor_information": {
    "contractor_name": "ABC Construction Corporation",
    "contractor_id": "CON-12345",
    "contractor_license": "PCAB-AAA-2024-001",
    "contact_person": "Engr. Juan dela Cruz",
    "contact_email": "juandc@abcconstruction.ph",
    "contact_phone": "09171234567",
    "company_address": "123 Builder St., Manila",
    "bid_amount": 14500000.00,
    "awarded_date": "2025-12-15"
  },
  
  "timeline": {
    "contract_start_date": "2026-01-15",
    "estimated_completion_date": "2026-12-31",
    "duration_days": 350,
    "mobilization_date": "2026-01-10"
  },
  
  "budget_breakdown": {
    "total_approved": 14500000.00,
    "mobilization_fee": 1450000.00,
    "progress_billing": [
      {"phase": "Foundation", "amount": 3625000.00, "percentage": 25},
      {"phase": "Structural", "amount": 4350000.00, "percentage": 30},
      {"phase": "Finishing", "amount": 3625000.00, "percentage": 25},
      {"phase": "Final", "amount": 1450000.00, "percentage": 10}
    ],
    "retention": 725000.00
  },
  
  "project_team": {
    "project_manager": "Engr. Maria Santos",
    "site_engineer": "Engr. Pedro Reyes",
    "safety_officer": "Engr. Rosa Garcia"
  },
  
  "milestones": [
    {"milestone": "Site Preparation", "target_date": "2026-02-15"},
    {"milestone": "Foundation Complete", "target_date": "2026-04-30"},
    {"milestone": "Structural Complete", "target_date": "2026-08-31"},
    {"milestone": "Final Inspection", "target_date": "2026-12-20"},
    {"milestone": "Turnover", "target_date": "2026-12-31"}
  ],
  
  "materials_breakdown": [
    {
      "category": "structural",
      "total_cost": 6100000.00,
      "items": [
        {
          "material": "Concrete Grade 40",
          "quantity": 500,
          "unit": "cubic meters",
          "unit_cost": 5000.00,
          "total_cost": 2500000.00,
          "supplier": "XYZ Concrete Corp",
          "supplier_license": "PCAB-2024-789"
        },
        {
          "material": "Steel Reinforcement Bars",
          "quantity": 80,
          "unit": "tons",
          "unit_cost": 45000.00,
          "total_cost": 3600000.00,
          "supplier": "ABC Steel Inc",
          "supplier_license": "DTI-2024-456"
        }
      ]
    },
    {
      "category": "finishing",
      "total_cost": 3600000.00,
      "items": [
        {
          "material": "Sports Flooring (Rubber)",
          "quantity": 2000,
          "unit": "square meters",
          "unit_cost": 800.00,
          "total_cost": 1600000.00,
          "supplier": "Sports Floor Co",
          "supplier_license": "DTI-2024-321"
        },
        {
          "material": "Roofing System (Pre-engineered)",
          "quantity": 1,
          "unit": "set",
          "unit_cost": 2000000.00,
          "total_cost": 2000000.00,
          "supplier": "Roof Masters Inc",
          "supplier_license": "PCAB-2024-555"
        }
      ]
    },
    {
      "category": "equipment",
      "total_cost": 2400000.00,
      "items": [
        {
          "material": "Aluminum Bleachers",
          "quantity": 1000,
          "unit": "seats",
          "unit_cost": 1200.00,
          "total_cost": 1200000.00,
          "supplier": "Stadium Equipment Ltd",
          "supplier_license": "DTI-2024-888"
        },
        {
          "material": "Sports Lighting System",
          "quantity": 20,
          "unit": "units",
          "unit_cost": 60000.00,
          "total_cost": 1200000.00,
          "supplier": "Lighting Solutions Corp",
          "supplier_license": "DTI-2024-999"
        }
      ]
    }
  ],
  
  "transparency_documents": {
    "budget_approval_url": "https://infra.caloocan.gov.ph/docs/budget-approval-IPM-2025-045.pdf",
    "contractor_bidding_results_url": "https://infra.caloocan.gov.ph/docs/bidding-IPM-2025-045.pdf",
    "building_permit_url": "https://permits.caloocan.gov.ph/BP-2025-12345.pdf",
    "environmental_clearance_url": "https://infra.caloocan.gov.ph/docs/env-clearance-IPM-2025-045.pdf",
    "procurement_documents_url": "https://infra.caloocan.gov.ph/docs/procurement-IPM-2025-045.pdf",
    "construction_photos_album_url": "https://infra.caloocan.gov.ph/photos/IPM-2025-045"
  },
  
  "cost_tracking": {
    "total_approved": 14500000.00,
    "spent_to_date": 10875000.00,
    "percentage_spent": 75.0,
    "remaining_budget": 3625000.00,
    "is_on_budget": true,
    "budget_variance_percentage": 0.0
  }
}
```

---

#### **C. Progress Tracking**
**Location:** `/superadmin/facilities/construction-projects/{id}/progress`

**Features:**
- ✅ View detailed construction progress
- ✅ Visual progress bar (0-100%)
- ✅ Milestone timeline with completion status
- ✅ Progress photos from Infrastructure PM (optional)
- ✅ Construction delay alerts
- ✅ Budget variance tracking
- ✅ Site visit logs (optional)

**Milestone Update Format:**
```json
{
  "project_id": "IPM-2025-045",
  "milestone": "Foundation Complete",
  "target_date": "2026-04-30",
  "actual_date": "2026-04-28",
  "progress_percentage": 25,
  "status": "completed",
  "notes": "Completed 2 days ahead of schedule",
  "photos": ["url1", "url2"]
}
```

---

#### **D. Facility Turnover & Activation**
**Location:** `/superadmin/facilities/construction-projects/{id}/turnover`

**Features:**
- ✅ Receive turnover notification from Infrastructure PM
- ✅ View completion certificate
- ✅ Review project completion report
- ✅ View as-built plans/blueprints
- ✅ Check warranty information
- ✅ Conduct final inspection checklist:
  - Structural integrity
  - Amenities functionality
  - Safety compliance
  - Cleanliness
- ✅ Accept/Reject turnover
- ✅ If accepted → Activate facility for booking
- ✅ If rejected → Send back to Infrastructure PM with issues
- ✅ Set facility operational status

**Turnover Data:**
```json
{
  "project_id": "IPM-2025-045",
  "reference_request_id": "PFR-2025-001",
  "project_status": "completed",
  "completion_certificate": "CERT-2026-001",
  "handover_date": "2026-12-31",
  "warranty_period_months": 12,
  "warranty_valid_until": "2027-12-31",
  "as_built_plans_url": "https://...",
  "final_inspection_report": {
    "inspection_date": "2026-12-20",
    "inspector_name": "Engr. Maria Santos",
    "status": "passed",
    "notes": "All systems operational"
  },
  "permit_status": {
    "building_permit": "BP-2025-12345",
    "occupancy_permit": "OP-2026-789",
    "fire_safety_certificate": "FSC-2026-456"
  }
}
```

---

#### **E. Warranty & Defect Management**
**Location:** `/superadmin/facilities/warranty-claims`

**Features:**
- ✅ View all facilities under warranty
- ✅ Receive defect reports from Admin/Staff
- ✅ Review defect details with photos
- ✅ Submit warranty claim to Infrastructure PM
- ✅ Track claim status:
  - Submitted
  - Under Review
  - Approved
  - Contractor Dispatched
  - Fixed
  - Verified
- ✅ Close warranty claim after verification
- ✅ Warranty expiration alerts

**Warranty Claim Format:**
```json
{
  "claim_id": "WC-2027-001",
  "facility_id": 123,
  "project_id": "IPM-2025-045",
  "defect_type": "structural",
  "severity": "medium",
  "description": "Crack in wall near entrance",
  "reported_by_user_id": 5,
  "reported_date": "2027-05-15",
  "photos": ["photo1.jpg", "photo2.jpg"],
  "warranty_valid_until": "2027-12-31",
  "location_description": "Main entrance, east wall"
}
```

---

### **2. EIS SUPER ADMIN** ⭐ (Technical Oversight Only)

#### **A. System Configuration**
**Location:** `/superadmin/settings/infrastructure-integration`

**Features:**
- ✅ Configure API connection to Infrastructure PM system
- ✅ Manage API keys and authentication
- ✅ Set up webhook endpoints
- ✅ Configure integration settings
- ✅ Monitor integration health
- ✅ View system logs
- ✅ Override any decision if needed (emergency only)

**Note:** EIS Super Admin does NOT handle daily facility requests or operational coordination. That's the Admin's job

---

#### **B. View Construction Projects (Read-Only)**
**Location:** `/admin/facilities/construction-projects`

**Features:**
- ✅ View all construction projects
- ✅ Filter by status
- ✅ View progress and milestones
- ✅ View contractor information
- ❌ Cannot approve/reject turnover
- ❌ Cannot submit warranty claims
- ✅ Can flag issues for Super Admin review
- ✅ Export project reports

---

#### **C. Report Defects**
**Location:** `/admin/facilities/{id}/report-defect`

**Features:**
- ✅ Report construction defects
- ✅ Upload defect photos
- ✅ Specify severity level (Low, Medium, High, Critical)
- ✅ Submit to Super Admin (not directly to Infrastructure PM)
- ✅ Track defect report status

---

### **3. STAFF (Reservations Staff)** ⭐

#### **A. Construction Calendar**
**Location:** `/staff/calendar/construction`

**Features:**
- ✅ View construction timeline
- ✅ See upcoming facility openings
- ✅ Estimated completion dates
- ✅ Filter by facility type
- ✅ Calendar view with milestones
- ✅ Get notifications for completion
- ✅ Countdown to opening

---

#### **B. Coming Soon Facilities**
**Location:** `/staff/facilities/coming-soon`

**Features:**
- ✅ List of facilities under construction
- ✅ View progress percentage
- ✅ Estimated opening date
- ✅ Facility specifications preview
- ✅ Prepare facility for launch checklist

---

#### **C. Facility Setup (Post-Construction)**
**Location:** `/staff/facilities/{id}/setup`

**Features:**
- ✅ Upload facility photos
- ✅ Add detailed description
- ✅ Set booking rules
- ✅ Configure per-person pricing (auto-populated from `system_configurations` based on facility size)
- ✅ Set capacity limits (validates against size category thresholds)
- ✅ Add amenities list
- ✅ Set operating hours
- ✅ Test booking system
- ✅ Preview facility page
- ✅ Submit for Admin approval

---

#### **D. Report Construction Issues (Optional)**
**Location:** `/staff/facilities/{id}/report-issue`

**Features:**
- ✅ Report defects during site visits
- ✅ Upload issue photos
- ✅ Submit to Admin/Super Admin
- ✅ Track issue resolution

---

### **4. CITIZEN** ⭐

#### **A. Coming Soon Facilities Page (With Construction Transparency)**
**Location:** `/citizen/facilities/coming-soon`

**Features:**
- ✅ Browse upcoming facilities
- ✅ See "Coming Soon" badge
- ✅ View construction progress:
  - Progress percentage (e.g., "75% Complete")
  - Estimated completion date
  - "Opening in 3 months" countdown
- ✅ View facility preview:
  - Facility name & type
  - Location & address
  - Target capacity
  - Expected amenities
  - Placeholder image or blueprint
- ✅ **View construction transparency:**
  - Contractor information (name, license, project manager)
  - Total budget and funding source
  - Budget breakdown by construction phase
  - Materials used (concrete, steel, equipment)
  - Material quantities and costs
  - Supplier information
  - Cost vs. progress tracking
- ✅ **Download transparency documents:**
  - Approved budget document
  - Contractor bidding results
  - Building permits
  - Environmental clearance
  - Procurement documents
- ✅ View construction photos (from Infrastructure PM)
- ✅ Filter by:
  - Facility type
  - Location (city, district)
  - Expected opening date
- ✅ Sort by:
  - Opening date (nearest first)
  - Capacity (largest first)
  - Progress (most complete first)
- ✅ Subscribe to opening notifications

**Display Example (Enhanced with Transparency):**
```
┌──────────────────────────────────────────────┐
│ 🏗️ COMING SOON FACILITY                      │
├──────────────────────────────────────────────┤
│                                              │
│ Nueva Caloocan Sports Complex                │
│ Large Sports Facility · District 1           │
│                                              │
│ ████████████░░░░ 75% Complete               │
│                                              │
│ 📅 Opening: March 2026 (3 months)           │
│ 👥 Capacity: 1,500 people                   │
│ 📍 Camarin Road, Caloocan City              │
│ 💰 Rate: ₱200/person (when operational)     │
│                                              │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━  │
│                                              │
│ 🏗️ CONSTRUCTION TRANSPARENCY                │
│                                              │
│ 👷 Contractor:                               │
│ ABC Construction Corporation                 │
│ License: PCAB-AAA-2024-001 ✅                │
│ Project Manager: Engr. Maria Santos          │
│ Contact: 09171234567                         │
│                                              │
│ 💰 Budget:                                   │
│ Total: ₱14,500,000.00                       │
│ Funded by: City Budget 2025                  │
│ Spent so far: ₱10,875,000 (75%)             │
│ Status: On Budget ✅                         │
│                                              │
│ 📊 Cost Breakdown:                           │
│ • Foundation: ₱3,625,000 (25%) ✅ Complete  │
│ • Structural: ₱4,350,000 (30%) ✅ Complete  │
│ • Finishing: ₱3,625,000 (25%) 🔄 In Progress│
│ • Equipment: ₱1,450,000 (10%) ⏳ Pending    │
│ • Retention: ₱725,000 (5%)                  │
│ • Contingency: ₱725,000 (5%)                │
│                                              │
│ 🧱 Major Materials Used:                     │
│ • Concrete Grade 40: 500 m³ (₱2.5M)         │
│ • Steel Reinforcement: 80 tons (₱3.6M)      │
│ • Sports Flooring: 2,000 m² (₱1.6M)         │
│ • Aluminum Bleachers: 1,000 seats (₱1.2M)   │
│ • Roofing System: Pre-engineered (₱2.8M)    │
│                                              │
│ 📄 Transparency Documents:                   │
│ [📥 Budget Approval]                         │
│ [📥 Contractor Bidding Results]              │
│ [📥 Building Permits]                        │
│ [📥 Materials Procurement Records]           │
│                                              │
│ 📸 Construction Progress:                    │
│ [View 45 Photos] [View Blueprint]            │
│                                              │
│ [📍 View Site on Map]                        │
│ [🔔 Notify Me When Complete]                 │
│                                              │
│ 💚 Your tax money at work - Full transparency!│
└──────────────────────────────────────────────┘
```

**Transparency Benefits:**
- ✅ Citizens see WHO is building (contractor accountability)
- ✅ Citizens see HOW MUCH it costs (budget transparency)
- ✅ Citizens see WHAT materials used (quality verification)
- ✅ Citizens see WHERE money goes (cost breakdown)
- ✅ Citizens can verify prices are fair (market comparison)
- ✅ Prevents corruption and overpricing
- ✅ Builds public trust in government projects

---

#### **B. Notification Preferences**
**Location:** `/citizen/profile` (Settings tab)

**Features:**
- ✅ Subscribe to specific facility openings
- ✅ Choose notification method:
  - Email
  - SMS (if available)
  - In-app notification
- ✅ Unsubscribe from notifications
- ✅ View subscribed facilities
- ✅ Notification timing:
  - When 100% complete
  - 1 week before opening
  - On opening day

---

#### **C. Browse Facilities (Enhanced)**
**Location:** `/citizen/facilities`

**Features:**
- ✅ New filter option: "Show only available" or "Include coming soon"
- ✅ Mixed results showing:
  - Active facilities (can book now)
  - Coming soon facilities (preview only)
- ✅ Clear visual distinction between active and coming soon
- ✅ "Book Now" vs "Coming Soon" buttons

---

## 🗂️ NEW NAVIGATION STRUCTURE

### **EIS Super Admin Sidebar:**
```
⚙️ System Administration
   ├── System Configuration
   ├── API Integration Settings
   └── Technical Monitoring
```

### **Admin Sidebar:**
```
📋 Facilities Management
   ├── Active Facilities
   ├── Construction Projects ⭐ NEW
   │   ├── All Projects
   │   ├── Under Construction
   │   └── Pending Turnover
   ├── Request New Facility ⭐ NEW
   └── Warranty Claims ⭐ NEW
```

### **Staff Sidebar:**
```
📅 Calendar
   ├── Booking Calendar
   └── Construction Calendar ⭐ NEW

🏢 Facilities
   ├── Active Facilities
   ├── Coming Soon ⭐ NEW
   └── Facility Setup ⭐ NEW
```

### **Citizen Sidebar:**
```
🏢 Facilities
   ├── Browse Facilities
   ├── Coming Soon ⭐ NEW
   └── Facility Calendar
```

---

## 📊 DATABASE CHANGES NEEDED

### **New Tables:**

#### **1. `construction_projects`**
```sql
CREATE TABLE construction_projects (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    request_id VARCHAR(50) UNIQUE NOT NULL,
    project_id VARCHAR(50) UNIQUE,
    
    -- Facility Details
    facility_name VARCHAR(255) NOT NULL,
    facility_type VARCHAR(100) NOT NULL,
    size_category ENUM('small', 'medium', 'large') NOT NULL,
    target_capacity INT NOT NULL,
    estimated_area_sqm DECIMAL(10,2),
    
    -- Status
    status ENUM('draft', 'pending', 'approved', 'under_construction', 'completed', 'rejected') DEFAULT 'draft',
    rejection_reason TEXT,
    
    -- Contractor
    contractor_name VARCHAR(255),
    contractor_id VARCHAR(50),
    contractor_contact VARCHAR(255),
    contractor_email VARCHAR(255),
    
    -- Budget
    budget_estimated DECIMAL(15,2) NOT NULL,
    budget_approved DECIMAL(15,2),
    
    -- Timeline
    start_date DATE,
    target_completion_date DATE,
    actual_completion_date DATE,
    duration_days INT,
    
    -- Progress
    progress_percentage INT DEFAULT 0,
    
    -- Location
    city VARCHAR(100),
    district VARCHAR(100),
    barangay VARCHAR(100),
    address TEXT,
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    
    -- Warranty
    warranty_period_months INT DEFAULT 12,
    warranty_valid_until DATE,
    
    -- Transparency Data (from Infrastructure PM)
    materials_breakdown JSON,
    transparency_documents JSON,
    
    -- Cost Tracking
    cost_to_date DECIMAL(15,2) DEFAULT 0.00,
    budget_variance_percentage DECIMAL(5,2) DEFAULT 0.00,
    is_on_budget BOOLEAN DEFAULT TRUE,
    
    -- Metadata
    requested_by_user_id BIGINT UNSIGNED,
    approved_by_user_id BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_status (status),
    INDEX idx_city (city),
    INDEX idx_progress (progress_percentage)
);

-- Example of materials_breakdown JSON structure:
/*
{
  "structural": {
    "total_cost": 6100000.00,
    "items": [
      {
        "material": "Concrete Grade 40",
        "quantity": 500,
        "unit": "cubic meters",
        "unit_cost": 5000.00,
        "total_cost": 2500000.00,
        "supplier": "XYZ Concrete Corp"
      }
    ]
  },
  "finishing": {...},
  "equipment": {...}
}
*/

-- Example of transparency_documents JSON structure:
/*
{
  "budget_approval_url": "https://...",
  "contractor_bidding_results_url": "https://...",
  "building_permit_url": "https://...",
  "environmental_clearance_url": "https://...",
  "procurement_documents_url": "https://...",
  "construction_photos_album_url": "https://..."
}
*/
```

#### **2. `construction_milestones`**
```sql
CREATE TABLE construction_milestones (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    construction_project_id BIGINT UNSIGNED NOT NULL,
    
    milestone_name VARCHAR(255) NOT NULL,
    milestone_description TEXT,
    
    target_date DATE NOT NULL,
    actual_date DATE,
    
    progress_percentage INT NOT NULL,
    status ENUM('pending', 'in_progress', 'completed', 'delayed') DEFAULT 'pending',
    
    notes TEXT,
    photos JSON,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (construction_project_id) REFERENCES construction_projects(id) ON DELETE CASCADE,
    INDEX idx_project (construction_project_id),
    INDEX idx_status (status)
);
```

#### **3. `warranty_claims`**
```sql
CREATE TABLE warranty_claims (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    claim_id VARCHAR(50) UNIQUE NOT NULL,
    
    facility_id BIGINT UNSIGNED NOT NULL,
    construction_project_id BIGINT UNSIGNED NOT NULL,
    
    defect_type ENUM('structural', 'electrical', 'plumbing', 'finishing', 'other') NOT NULL,
    severity ENUM('low', 'medium', 'high', 'critical') NOT NULL,
    
    description TEXT NOT NULL,
    location_description VARCHAR(255),
    photos JSON,
    
    reported_by_user_id BIGINT UNSIGNED NOT NULL,
    reported_date DATE NOT NULL,
    
    claim_status ENUM('submitted', 'under_review', 'approved', 'in_progress', 'fixed', 'verified', 'rejected') DEFAULT 'submitted',
    
    resolution_notes TEXT,
    resolved_date DATE,
    verified_by_user_id BIGINT UNSIGNED,
    verified_date DATE,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (facility_id) REFERENCES facilities(id) ON DELETE CASCADE,
    FOREIGN KEY (construction_project_id) REFERENCES construction_projects(id) ON DELETE CASCADE,
    INDEX idx_status (claim_status),
    INDEX idx_facility (facility_id)
);
```

#### **4. `facility_opening_subscriptions`**
```sql
CREATE TABLE facility_opening_subscriptions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    citizen_id BIGINT UNSIGNED NOT NULL,
    construction_project_id BIGINT UNSIGNED NOT NULL,
    
    notification_method ENUM('email', 'sms', 'in_app') DEFAULT 'email',
    
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notified_at TIMESTAMP NULL,
    
    FOREIGN KEY (construction_project_id) REFERENCES construction_projects(id) ON DELETE CASCADE,
    UNIQUE KEY unique_subscription (citizen_id, construction_project_id),
    INDEX idx_citizen (citizen_id),
    INDEX idx_project (construction_project_id)
);
```

### **Modified Tables:**

#### **`facilities` table - Add columns:**
```sql
ALTER TABLE facilities
ADD COLUMN construction_project_id BIGINT UNSIGNED NULL AFTER id,
ADD COLUMN coming_soon BOOLEAN DEFAULT FALSE AFTER status,
ADD COLUMN estimated_opening_date DATE NULL AFTER coming_soon,
ADD COLUMN construction_progress INT DEFAULT 0 AFTER estimated_opening_date,
ADD FOREIGN KEY (construction_project_id) REFERENCES construction_projects(id) ON DELETE SET NULL;
```

---

## 🔗 API ENDPOINTS TO CREATE

### **Outbound (To Infrastructure PM):**

#### **1. Request New Facility**
```
POST /api/infra/projects/request
Content-Type: application/json
Authorization: Bearer {api_key}

Body: {facility request JSON}
Response: {
  "success": true,
  "project_id": "IPM-2025-045",
  "message": "Request received and queued for review"
}
```

#### **2. Submit Warranty Claim**
```
POST /api/infra/projects/warranty-claim
Content-Type: application/json
Authorization: Bearer {api_key}

Body: {warranty claim JSON}
Response: {
  "success": true,
  "claim_id": "WC-2027-001",
  "message": "Warranty claim submitted successfully"
}
```

---

### **Inbound (From Infrastructure PM):**

#### **1. Contractor Assignment**
```
POST /api/facilities/construction/assigned
Content-Type: application/json
Authorization: Bearer {api_key}

Body: {contractor assignment JSON}
Response: {
  "success": true,
  "message": "Contractor assignment received"
}
```

#### **2. Progress Update**
```
POST /api/facilities/construction/progress
Content-Type: application/json
Authorization: Bearer {api_key}

Body: {milestone update JSON}
Response: {
  "success": true,
  "message": "Progress update recorded"
}
```

#### **3. Facility Turnover**
```
POST /api/facilities/construction/complete
Content-Type: application/json
Authorization: Bearer {api_key}

Body: {turnover JSON}
Response: {
  "success": true,
  "message": "Turnover received. Awaiting Super Admin approval"
}
```

#### **4. Warranty Claim Resolution**
```
POST /api/facilities/construction/warranty-resolved
Content-Type: application/json
Authorization: Bearer {api_key}

Body: {
  "claim_id": "WC-2027-001",
  "status": "fixed",
  "resolution_notes": "Crack repaired and reinforced",
  "resolved_date": "2027-05-20"
}
Response: {
  "success": true,
  "message": "Warranty claim resolution recorded"
}
```

---

## ✅ IMPLEMENTATION PRIORITY

### **Phase 1 - Essential (MVP):**
1. ✅ Database tables: `construction_projects`, `construction_milestones`
2. ✅ Super Admin: Request New Facility form & submission
3. ✅ Super Admin: Construction Projects Dashboard (list view)
4. ✅ API Endpoint: Receive contractor assignment
5. ✅ Super Admin: Approve Turnover workflow
6. ✅ Citizen: Coming Soon page (basic view)
7. ✅ Auto-activate facility after turnover approval

### **Phase 2 - Enhanced:**
8. ✅ API Endpoint: Receive progress updates
9. ✅ Super Admin: Progress tracking with milestones
10. ✅ Citizen: Progress percentage display
11. ✅ Citizen: Notification subscriptions
12. ✅ Staff: Construction Calendar
13. ✅ Admin: Draft facility requests

### **Phase 3 - Advanced:**
14. ✅ Database table: `warranty_claims`
15. ✅ Super Admin: Warranty management
16. ✅ Staff: Facility setup wizard
17. ✅ Progress photos display
18. ✅ Budget tracking & variance alerts
19. ✅ Email notifications for all stakeholders
20. ✅ Construction reports & analytics

---

## 📝 TECHNICAL CONSIDERATIONS

### **1. Authentication & Security**
- Use API keys or JWT tokens for all external API calls
- Implement rate limiting (e.g., 100 requests/minute)
- Validate all incoming data from Infrastructure PM
- Log all integration transactions for audit trail
- Encrypt sensitive data (contractor info, budget details)

### **2. Data Synchronization**
- **Webhook vs Polling:**
  - Prefer webhooks for real-time updates (progress, turnover)
  - Fallback to polling every 1 hour if webhooks fail
- **Data Consistency:**
  - Use database transactions for critical operations
  - Implement retry logic for failed API calls
  - Queue system for handling bulk updates

### **3. Error Handling**
- Graceful fallbacks if Infrastructure PM API is down
- Display cached data when external system unavailable
- Alert Super Admin of integration failures
- Retry failed requests with exponential backoff

### **4. Testing**
- Create mock Infrastructure PM API for local development
- Unit tests for all integration functions
- Integration tests with test data
- Load testing for API endpoints

### **5. Database-Driven Design** ⚠️ CRITICAL
- **NO Hardcoded Values:**
  - All dropdown options (facility types, amenities, defect types) → Database tables
  - All pricing rates → `system_configurations` table
  - All capacity thresholds → `system_configurations` table
  - All system limits → `system_configurations` table
- **Use Existing Data:**
  - Cities, Districts, Barangays → Use `auth_db` tables
  - Do NOT duplicate location data
- **Super Admin Control:**
  - All master data manageable through UI
  - No developer required for data changes
- **File Uploads:**
  - Store file paths in database
  - Physical files in `storage/app/public/` or `public/uploads/`
  - Never hardcode file paths
- **The ONLY Hardcoded Elements:**
  - Application logic (PHP business rules)
  - UI structure (HTML/Blade templates)
  - Core workflow ENUMs (pending/approved/rejected states)

### **6. Monitoring & Logging**
- Log all API requests/responses
- Monitor API response times
- Alert on repeated failures
- Track integration health dashboard

### **6. Performance**
- Cache frequently accessed data (contractor list, project status)
- Use database indexes on foreign keys
- Paginate large result sets
- Optimize image loading for progress photos

---

## 🎨 UI/UX GUIDELINES

### **Coming Soon Facilities**
- Use construction-themed colors (orange/yellow accents)
- Animated progress bars
- Countdown timers
- Blueprint-style placeholder images
- "Under Construction" badges
- Estimated opening dates prominently displayed

### **Construction Dashboard**
- Timeline view with milestones
- Gantt chart for project schedules (optional)
- Color-coded status indicators:
  - 🟢 On Track
  - 🟡 Minor Delays
  - 🔴 Major Delays
  - ✅ Completed
- Budget vs Actual charts

### **Notifications**
- Real-time toast notifications for status changes
- Email summaries for major milestones
- Push notifications for citizens (when subscribed facility opens)

---

## 🚀 DEPLOYMENT CHECKLIST

- [ ] Create all database tables and migrations
- [ ] Set up API authentication with Infrastructure PM
- [ ] Configure webhook endpoints
- [ ] Test API integration with Infrastructure PM team
- [ ] Deploy Super Admin request facility feature
- [ ] Deploy construction dashboard
- [ ] Deploy citizen coming soon page
- [ ] Set up email notification system
- [ ] Configure rate limiting
- [ ] Set up monitoring and logging
- [ ] Create admin user guide
- [ ] Create citizen user guide
- [ ] Conduct UAT (User Acceptance Testing)
- [ ] Go live!

---

## 📞 COORDINATION WITH INFRASTRUCTURE PM TEAM

### **Questions to Discuss:**
1. ✅ Do they agree with this integration flow?
2. ✅ Can they provide milestone updates during construction?
3. ✅ What's their preferred API format? (REST, GraphQL, WebHooks?)
4. ✅ What authentication method? (API Keys, OAuth2, JWT?)
5. ✅ Do they handle warranty/defect reports?
6. ✅ What's their expected response time for facility requests?
7. ✅ Can they provide progress photos?
8. ✅ What's their API rate limit?
9. ✅ Do they have a staging/test environment?
10. ✅ Who's the technical contact for integration issues?

### **Agreement Points:**
- API specification document (OpenAPI/Swagger)
- Data format standards
- Error code conventions
- Response time SLAs
- Downtime notification procedures
- Security protocols

---

## 💡 FUTURE ENHANCEMENTS

- **3D Model Viewer:** View facility 3D models during construction
- **Live Webcam Feed:** Real-time construction site camera
- **AR Preview:** Augmented reality preview of completed facility
- **Citizen Feedback:** Collect community input on facility designs
- **Cost Transparency:** Public dashboard showing budget allocation
- **Sustainability Metrics:** Track eco-friendly construction practices
- **Community Events:** Host groundbreaking/ribbon-cutting ceremonies

---

**END OF DOCUMENT**

**Last Updated:** November 30, 2025  
**Prepared By:** AI Assistant  
**Approved By:** [Pending User Approval]

---

## 📋 CHANGE LOG

| Version | Date | Changes |
|---------|------|---------|
| 1.0 | Nov 30, 2025 | Initial document created |
| 1.1 | Nov 30, 2025 | Added comprehensive database-driven design section with master data tables |

---

