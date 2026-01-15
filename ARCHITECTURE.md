# 🏗️ SYSTEM ARCHITECTURE - PUBLIC FACILITIES RESERVATION SYSTEM

**Project:** LGU1 Public Facilities Reservation System  
**Created:** December 10, 2025  
**Last Updated:** December 10, 2025  
**Version:** 1.0

---

## 📋 TABLE OF CONTENTS

1. [System Overview](#system-overview)
2. [Multi-Location Architecture](#multi-location-architecture)
3. [Authentication & Authorization](#authentication--authorization)
4. [Core Workflows](#core-workflows)
5. [Technical Stack](#technical-stack)
6. [Database Architecture](#database-architecture)
7. [API Integration Strategy](#api-integration-strategy)
8. [Security Architecture](#security-architecture)
9. [AI Analytics Module](#ai-analytics-module)
10. [Deployment Architecture](#deployment-architecture)

---

## 🎯 SYSTEM OVERVIEW

### **Purpose**
A comprehensive public facility reservation system that enables citizens to book government facilities (gymnasiums, convention centers, function halls) with automated conflict detection, multi-level approval workflows, and AI-powered usage analytics.

### **Core Objectives**
1. **Transparency** - Real-time availability calendar accessible to all citizens
2. **Efficiency** - Automated scheduling, conflict detection, and approval routing
3. **Accountability** - Multi-level approval with full audit trails
4. **Accessibility** - Mobile-responsive, unified login for all user types
5. **Intelligence** - AI-powered pattern recognition for resource optimization

### **Key Innovation**
- **Process-by-Process Development** (vertical slices) ensuring each workflow is complete and demonstrable
- **Multi-location Support** with city-specific configurations
- **Configurable Payment System** (hourly vs. per-person, fees vs. free)
- **AI Pattern Recognition** (not prediction) based on historical data

---

## 🌍 MULTI-LOCATION ARCHITECTURE

### **Supported Locations**
Based on interview findings from:
- **Caloocan City** - South City General Services Department
- **Quezon City** - Public Affairs and Information Services Department

### **Location-Specific Configuration**

```php
// locations table
id | location_name | location_code | admin_email | config_json
1  | Caloocan City | CAL          | admin@cal.gov.ph | {...}
2  | Quezon City   | QC           | admin@qc.gov.ph  | {...}
```

### **Config JSON Structure**
```json
{
  "payment_mode": "hourly", // or "per_person"
  "base_rate": 1000,
  "currency": "PHP",
  "operating_hours": {
    "start": "06:00",
    "end": "22:00"
  },
  "advance_booking_days": 180,
  "cancellation_deadline_hours": 48,
  "approval_levels": ["staff", "admin"],
  "discount_tiers": {
    "pwd": 20,
    "senior": 20,
    "student": 10
  },
  "requires_deposit": true,
  "deposit_percentage": 30,
  "allow_recurring_bookings": true
}
```

### **Benefits**
- **Scalability** - Add new cities without code changes
- **Flexibility** - Each location can customize their rules
- **Demo-Ready** - Show multi-location during defense

---

## 🔐 AUTHENTICATION & AUTHORIZATION

### **Unified Login System**

**Single Entry Point:** One login page for all user roles
- No separate login pages per role
- Role-based redirection after authentication
- Leverage existing user data (email + password + OTP)

### **User Roles & Permissions**

```
┌─────────────────────────────────────────────────────┐
│                  LGU1 USERS TABLE                   │
└─────────────────────────────────────────────────────┘
           ↓                    ↓                ↓
    ┌──────────┐        ┌──────────┐      ┌──────────┐
    │  SUPER   │        │  ADMIN   │      │  STAFF   │
    │  ADMIN   │        │          │      │          │
    └──────────┘        └──────────┘      └──────────┘
         ↓                    ↓                ↓
  System Config      Approve/Reject    Verify Documents
  User Management    Manage Facilities  Check Eligibility
  Reports Access     View Analytics     Forward to Admin
  Audit Logs        Set Pricing        Support Citizens

           ┌──────────┐
           │ CITIZEN  │
           │          │
           └──────────┘
                ↓
          Book Facilities
          View My Bookings
          Track Status
          Pay Fees
```

### **Role Hierarchy**

| Role | Level | Access | Redirect After Login |
|------|-------|--------|---------------------|
| **EIS Super Admin** | 1 | All systems + user management | `/superadmin/dashboard` |
| **Admin** | 2 | Full facility management + approvals | `/admin/dashboard` |
| **Staff** | 3 | Document verification + support | `/staff/dashboard` |
| **Citizen** | 4 | Booking + view own reservations | `/citizen/dashboard` |

### **Authentication Flow**

```
┌─────────────────────────────────────────────────────┐
│                 LOGIN PAGE                          │
│         (Single page for all roles)                 │
└─────────────────────────────────────────────────────┘
                      ↓
         Email + Password Verification
                      ↓
               OTP Verification
            (1-minute expiry)
                      ↓
        ┌─────────────────────────┐
        │   Check user role       │
        └─────────────────────────┘
         ↓        ↓        ↓       ↓
    Super Admin  Admin  Staff  Citizen
         ↓        ↓        ↓       ↓
    Respective Dashboard (role-based)
```

### **Session Management**
- **Timeout:** 2 minutes of inactivity (MANDATORY for defense)
- **Auto-logout:** Immediate redirect to login on timeout
- **CSRF Protection:** Token refresh every 30 seconds on auth pages
- **Token Refresh:** Silent refresh on 419 errors (no modal)

### **Future: LGU1 Portal Integration**

```
┌─────────────────────────────────────────────────────┐
│           LGU1 CENTRAL PORTAL (Future)              │
│         Single Sign-On (SSO) for all systems        │
└─────────────────────────────────────────────────────┘
                      ↓ API Token
    ┌──────────────────────────────────────────────┐
    │  Facilities | Housing | Energy | Transport   │
    └──────────────────────────────────────────────┘
```

**Current State:** Standalone citizen login  
**Future State:** API-based SSO with JWT tokens  
**Strategy:** Build standalone now, integrate later

---

## 🔄 CORE WORKFLOWS

### **Process-by-Process Approach**

Instead of building by role (all Super Admin features, then all Admin features), we build by **process** (complete workflows):

```
❌ WRONG: Role-by-Role (Horizontal Layers)
Week 1: Build all Super Admin features
Week 2: Build all Admin features
Week 3: Build all Staff features
Week 4: Build all Citizen features
Problem: No working workflow until week 4

✅ CORRECT: Process-by-Process (Vertical Slices)
Week 1: Complete Facility Directory & Availability Calendar
Week 2: Complete Booking Request → Staff Verify → Admin Approve
Week 3: Complete Payment Processing & Receipt Generation
Week 4: Complete Conflict Detection & Auto-Notifications
Result: Working demos every week!
```

---

### **5 CORE WORKFLOWS**

Based on interview findings from Caloocan and Quezon City:

---

#### **WORKFLOW 1: FACILITY DIRECTORY & AVAILABILITY CALENDAR**

**User Story:** *"As a citizen, I want to see all available facilities and their real-time availability so I can plan my event booking."*

```
┌─────────────────────────────────────────────────────┐
│  PUBLIC ACCESS (No Login Required)                  │
└─────────────────────────────────────────────────────┘
          ↓
  Facility Directory Page
  - List all active facilities
  - Photos, capacity, amenities
  - Base pricing information
  - Location and contact details
          ↓
  Availability Calendar
  - Month/Week/Day view
  - Color-coded status:
    ✓ Available (green)
    ⊗ Booked (gray)
    ⚠ Under Maintenance (yellow)
    ⊗ Blocked (red)
  - Click date → View hourly slots
          ↓
  Facility Details Modal
  - Full specifications
  - Equipment available
  - Rules and guidelines
  - "Book Now" button → Login required
```

**Database Tables Involved:**
- `facilities` - Facility master data
- `facility_schedules` - Availability blocks
- `bookings` - Confirmed reservations
- `maintenance_schedules` - Blocked dates

**Roles Involved:**
- **Public/Guest** - View directory and calendar
- **Citizen** - Book after login
- **Admin** - Manage facilities, set availability
- **Staff** - Mark maintenance periods

**Success Criteria:**
- ✅ Public can view all facilities without login
- ✅ Calendar shows real-time availability
- ✅ No booking conflicts visible
- ✅ Mobile-responsive calendar

---

#### **WORKFLOW 2: BOOKING REQUEST & APPROVAL CHAIN**

**User Story:** *"As a citizen, I want to submit a booking request with my documents so the government can approve my facility reservation."*

```
┌─────────────────────────────────────────────────────┐
│  CITIZEN: Submit Booking Request                    │
└─────────────────────────────────────────────────────┘
  1. Select facility and date/time
  2. Choose equipment (if needed)
  3. Specify number of attendees
  4. Upload requirements:
     - Valid ID
     - Event permit (if required)
     - Barangay clearance
     - PWD/Senior/Student ID (for discounts)
  5. Review pricing breakdown
  6. Submit booking request
          ↓
          Status: "Pending Staff Verification"
          ↓
┌─────────────────────────────────────────────────────┐
│  STAFF: Document Verification                       │
└─────────────────────────────────────────────────────┘
  1. View pending requests queue
  2. Open booking details
  3. Verify uploaded documents:
     - Check ID validity
     - Confirm discount eligibility
     - Validate event permit
  4. Check for schedule conflicts
  5. Add verification notes
  6. Action:
     → Approve & Forward to Admin
     → Reject with reason
          ↓
          Status: "Pending Admin Approval"
          ↓
┌─────────────────────────────────────────────────────┐
│  ADMIN: Final Approval & Confirmation               │
└─────────────────────────────────────────────────────┘
  1. View staff-verified requests
  2. Review booking details
  3. Verify no conflicts with priority events
  4. Check resource availability
  5. Final pricing verification
  6. Action:
     → Approve (booking confirmed)
     → Reject with reason
     → Request more info
          ↓
          Status: "Confirmed" or "Rejected"
          ↓
┌─────────────────────────────────────────────────────┐
│  SYSTEM: Auto-Notifications                         │
└─────────────────────────────────────────────────────┘
  - Email citizen with confirmation/rejection
  - Generate booking reference number
  - Send payment instructions (if approved)
  - Add to facility calendar
  - Block time slot
```

**Database Tables Involved:**
- `bookings` - Main booking record
- `booking_documents` - Uploaded files
- `booking_approvals` - Approval chain history
- `booking_notes` - Staff/Admin comments
- `audit_logs` - Full activity trail

**Roles Involved:**
- **Citizen** - Submit request, track status
- **Staff** - Verify documents, check eligibility
- **Admin** - Final approval, confirm booking

**Success Criteria:**
- ✅ Multi-level approval with accountability
- ✅ Each role sees only their tasks
- ✅ Full audit trail of actions
- ✅ Auto-notifications at each stage
- ✅ Cannot double-book same slot

---

#### **WORKFLOW 3: PAYMENT PROCESSING & RECEIPT GENERATION**

**User Story:** *"As a citizen, I want to pay my facility fee and receive an official receipt so I can complete my reservation."*

```
┌─────────────────────────────────────────────────────┐
│  CITIZEN: View Payment Details                      │
└─────────────────────────────────────────────────────┘
  After admin approval:
  1. Receive email with payment breakdown:
     - Base facility fee
     - Equipment charges
     - Deposit (if required)
     - Discounts applied:
       * PWD: 20% off
       * Senior Citizen: 20% off
       * Student: 10% off
     - Total amount due
  2. Payment options:
     → Online (GCash, PayMaya)
     → Over-the-counter (Treasurer's Office)
  3. Payment deadline: 3 days from approval
          ↓
┌─────────────────────────────────────────────────────┐
│  TREASURER'S OFFICE: Payment Verification           │
└─────────────────────────────────────────────────────┘
  1. Receive payment from citizen
  2. Generate Official Receipt (OR)
  3. Log in system:
     - OR number
     - Payment amount
     - Payment method
     - Date and time
  4. Mark booking as "Paid"
          ↓
┌─────────────────────────────────────────────────────┐
│  SYSTEM: Finalize Booking                           │
└─────────────────────────────────────────────────────┘
  - Update booking status: "Confirmed & Paid"
  - Email citizen with OR and booking details
  - Add to calendar (final confirmation)
  - Reserve equipment (if applicable)
  - Send reminder notifications:
    * 7 days before event
    * 3 days before event
    * 1 day before event
```

**Payment Calculation Logic:**

```php
// Two-Tier Discount System (Interview Finding)
public function calculatePrice($facilityRate, $hours, $discountType) {
    $basePrice = $facilityRate * $hours;
    
    // Tier 1: PWD & Senior Citizen (20% discount)
    if (in_array($discountType, ['pwd', 'senior'])) {
        $discount = $basePrice * 0.20;
        return $basePrice - $discount;
    }
    
    // Tier 2: Student (10% discount)
    if ($discountType === 'student') {
        $discount = $basePrice * 0.10;
        return $basePrice - $discount;
    }
    
    // No discount
    return $basePrice;
}
```

**Database Tables Involved:**
- `bookings` - Payment status
- `payments` - Payment records
- `official_receipts` - OR generation
- `discount_validations` - Discount verification

**Roles Involved:**
- **Citizen** - View payment details, pay fee
- **Treasurer** - Verify payment, issue OR
- **Admin** - Monitor payment status

**Success Criteria:**
- ✅ Accurate discount calculations
- ✅ Payment integration ready
- ✅ OR generation working
- ✅ Payment deadline tracking
- ✅ Auto-cancellation if unpaid

---

#### **WORKFLOW 4: CONFLICT DETECTION & AUTO-NOTIFICATIONS**

**User Story:** *"As an admin, I want the system to automatically detect schedule conflicts so I don't double-book facilities."*

```
┌─────────────────────────────────────────────────────┐
│  SYSTEM: Real-Time Conflict Detection               │
└─────────────────────────────────────────────────────┘
  When citizen selects date/time:
  
  1. Check existing bookings
     - Same facility
     - Overlapping time range
     - Status: confirmed or pending
  
  2. Check maintenance schedules
     - Facility under repair
     - Scheduled cleaning
     - Annual inspection
  
  3. Check equipment availability
     - Requested items in use
     - Quantity available
     - Conflicting reservations
  
  4. Check priority events
     - Government events
     - Emergency reservations
     - VIP bookings
  
  Result:
  ✓ Available → Allow booking
  ⊗ Conflict → Block booking + Show alternative dates
  ⚠ Pending → Show warning + Allow with approval
```

**Conflict Detection Rules:**

```php
public function hasConflict($facilityId, $startTime, $endTime) {
    // Rule 1: No overlapping confirmed bookings
    $overlapping = Booking::where('facility_id', $facilityId)
        ->where('status', 'confirmed')
        ->where(function($q) use ($startTime, $endTime) {
            $q->whereBetween('start_time', [$startTime, $endTime])
              ->orWhereBetween('end_time', [$startTime, $endTime])
              ->orWhere(function($q2) use ($startTime, $endTime) {
                  $q2->where('start_time', '<=', $startTime)
                     ->where('end_time', '>=', $endTime);
              });
        })
        ->exists();
    
    // Rule 2: No maintenance during requested period
    $maintenance = MaintenanceSchedule::where('facility_id', $facilityId)
        ->where('start_date', '<=', $endTime)
        ->where('end_date', '>=', $startTime)
        ->exists();
    
    // Rule 3: Equipment availability
    $equipmentConflict = $this->checkEquipmentAvailability(
        $requestedEquipment, 
        $startTime, 
        $endTime
    );
    
    return $overlapping || $maintenance || $equipmentConflict;
}
```

**Auto-Notification System:**

```
Trigger Events:
├─ Booking submitted → Email staff
├─ Staff verified → Email admin
├─ Admin approved → Email citizen + SMS
├─ Payment received → Email OR to citizen
├─ 7 days before → Reminder email
├─ 3 days before → Reminder SMS
├─ 1 day before → Final reminder
├─ Booking rejected → Email with reason
└─ Payment overdue → Warning email
```

**Database Tables Involved:**
- `bookings` - Current reservations
- `maintenance_schedules` - Facility downtime
- `equipment_reservations` - Equipment tracking
- `notifications` - Notification queue

**Roles Involved:**
- **System** - Auto-detect conflicts
- **Admin** - Resolve complex conflicts
- **Staff** - Flag potential issues

**Success Criteria:**
- ✅ Zero double-bookings possible
- ✅ Real-time conflict checking
- ✅ Auto-notifications working
- ✅ Alternative date suggestions

---

#### **WORKFLOW 5: REPORTS & ANALYTICS**

**User Story:** *"As an admin, I want to see usage patterns and revenue reports so I can optimize facility operations."*

```
┌─────────────────────────────────────────────────────┐
│  ADMIN: Reports Dashboard                           │
└─────────────────────────────────────────────────────┘
  1. Usage Reports
     - Bookings per facility
     - Peak hours/days
     - Cancellation rate
     - Average occupancy
     - Event types distribution
  
  2. Revenue Reports
     - Total revenue per facility
     - Payment method breakdown
     - Discount impact analysis
     - Outstanding payments
     - Revenue trends (monthly/yearly)
  
  3. Operational Reports
     - Staff performance metrics
     - Average approval time
     - Rejection reasons
     - Maintenance frequency
     - Equipment utilization
  
  4. Export Options
     ✓ Export to CSV
     ✓ Export to PDF
     ✓ Schedule recurring reports
     ✓ Email to stakeholders
```

**AI Analytics Module:**

```
┌─────────────────────────────────────────────────────┐
│  AI: Pattern Recognition (NOT Prediction!)          │
└─────────────────────────────────────────────────────┘
  Based on historical booking data:
  
  1. Usage Pattern Recognition
     - Identify peak seasons
     - Popular event types
     - Frequently booked time slots
     - Citizen booking behavior
  
  2. Resource Optimization Insights
     - Underutilized facilities
     - Over-requested equipment
     - Bottlenecks in approval chain
     - Staff workload distribution
  
  3. Capacity Planning Helper
     - Suggest facility improvements
     - Equipment purchase recommendations
     - Staffing adjustments
     - Pricing optimization hints
  
  Technology: TensorFlow.js (client-side)
  Model: LSTM for time-series pattern analysis
  Training Data: Historical bookings (last 2 years)
```

**Database Tables Involved:**
- `bookings` - Historical data
- `payments` - Revenue tracking
- `booking_approvals` - Process metrics
- `audit_logs` - Activity analysis

**Roles Involved:**
- **Admin** - View reports, export data
- **Super Admin** - System-wide analytics
- **Staff** - Personal performance metrics

**Success Criteria:**
- ✅ Accurate revenue calculations
- ✅ Real-time analytics dashboard
- ✅ CSV/PDF export working
- ✅ AI insights actionable
- ✅ Historical data analysis

---

## 🛠️ TECHNICAL STACK

### **Backend**
- **Framework:** Laravel 11 (PHP 8.2+)
- **Database:** MySQL 8.0+ (ONLY MySQL, no other DB)
- **Authentication:** Custom session-based (Future: JWT for SSO)
- **Caching:** Redis (sessions + data cache)
- **Queue:** Laravel Queue (notifications)

### **Frontend**
- **Templating:** Blade (no React/Vue)
- **CSS Framework:** Tailwind CSS v4
- **JavaScript:** Alpine.js (lightweight reactivity)
- **Icons:** Lucide Icons (NO emojis)
- **Alerts:** SweetAlert2 (ALL alerts must be modal)
- **Charts/Graphs:** ApexCharts (open-source charting library)
- **Typography:** Poppins font (all weights)

### **AI/Analytics**
- **Library:** TensorFlow.js
- **Execution:** Client-side (browser)
- **Model:** LSTM for pattern recognition
- **Data:** Historical bookings (JSON feed)

### **File Storage**
- **Local:** Laravel Storage (documents, photos)
- **Cloud:** AWS S3 (optional, for production)

### **Third-Party Services**
- **Email:** SMTP (Laravel Mail)
- **SMS:** (Integration-ready for future)
- **Payment:** GCash/PayMaya (mock for now)

### **Development Tools**
- **Version Control:** Git
- **Build Tool:** Vite
- **Testing:** PHPUnit + Browser Tests
- **API Documentation:** Laravel Scribe

---

## 🗄️ DATABASE ARCHITECTURE

See **[DATABASE_SCHEMA.md](DATABASE_SCHEMA.md)** for complete table structures.

### **Key Design Principles**
1. **Soft Deletes** - ALL tables use `deleted_at` (no permanent deletion)
2. **Audit Logs** - Every CRUD operation logged
3. **UUID Primary Keys** - For security and distribution
4. **JSON Columns** - For flexible configuration
5. **Indexes** - On all foreign keys and search fields
6. **Timestamps** - `created_at`, `updated_at` on all tables

### **Core Tables**
```
users
├─ locations (multi-city support)
├─ facilities
│  ├─ facility_schedules
│  ├─ facility_photos
│  ├─ maintenance_schedules
│  └─ equipment
├─ bookings
│  ├─ booking_documents
│  ├─ booking_approvals
│  ├─ booking_notes
│  ├─ booking_equipment
│  └─ payments
├─ official_receipts
├─ notifications
└─ audit_logs
```

---

## 🔌 API INTEGRATION STRATEGY

### **Current State: Mock APIs**
For demo and development, all external integrations use mocks:

```php
// Interface-driven architecture
interface InfrastructureAPIInterface {
    public function createProject(array $data): array;
    public function getProjectStatus(string $id): array;
}

// Mock implementation (use now)
class MockInfrastructureAPI implements InfrastructureAPIInterface {
    public function createProject(array $data): array {
        return [
            'project_id' => 'MOCK-' . uniqid(),
            'status' => 'approved',
            'estimated_completion' => now()->addMonths(6)
        ];
    }
}

// Real implementation (swap later)
class RealInfrastructureAPI implements InfrastructureAPIInterface {
    public function createProject(array $data): array {
        $response = Http::post(
            config('external.infrastructure.url'),
            $data
        );
        return $response->json();
    }
}
```

### **External Systems (8 Total)**
1. Infrastructure Team - New facility construction
2. Urban Planning - Land selection
3. Utility Billing - Water/electricity connection
4. Energy Efficiency - Government event bookings
5. Housing & Resettlement - Beneficiary verification
6. Road & Transportation - Traffic impact assessment
7. Community Maintenance - Repair requests
8. Treasurer's Office - Payment verification

### **Future State: Real APIs**
- JWT token authentication
- RESTful endpoints
- Webhook notifications
- Rate limiting
- Error handling and retries

---

## 🔒 SECURITY ARCHITECTURE

### **Authentication Security**
- ✅ Bcrypt password hashing
- ✅ OTP verification (1-minute expiry)
- ✅ CSRF token protection
- ✅ Session timeout (2 minutes)
- ✅ Brute-force protection (rate limiting)
- ✅ Email verification required

### **Authorization Security**
- ✅ Role-based access control (RBAC)
- ✅ Laravel Gates and Policies
- ✅ Middleware protection on all routes
- ✅ Permission checks in views

### **Data Security**
- ✅ SQL injection prevention (Eloquent ORM)
- ✅ XSS protection (Blade escaping)
- ✅ File upload validation
- ✅ Encrypted storage for sensitive data
- ✅ Audit logs for all actions

### **API Security (Future)**
- ✅ JWT token authentication
- ✅ API rate limiting
- ✅ IP whitelisting
- ✅ Request signing
- ✅ HTTPS only

---

## 🤖 AI ANALYTICS MODULE

### **Purpose**
Pattern recognition and insights (NOT prediction!) based on historical booking data.

### **Technology**
- **Library:** TensorFlow.js
- **Execution:** Client-side (browser)
- **Model:** LSTM (Long Short-Term Memory)
- **Training Data:** Historical bookings (JSON)

### **Features**

#### **1. Usage Pattern Recognition**
```javascript
// Identify patterns in historical data
patterns = {
  peak_seasons: ['December', 'May'], // Wedding season
  popular_times: ['Saturday PM', 'Sunday AM'],
  event_types: {
    'wedding': 45%,
    'birthday': 30%,
    'corporate': 15%,
    'other': 10%
  }
}
```

#### **2. Resource Optimization**
```javascript
insights = {
  underutilized: ['Facility C - 30% occupancy'],
  over_requested: ['Sound System - 90% utilization'],
  bottlenecks: ['Staff verification takes avg 3 days'],
  suggestions: [
    'Consider adding more chairs to Facility A',
    'Hire additional staff for peak season',
    'Promote Facility C with discount'
  ]
}
```

#### **3. Capacity Planning Helper**
```javascript
// Based on historical growth
capacity_insights = {
  current_trend: 'Bookings increased 25% this year',
  projected_capacity: 'Current facilities at 75% capacity',
  recommendations: [
    'Expand Facility A by 50 seats',
    'Purchase 2 additional projectors',
    'Add 3rd approval staff for December'
  ]
}
```

### **Implementation**
```html
<!-- Admin Dashboard -->
<div id="ai-analytics-dashboard">
  <div class="usage-patterns">
    <h3>Usage Patterns Detected</h3>
    <canvas id="pattern-chart"></canvas>
  </div>
  
  <div class="optimization-insights">
    <h3>Resource Optimization</h3>
    <ul id="insights-list"></ul>
  </div>
  
  <div class="capacity-planning">
    <h3>Capacity Planning Helper</h3>
    <div id="capacity-recommendations"></div>
  </div>
</div>

<script>
// Load historical data
fetch('/api/bookings/historical')
  .then(r => r.json())
  .then(data => {
    // Initialize TensorFlow model
    const model = tf.sequential({
      layers: [
        tf.layers.lstm({ units: 64, inputShape: [30, 5] }),
        tf.layers.dense({ units: 32, activation: 'relu' }),
        tf.layers.dense({ units: 10, activation: 'softmax' })
      ]
    });
    
    // Analyze patterns (not predict!)
    const patterns = analyzeHistoricalPatterns(data);
    displayInsights(patterns);
  });
</script>
```

### **Critical: No "Predictions"**
- ❌ DON'T: "Forecasting future bookings"
- ❌ DON'T: "Predicting demand"
- ✅ DO: "Pattern recognition"
- ✅ DO: "Historical analysis"
- ✅ DO: "Optimization insights"

---

## 🚀 DEPLOYMENT ARCHITECTURE

### **Development Environment**
```
Local Machine
├─ Laragon (Windows) / Valet (Mac)
├─ PHP 8.2+
├─ MySQL 8.0
├─ Node.js 18+ (for Vite)
└─ Redis (optional for caching)
```

### **Staging Environment**
```
Staging Server
├─ Linux (Ubuntu 22.04)
├─ Nginx
├─ PHP-FPM 8.2
├─ MySQL 8.0
├─ Redis
├─ SSL Certificate
└─ Backup System
```

### **Production Environment (Future)**
```
Production Server
├─ Load Balancer (Nginx)
├─ Application Servers (2+)
│  ├─ PHP-FPM 8.2
│  ├─ Queue Workers
│  └─ Redis Cache
├─ Database Cluster
│  ├─ MySQL Primary
│  └─ MySQL Replica (read)
├─ File Storage (S3)
├─ CDN (CloudFlare)
└─ Monitoring (New Relic)
```

### **Backup Strategy**
- **Database:** Daily automated backups (retain 30 days)
- **Files:** Weekly backups to S3
- **Logs:** Real-time log aggregation
- **Recovery Time Objective (RTO):** 4 hours
- **Recovery Point Objective (RPO):** 24 hours

---

## 📊 SYSTEM METRICS

### **Performance Targets**
- **Page Load Time:** < 2 seconds
- **API Response Time:** < 500ms
- **Concurrent Users:** 1,000+ supported
- **Database Queries:** < 50ms average
- **Uptime:** 99.9% availability

### **Monitoring**
- **Application:** Laravel Telescope (dev)
- **Server:** New Relic (production)
- **Errors:** Sentry (real-time alerts)
- **Logs:** ELK Stack (Elasticsearch, Logstash, Kibana)

---

## 📞 QUESTIONS & SUPPORT

For technical questions or clarifications, refer to:
- **[PROJECT_DESIGN_RULES.md](PROJECT_DESIGN_RULES.md)** - Design standards
- **[DATABASE_SCHEMA.md](DATABASE_SCHEMA.md)** - Database structure
- **[FEATURES_CHECKLIST.md](FEATURES_CHECKLIST.md)** - Development plan

---

**Last Updated:** December 10, 2025  
**Version:** 1.0  
**Status:** 🔒 LOCKED FOR DEVELOPMENT

---

*This architecture ensures scalability, security, and maintainability for the final defense and beyond.*

