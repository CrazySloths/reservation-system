# 🏘️ HOUSING AND RESETTLEMENT INTEGRATION - FEATURE LIST

**Document Version:** 1.0  
**Date:** December 6, 2025  
**System:** Public Facilities Reservation System  
**External Integration:** Housing and Resettlement Management System

---

## 📋 INTEGRATION OVERVIEW

### **Integration Purpose:**
Enable Housing and Resettlement Management department to book public facilities for housing beneficiary events with complete transparency and accountability.

### **Integration Flow:**
```
Phase 1: Housing & Resettlement → Request Facility (for beneficiary events)
Phase 2: Public Facilities → Coordinate (organizer, speaker, finance, facility)
Phase 3: Finance System → Approve Funds (for event consumables)
Phase 4: Public Facilities → Confirm Booking (approved schedule)
Phase 5: Event Execution → Post-Event Transparency (itemized reporting)
```

### **Key Principle:**
- **Beneficiaries attend FREE** (no admission fee)
- **Facility provided FREE** (government asset, waived fees)
- **Funds requested for CONSUMABLES ONLY** (food, materials, supplies, speakers)
- **Complete transparency** (pre-event and post-event reporting)
- **Real, verifiable prices** (citizens can verify market rates)
- **Database-driven** (no hardcoded values)

---

## 🔗 SUBMODULE INTEGRATION MAPPING

### **Housing & Resettlement Submodules (ALL 5 can request facilities):**

| Housing & Resettlement Submodule | Event Types | Public Facilities Submodules | Data Flow |
|----------------------------------|-------------|------------------------------|-----------|
| **Unit Assignment and Occupancy Tracking** | Housing Orientation<br>Turnover Ceremonies<br>Move-in Briefings | • Facility Directory and Calendar<br>• Online Booking and Approval<br>• Usage Fee Calculation<br>• Schedule Conflicts Alert<br>• Usage Reports and Feedback | BI-DIRECTIONAL |
| **Resettlement and Plan Schedule** | Resettlement Meetings<br>Relocation Briefings<br>Stakeholder Consultations | • Facility Directory and Calendar<br>• Online Booking and Approval<br>• Usage Fee Calculation<br>• Schedule Conflicts Alert<br>• Usage Reports and Feedback | BI-DIRECTIONAL |
| **Beneficiary Eligibility Verification** | Application Sessions<br>Document Verification<br>Screening Interviews | • Facility Directory and Calendar<br>• Online Booking and Approval<br>• Usage Fee Calculation<br>• Schedule Conflicts Alert<br>• Usage Reports and Feedback | BI-DIRECTIONAL |
| **Housing Loan and Payment Tracking** | Loan Orientation<br>Payment Workshops<br>Financial Literacy Training | • Facility Directory and Calendar<br>• Online Booking and Approval<br>• Usage Fee Calculation<br>• Schedule Conflicts Alert<br>• Usage Reports and Feedback | BI-DIRECTIONAL |
| **Housing Project Registry** | Project Announcements<br>Site Tour Briefings<br>Housing Expo Events | • Facility Directory and Calendar<br>• Online Booking and Approval<br>• Usage Fee Calculation<br>• Schedule Conflicts Alert<br>• Usage Reports and Feedback | BI-DIRECTIONAL |

### **Integration Summary:**
- **ALL 5 Housing submodules** can request facilities for different event types
- **ALL 5 Public Facilities submodules** are used for each request
- **Same workflow** regardless of which Housing submodule initiates the request
- **Different metadata** based on event type (orientation, resettlement, loans, etc.)

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

#### **A. Request Management** (`/admin/government-programs/requests`)
- ✅ Receive facility requests from Housing & Resettlement system
- ✅ Review event details (name, date, attendees, budget)
- ✅ View organizer contact information
- ✅ Identify event source (which Housing submodule sent request)
- ✅ Assign requests to staff
- ✅ Set request priority

**Request Data Includes:**
```
- Event Name: "Housing Orientation - Caloocan Heights Phase 3 Batch 5"
- Event Type: orientation | turnover | resettlement_meeting | loan_orientation | project_announcement
- Organizer: Maria Santos (Housing Department)
- Contact: 09171234567
- Expected Attendees: 80 beneficiaries
- Requested Funds: ₱25,000
- Source Submodule: Unit Assignment and Occupancy Tracking
- Housing Project: Caloocan Heights Phase 3
```

#### **B. Organizer Coordination** (`/admin/government-programs/requests/{id}/coordination`)
- ✅ Call organizer (with call log tracking)
- ✅ Discuss fund breakdown in detail
- ✅ Record call notes and agreements
- ✅ Request additional information (beneficiary list, event agenda)
- ✅ Negotiate budget items
- ✅ Confirm event requirements
- ✅ Coordinate with Housing department for specific needs

**Housing-Specific Coordination:**
```
For Housing Orientation:
- How many housing units involved?
- Topics to cover? (house rules, payment schedules, maintenance)
- Need Pag-IBIG representative?
- Beneficiary list available?

For Resettlement Meetings:
- Affected area and number of families?
- Relocation site details?
- Community concerns to address?
- Need barangay officials present?

For Turnover Ceremonies:
- How many units for turnover?
- Need stage setup?
- Certificate printing needed?
- Photo/video documentation?
```

#### **C. Speaker Coordination** (`/admin/government-programs/requests/{id}/speaker-coordination`)
- ✅ Contact external speakers (offline):
  - Pag-IBIG Fund officers (for loan orientation)
  - Bank representatives (for housing loans)
  - Real estate experts (for property management)
  - Community organizers (for resettlement)
  - Legal experts (for housing rights)
- ✅ Check speaker availability
- ✅ Confirm speakers for event dates
- ✅ Record coordination notes
- ✅ Track speaker fees (if external)
- ✅ Track speaker travel requirements

**Note:** City Hall employees speak for FREE (already on government payroll)

#### **D. Quotation Management** (`/admin/government-programs/requests/{id}/quotations`)
- ✅ Request quotations from suppliers (database-driven)
- ✅ Generate quotation request forms
- ✅ Upload received quotations
- ✅ Compare supplier prices
- ✅ Verify prices against database
- ✅ Flag overpriced items

