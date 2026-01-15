# 🚀 Development Roadmap - Public Facilities Reservation System

## Phase 1: CITIZEN Role (Priority 1) - Weeks 1-2

### Week 1: Core Booking Flow
**Goal**: Citizen can browse facilities, create bookings, and submit requirements

#### Day 1-2: Facility Directory & Calendar
- [ ] **Controller**: `FacilityController` - Browse facilities, filter by city, view calendar
- [ ] **Views**: 
  - `resources/views/citizen/facilities/index.blade.php` - Facility grid with search/filter
  - `resources/views/citizen/facilities/show.blade.php` - Facility details + calendar
- [ ] **Features**:
  - Display all Caloocan facilities (mark QC as "Coming Soon")
  - Calendar view showing available/booked dates
  - Show 2-hour maintenance gaps
  - Filter by capacity, amenities
  - Search functionality

#### Day 3-4: Online Booking Form
- [ ] **Controller**: `BookingController@create` - Booking form with pricing preview
- [ ] **Views**:
  - `resources/views/citizen/bookings/create.blade.php` - Multi-step booking form
  - Include equipment selection (optional)
  - ID type selection (Regular, Senior, PWD, Student)
  - Real-time price calculation using `PricingCalculatorService`
- [ ] **Features**:
  - Live pricing preview (shows subtotal, discounts, total)
  - Equipment browsing and selection
  - Document upload (Valid ID front/back, selfie, authorization letter, event proposal)
  - Digital signature capture
  - Schedule conflict validation

#### Day 5: Booking Submission & Status Tracking
- [ ] **Controller**: `BookingController@store` - Save booking, send to staff queue
- [ ] **Views**:
  - `resources/views/citizen/bookings/index.blade.php` - My bookings list
  - `resources/views/citizen/bookings/show.blade.php` - Booking details with status timeline
- [ ] **Features**:
  - Status badges (Reserved 24h, Tentative, Pending Verification, Approved, etc.)
  - Countdown timer for 24-hour hold
  - Upload additional documents if rejected
  - Cancel booking (if not yet paid)

### Week 2: Payment & Feedback

#### Day 6-7: Usage Fee Calculation & Payment
- [ ] **Controller**: `PaymentController` - View payment slip, simulate payment
- [ ] **Views**:
  - `resources/views/citizen/payments/show.blade.php` - Payment slip with QR code
  - Breakdown: Facility fee + Equipment + Discounts = Total
- [ ] **Features**:
  - Download/print payment slip
  - Payment method selection (Cash, GCash, PayMaya, Bank)
  - Mock payment for testing (mark as paid)
  - Payment confirmation via webhook (stub for now)

#### Day 8: Usage Reports & Feedback
- [ ] **Controller**: `FeedbackController` - Submit feedback after event
- [ ] **Views**:
  - `resources/views/citizen/feedback/create.blade.php` - Feedback form
  - `resources/views/citizen/facilities/reviews.blade.php` - Public reviews on facility page
- [ ] **Features**:
  - Star rating (1-5)
  - Comment box
  - Photo upload (optional)
  - View feedback from other users (moderated)

#### Day 9-10: Citizen Dashboard & Polish
- [ ] **Views**:
  - `resources/views/citizen/dashboard.blade.php` - Overview of bookings, upcoming events
- [ ] **Features**:
  - Quick stats: Upcoming bookings, pending payments, total savings from discounts
  - Announcements from admin
  - SweetAlert2 for all notifications
  - Mobile responsive design

---

## Phase 2: STAFF Role (Priority 2) - Week 3

### Staff Verification Workflow
**Goal**: Staff can verify citizen documents and approve/reject bookings

#### Day 11-12: Staff Dashboard & Queue
- [ ] **Controller**: `Staff\BookingVerificationController`
- [ ] **Views**:
  - `resources/views/staff/dashboard.blade.php` - Pending verifications count, recent activity
  - `resources/views/staff/verifications/index.blade.php` - Queue of pending bookings
- [ ] **Features**:
  - Filter by status, date, facility
  - Priority queue (24-hour hold expiring soon shown first)
  - Quick view of applicant details

