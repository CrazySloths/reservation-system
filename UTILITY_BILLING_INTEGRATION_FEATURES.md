# 💧 UTILITY BILLING INTEGRATION - FEATURE LIST

**Document Version:** 1.0  
**Date:** December 3, 2025  
**System:** Public Facilities Reservation System  
**External Integration:** Utility Billing and Management System

---

## 📋 INTEGRATION OVERVIEW

### **Integration Purpose:**
Enable Admin to request water connection for new public facilities during the construction phase, ensuring utilities are properly installed and activated before facility turnover.

### **Integration Context:**
This is a **CRITICAL STEP** in the Infrastructure Project Management workflow:
```
Step 1: Urban Planning Integration → Find suitable land
Step 2: Infrastructure PM Integration → Build facility on selected land
Step 3: Utility Billing Integration → Connect water supply ⭐ NEW
Step 4: Facility Turnover → Activate facility
```

### **Key Principle:**
- **One-time setup** per new facility construction
- **Water connection only** during construction phase
- **New facilities only** - not for existing facility maintenance
- **Budget included** in construction budget (no separate Finance workflow)

---

## 🔗 SUBMODULE INTEGRATION MAPPING

### **Important Note:**
This integration does NOT map to the 5 core Public Facilities submodules (Facility Directory, Booking, Fee Calculation, Schedule Conflict, Reports). Those handle **existing facilities**.

Utility Billing integration is part of the **Request New Facility** feature (Infrastructure Integration) which handles facilities under construction.

### **Integration Map:**

| Public Facilities Feature | ↔️ | Utility Billing Submodule | Data Flow |
|---------------------------|---|---------------------------|-----------|
| **Request New Facility (NEW)** | → | **Service Connection and Disconnection Requests** | ONE-WAY: Request water connection |
| **Request New Facility (NEW)** | ← | **Service Connection and Disconnection Requests** | RESPONSE: Provide water supplies (meter, account, activation) |

### **Utility Billing and Management System - 5 Submodules:**
1. Meter Reading and Monitoring
2. Billing and Invoicing
3. Payment Collection and Tracking
4. **Service Connection and Disconnection Requests** ← **OUR INTEGRATION!** 💧
5. Utility Complaint and Feedback Handling

---

## ⚠️ IMPORTANT: ROLE DEFINITIONS

### **EIS SUPER ADMIN** (Lead Programmer - Technical Role)
- **Created by:** EIS Lead Programmer (centralized in lgu1_auth)
- **Access:** All 10 subsystems (technical oversight)
- **Focus:** System configuration, API setup with Utility Billing
- **Time commitment:** Occasional (setup, maintenance)

**NOT responsible for requesting water connections or facility operations.**

### **ADMIN** (Operations Manager - Primary Operational Role)
- **Created in:** Public Facilities subsystem
- **Access:** Public Facilities Reservation System only
- **Focus:** Request new facilities, coordinate water connections, manage construction utilities
- **Time commitment:** Full-time (main working role)

**This is the PRIMARY role that requests water connections for new facilities.**

---

## 🎯 FEATURES BY ROLE

### **1. ADMIN** ⭐ (Primary User)

#### **A. Request Water Connection**
**Location:** `/admin/construction-projects/{id}/utilities/water`

**When:** During construction - utility installation phase (typically 40-60% progress)

**Features:**
- ✅ View construction project summary
  - Facility name and type
  - Location/address (from Urban Planning)
  - Building specifications
  - Construction progress status
- ✅ **Fill water connection request form:**
  - Facility details (auto-filled from project)
  - Location/address (auto-filled from selected land)
  - GPS coordinates (auto-filled)
  - **Water fixture counts (manual input):**
    - Number of toilets
    - Number of sinks/faucets
    - Number of showers
    - Number of drinking fountains
    - Other fixtures (text field for janitor sinks, outdoor hose, etc.)
  - Required connection date (date picker)
  - Contact person (auto-filled, editable)
  - **Budget allocation:**
    - Service connection fee
    - Meter deposit
    - Total allocated (from construction budget)
    - Funding source (auto-filled from project budget)
- ✅ **Validation before submit:**
  - Construction must be 40%+ complete
  - Budget must be allocated
  - All required fields filled
  - Contact information valid
- ✅ Submit request to Utility Billing (API call)
- ✅ Receive request ID confirmation
- ✅ Save request to database
- ✅ Send SMS notification to contact person

**Display:**
```
┌─────────────────────────────────────────────────────┐
│  💧 Request Water Connection                        │
│                                                      │
│  Construction Project: Barangay 20 Sports Complex   │
│  Current Progress: 45% (Utility Phase)              │
│  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━  │
│                                                      │
│  📍 Location Information                            │
│  Address: 123 Camarin Road, Brgy 20, Caloocan      │
│  Land ID: LAND-2025-078                             │
│  GPS: 14.7583, 120.9834                             │
│                                                      │
│  🚰 Water Fixture Requirements                      │
│  Toilets:            [12] fixtures                  │
│  Sinks/Faucets:      [8] fixtures                   │
│  Showers:            [4] fixtures                   │
│  Drinking Fountains: [3] fixtures                   │
│  Other Fixtures:                                    │
│  [2 janitor sinks, 1 outdoor hose connection]       │
│                                                      │
│  📅 Required Connection Date                        │
│  [June 1, 2025] 📅                                  │
│                                                      │
│  👤 Contact Person                                  │
│  Name:  Maria Santos                                │
│  Role:  Public Facilities Admin                     │
│  Phone: +63 917 123 4567                            │
│  Email: m.santos@caloocan.gov.ph                    │
│                                                      │
│  💰 Budget Allocation                               │
│  Service Connection Fee: ₱25,000.00                 │
│  Meter Deposit:          ₱5,000.00                  │
│  ─────────────────────────────────────              │
│  Total Allocated:        ₱30,000.00                 │
│  Funding Source: Construction Budget (PROJ-078)     │
│                                                      │
│  [ Cancel ]  [ Save as Draft ]  [ Submit Request ]  │
└─────────────────────────────────────────────────────┘
```

---

#### **B. Track Water Connection Status**
**Location:** `/admin/construction-projects/{id}/utilities/water`