**Typical Items for Housing Events:**
```
Food & Refreshments:
- Breakfast/Lunch/Snacks per person
- Bottled water
- Coffee/tea

Event Materials:
- Printed handouts (house rules, payment schedules)
- Housing unit keys/keychains (for turnover)
- Certificates of occupancy
- Folders/envelopes for documents
- Signage/tarpaulins

Miscellaneous:
- External speaker fees (if applicable)
- Transportation for site tours
- Photo/video documentation
- Tokens/giveaways for beneficiaries
```

#### **E. Finance Coordination** (`/admin/government-programs/requests/{id}/finance`)
- ✅ Submit fund request to Finance system (via API)
- ✅ Track Finance approval status
- ✅ Receive approval notifications
- ✅ View approved fund details (check #, release date)
- ✅ Follow up on pending approvals
- ✅ Handle fund adjustments if needed

**Finance Request Example:**
```
Event: Housing Orientation - Caloocan Heights Phase 3 Batch 5
Beneficiaries: 80 persons
Requested Amount: ₱25,000

Budget Breakdown:
- Food (lunch): 80 pax × ₱150 = ₱12,000
- Snacks: 80 pax × ₱50 = ₱4,000
- Printed materials: ₱3,000
- Folders/supplies: ₱2,000
- Speaker fee: ₱3,000
- Miscellaneous: ₱1,000
Total: ₱25,000
```

#### **F. Facility Assignment** (`/admin/government-programs/requests/{id}/facility`)
- ✅ View suggested facilities (based on attendee count)
- ✅ Check facility availability
- ✅ Assign facility to event
- ✅ Waive facility fees (government program)
- ✅ Include equipment (tables, chairs, sound system - FREE)
- ✅ Generate booking confirmation

**Facility Selection Logic:**
```
Expected Attendees: 80 beneficiaries

Suggested Facilities:
✅ City Hall Main Hall (capacity: 100) - AVAILABLE
✅ Barangay Covered Court (capacity: 150) - AVAILABLE
❌ Conference Room A (capacity: 50) - TOO SMALL

Selected: City Hall Main Hall
Regular Fee: ₱5,000
Government Program Discount: -₱5,000 (100% waived)
Final Fee: ₱0.00

Included Equipment (FREE):
- 80 monobloc chairs
- 10 tables (6x3 ft)
- Sound system with microphone
- Projector and screen
```

#### **G. Event Finalization** (`/admin/government-programs/requests/{id}/finalize`)
- ✅ Verify all requirements met:
  - Speaker confirmed
  - Funds approved by Finance
  - Facility assigned
  - Equipment ready
  - Quotations finalized
- ✅ Set final event date
- ✅ Send confirmation to Housing & Resettlement (API)
- ✅ Send SMS to organizer
- ✅ Publish event on public calendar (citizens can see)

**Confirmation Sent to Housing System:**
```json
{
  "booking_id": "BK-2025-001234",
  "external_request_id": "HR-2025-00567",
  "status": "confirmed",
  "facility_name": "City Hall Main Hall",
  "event_date": "2025-04-15",
  "time_start": "09:00",
  "time_end": "17:00",
  "approved_funds": 25000,
  "facility_fee": 0.00,
  "equipment_included": ["chairs", "tables", "sound_system", "projector"],
  "confirmation_url": "https://facilities.caloocan.gov.ph/bookings/BK-2025-001234"
}
```

#### **H. Pre-Event Transparency** (`/admin/government-programs/{id}/transparency/preview`)
- ✅ Create pre-event transparency report
- ✅ Input planned budget breakdown
- ✅ Add supplier information
- ✅ Publish for citizens BEFORE event
- ✅ Display on public transparency dashboard

**Transparency Report (Published BEFORE Event):**
```
UPCOMING GOVERNMENT PROGRAM
Event: Housing Orientation - Caloocan Heights Phase 3 Batch 5
Date: April 15, 2025
Beneficiaries: 80 housing beneficiaries
Facility: City Hall Main Hall (Fee Waived: ₱5,000)
Approved Budget: ₱25,000

Planned Expenses:
┌────────────────────────────────────────────────┐
│ Food and Refreshments              ₱16,000.00  │
│ - Lunch (80 pax × ₱150)            ₱12,000.00  │
│ - Snacks (80 pax × ₱50)            ₱4,000.00   │
│ Supplier: ABC Catering Services                │
├────────────────────────────────────────────────┤
│ Event Materials                    ₱3,000.00   │
│ - Printed handouts                 ₱2,000.00   │
│ - Folders/supplies                 ₱1,000.00   │
│ Supplier: XYZ Print Shop                       │
├────────────────────────────────────────────────┤
│ Speaker Fee                        ₱3,000.00   │
│ - Pag-IBIG Representative          ₱3,000.00   │
├────────────────────────────────────────────────┤
│ Miscellaneous                      ₱3,000.00   │
│ - Tokens/giveaways                 ₱2,000.00   │
│ - Photo documentation              ₱1,000.00   │
└────────────────────────────────────────────────┘
TOTAL PLANNED BUDGET:                ₱25,000.00

Citizens can verify supplier prices at market rates.
```

#### **I. Post-Event Liquidation** (`/admin/government-programs/{id}/liquidation`)
- ✅ Create liquidation report
- ✅ Upload official receipts (itemized)
- ✅ Enter actual purchases with specifications
- ✅ Link to suppliers in database
- ✅ Verify prices match market rates
- ✅ Upload product photos
- ✅ Record actual attendance
- ✅ Calculate savings (if under budget)
- ✅ Publish post-event transparency report

**Liquidation Report (Published AFTER Event):**
```
GOVERNMENT PROGRAM COMPLETED
Event: Housing Orientation - Caloocan Heights Phase 3 Batch 5
Date: April 15, 2025
Expected Beneficiaries: 80
Actual Attendance: 75 (93.75%)

ACTUAL EXPENSES vs PLANNED BUDGET:
┌─────────────────────────────────────────────────────┐
│ Food and Refreshments                               │
│ Planned: ₱16,000.00 | Actual: ₱15,375.00            │
│ - Lunch: 75 pax × ₱150 = ₱11,250.00                 │
│   • Pork Adobo, Rice, Vegetable, Drink              │
│   • Receipt: OR-12345 from ABC Catering             │
│   • Photo: [lunch_photo.jpg]                        │
│ - Snacks: 75 pax × ₱55 = ₱4,125.00                  │
│   • Pansit, Juice                                   │
│   • Receipt: OR-12346 from ABC Catering             │
│   • Photo: [snacks_photo.jpg]                       │
│ Savings: ₱625.00                                    │
├─────────────────────────────────────────────────────┤
│ Event Materials                                     │
│ Planned: ₱3,000.00 | Actual: ₱2,850.00              │
│ - Printed handouts: ₱1,900.00                       │
│   • 75 copies × ₱25.33 per copy                     │
│   • Content: Housing rules, payment guide           │
│   • Receipt: OR-54321 from XYZ Print Shop           │
│ - Folders: ₱950.00                                  │
│   • 75 pcs × ₱12.67 per folder                      │
│   • Brand: Marbig, Color: Blue                      │
│ Savings: ₱150.00                                    │
├─────────────────────────────────────────────────────┤
│ Speaker Fee                                         │
│ Planned: ₱3,000.00 | Actual: ₱3,000.00              │
│ - Pag-IBIG Representative                           │
│   • Name: Atty. Juan Dela Cruz                      │
│   • Topic: Housing loan procedures                  │
│   • Receipt: OR-99999                               │
│ Savings: ₱0.00                                      │
├─────────────────────────────────────────────────────┤
│ Miscellaneous                                       │
│ Planned: ₱3,000.00 | Actual: ₱2,500.00              │
│ - Tokens (keychains): ₱1,500.00 (75 pcs × ₱20)     │
│ - Photo documentation: ₱1,000.00                    │
│ Savings: ₱500.00                                    │
└─────────────────────────────────────────────────────┘

TOTAL PLANNED:  ₱25,000.00
TOTAL ACTUAL:   ₱23,725.00
TOTAL SAVINGS:  ₱1,275.00 (5.1% under budget)

All receipts and photos available for public verification.
```

#### **J. Supplier Database (View & Request)** (`/admin/suppliers`)
- ✅ View all suppliers
- ✅ View supplier products and prices
- ✅ Request quotations from existing suppliers
- ✅ Suggest new suppliers to EIS Super Admin
- ❌ Cannot add/edit/delete suppliers (EIS Super Admin only)
- ❌ Cannot update prices in catalog (EIS Super Admin only)

**Supplier Categories for Housing Events:**
```
Catering Services:
- ABC Catering (₱120-₱200 per pax)
- DEF Food Services (₱100-₱180 per pax)

Printing/Materials:
- XYZ Print Shop (documents, certificates)
- QuickPrint Services (signage, tarpaulins)

Event Supplies:
- Party Central (tokens, giveaways)
- Office Depot (folders, pens, supplies)

Documentation:
- Pixel Perfect Photography (₱2,000-₱5,000)
- Video Memories (₱3,000-₱8,000)
```

---

### **2. EIS SUPER ADMIN** ⭐ (Technical Role Only)

**Location:** `/eis-superadmin/` (centralized across all 10 subsystems)

#### **A. Supplier Management (Add/Edit/Delete)** (`/eis-superadmin/suppliers`)
- ✅ Add new suppliers to database
- ✅ Edit supplier information (name, contact, address)
- ✅ Delete inactive suppliers
- ✅ Categorize suppliers (catering, printing, supplies, etc.)
- ✅ Set supplier status (active/inactive)
- ✅ Manage supplier contacts

**Admin can only VIEW and REQUEST from this data - cannot modify.**

#### **B. Product Catalog (Price Updates)** (`/eis-superadmin/suppliers/{id}/products`)
- ✅ Add products to supplier catalog
- ✅ Update product prices (based on market rates)
- ✅ Set product specifications
- ✅ Manage product categories
- ✅ Set price validity dates
- ✅ Mark products as discontinued

**Example Product Entry:**
```
Supplier: ABC Catering Services
Product: Lunch Package A
Price: ₱150.00 per person
Includes: 
- 1 main dish (choice of: Chicken Adobo, Pork Menudo, Fish Fillet)
- Rice
- 1 vegetable side
- 1 drink (juice or softdrink)
Category: Food & Catering
Last Updated: March 1, 2025
Valid Until: May 31, 2025
Status: Active
```

#### **C. System Configuration** (`/eis-superadmin/config/public-facilities`)
- ✅ Configure API endpoints for Housing & Resettlement integration
- ✅ Set up webhook URLs for notifications
- ✅ Configure fee waiver rules for government programs
- ✅ Set transparency report templates
- ✅ Configure event type classifications

**API Configuration:**
```php
// Housing & Resettlement API endpoint
'housing_api_url' => 'https://housing.caloocan.gov.ph/api/v1',
'housing_api_key' => 'hr_live_xxxxxxxxxxxxx',

// Webhook for booking confirmations
'housing_webhook_url' => 'https://housing.caloocan.gov.ph/webhooks/facility-confirmation',

// Fee waiver rules
'government_program_fee_waiver' => [
    'energy_efficiency' => 100,  // 100% waived
    'housing_resettlement' => 100, // 100% waived
    'health_services' => 100,      // 100% waived
],
```

#### **D. Technical Oversight** (`/eis-superadmin/logs/integrations`)
- ✅ Monitor API integration logs
- ✅ Track data exchange errors
- ✅ Review system performance
- ✅ Troubleshoot integration issues
- ✅ View webhook delivery status

---

### **3. STAFF** (Facility Staff)

**Location:** `/staff/` (Public Facilities staff dashboard)

#### **A. View Assigned Housing Events** (`/staff/government-programs/assigned`)
- ✅ View events assigned to them
- ✅ See event details and requirements
- ✅ View beneficiary count
- ✅ Check equipment needs
- ✅ View setup instructions

#### **B. Facility Setup and Preparation** (`/staff/government-programs/{id}/setup`)
- ✅ Mark setup as in-progress
- ✅ Checklist for room preparation:
  - Tables arranged
  - Chairs set up
  - Sound system tested
  - Projector working
  - Signage posted
  - Registration table ready
- ✅ Upload setup photos
- ✅ Mark setup as complete

#### **C. Event Day Coordination** (`/staff/government-programs/{id}/event-day`)
- ✅ Mark event as started
- ✅ Record actual attendance (beneficiaries who showed up)
- ✅ Note any issues during event
- ✅ Assist organizer as needed
- ✅ Monitor facility condition

#### **D. Post-Event Inspection** (`/staff/government-programs/{id}/inspection`)
- ✅ Inspect facility after event
- ✅ Report any damage:
  - Broken chairs
  - Damaged tables
  - Missing equipment
  - Facility cleanliness issues
- ✅ Upload inspection photos
- ✅ Mark teardown as complete
- ✅ Submit inspection report to Admin

**Inspection Checklist:**
```
Facility Condition:
☑ Floor clean and dry
☑ Walls free from damage
☑ Lights/fixtures intact
☑ Restrooms clean

Equipment Inventory:
☑ All chairs accounted for (80/80)
☑ All tables accounted for (10/10)
☑ Sound system returned
☑ Projector returned
☐ 2 microphones missing → Report to Admin

Damage Report:
☐ 3 chairs broken (leg detached)
  → Responsibility: To be determined
  → Photos: [chair_damage.jpg]
```

---

### **4. CITIZEN** (Public Transparency View)

**Location:** `/transparency/government-programs` (Public website)

#### **A. View Upcoming Housing Events** (`/transparency/upcoming-events`)
- ✅ Browse upcoming housing-related government programs
- ✅ See event details (date, venue, target beneficiaries)
- ✅ View planned budget transparency
- ✅ See which housing project is involved
- ✅ View event calendar

**Public Event Card:**
```
┌─────────────────────────────────────────────┐
│ 🏘️ UPCOMING HOUSING EVENT                   │
├─────────────────────────────────────────────┤
│ Housing Orientation                         │
│ Caloocan Heights Phase 3 - Batch 5          │
│                                             │
│ 📅 Date: April 15, 2025                     │
│ ⏰ Time: 9:00 AM - 5:00 PM                  │
│ 📍 Venue: City Hall Main Hall               │
│ 👥 Target: 80 housing beneficiaries         │
│ 💰 Budget: ₱25,000.00                       │
│                                             │
│ [View Budget Breakdown]                     │
└─────────────────────────────────────────────┘
```

#### **B. View Budget Transparency** (`/transparency/government-programs/{id}/budget`)
- ✅ See detailed pre-event budget breakdown
- ✅ View supplier information
- ✅ Verify prices against market rates
- ✅ See what items will be provided
- ✅ View historical pricing

**Transparency View:**
```
PLANNED BUDGET BREAKDOWN

Food & Refreshments: ₱16,000.00
├─ Lunch: 80 pax × ₱150 = ₱12,000.00
│  Supplier: ABC Catering Services
│  Market Rate: ₱120-₱180 per pax ✓ Within range
└─ Snacks: 80 pax × ₱50 = ₱4,000.00
   Supplier: ABC Catering Services
   Market Rate: ₱40-₱60 per pax ✓ Within range

Citizens can verify these prices at local caterers.
All receipts will be published after the event.
```

#### **C. View Post-Event Reports** (`/transparency/government-programs/{id}/report`)
- ✅ See actual expenses vs planned budget
- ✅ View uploaded receipts (itemized)
- ✅ See product photos
- ✅ View actual attendance
- ✅ See savings (if any)
- ✅ Download transparency report (PDF)

#### **D. View Housing Event History** (`/transparency/housing-events/history`)
- ✅ Browse past housing events
- ✅ See total beneficiaries served
- ✅ View total budget spent
- ✅ Track transparency metrics
- ✅ Compare events across housing projects

**Historical Dashboard:**
```
HOUSING EVENTS - 2025 SUMMARY

Total Events: 24
Total Beneficiaries Served: 1,850 families
Total Budget: ₱580,000.00
Average Savings: 4.2% under budget

Recent Events:
1. Housing Orientation - Caloocan Heights Phase 3
   Date: April 15, 2025 | Beneficiaries: 75 | Budget: ₱25,000
   
2. Turnover Ceremony - Rodriguez Highlands 
   Date: March 20, 2025 | Beneficiaries: 120 | Budget: ₱45,000
   
3. Resettlement Meeting - Tala Road Widening
   Date: March 5, 2025 | Beneficiaries: 150 | Budget: ₱38,000
```

---

## 📊 DATA EXCHANGE SPECIFICATIONS

### **1. Request Facility (Housing → Public Facilities)**

**Endpoint:** `POST /api/v1/facility-requests/government-program`

**Request Payload:**
```json
{
  "external_request_id": "HR-2025-00567",
  "source_system": "housing_resettlement",
  "source_submodule": "unit_assignment_occupancy",
  "organizer_name": "Maria Santos",
  "organizer_position": "Housing Coordinator",
  "organizer_contact": "09171234567",
  "organizer_email": "maria.santos@caloocan.gov.ph",
  "department": "Housing and Resettlement Management",
  "event_name": "Housing Orientation - Caloocan Heights Phase 3 Batch 5",
  "event_type": "housing_orientation",
  "event_description": "Orientation for new housing beneficiaries covering house rules, payment schedules, and maintenance responsibilities",
  "preferred_date": "2025-04-15",
  "preferred_time_start": "09:00",
  "preferred_time_end": "17:00",
  "expected_attendees": 80,
  "requested_funds": 25000.00,
  "event_metadata": {
    "housing_project": "Caloocan Heights Phase 3",
    "housing_project_id": "HP-2024-003",
    "unit_count": 80,
    "unit_types": ["Studio", "1BR"],
    "orientation_topics": [
      "House rules and regulations",
      "Payment schedules and procedures",
      "Maintenance responsibilities",
      "Community guidelines"
    ],
    "beneficiary_list_available": true,
    "beneficiary_list_url": "https://housing.caloocan.gov.ph/files/beneficiaries/batch-5.pdf",
    "special_requirements": [
      "Need projector for presentation",
      "Registration table required",
      "Pag-IBIG representative invited"
    ]
  },
  "fund_breakdown": {
    "food": {
      "lunch": 12000.00,
      "snacks": 4000.00
    },
    "materials": {
      "printed_handouts": 2000.00,
      "folders_supplies": 1000.00
    },
    "speakers": {
      "external_speaker_fee": 3000.00
    },
    "miscellaneous": {
      "tokens_giveaways": 2000.00,
      "documentation": 1000.00
    }
  }
}
```

**Response:**
```json
{
  "success": true,
  "message": "Facility request received successfully",
  "data": {
    "request_id": "REQ-2025-001234",
    "external_request_id": "HR-2025-00567",
    "status": "pending",
    "received_at": "2025-03-15T10:30:00+08:00",
    "estimated_response_time": "3-5 business days",
    "tracking_url": "https://facilities.caloocan.gov.ph/track/REQ-2025-001234",
    "contact_person": "Admin Office",
    "contact_number": "02-1234-5678"
  }
}
```

---

### **2. Send Approved Schedule (Public Facilities → Housing)**

**Endpoint:** `POST https://housing.caloocan.gov.ph/api/v1/facility-confirmations`

**Request Payload:**
```json
{
  "booking_id": "BK-2025-001234",
  "external_request_id": "HR-2025-00567",
  "status": "confirmed",
  "facility": {
    "facility_id": "FAC-001",
    "facility_name": "City Hall Main Hall",
    "facility_address": "Caloocan City Hall, A. Mabini St., Caloocan City",
    "capacity": 100,
    "amenities": ["Air-conditioned", "Stage", "Sound system", "Projector"]
  },
  "booking_details": {
    "event_date": "2025-04-15",
    "time_start": "09:00",
    "time_end": "17:00",
    "setup_time_start": "07:00",
    "cleanup_time_end": "18:00"
  },
  "financial": {
    "regular_facility_fee": 5000.00,
    "discount_amount": 5000.00,
    "discount_reason": "Government program - 100% waived",
    "final_facility_fee": 0.00,
    "approved_event_funds": 25000.00,
    "finance_check_number": "CHK-2025-0789",
    "finance_release_date": "2025-04-10"
  },
  "equipment_included": [
    {
      "item": "Monobloc chairs",
      "quantity": 80,
      "rental_fee": 0.00,
      "notes": "White plastic chairs"
    },
    {
      "item": "Tables",
      "quantity": 10,
      "specifications": "6ft x 3ft folding tables",
      "rental_fee": 0.00
    },
    {
      "item": "Sound system",
      "quantity": 1,
      "includes": ["Amplifier", "2 speakers", "2 wireless microphones"],
      "rental_fee": 0.00
    },
    {
      "item": "Projector and screen",
      "quantity": 1,
      "specifications": "LCD projector, 100-inch screen",
      "rental_fee": 0.00
    }
  ],
  "staff_assigned": [
    {
      "name": "Juan Reyes",
      "role": "Facility Coordinator",
      "contact": "09181234567",
      "responsibilities": ["Facility setup", "Equipment preparation", "Event day coordination"]
    }
  ],
  "contact_information": {
    "admin_name": "Public Facilities Admin",
    "admin_contact": "02-1234-5678",
    "admin_email": "facilities@caloocan.gov.ph",
    "emergency_contact": "09171234567"
  },
  "transparency": {
    "pre_event_report_url": "https://facilities.caloocan.gov.ph/transparency/BK-2025-001234/preview",
    "planned_budget_published": true,
    "public_calendar_url": "https://facilities.caloocan.gov.ph/calendar"
  },
  "additional_notes": [
    "Please arrive 30 minutes before event for final coordination",
    "Registration table will be set up near entrance",
    "Projector requires HDMI connection - please bring adapter if needed",
    "Beneficiary list should be provided to staff for attendance tracking"
  ],
  "confirmed_at": "2025-03-18T14:45:00+08:00"
}
```

**Response from Housing System:**
```json
{
  "success": true,
  "message": "Booking confirmation received and recorded",
  "data": {
    "external_request_id": "HR-2025-00567",
    "booking_id": "BK-2025-001234",
    "confirmation_status": "acknowledged",
    "beneficiaries_notified": true,
    "notification_method": ["SMS", "Email", "System notification"],
    "received_at": "2025-03-18T14:45:15+08:00"
  }
}
```

---

## 🗄️ DATABASE CHANGES

### **1. Reuse Existing Table: `government_program_requests`**

**This table is shared with Energy Efficiency integration!**

**Add new ENUM value for `source_system`:**
```sql
ALTER TABLE government_program_requests 
MODIFY COLUMN source_system ENUM(
  'energy_efficiency', 
  'housing_resettlement'  -- NEW
) NOT NULL;
```

**Table Structure (Reference):**
```sql
CREATE TABLE government_program_requests (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  
  -- External System Info
  external_request_id VARCHAR(100) UNIQUE,
  source_system ENUM('energy_efficiency', 'housing_resettlement') NOT NULL,
  source_submodule VARCHAR(100),  -- NEW: Track which Housing submodule sent request
  
  -- Organizer Information
  organizer_name VARCHAR(255) NOT NULL,
  organizer_position VARCHAR(100),
  organizer_contact VARCHAR(20) NOT NULL,
  organizer_email VARCHAR(255),
  department VARCHAR(255),
  
  -- Event Information
  event_name VARCHAR(255) NOT NULL,
  event_type VARCHAR(100) NOT NULL,  -- orientation, turnover, resettlement_meeting, etc.
  event_description TEXT,
  preferred_date DATE NOT NULL,
  preferred_time_start TIME,
  preferred_time_end TIME,
  expected_attendees INT NOT NULL,  -- ✅ Universal field (beneficiaries OR citizens)
  requested_funds DECIMAL(10,2),
  
  -- System-specific metadata (JSON)
  event_metadata JSON,  -- Housing project info, beneficiary details, etc.
  fund_breakdown JSON,
  
  -- Coordination Tracking
  request_status ENUM('pending', 'coordinating', 'finance_pending', 'approved', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
  assigned_to BIGINT UNSIGNED,  -- Staff ID
  priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
  
  -- Facility Assignment
  facility_id BIGINT UNSIGNED,
  booking_id VARCHAR(50),
  event_date DATE,
  time_start TIME,
  time_end TIME,
  
  -- Financial
  approved_funds DECIMAL(10,2),
  finance_check_number VARCHAR(50),
  finance_release_date DATE,
  actual_expenses DECIMAL(10,2),
  savings_amount DECIMAL(10,2),
  
  -- Transparency
  pre_event_transparency_published BOOLEAN DEFAULT FALSE,
  post_event_transparency_published BOOLEAN DEFAULT FALSE,
  
  -- Timestamps
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  completed_at TIMESTAMP NULL,
  
  -- Foreign Keys
  FOREIGN KEY (assigned_to) REFERENCES users(id),
  FOREIGN KEY (facility_id) REFERENCES facilities(id),
  
  -- Indexes
  INDEX idx_source_system (source_system),
  INDEX idx_source_submodule (source_submodule),  -- NEW
  INDEX idx_event_type (event_type),  -- NEW
  INDEX idx_request_status (request_status),
  INDEX idx_event_date (event_date)
);
```

### **Example `event_metadata` for Housing Events:**

**Housing Orientation:**
```json
{
  "housing_project": "Caloocan Heights Phase 3",
  "housing_project_id": "HP-2024-003",
  "unit_count": 80,
  "unit_types": ["Studio", "1BR"],
  "orientation_topics": [
    "House rules and regulations",
    "Payment schedules",
    "Maintenance responsibilities"
  ],
  "beneficiary_list_available": true,
  "beneficiary_list_url": "https://housing.caloocan.gov.ph/files/beneficiaries/batch-5.pdf",
  "special_requirements": ["Projector", "Registration table"]
}
```

**Resettlement Meeting:**
```json
{
  "affected_area": "Barangay Tala",
  "affected_families": 150,
  "resettlement_site": "Rodriguez Highlands",
  "relocation_date": "2025-06-01",
  "meeting_purpose": "Explain relocation benefits and process",
  "community_concerns": [
    "Distance from workplace",
    "School availability",
    "Transportation access"
  ],
  "barangay_officials_invited": true
}
```

**Turnover Ceremony:**
```json
{
  "housing_project": "Rodriguez Highlands Phase 1",
  "units_for_turnover": 120,
  "unit_types": ["1BR", "2BR"],
  "ceremony_program": [
    "Welcome remarks",
    "Key turnover",
    "Certificate distribution",
    "Photo opportunity"
  ],
  "vip_guests": ["City Mayor", "DHSUD Representative"],
  "stage_setup_required": true,
  "photo_video_documentation": true
}
```

---

### **2. Reuse Existing Tables (No Changes Needed):**

These tables are already created for Energy Efficiency integration:

- ✅ `program_coordination_logs` - Track organizer calls and meetings
- ✅ `program_quotations` - Store supplier quotations
- ✅ `program_liquidations` - Post-event liquidation records
- ✅ `program_transparency_reports` - Pre and post-event transparency
- ✅ `program_inspection_reports` - Post-event facility inspections

**All tables are already generic and support multiple source systems!**

---

## 🔄 KEY WORKFLOWS

### **Workflow 1: Housing Orientation Event**

```
┌─────────────────────────────────────────────────────────┐
│ HOUSING & RESETTLEMENT SYSTEM                           │
│ (Unit Assignment and Occupancy Tracking)                │
└────────────────┬────────────────────────────────────────┘
                 │
                 │ 1. REQUEST FACILITY
                 │    - 80 units assigned in Caloocan Heights Phase 3
                 │    - Need orientation for beneficiaries
                 │    - Request venue for April 15, 2025
                 │
                 ▼
┌─────────────────────────────────────────────────────────┐
│ PUBLIC FACILITIES - ADMIN                               │
│                                                         │
│ 2. RECEIVE REQUEST (REQ-2025-001234)                   │
│    ✓ Review: 80 beneficiaries, ₱25,000 budget          │
│    ✓ Assign to Staff: Juan Reyes                       │
│                                                         │
│ 3. COORDINATE WITH ORGANIZER                           │
│    ✓ Call Maria Santos (organizer)                     │
│    ✓ Discuss budget breakdown                          │
│    ✓ Confirm need for Pag-IBIG speaker                 │
│    ✓ Request beneficiary list                          │
│                                                         │
│ 4. COORDINATE WITH SPEAKERS                            │
│    ✓ Contact Pag-IBIG representative                   │
│    ✓ Confirm availability for April 15                 │
│    ✓ Agree on ₱3,000 speaker fee                       │
│                                                         │
│ 5. REQUEST QUOTATIONS                                  │
│    ✓ ABC Catering: Lunch + snacks quote               │
│    ✓ XYZ Print Shop: Handouts + folders quote          │
│    ✓ Compare prices with database                      │
│                                                         │
│ 6. SUBMIT TO FINANCE (via API)                         │
│    ✓ Send fund request: ₱25,000                        │
│    ✓ Attach quotations and budget breakdown            │
│    ✓ Wait for approval...                              │
│                                                         │
│ 7. FINANCE APPROVES (Notification received)            │
│    ✓ Approved: ₱25,000                                 │
│    ✓ Check #: CHK-2025-0789                            │
│    ✓ Release date: April 10, 2025                      │
│                                                         │
│ 8. ASSIGN FACILITY                                     │
│    ✓ Check availability: April 15, 2025                │
│    ✓ Suggest: City Hall Main Hall (100 capacity)       │
│    ✓ Regular fee: ₱5,000 → Waived to ₱0.00            │
│    ✓ Include equipment: chairs, tables, sound, projector│
│                                                         │
│ 9. FINALIZE BOOKING                                    │
│    ✓ Create booking: BK-2025-001234                    │
│    ✓ Publish pre-event transparency report             │
│    ✓ Send confirmation to Housing system (API)         │
│    ✓ SMS organizer: "Booking confirmed!"               │
│    ✓ Publish on public calendar                        │
│                                                         │
└────────────────┬────────────────────────────────────────┘
                 │
                 │ 10. CONFIRMATION SENT
                 │     - Booking ID: BK-2025-001234
                 │     - Facility: City Hall Main Hall
                 │     - Date: April 15, 2025, 9AM-5PM
                 │     - Approved funds: ₱25,000
                 │
                 ▼
┌─────────────────────────────────────────────────────────┐
│ HOUSING & RESETTLEMENT SYSTEM                           │
│                                                         │
│ 11. RECEIVE CONFIRMATION                               │
│     ✓ Record booking in system                         │
│     ✓ Notify 80 beneficiaries (SMS/Email)              │
│     ✓ Prepare orientation materials                    │
│     ✓ Coordinate with Pag-IBIG speaker                 │
│                                                         │
└─────────────────────────────────────────────────────────┘

        ⏰ APRIL 15, 2025 - EVENT DAY

┌─────────────────────────────────────────────────────────┐
│ PUBLIC FACILITIES - STAFF                               │
│                                                         │
│ 12. FACILITY SETUP (7:00 AM - 8:30 AM)                 │
│     ✓ Arrange 80 chairs in rows                        │
│     ✓ Set up 10 tables                                 │
│     ✓ Test sound system and projector                  │
│     ✓ Set up registration table                        │
│     ✓ Upload setup photos                              │
│                                                         │
│ 13. EVENT COORDINATION (9:00 AM - 5:00 PM)             │
│     ✓ Mark event as started                            │
│     ✓ Record attendance: 75 beneficiaries showed up    │
│     ✓ Assist organizer as needed                       │
│     ✓ Monitor facility condition                       │
│                                                         │
│ 14. POST-EVENT INSPECTION (5:00 PM - 6:00 PM)          │
│     ✓ Inspect facility for damage                      │
│     ✓ Check equipment inventory                        │
│     ✓ Note: All equipment accounted for                │
│     ✓ Note: Facility in good condition                 │
│     ✓ Upload inspection photos                         │
│     ✓ Submit report to Admin                           │
│                                                         │
└────────────────┬────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────────┐
│ PUBLIC FACILITIES - ADMIN                               │
│                                                         │
│ 15. POST-EVENT LIQUIDATION (April 16-18)               │
│     ✓ Upload official receipts:                        │
│       - ABC Catering: ₱15,375.00 (OR-12345, OR-12346)  │
│       - XYZ Print Shop: ₱2,850.00 (OR-54321)           │
│       - Speaker fee: ₱3,000.00 (OR-99999)              │
│       - Tokens/documentation: ₱2,500.00                │
│     ✓ Upload product photos                            │
│     ✓ Enter actual attendance: 75 (93.75% turnout)     │
│     ✓ Calculate savings: ₱1,275.00 (5.1% under budget) │
│     ✓ Publish post-event transparency report           │
│     ✓ Mark event as completed                          │
│                                                         │
└─────────────────────────────────────────────────────────┘

        📊 PUBLIC TRANSPARENCY DASHBOARD

┌─────────────────────────────────────────────────────────┐
│ CITIZENS CAN NOW VIEW:                                  │
│                                                         │
│ ✓ Event summary (80 invited, 75 attended)              │
│ ✓ Budget: ₱25,000 planned, ₱23,725 actual              │
│ ✓ All itemized receipts with photos                    │
│ ✓ Savings: ₱1,275.00                                   │
│ ✓ Supplier verification (market rate comparison)       │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

---

### **Workflow 2: Resettlement Community Meeting**

```
┌─────────────────────────────────────────────────────────┐
│ HOUSING & RESETTLEMENT SYSTEM                           │
│ (Resettlement and Plan Schedule)                        │
│                                                         │
│ 1. INITIATE REQUEST                                    │
│    - 150 families affected by road widening project    │
│    - Need community meeting to explain relocation      │
│    - Urgent: Relocation scheduled June 1, 2025         │
│                                                         │
└────────────────┬────────────────────────────────────────┘
                 │
                 │ 2. REQUEST FACILITY (HIGH PRIORITY)
                 │    - Event: Resettlement Community Meeting
                 │    - Affected area: Barangay Tala
                 │    - Expected: 150 families
                 │    - Budget: ₱30,000
                 │
                 ▼
┌─────────────────────────────────────────────────────────┐
│ PUBLIC FACILITIES - ADMIN                               │
│                                                         │
│ 3. EXPEDITED PROCESSING (Priority: HIGH)               │
│    ✓ Recognize urgency (relocation deadline June 1)    │
│    ✓ Fast-track coordination                           │
│    ✓ Call organizer immediately                        │
│    ✓ Discuss community concerns to address             │
│                                                         │
│ 4. FACILITY SELECTION                                  │
│    ✓ Choose venue IN or NEAR Barangay Tala             │
│    ✓ Selected: Barangay Tala Covered Court             │
│    ✓ Capacity: 200 (sufficient for 150 families)       │
│    ✓ Advantage: Familiar location for residents        │
│                                                         │
│ 5. COORDINATE STAKEHOLDERS                             │
│    ✓ Invite Barangay Tala officials                    │
│    ✓ Invite DHSUD representative                       │
│    ✓ Arrange transportation assistance (if needed)     │
│                                                         │
│ 6. FAST-TRACK FINANCE APPROVAL                         │
│    ✓ Mark as urgent in Finance system                  │
│    ✓ Approved within 24 hours                          │
│                                                         │
│ 7. CONFIRM BOOKING                                     │
│    ✓ Send confirmation to Housing system               │
│    ✓ Publish transparency report                       │
│    ✓ Notify community through barangay                 │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

---

### **Workflow 3: Housing Turnover Ceremony**

```
┌─────────────────────────────────────────────────────────┐
│ HOUSING & RESETTLEMENT SYSTEM                           │
│ (Unit Assignment and Occupancy Tracking)                │
│                                                         │
│ 1. TURNOVER EVENT PLANNING                             │
│    - 120 housing units ready for turnover              │
│    - Rodriguez Highlands Phase 1 completed             │
│    - Invite City Mayor and DHSUD for ceremony          │
│    - Need large venue for beneficiaries + VIPs         │
│                                                         │
└────────────────┬────────────────────────────────────────┘
                 │
                 │ 2. REQUEST FACILITY
                 │    - Event: Housing Turnover Ceremony
                 │    - Expected: 120 beneficiaries + 50 guests
                 │    - Budget: ₱45,000 (larger event)
                 │    - Special: Need stage, sound, photo/video
                 │
                 ▼
┌─────────────────────────────────────────────────────────┐
│ PUBLIC FACILITIES - ADMIN                               │
│                                                         │
│ 3. VIP EVENT COORDINATION                              │
│    ✓ Recognize high-profile event (Mayor attending)    │
│    ✓ Select premium venue: City Hall Auditorium        │
│    ✓ Coordinate stage setup and decorations            │
│    ✓ Arrange professional photo/video documentation    │
│                                                         │
│ 4. ENHANCED QUOTATIONS                                 │
│    ✓ Premium catering (150 pax + VIP guests)           │
│    ✓ Certificate printing (120 beneficiaries)          │
│    ✓ Tokens/keychains for new homeowners               │
│    ✓ Professional photography/videography              │
│    ✓ Stage decorations and signage                     │
│                                                         │
│ 5. TRANSPARENCY ENHANCEMENT                            │
│    ✓ Publish detailed pre-event budget                 │
│    ✓ Highlight ceremony significance                   │
│    ✓ Show unit distribution breakdown                  │
│    ✓ Post-event: Publish ceremony photos publicly      │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

---

## 📅 IMPLEMENTATION PRIORITY

### **Phase 1: Core Integration (Weeks 1-4)**
1. ✅ Create database migration (add `housing_resettlement` to ENUM)
2. ✅ Set up API endpoints for Housing system
3. ✅ Configure webhook for booking confirmations
4. ✅ Add Housing event types to system
5. ✅ Test basic request → confirmation flow

### **Phase 2: Admin Features (Weeks 5-8)**
1. ✅ Request Management dashboard
2. ✅ Organizer Coordination interface
3. ✅ Quotation Management for housing events
4. ✅ Finance API integration
5. ✅ Facility Assignment workflow

### **Phase 3: Transparency & Reporting (Weeks 9-12)**
1. ✅ Pre-event transparency reports
2. ✅ Post-event liquidation interface
3. ✅ Public transparency dashboard
4. ✅ Historical event tracking
5. ✅ Analytics for housing events

### **Phase 4: Staff & Citizen Features (Weeks 13-16)**
1. ✅ Staff coordination and inspection tools
2. ✅ Citizen transparency views
3. ✅ Public event calendar
4. ✅ Receipt and photo upload system
5. ✅ PDF report generation

---

## 🎯 KEY INTEGRATION PRINCIPLES

### **1. Reuse Energy Efficiency Code**
- ✅ Same database tables (just add Housing to ENUM)
- ✅ Same Admin features (coordination, quotation, liquidation)
- ✅ Same transparency workflow
- ✅ Same Finance API integration
- ✅ **Only difference:** Event types and metadata

### **2. Support Multiple Housing Submodules**
- ✅ ALL 5 Housing submodules can request facilities
- ✅ Track `source_submodule` to identify request origin
- ✅ Different event types for different purposes:
  - Unit Assignment → Orientation, Turnover
  - Resettlement → Community meetings
  - Eligibility → Application screening
  - Loan Tracking → Loan orientation
  - Project Registry → Project announcements

### **3. Beneficiary-Centric Approach**
- ✅ Use `expected_attendees` for beneficiary count
- ✅ Track housing project information in metadata
- ✅ Support beneficiary list attachments
- ✅ Show housing-specific context in transparency reports

### **4. Complete Transparency**
- ✅ Pre-event budget published BEFORE event
- ✅ Post-event receipts with photos
- ✅ Citizens verify market prices
- ✅ Historical tracking of all housing events
- ✅ Public accountability for government funds

### **5. Scalability**
- ✅ Easy to add more government departments:
  - Health Services (vaccination programs)
  - Agriculture Office (farmer training)
  - Social Welfare (feeding programs)
- ✅ All use same `government_program_requests` table
- ✅ Same workflow, different metadata
- ✅ Consistent UX for Admin across all programs

---

## 📊 SUCCESS METRICS

### **Efficiency Metrics:**
- Average request processing time: < 3 business days
- Finance approval turnaround: < 2 business days
- Booking confirmation delivery: < 1 hour

### **Transparency Metrics:**
- Pre-event transparency publication rate: 100%
- Post-event liquidation completion rate: > 95%
- Average savings vs budget: 3-5%

### **Beneficiary Metrics:**
- Total housing beneficiaries served per year
- Event attendance rate: > 85%
- Event types distribution (orientation, turnover, etc.)

### **Financial Metrics:**
- Total budget for housing events
- Average cost per beneficiary
- Comparison across housing projects

---

## ✅ INTEGRATION CHECKLIST

### **Technical Setup:**
- [ ] Add `housing_resettlement` to `source_system` ENUM
- [ ] Add `source_submodule` column to track which Housing submodule
- [ ] Configure Housing API endpoint in EIS Super Admin
- [ ] Set up webhook URL for confirmations
- [ ] Test API connectivity with Housing system

### **Feature Configuration:**
- [ ] Add Housing event types (orientation, turnover, resettlement, etc.)
- [ ] Configure fee waiver rules (100% for government programs)
- [ ] Set up transparency report templates for Housing events
- [ ] Add Housing-specific metadata fields
- [ ] Configure beneficiary list attachment support

### **User Training:**
- [ ] Train Admin on Housing event coordination
- [ ] Train Staff on beneficiary event setup
- [ ] Provide Housing department with API documentation
- [ ] Create user guides for Housing-specific workflows

### **Testing:**
- [ ] Test Housing Orientation workflow (end-to-end)
- [ ] Test Resettlement Meeting workflow
- [ ] Test Turnover Ceremony workflow
- [ ] Test transparency report generation
- [ ] Test API integration with Housing system

---

## 🎉 SUMMARY

The **Housing and Resettlement integration** follows the **same proven pattern** as the Energy Efficiency integration:

✅ **ALL 5 Housing submodules** can request facilities for different event types  
✅ **Same workflow:** Request → Coordinate → Finance → Confirm → Execute → Liquidate  
✅ **Same transparency:** Pre-event budgets and post-event receipts published  
✅ **Same database:** Reuse `government_program_requests` table  
✅ **Different data:** Housing projects, beneficiary lists, event types  
✅ **Scalable design:** Easy to add more government departments later

**This integration brings transparency and accountability to housing programs while efficiently managing facility bookings for thousands of beneficiaries annually!** 🏘️✨

---

**Document End** | Version 1.0 | December 6, 2025