#### Day 13-14: Document Verification
- [ ] **Controller**: `Staff\BookingVerificationController@show`
- [ ] **Views**:
  - `resources/views/staff/verifications/review.blade.php` - Document viewer with side-by-side comparison
- [ ] **Features**:
  - View all uploaded documents (ID front/back, selfie, authorization, proposal)
  - Zoom, rotate images
  - ID verification checklist (photo matches, ID not expired, etc.)
  - Discount eligibility validation (check if Senior ID matches DOB, PWD ID is valid, Student ID current)
  - Approve ✅ or Reject ❌ with reason categories
  - Add staff notes (visible to admin)
  - Send booking to admin approval queue

#### Day 15: Staff Schedule Management
- [ ] **Controller**: `Staff\ScheduleController`
- [ ] **Views**:
  - `resources/views/staff/schedules/calendar.blade.php` - Full calendar view
- [ ] **Features**:
  - View all bookings across all facilities
  - Schedule conflict alerts (real-time)
  - Mark maintenance windows (2-hour gaps)
  - Export schedule to PDF/Excel

---

## Phase 3: ADMIN Role (Priority 3) - Week 4

### Admin Approval & System Management
**Goal**: Admin has final approval, manages facilities, handles city events

#### Day 16-17: Admin Dashboard with Analytics
- [ ] **Controller**: `Admin\DashboardController`
- [ ] **Views**:
  - `resources/views/admin/dashboard.blade.php` - Comprehensive analytics
- [ ] **Features**:
  - Charts: Bookings per month, revenue, facility usage %
  - TensorFlow.js: Usage Pattern Analytics (trends, peak days, popular facilities)
  - Pending approvals count
  - Staff performance metrics

#### Day 18-19: Final Booking Approval
- [ ] **Controller**: `Admin\BookingApprovalController`
- [ ] **Views**:
  - `resources/views/admin/approvals/index.blade.php` - Staff-verified bookings queue
  - `resources/views/admin/approvals/review.blade.php` - Final review with staff notes
- [ ] **Features**:
  - See staff verification notes and decision
  - Override capability (approve/reject even if staff said otherwise)
  - Priority city events: Block dates for government use
  - Send booking to payment pending status
  - Generate payment slip automatically

#### Day 20: Facility Management
- [ ] **Controller**: `Admin\FacilityController`
- [ ] **Views**:
  - `resources/views/admin/facilities/index.blade.php` - All facilities list
  - `resources/views/admin/facilities/edit.blade.php` - Edit facility details, pricing
- [ ] **Features**:
  - Update facility info, capacity, amenities, photos
  - Change base rate (₱5,000/3hrs, ₱2,000/2hr extension)
  - Mark facility as unavailable (maintenance, renovation)
  - Assign facility to LGU city

#### Day 21: Reports & External Integration Setup
- [ ] **Controller**: `Admin\ReportController`, `Admin\IntegrationController`
- [ ] **Views**:
  - `resources/views/admin/reports/index.blade.php` - Generate various reports
  - `resources/views/admin/integrations/index.blade.php` - External system configs
- [ ] **Features**:
  - Usage reports: By facility, date range, citizen demographics
  - Revenue reports: Total fees, discount breakdowns
  - Export to PDF/Excel
  - Send data to external systems (stubs for now):
    - Maintenance Schedule (outgoing requests)
    - Energy Consumption (usage reports)
    - Project Planning (receive project data)
    - Road Maintenance (event schedules)
    - Treasurer's Office (payment data)

---

## Phase 4: SUPER ADMIN Role (Priority 4) - Week 5

### System-wide Management
**Goal**: Super Admin manages all users, roles, and system configuration

#### Day 22-23: User Management
- [ ] **Controller**: `SuperAdmin\UserController`
- [ ] **Views**:
  - `resources/views/superadmin/users/index.blade.php` - All users across all roles
  - `resources/views/superadmin/users/edit.blade.php` - Edit user, assign roles
- [ ] **Features**:
  - Search/filter by role, status, registration date
  - Assign multiple roles (Citizen, Staff, Admin)
  - Suspend/activate accounts
  - Reset passwords
  - View user activity logs