**Features:**
- ✅ **View request status with progress timeline:**
  - ⏳ Pending Review (Submitted: Mar 15, 2025)
  - ✅ Approved (Approved: Mar 20, 2025)
  - 🔧 Meter Installation Scheduled (Scheduled: May 28, 2025)
  - ✅ Meter Installed (Installed: May 28, 2025)
  - 💧 Connected & Active (Activated: May 30, 2025)
  - ❌ Rejected (with reason, if applicable)
- ✅ **View request details:**
  - Request ID
  - Request date
  - Facility information
  - Fixture counts submitted
  - Budget allocated
- ✅ **View meter information (when assigned):**
  - Meter number
  - Meter type
  - Installation date
  - Initial reading
  - Meter location on property
- ✅ **View connection details:**
  - Connection type
  - Pipe size
  - Connection date
  - Activation date
  - Water pressure
  - Water source
  - Supply line information
  - Shutoff valve location
- ✅ **View billing information:**
  - Account number
  - Account name
  - Billing cycle
  - Rate category
  - First billing date
- ✅ **View charges paid:**
  - Service connection fee
  - Meter deposit
  - Installation fee
  - Total paid
  - Payment status
- ✅ **Download documents:**
  - Service Connection Permit
  - Meter Installation Report
  - Billing Account Setup Confirmation
- ✅ **Contact Utility Billing:**
  - Phone number displayed
  - Email address displayed
  - Quick message button
- ✅ **Receive real-time updates:**
  - Webhook notifications from Utility Billing
  - SMS alerts on status changes
  - Dashboard badge for new updates

**Display:**
```
┌─────────────────────────────────────────────────────┐
│  💧 Water Connection Status                         │
│                                                      │
│  Request ID: WC-2025-001                            │
│  Facility: Barangay 20 Sports Complex               │
│                                                      │
│  🔄 Connection Timeline                             │
│  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━  │
│  ✅ Mar 15  Request Submitted                       │
│  ✅ Mar 20  Request Approved                        │
│  ✅ May 28  Meter Installed                         │
│  ✅ May 30  Connection Activated                    │
│                                                      │
│  📊 Current Status: 💧 CONNECTED & ACTIVE           │
│                                                      │
│  📟 Meter Information                               │
│  Meter Number:    MTR-789654                        │
│  Meter Type:      Commercial - High Flow            │
│  Installed:       May 28, 2025                      │
│  Initial Reading: 0.00 m³                           │
│  Location:        Front entrance, left side         │
│                                                      │
│  🔧 Connection Details                              │
│  Connection Type: New Service Connection            │
│  Pipe Size:       2 inches                          │
│  Activated:       May 30, 2025                      │
│  Water Pressure:  45 PSI                            │
│  Water Source:    Caloocan Water District - Zone 3  │
│  Shutoff Valve:   Property line (blue marker)       │
│                                                      │
│  💳 Billing Information                             │
│  Account Number: ACCT-2025-789                      │
│  Billing Cycle:  Monthly                            │
│  Rate Category:  Government Facility                │
│  First Bill:     July 1, 2025                       │
│                                                      │
│  💰 Charges (Paid from Construction Budget)         │
│  Service Connection Fee: ₱25,000.00 ✅              │
│  Meter Deposit:          ₱5,000.00 ✅               │
│  ────────────────────────────────────               │
│  Total:                  ₱30,000.00 ✅ PAID         │
│                                                      │
│  📄 Documents                                       │
│  [📥 Download Service Connection Permit]            │
│  [📥 Download Meter Installation Report]            │
│                                                      │
│  📞 Need Help?                                      │
│  Utility Billing: (02) 8123-4567                    │
│  Email: connections@caloocanwater.gov.ph            │
│  [📧 Send Message]                                  │
│                                                      │
│  [ Back to Project ]  [ Print Report ]              │
└─────────────────────────────────────────────────────┘
```

---

#### **C. Connection Management**
**Location:** `/admin/construction-projects/{id}`

**Features:**
- ✅ **Mark connection as "Required" in construction checklist:**
  - Add water connection to project requirements
  - Set reminder for when to request
  - Track as critical milestone
- ✅ **Verify connection before facility turnover:**
  - Check connection status (must be "Active")
  - Verify meter is installed
  - Confirm billing account is set up
  - Test water flow (optional checklist item)
- ✅ **Upload connection completion photos:**
  - Photo of water meter
  - Photo of meter location
  - Photo of shutoff valve
  - Photo of water fixtures working
- ✅ **Add water connection info to facility profile:**
  - After facility turnover
  - Meter number saved in facility record
  - Account number saved for future billing
  - Connection details available for maintenance staff
- ✅ **Generate connection summary report:**
  - For facility handover documentation
  - For COA audit compliance
  - For future reference

**Integration with Turnover Checklist:**
```
┌─────────────────────────────────────────────────────┐
│  ✅ Pre-Turnover Checklist                          │
│                                                      │
│  ☑️ Construction 100% complete                      │
│  ☑️ Safety inspection passed                        │
│  ☑️ 💧 Water connection active (MTR-789654) ⭐ NEW  │
│  ☑️ Electrical connection active                    │
│  ☑️ Final cleanup done                              │
│  ☐ Acceptance documents signed                      │
│  ☐ Warranty documents received                      │
│                                                      │
│  [ Mark All Complete ]  [ Proceed to Turnover ]     │
└─────────────────────────────────────────────────────┘
```

---

### **2. EIS SUPER ADMIN** ⭐ (Technical Setup Only)

#### **A. API Configuration**
**Location:** `/superadmin/settings/utility-billing-integration`

**Features:**
- ✅ Configure API connection to Utility Billing system
- ✅ Manage API keys and authentication tokens
- ✅ Set up webhook endpoints for status updates:
  - Connection approved webhook
  - Meter scheduled webhook
  - Meter installed webhook
  - Connection activated webhook
  - Rejection webhook
- ✅ Configure integration settings:
  - API timeout values
  - Retry logic
  - Error handling
- ✅ Monitor integration health:
  - API uptime
  - Response times
  - Error rates
- ✅ View integration logs:
  - Request logs
  - Response logs
  - Error logs
  - Webhook logs
- ✅ Test connection:
  - Test API authentication
  - Test request submission
  - Test webhook reception
- ✅ Handle technical issues:
  - Retry failed requests
  - Resync data
  - Debug connection problems

**Note:** EIS Super Admin does NOT request water connections or manage facility operations. That's the Admin's job.

---

### **3. STAFF** 👀 (View Only)

#### **A. View Water Connection Status**
**Location:** `/staff/construction-projects/{id}`

**Features:**
- ✅ View water connection status for construction projects
- ✅ View meter information (number, location)
- ✅ View connection details (pipe size, pressure, activation date)
- ✅ View billing account number
- ✅ Download connection documents (if needed for coordination)
- ❌ Cannot request water connections
- ❌ Cannot modify connection requests
- ❌ Cannot submit forms to Utility Billing

**Purpose:**
- Staff can see utility status for projects they're monitoring
- Staff can provide information to citizens who inquire
- Staff can reference connection info for facility coordination

---

### **4. CITIZEN** 💚 (Transparency)

#### **A. View Water Connection Status in "Coming Soon"**
**Location:** `/citizen/facilities/coming-soon/{id}`

**Features:**
- ✅ See utility status as part of construction progress
- ✅ **Status displays:**
  - "💧 Water Connection: Not yet requested"
  - "💧 Water Connection: Request pending approval"
  - "💧 Water Connection: Scheduled for June 2025"
  - "💧 Water Connection: ✅ Meter installed"
  - "💧 Water Connection: ✅ Connected & Active"
- ✅ See water connection as part of construction timeline
- ✅ Understand facility readiness

**Purpose:**
- Transparency in construction progress
- Citizens know when facility will be ready
- Citizens see government is ensuring proper utilities

**Display Example:**
```
┌─────────────────────────────────────────────────────┐
│  🏟️ Coming Soon: Barangay 20 Sports Complex         │
│                                                      │
│  📍 Location: 123 Camarin Road, Brgy 20             │
│  🏗️ Construction Progress: 75%                      │
│  📅 Expected Opening: September 2025                │
│                                                      │
│  🔄 Construction Status                             │
│  ✅ Foundation Complete                             │
│  ✅ Structure Complete                              │
│  🔧 Utilities Installation (In Progress)            │
│     ├─ 💧 Water: ✅ Connected & Active ⭐ NEW       │
│     └─ ⚡ Electricity: 🔧 Scheduled                 │
│  ⏳ Interior Finishing                              │
│  ⏳ Landscaping                                     │
│                                                      │
│  👷 Contractor: ABC Construction Corp.               │
│  💰 Budget: ₱14,500,000                             │
│                                                      │
│  [ View Full Details ]                              │
└─────────────────────────────────────────────────────┘
```

---

## 📤 DATA EXCHANGE

### **API Endpoint 1: Public Facilities → Utility Billing**

**Request Water Connection**

**Endpoint:** `POST /api/utility-billing/water-connections/request`

**Request Payload:**
```json
{
  "request_id": "WC-2025-001",
  "request_date": "2025-03-15T10:30:00Z",
  "construction_project_id": "PROJ-2025-078",
  "facility_details": {
    "facility_name": "Barangay 20 Sports Complex",
    "facility_type": "Sports Complex",
    "lot_area_sqm": 8000,
    "building_floor_area_sqm": 2500,
    "building_type": "Government Facility - Recreational"
  },
  "location": {
    "land_id": "LAND-2025-078",
    "address": "123 Camarin Road, Barangay 20, Caloocan City",
    "barangay": "Barangay 20",
    "district": "District 1",
    "gps_coordinates": {
      "latitude": 14.7583,
      "longitude": 120.9834
    }
  },
  "water_requirements": {
    "toilet_fixtures": 12,
    "sink_fixtures": 8,
    "shower_fixtures": 4,
    "drinking_fountains": 3,
    "other_fixtures": "2 janitor sinks, 1 outdoor hose connection",
    "total_fixture_count": 29
  },
  "construction_status": "Under Construction - Utility Phase",
  "construction_progress_percentage": 45,
  "required_connection_date": "2025-06-01",
  "contact_person": {
    "name": "Maria Santos",
    "role": "Public Facilities Admin",
    "phone": "+63 917 123 4567",
    "email": "m.santos@caloocan.gov.ph"
  },
  "budget_allocation": {
    "service_connection_fee": 25000.00,
    "meter_deposit": 5000.00,
    "installation_fee": 0.00,
    "total_allocated": 30000.00,
    "funding_source": "Construction Budget (PROJ-2025-078)",
    "budget_approval_status": "Approved"
  },
  "remarks": "Urgent request for sports complex opening by Q3 2025"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Water connection request received successfully",
  "data": {
    "request_id": "WC-2025-001",
    "utility_billing_reference": "UB-REQ-456",
    "status": "Pending Review",
    "estimated_review_time": "3-5 business days",
    "tracking_url": "https://utility.caloocan.gov.ph/track/UB-REQ-456",
    "contact_info": {
      "office": "Service Connection Office",
      "phone": "(02) 8123-4567",
      "email": "connections@caloocanwater.gov.ph"
    }
  }
}
```

---

### **API Endpoint 2: Utility Billing → Public Facilities**

**Provide Water Supplies (Webhook)**

**Endpoint:** `POST /api/public-facilities/webhooks/water-connection-update`

**Webhook Payload (Connection Approved):**
```json
{
  "webhook_type": "water_connection_update",
  "webhook_id": "WH-2025-789",
  "timestamp": "2025-03-20T14:20:00Z",
  "connection_id": "CONN-2025-456",
  "request_id": "WC-2025-001",
  "status": "Approved",
  "approval_details": {
    "approved_by": "Engr. Juan Cruz",
    "approved_by_position": "Chief Engineer - Service Connections",
    "approved_date": "2025-03-20T14:00:00Z",
    "approval_notes": "Standard connection approved. Meter installation scheduled.",
    "approval_reference": "APR-2025-456"
  },
  "next_steps": {
    "step": "Meter Installation Scheduling",
    "estimated_date": "2025-05-28",
    "status": "Scheduled"
  }
}
```