#### Day 24: Role & Permission Management
- [ ] **Controller**: `SuperAdmin\RoleController`
- [ ] **Views**:
  - `resources/views/superadmin/roles/index.blade.php` - Role list with permissions
  - `resources/views/superadmin/roles/edit.blade.php` - Edit role permissions
- [ ] **Features**:
  - Create custom roles
  - Assign granular permissions
  - View role hierarchy

#### Day 25: Equipment Management
- [ ] **Controller**: `SuperAdmin\EquipmentController`
- [ ] **Views**:
  - `resources/views/superadmin/equipment/index.blade.php` - All equipment items
  - `resources/views/superadmin/equipment/create.blade.php` - Add new equipment
- [ ] **Features**:
  - CRUD for equipment (chairs, tables, sound systems, etc.)
  - Update pricing, stock quantities
  - Mark as available/unavailable
  - Upload equipment photos
  - View equipment rental history

#### Day 26: Multi-City LGU Setup
- [ ] **Controller**: `SuperAdmin\LguCityController`
- [ ] **Views**:
  - `resources/views/superadmin/cities/index.blade.php` - All LGU cities
  - `resources/views/superadmin/cities/create.blade.php` - Add new city
- [ ] **Features**:
  - Add new LGU cities (Quezon City, Manila, etc.)
  - Mark as Active, Coming Soon, or Inactive
  - Configure external integration settings per city
  - Assign facilities to cities

#### Day 27: System Configuration & Logs
- [ ] **Controller**: `SuperAdmin\ConfigController`, `SuperAdmin\AuditLogController`
- [ ] **Views**:
  - `resources/views/superadmin/config/index.blade.php` - System settings
  - `resources/views/superadmin/logs/index.blade.php` - Audit trail
- [ ] **Features**:
  - Update discount percentages (currently 30% city, 20% identity)
  - Configure booking rules (max days in advance, cancellation policy)
  - Set payment due dates
  - View all system logs (login, booking changes, approvals)

---

## Phase 5: AI Integration & Polish - Week 6

### TensorFlow.js Implementation
**Goal**: Display usage pattern analytics on dashboards

#### Day 28-29: Usage Pattern Analytics
- [ ] Train model on booking history data
- [ ] Display insights:
  - Peak booking days (e.g., "Saturdays are 80% booked")
  - Facility popularity trends
  - Seasonal patterns
  - Equipment rental frequency
- [ ] Visualize with Chart.js

#### Day 30: Testing & Refinement
- [ ] End-to-end testing (Citizen → Staff → Admin flow)
- [ ] Mobile responsiveness check
- [ ] Cross-browser testing
- [ ] Performance optimization
- [ ] Security audit
- [ ] Panel presentation preparation

---

## 🎨 Design Standards (All Phases)

### Required for Every Page:
✅ **Tailwind CSS** only (no other CSS frameworks)
✅ **Lucide Icons** only
✅ **SweetAlert2** for all alerts/confirmations
✅ **Consistent typography** across role views
✅ **Search functionality** where applicable
✅ **Mobile-first responsive design**

### Prohibited:
❌ No emojis in production code
❌ No design redundancy (use reusable components)
❌ No very long comments (keep code clean)
❌ No native browser alerts (use SweetAlert2)

---

## 📋 Development Checklist

### Before Starting:
- [x] Database migrations complete
- [x] Models created and configured
- [x] Equipment data seeded
- [ ] Authentication system working (from lgu1_auth)
- [ ] Middleware for role-based access

### For Each Feature:
1. [ ] Create controller method
2. [ ] Create blade view with Tailwind
3. [ ] Add route in `routes/web.php`
4. [ ] Test functionality
5. [ ] Test on mobile
6. [ ] Add to sidebar navigation

---

## 🚀 Let's Start with Citizen Dashboard!

Ready to begin Phase 1? I'll create:
1. Citizen routes structure
2. Base layout with sidebar
3. Dashboard controller
4. First views with proper design system

Say the word and we'll start coding! 💪