**Webhook Payload (Meter Installed & Connected):**
```json
{
  "webhook_type": "water_connection_update",
  "webhook_id": "WH-2025-790",
  "timestamp": "2025-05-30T09:15:00Z",
  "connection_id": "CONN-2025-456",
  "request_id": "WC-2025-001",
  "status": "Connected",
  "meter_information": {
    "meter_number": "MTR-789654",
    "meter_type": "Commercial - High Flow",
    "meter_brand": "Sensus",
    "meter_capacity": "50 m³/hr",
    "installation_date": "2025-05-28",
    "installation_time": "10:30 AM",
    "installed_by": "Technician Team 3",
    "initial_reading": 0.00,
    "meter_location": "Front entrance, left side of main gate",
    "meter_photo_url": "/documents/meter-photos/MTR-789654.jpg"
  },
  "connection_details": {
    "connection_type": "New Service Connection",
    "pipe_size": "2 inches",
    "pipe_material": "HDPE",
    "connection_date": "2025-05-28",
    "activation_date": "2025-05-30",
    "activation_time": "09:00 AM",
    "water_pressure": "45 PSI",
    "water_quality_test": "Passed",
    "flow_rate_test": "Passed - 48 m³/hr"
  },
  "technical_specs": {
    "water_source": "Caloocan Water District - Zone 3",
    "supply_line": "Main line - Camarin Road",
    "supply_line_size": "6 inches",
    "shutoff_valve_location": "Property line, east side, marked with blue marker",
    "shutoff_valve_type": "Gate valve - 2 inches",
    "backflow_preventer": "Installed - complies with PNWS standards"
  },
  "billing_information": {
    "account_number": "ACCT-2025-789",
    "account_name": "Barangay 20 Sports Complex",
    "account_type": "Government Facility",
    "billing_cycle": "Monthly",
    "billing_date": "1st of every month",
    "rate_category": "Government Facility - Recreational",
    "basic_charge": 1500.00,
    "cubic_meter_rate": 25.00,
    "first_billing_date": "2025-07-01",
    "billing_contact": {
      "office": "Government Accounts Division",
      "phone": "(02) 8123-4599",
      "email": "gov-billing@caloocanwater.gov.ph"
    }
  },
  "charges": {
    "service_connection_fee": 25000.00,
    "meter_deposit": 5000.00,
    "installation_fee": 0.00,
    "other_charges": 0.00,
    "total_charges": 30000.00,
    "total_paid": 30000.00,
    "payment_status": "Paid",
    "payment_date": "2025-03-15",
    "payment_reference": "Construction Budget - PROJ-2025-078",
    "receipt_number": "OR-2025-12345"
  },
  "documents": [
    {
      "type": "Service Connection Permit",
      "file_name": "connection-permit-WC-2025-001.pdf",
      "file_url": "/documents/permits/connection-permit-WC-2025-001.pdf",
      "date_issued": "2025-03-20",
      "issued_by": "Engr. Juan Cruz"
    },
    {
      "type": "Meter Installation Report",
      "file_name": "meter-install-WC-2025-001.pdf",
      "file_url": "/documents/reports/meter-install-WC-2025-001.pdf",
      "date_issued": "2025-05-28",
      "issued_by": "Technician Team 3"
    },
    {
      "type": "Water Quality Test Certificate",
      "file_name": "water-quality-WC-2025-001.pdf",
      "file_url": "/documents/certificates/water-quality-WC-2025-001.pdf",
      "date_issued": "2025-05-29",
      "issued_by": "Water Quality Lab"
    },
    {
      "type": "Billing Account Setup Confirmation",
      "file_name": "billing-setup-ACCT-2025-789.pdf",
      "file_url": "/documents/billing/billing-setup-ACCT-2025-789.pdf",
      "date_issued": "2025-05-30",
      "issued_by": "Government Accounts Division"
    }
  ],
  "warranty_information": {
    "meter_warranty_period": "2 years",
    "meter_warranty_expiry": "2027-05-28",
    "installation_warranty_period": "1 year",
    "installation_warranty_expiry": "2026-05-28"
  },
  "maintenance_schedule": {
    "first_meter_reading": "2025-06-30",
    "reading_frequency": "Monthly",
    "meter_calibration_due": "2027-05-28"
  }
}
```

**Webhook Payload (Rejection):**
```json
{
  "webhook_type": "water_connection_update",
  "webhook_id": "WH-2025-791",
  "timestamp": "2025-03-22T11:00:00Z",
  "connection_id": null,
  "request_id": "WC-2025-002",
  "status": "Rejected",
  "rejection_details": {
    "rejected_by": "Engr. Juan Cruz",
    "rejected_date": "2025-03-22T10:45:00Z",
    "rejection_reason": "Location outside current water service area",
    "rejection_code": "NO_SERVICE_AREA",
    "rejection_notes": "The requested location is not yet covered by the water distribution network. Alternative: Private water source or wait for network expansion (estimated 2026).",
    "alternative_solutions": [
      "Install private deep well",
      "Wait for network expansion (Q2 2026)",
      "Connect to nearby facility (if available)"
    ]
  },
  "contact_for_appeal": {
    "office": "Service Connection Office",
    "phone": "(02) 8123-4567",
    "email": "connections@caloocanwater.gov.ph"
  }
}
```

---

## 📊 DATABASE CHANGES

### **1. Alter `construction_projects` table**

Add water connection tracking fields:

```sql
ALTER TABLE construction_projects
ADD COLUMN water_connection_request_id VARCHAR(50) NULL COMMENT 'Public Facilities request ID',
ADD COLUMN water_connection_status ENUM(
  'not_required', 
  'pending', 
  'approved', 
  'meter_scheduled', 
  'meter_installed', 
  'connected', 
  'rejected'
) DEFAULT 'not_required' COMMENT 'Current water connection status',
ADD COLUMN water_meter_number VARCHAR(50) NULL COMMENT 'Assigned meter number from Utility Billing',
ADD COLUMN water_account_number VARCHAR(50) NULL COMMENT 'Billing account number',
ADD COLUMN water_connection_date DATE NULL COMMENT 'Date water was connected',
ADD COLUMN water_connection_details JSON NULL COMMENT 'Full connection details from Utility Billing';

-- Add index for faster queries
CREATE INDEX idx_water_connection_status ON construction_projects(water_connection_status);
CREATE INDEX idx_water_meter_number ON construction_projects(water_meter_number);
```

---

### **2. Create `utility_connections` table**

Store detailed water connection requests and responses:

```sql
CREATE TABLE utility_connections (
  -- Primary Key
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  
  -- Foreign Keys
  construction_project_id BIGINT UNSIGNED NOT NULL COMMENT 'Links to construction_projects table',
  requested_by BIGINT UNSIGNED NOT NULL COMMENT 'Admin user who requested',
  
  -- Basic Info
  utility_type ENUM('water', 'electricity', 'other') DEFAULT 'water' COMMENT 'Type of utility connection',
  request_id VARCHAR(50) UNIQUE NOT NULL COMMENT 'Public Facilities request ID (e.g., WC-2025-001)',
  request_date DATETIME NOT NULL COMMENT 'When request was submitted',
  status VARCHAR(50) NOT NULL COMMENT 'Current status (pending, approved, connected, etc.)',
  
  -- Request Details (what we send)
  facility_name VARCHAR(255) NOT NULL,
  facility_type VARCHAR(100) NOT NULL,
  location_address TEXT NOT NULL,
  gps_latitude DECIMAL(10, 8) NULL,
  gps_longitude DECIMAL(11, 8) NULL,
  
  -- Water Requirements
  fixture_count JSON NULL COMMENT 'Toilet, sink, shower, fountain counts',
  required_connection_date DATE NULL,
  
  -- Budget
  budget_allocated DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Total budget for connection',
  service_connection_fee DECIMAL(15,2) DEFAULT 0.00,
  meter_deposit DECIMAL(15,2) DEFAULT 0.00,
  
  -- Response from Utility Billing (what they send back)
  utility_billing_reference VARCHAR(100) NULL COMMENT 'Their reference number',
  connection_id VARCHAR(50) NULL COMMENT 'Utility Billing connection ID',
  meter_number VARCHAR(50) NULL COMMENT 'Assigned meter number',
  meter_type VARCHAR(100) NULL,
  meter_installation_date DATE NULL,
  
  -- Billing Account
  account_number VARCHAR(50) NULL COMMENT 'Billing account number',
  billing_cycle VARCHAR(50) NULL,
  rate_category VARCHAR(100) NULL,
  first_billing_date DATE NULL,
  
  -- Connection Technical Details
  connection_date DATE NULL COMMENT 'Date physically connected',
  activation_date DATE NULL COMMENT 'Date service activated',
  pipe_size VARCHAR(50) NULL,
  water_pressure VARCHAR(50) NULL,
  
  -- Full Response Data
  connection_details JSON NULL COMMENT 'Complete response from Utility Billing',
  
  -- Rejection (if applicable)
  rejection_reason TEXT NULL,
  rejection_code VARCHAR(50) NULL,
  
  -- Documents
  documents JSON NULL COMMENT 'Array of document URLs',
  
  -- Timestamps
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  -- Foreign Key Constraints
  FOREIGN KEY (construction_project_id) REFERENCES construction_projects(id) ON DELETE CASCADE,
  FOREIGN KEY (requested_by) REFERENCES users(id),
  
  -- Indexes for performance
  INDEX idx_construction_project (construction_project_id),
  INDEX idx_status (status),
  INDEX idx_meter_number (meter_number),
  INDEX idx_account_number (account_number),
  INDEX idx_request_date (request_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### **3. Create `utility_connection_logs` table**

Audit trail for all status updates:

```sql
CREATE TABLE utility_connection_logs (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  utility_connection_id BIGINT UNSIGNED NOT NULL,
  
  -- Log Entry
  status_from VARCHAR(50) NULL COMMENT 'Previous status',
  status_to VARCHAR(50) NOT NULL COMMENT 'New status',
  changed_by VARCHAR(100) NOT NULL COMMENT 'Who/what changed it',
  change_type ENUM('manual', 'webhook', 'api_response', 'system') DEFAULT 'system',
  
  -- Details
  remarks TEXT NULL,
  webhook_data JSON NULL COMMENT 'Full webhook payload if applicable',
  
  -- Timestamp
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  -- Foreign Key
  FOREIGN KEY (utility_connection_id) REFERENCES utility_connections(id) ON DELETE CASCADE,
  
  -- Index
  INDEX idx_utility_connection (utility_connection_id),
  INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### **4. Update `facilities` table**

Add water connection info to facility profile (after turnover):

```sql
ALTER TABLE facilities
ADD COLUMN water_meter_number VARCHAR(50) NULL COMMENT 'Water meter number (from construction)',
ADD COLUMN water_account_number VARCHAR(50) NULL COMMENT 'Utility billing account',
ADD COLUMN water_connection_date DATE NULL COMMENT 'When water was first connected',
ADD COLUMN has_water_supply BOOLEAN DEFAULT FALSE COMMENT 'Whether facility has water';

-- Index for quick lookup
CREATE INDEX idx_water_meter_number ON facilities(water_meter_number);
```

---

## 🔄 KEY WORKFLOWS

### **Workflow 1: Standard Water Connection Request**

```
ADMIN INITIATES REQUEST

Step 1: Check Construction Progress
├─ Admin opens construction project dashboard
├─ Sees: "Construction 45% complete - Utility phase"
├─ Dashboard shows: "💧 Water Connection: Not yet requested"
└─ Button available: [Request Water Connection]

Step 2: Fill Request Form
├─ Click [Request Water Connection]
├─ Form opens with auto-filled data:
│  ├─ Facility name: Barangay 20 Sports Complex
│  ├─ Location: 123 Camarin Road (from Urban Planning)
│  └─ GPS: 14.7583, 120.9834
│
├─ Admin fills fixture counts:
│  ├─ Toilets: 12
│  ├─ Sinks: 8
│  ├─ Showers: 4
│  └─ Drinking fountains: 3
│
├─ Set required date: June 1, 2025
├─ Budget auto-filled: ₱30,000 (from construction budget)
└─ Contact info auto-filled (Admin's info)

Step 3: Submit Request
├─ Click [Submit Request]
├─ System validates:
│  ├─ ✅ Construction >= 40% complete
│  ├─ ✅ Budget allocated
│  ├─ ✅ All required fields filled
│  └─ ✅ Validation passed
│
├─ Generate request ID: WC-2025-001
├─ Save to database (utility_connections table)
├─ Send API request to Utility Billing
│  └─ POST /api/utility-billing/water-connections/request
│
└─ Receive confirmation:
   ├─ Utility Billing reference: UB-REQ-456
   ├─ Status: Pending Review
   └─ Estimated review: 3-5 business days

Step 4: Admin Receives Confirmation
├─ Success message displayed
├─ SMS sent to Admin: "Water connection request WC-2025-001 submitted"
├─ Dashboard updated:
│  └─ "💧 Water Connection: ⏳ Pending Review"
└─ Admin can now track status

────────────────────────────────────────────────

UTILITY BILLING PROCESSES REQUEST

Step 5: Utility Billing Reviews (Offline)
├─ Utility Billing receives request
├─ Engineer reviews:
│  ├─ Check location (within service area? ✅)
│  ├─ Check water availability ✅
│  ├─ Check facility specs ✅
│  └─ Approve request ✅
│
└─ Decision: APPROVED

Step 6: Webhook - Request Approved
├─ Utility Billing sends webhook:
│  └─ POST /api/public-facilities/webhooks/water-connection-update
│
├─ Public Facilities receives webhook:
│  ├─ Status: Approved
│  ├─ Approved by: Engr. Juan Cruz
│  ├─ Meter installation scheduled: May 28, 2025
│  └─ Connection ID: CONN-2025-456
│
├─ Database updated:
│  ├─ utility_connections.status = 'approved'
│  └─ Log created in utility_connection_logs
│
├─ Admin notified:
│  ├─ SMS: "Water connection WC-2025-001 approved!"
│  └─ Dashboard badge: "1 new update"
│
└─ Dashboard shows:
   "💧 Water Connection: ✅ Approved - Meter scheduled May 28"

Step 7: Webhook - Meter Scheduled
├─ Utility Billing confirms schedule
├─ Webhook sent with schedule details
├─ Admin sees: "🔧 Meter Installation: May 28, 2025 at 10:30 AM"
└─ Admin can coordinate with construction team

Step 8: Meter Installation (Offline)
├─ Utility Billing team arrives on-site
├─ Installs water meter: MTR-789654
├─ Connects pipes
├─ Tests water flow
└─ Takes photos

Step 9: Webhook - Meter Installed
├─ Webhook sent with meter details:
│  ├─ Meter number: MTR-789654
│  ├─ Installation date: May 28, 2025
│  ├─ Meter location: Front entrance, left side
│  └─ Photos attached
│
├─ Database updated:
│  ├─ utility_connections.meter_number = 'MTR-789654'
│  └─ Status = 'meter_installed'
│
└─ Dashboard shows:
   "💧 Water Connection: ✅ Meter Installed (MTR-789654)"

Step 10: Water Activated
├─ Utility Billing activates water supply
├─ Tests pressure and flow
├─ Sets up billing account: ACCT-2025-789
└─ Connection is LIVE

Step 11: Webhook - Connected & Active
├─ Final webhook sent with complete details:
│  ├─ Status: Connected
│  ├─ Activation date: May 30, 2025
│  ├─ Meter number: MTR-789654
│  ├─ Account number: ACCT-2025-789
│  ├─ Water pressure: 45 PSI
│  ├─ Billing starts: July 1, 2025
│  └─ Documents: Permit, Installation Report, Quality Test
│
├─ Database fully updated:
│  ├─ Status = 'connected'
│  ├─ All connection details saved
│  └─ construction_projects.water_connection_status = 'connected'
│
├─ Admin notified:
│  └─ SMS: "Water connection ACTIVE! Meter: MTR-789654"
│
└─ Dashboard shows:
   "💧 Water Connection: ✅ CONNECTED & ACTIVE"

────────────────────────────────────────────────

ADMIN VERIFIES BEFORE TURNOVER

Step 12: Pre-Turnover Verification
├─ Admin opens turnover checklist
├─ Sees: "☑️ 💧 Water connection active (MTR-789654)"
├─ Clicks [View Details]
├─ Reviews:
│  ├─ Meter installed ✅
│  ├─ Water flowing ✅
│  ├─ Account set up ✅
│  └─ Documents complete ✅
│
└─ Marks as verified in checklist

Step 13: Facility Turnover
├─ Construction completes
├─ Facility turnover to Public Facilities
├─ Water connection info copied to facilities table:
│  ├─ facilities.water_meter_number = 'MTR-789654'
│  ├─ facilities.water_account_number = 'ACCT-2025-789'
│  └─ facilities.has_water_supply = TRUE
│
└─ Facility is now ACTIVE with water supply

Step 14: Ongoing Monitoring
├─ Facility is operational
├─ Monthly water bills go to Government Accounts
├─ Meter readings tracked by Utility Billing
└─ Connection info available for future maintenance
```

---

### **Workflow 2: Rejected Request**

```
Step 1-5: Same as above (Admin submits request)

Step 6: Utility Billing Rejects
├─ Utility Billing reviews request
├─ Issue found: Location outside service area
├─ Decision: REJECTED
└─ Reason: No water distribution network in area

Step 7: Webhook - Rejection
├─ Webhook sent:
│  ├─ Status: Rejected
│  ├─ Reason: "Location outside current water service area"
│  ├─ Code: NO_SERVICE_AREA
│  └─ Alternative: "Install private deep well or wait for expansion"
│
├─ Database updated:
│  ├─ Status = 'rejected'
│  └─ Rejection details saved
│
└─ Admin notified:
   └─ SMS: "Water connection request rejected - see details"

Step 8: Admin Reviews Rejection
├─ Opens request details
├─ Sees rejection reason
├─ Views alternative solutions:
│  ├─ Option 1: Private deep well
│  ├─ Option 2: Wait for network expansion (2026)
│  └─ Option 3: Connect to nearby facility
│
└─ Admin decides next steps

Step 9: Admin Takes Action
├─ Option A: Appeal rejection (contact Utility Billing)
├─ Option B: Adjust construction plan (private water source)
├─ Option C: Escalate to Infrastructure PM
└─ Document decision in project notes
```

---

### **Workflow 3: Integration with Construction Progress**

```
Construction Timeline with Water Connection:

Month 1-2: Foundation & Structure
├─ Construction: 0-30%
└─ Water: Not yet needed

Month 3-4: Utility Phase
├─ Construction: 40-60%
├─ ⭐ TRIGGER: Request water connection
│  └─ Admin submits water request (Step 1-4 above)
│
└─ Parallel work:
   ├─ Construction continues (walls, roof)
   └─ Utility Billing processes request

Month 5: Meter Installation
├─ Construction: 70%
├─ ⭐ Meter installed (Step 9 above)
│  └─ Meter: MTR-789654
│
└─ Construction team coordinates with meter location

Month 6: Final Phase
├─ Construction: 90%
├─ ⭐ Water activated (Step 11 above)
│  └─ Plumbing can now be tested!
│
└─ All fixtures tested with live water

Month 7: Turnover
├─ Construction: 100%
├─ All utilities verified (water, electricity)
├─ ⭐ Water connection info transferred to facility profile
└─ Facility ready to open!
```

---

## 🔗 INTEGRATION WITH EXISTING FEATURES

### **1. Update Infrastructure Integration Document**

Add Utility Billing integration to the construction workflow.

**In `INFRASTRUCTURE_INTEGRATION_FEATURES.md`:**

#### **Add to Construction Projects Dashboard:**

```diff
Construction Project Card:
├── Basic Info
│   ├── Facility: Barangay 20 Sports Complex
│   ├── Contractor: ABC Construction Corp.
│   └── Status: Under Construction
├── Construction Progress
│   ├── Progress: 75%
│   ├── Timeline: On Schedule
│   └── Next Milestone: Interior Finishing
├── Budget Status
│   ├── Budget: ₱14,500,000
│   ├── Spent: ₱10,875,000
│   └── Variance: -₱0 (On Budget)
+└── 💧 Utilities Status: ⭐ NEW
+    ├── Water Connection: ✅ Connected (Meter #MTR-789654)
+    │   └── [View Details] → Opens utility connection page
+    └── Electricity: 🔧 Scheduled for June 15
```

#### **Add to Pre-Turnover Checklist:**

```diff
Pre-Turnover Checklist:
☑️ Construction 100% complete
☑️ Safety inspection passed
☑️ Quality assurance check
+☑️ 💧 Water connection active (MTR-789654) ⭐ NEW
+☑️ ⚡ Electricity connection active ⭐ NEW
☑️ Final cleanup done
☐ Acceptance documents signed
☐ Warranty documents received
```

#### **Add to Coming Soon Facilities (Citizen View):**

```diff
🏟️ Coming Soon: Barangay 20 Sports Complex

📍 Location: 123 Camarin Road, Brgy 20
🏗️ Construction Progress: 75%
📅 Expected Opening: September 2025

🔄 Construction Status
✅ Foundation Complete
✅ Structure Complete
🔧 Utilities Installation (In Progress)
+   ├─ 💧 Water: ✅ Connected & Active ⭐ NEW
+   └─ ⚡ Electricity: 🔧 Scheduled
⏳ Interior Finishing
⏳ Landscaping

👷 Contractor: ABC Construction Corp.
💰 Budget: ₱14,500,000
```

---

### **2. Add Navigation to Admin Sidebar**

**In Admin Dashboard:**

```diff
📋 Facilities Management
   ├── Active Facilities
   ├── Construction Projects
   │   ├── All Projects
   │   ├── Under Construction
   │   └── Pending Turnover
   ├── Request New Facility
+  ├── Utility Connections ⭐ NEW
+  │   ├── All Connections
+  │   ├── Pending Requests
+  │   └── Active Connections
   └── Warranty Claims
```

---

### **3. Add Utility Connection Widget to Dashboard**

**Admin Dashboard Overview:**

```
┌─────────────────────────────────────────────────────┐
│  📊 Public Facilities Dashboard                     │
│                                                      │
│  🏢 Active Facilities: 45                           │
│  🏗️ Under Construction: 3                           │
│  📅 Today's Bookings: 12                            │
│  💧 Utility Connections: 2 pending ⭐ NEW           │
│                                                      │
│  ┌─────────────────────────────────────────────┐   │
│  │ 💧 Utility Connection Updates               │   │
│  │                                              │   │
│  │ ✅ Brgy 20 Sports Complex                   │   │
│  │    Water: Connected & Active                │   │
│  │    Meter: MTR-789654                        │   │
│  │    [View Details]                           │   │
│  │                                              │   │
│  │ ⏳ Brgy 15 Community Center                 │   │
│  │    Water: Pending Approval                  │   │
│  │    Requested: 2 days ago                    │   │
│  │    [Track Status]                           │   │
│  └─────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────┘
```

---

## ✅ IMPLEMENTATION PRIORITY

### **Phase 1 - Core Functionality (MVP):** 
**Timeline: 2-3 weeks**

1. ✅ Database setup (tables, migrations)
2. ✅ API integration with Utility Billing:
   - Request water connection endpoint
   - Webhook for status updates
3. ✅ Admin request form (basic version)
4. ✅ Connection status tracking page
5. ✅ Integration with construction projects dashboard
6. ✅ Basic SMS notifications

**Deliverables:**
- Admin can request water connections
- Admin can track connection status
- System receives status updates from Utility Billing
- Connection info visible in construction dashboard

---

### **Phase 2 - Enhanced Features:**
**Timeline: 1-2 weeks**

1. ✅ Document management (upload/download)
2. ✅ Photo uploads (meter, location)
3. ✅ Detailed connection timeline view
4. ✅ Pre-turnover verification checklist
5. ✅ Copy connection info to facility profile
6. ✅ Utility connection widget on dashboard
7. ✅ Enhanced notifications (email + SMS)

**Deliverables:**
- Complete document handling
- Visual connection timeline
- Turnover integration
- Better admin UX

---

### **Phase 3 - Advanced Features:**
**Timeline: 1 week**

1. ✅ Auto-request trigger (when construction hits 40%)
2. ✅ Connection reminder notifications
3. ✅ Bulk connection status export
4. ✅ Analytics dashboard (connection trends)
5. ✅ Integration with maintenance system (future)
6. ✅ Mobile-responsive views

**Deliverables:**
- Automated workflows
- Better reporting
- Future-ready architecture

---

### **Phase 4 - Optimization & Expansion:**
**Timeline: Ongoing**

1. ✅ Electricity connection integration (same pattern)
2. ✅ Other utility types (gas, internet, etc.)
3. ✅ Performance optimization
4. ✅ Advanced analytics
5. ✅ Citizen mobile app integration

**Deliverables:**
- Expanded utility coverage
- Optimized performance
- Better citizen experience

---

## 🎯 KEY PRINCIPLES

### **1. PART OF CONSTRUCTION WORKFLOW**
- Water connection is a **construction phase task**, not standalone
- Triggered when construction reaches utility installation phase (40-60%)
- Completes before facility turnover

### **2. NEW FACILITIES ONLY**
- This integration is **ONLY for new facility construction**
- Not for maintenance or repairs of existing facilities
- One-time setup per facility

### **3. ADMIN-DRIVEN (with future automation)**
- **Phase 1:** Admin manually requests when ready (practical, flexible)
- **Phase 2:** System can auto-trigger request (efficient, systematic)
- Admin always has control and can override

### **4. BUDGET INCLUDED IN CONSTRUCTION**
- Water connection costs are **part of construction budget**
- No separate Finance workflow needed (unlike Energy Efficiency events)
- Payment already approved with construction project

### **5. SIMPLE REQUIREMENTS**
- No water consumption estimates (impossible to predict accurately)
- Use **fixture counts** instead (practical and verifiable)
- Focus on connection, not usage predictions

### **6. TRANSPARENCY FOR CITIZENS**
- Utility status visible in "Coming Soon" facilities
- Citizens see construction progress includes utilities
- Builds trust in government processes

### **7. INTEGRATION-READY**
- Database design supports multiple utility types (water, electricity, etc.)
- Webhook architecture allows real-time updates
- Expandable to other utilities in future phases

### **8. AUDIT TRAIL**
- All requests logged in database
- All status changes tracked with timestamps
- Complete history for COA compliance

---

## 📞 SUPPORT & COORDINATION

### **Roles & Responsibilities:**

**Public Facilities Admin:**
- Requests water connections
- Tracks status
- Coordinates with construction team
- Verifies before turnover

**Utility Billing Office:**
- Reviews requests
- Schedules installations
- Installs meters
- Activates connections
- Sends status updates via webhooks

**EIS Super Admin:**
- Sets up API integration
- Manages technical configuration
- Troubleshoots connection issues

**Infrastructure PM (indirect):**
- Provides construction schedule
- Coordinates on-site access for meter installation
- Ensures facility is ready for connection

---

## 📋 SUCCESS METRICS

### **Key Performance Indicators:**

1. **Request Processing Time:**
   - Target: 3-5 business days from request to approval
   - Measure: Average time between request and approval

2. **Connection Completion Rate:**
   - Target: 100% of facilities connected before turnover
   - Measure: % of facilities with active water before opening

3. **Automation Rate (Phase 2+):**
   - Target: 80% of requests auto-triggered at 40% construction
   - Measure: % of auto-triggered vs manual requests

4. **Data Accuracy:**
   - Target: 100% of connection data synced correctly
   - Measure: % of successful webhook updates

5. **Admin Satisfaction:**
   - Target: 4.5/5 rating
   - Measure: Admin feedback on ease of use

6. **Zero Delays:**
   - Target: 0 facility turnover delays due to water connection
   - Measure: # of delays attributed to water connection issues

---

## 🔐 SECURITY & VALIDATION

### **API Security:**
- ✅ Authentication tokens (OAuth 2.0 or API keys)
- ✅ HTTPS only (encrypted transmission)
- ✅ Request signing (verify authenticity)
- ✅ Rate limiting (prevent abuse)
- ✅ IP whitelisting (trusted sources only)

### **Data Validation:**
- ✅ Validate construction project exists
- ✅ Validate construction progress >= 40%
- ✅ Validate budget is allocated
- ✅ Validate contact information format
- ✅ Validate date ranges (required date in future)
- ✅ Sanitize all input data

### **Webhook Security:**
- ✅ Webhook signature verification
- ✅ Validate webhook source (from Utility Billing only)
- ✅ Idempotency (handle duplicate webhooks)
- ✅ Webhook retry logic (if endpoint fails)

### **Access Control:**
- ✅ Only Admin can request connections
- ✅ Only EIS Super Admin can configure API
- ✅ Staff can view but not modify
- ✅ Citizens can only see public data

---

## 📄 DOCUMENTATION REFERENCES

**Related Integration Documents:**
1. `INFRASTRUCTURE_INTEGRATION_FEATURES.md` - Construction project workflow
2. `URBAN_PLANNING_INTEGRATION_FEATURES.md` - Land selection prerequisite
3. `ENERGY_EFFICIENCY_INTEGRATION_FEATURES.md` - Government program coordination
4. `HOW_TO_DEMOTE_SUPERADMIN.md` - Role management guide

**API Documentation:**
- Utility Billing API documentation (to be provided by Utility Billing team)
- Webhook specification (to be defined with Utility Billing team)
- Authentication guide (OAuth 2.0 setup)

**Database Schema:**
- `construction_projects` table updates
- `utility_connections` table structure
- `utility_connection_logs` audit trail
- `facilities` table updates

---

## ✅ CHECKLIST FOR IMPLEMENTATION

### **Before Development:**
- [ ] Coordinate with Utility Billing team (API specs, webhooks)
- [ ] Agree on data exchange format (JSON structure)
- [ ] Set up API authentication (keys, tokens)
- [ ] Define webhook endpoints and security
- [ ] Review and finalize database schema
- [ ] Create test environment

### **During Development:**
- [ ] Implement database migrations
- [ ] Build API integration layer
- [ ] Create Admin request form UI
- [ ] Create status tracking page UI
- [ ] Implement webhook receiver
- [ ] Add to construction dashboard
- [ ] Set up SMS notifications
- [ ] Write unit tests
- [ ] Write integration tests

### **Before Launch:**
- [ ] Test with Utility Billing staging environment
- [ ] Test webhook scenarios (approved, rejected, installed, etc.)
- [ ] Test error handling (API down, timeout, etc.)
- [ ] User acceptance testing (UAT) with Admin
- [ ] Security audit (penetration testing)
- [ ] Performance testing (load, stress)
- [ ] Document deployment steps
- [ ] Train Admin users

### **After Launch:**
- [ ] Monitor API performance
- [ ] Monitor webhook reliability
- [ ] Collect Admin feedback
- [ ] Track success metrics
- [ ] Plan Phase 2 enhancements
- [ ] Document lessons learned

---

**Document End** 💧

---

**Next Steps:**
1. Review this document with Utility Billing team
2. Finalize API specifications and webhook formats
3. Create technical implementation plan
4. Begin Phase 1 development

**Questions? Contact:**
- Public Facilities Team: [Your contact info]
- Utility Billing Team: [Their contact info]
- EIS Lead Programmer: [Technical support]

