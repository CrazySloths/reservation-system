# PUBLIC FACILITIES RESERVATION SYSTEM (PFRS)
## Functional and Non-Functional Requirements Documentation

---

## FUNCTIONAL REQUIREMENTS FOR PFRS

### 1. User Management
The system should allow Citizens, Staff, Admin, and Super Admin users to securely log in with multi-factor authentication, manage their profiles with government ID verification, and enable Admins to create, update, deactivate, or lock user accounts with role-based access control.

### 2. Facility Management
The system should allow Admins to add, update, view, and archive facility records including information like facility name, location, capacity, amenities, pricing (with dynamic pricing for peak/off-peak periods), high-resolution photos, and availability status, while automatically calculating golden ratio-based layout recommendations for optimal space utilization.

### 3. Booking and Reservation Management
The system should allow Citizens to browse available facilities with real-time calendar views, submit booking requests with purpose justification and required documents, select time slots with conflict detection, and allow Staff/Admins to approve, reject, or modify bookings with automated email/SMS notifications sent to users about their booking status.

### 4. Government Program Integration (Cross-System Integration)
The system should accept seminar and event requests from external government systems (e.g., Energy Efficiency & Conservation system), log each request with status updates and tracking IDs, allow Admins to assign facilities and equipment, coordinate with organizers via call logs, approve budgets with itemized breakdowns, and send facility confirmation data back to the requesting system including speakers, equipment provided, and fund transparency reports.

### 5. Equipment Inventory Management
The system should allow Admins to manage equipment inventory including projectors, microphones, laptops, sound systems, and furniture with real-time availability tracking, automatically limit equipment allocation to available stock during booking assignments, track equipment status (available, in-use, maintenance), and generate equipment usage reports by facility and time period.

### 6. Payment and Financial Management
The system should calculate facility fees based on duration and amenities, apply fee waivers for government programs and senior citizens/PWDs, process online payments via GCash/PayMaya/Credit Card with automatic receipt generation, track payment status (pending, paid, refunded), and allow Admins to process refunds for cancelled bookings with documented reasons.

### 7. Calendar and Scheduling
The system should display an interactive calendar showing facility availability with color-coded status indicators, detect and prevent double-booking conflicts automatically, show maintenance schedules and blocked dates, allow bulk booking for recurring events, and send automated reminders 3 days, 1 day, and 2 hours before scheduled events.

### 8. Transparency and Fund Management (Government Programs)
The system should publish pre-event budget breakdowns showing planned expenses by category (food, materials, transportation, etc.) with supplier information, track actual spending with receipt uploads and official receipt numbers, calculate and display budget variances, and publish post-event liquidation reports with photos and itemized expenses accessible to citizens for public accountability.

### 9. Maintenance Scheduling
The system should allow Staff to schedule routine maintenance with automatic facility blocking during maintenance periods, log maintenance history with before/after photos, track maintenance costs and vendors, and generate preventive maintenance schedules based on facility usage patterns and AI-powered predictions.

### 10. City and District Management
The system should organize facilities by region, city, district, and barangay with hierarchical navigation, allow filtering by location proximity using geolocation, display facilities on an interactive map with driving directions, and show facility distribution analytics by area for capacity planning.

### 11. Document Management
The system should allow users to upload required documents (government IDs, permits, event proposals) in PDF/JPG format with automatic file size optimization, implement document verification with AI-powered authenticity checks, maintain secure document storage with encryption at rest and in transit, and allow Admins to approve or reject documents with timestamped audit trails.

### 12. Notifications and Alerts
The system should notify users in real-time via in-app notifications, email, and SMS about booking status changes, upcoming events, payment confirmations, new facility availability, maintenance schedules, and urgent announcements, with a notification center showing read/unread status and notification history for 90 days.

### 13. Reports Generation
The system should allow users to generate and export reports related to booking trends by facility and time period, revenue analysis by facility type, facility utilization rates with peak hours identification, user demographics and booking patterns, government program participation with fund transparency summaries, equipment usage statistics, and system activity logs, with role-based access to report types and data export in PDF/Excel/CSV formats.

### 14. Feedback and Rating System
The system should allow Citizens to rate facilities and services after event completion using a 5-star system with written reviews, display average ratings and testimonials on facility pages, allow Admins to respond to feedback publicly, flag inappropriate reviews for moderation, and generate satisfaction reports to improve service quality.

### 15. Audit Logs
The system should record all critical actions performed by users including user logins and logouts with IP addresses and device information, booking creation/modification/cancellation, payment transactions with amounts and methods, facility updates and changes, equipment assignments, document uploads and approvals, and system configuration changes, with timestamps and user identification, allowing Admins to view, search, and filter audit logs by user, action type, and date range for compliance and security monitoring.

---

## NON-FUNCTIONAL REQUIREMENTS FOR PFRS

### 1. Performance
The system should respond to user actions such as facility searches, booking submissions, calendar loads, and report generation within 2 seconds under normal operating conditions with up to 10,000 concurrent users.

### 2. Security
The system should encrypt all sensitive data (personal information, payment details, government IDs) using AES-256 encryption in transit (TLS 1.3) and at rest, enforce role-based access control with minimum privilege principle, implement session timeout after 15 minutes of inactivity with automatic logout, protect against SQL injection and XSS attacks through input sanitization, and maintain PCI-DSS compliance for payment processing.

### 3. Scalability
The system should support future growth in terms of data volume (millions of bookings), user accounts (100,000+ citizens), facilities (1,000+ venues), and additional features (new payment methods, integrations with other LGU systems) without requiring major architectural changes, using cloud-based auto-scaling infrastructure.

### 4. Availability and Reliability
The system should maintain a minimum of 99.9% uptime during operational hours (24/7/365), perform automated hourly backups with 30-day retention to prevent data loss, implement disaster recovery with 4-hour Recovery Time Objective (RTO) and 1-hour Recovery Point Objective (RPO), and provide graceful degradation during partial system failures.

### 5. Usability
The user interface should be intuitive and accessible across desktops, tablets, and mobile devices using responsive design, designed according to WCAG 2.1 Level AA accessibility standards, support Filipino and English languages with easy language switching, follow golden ratio design principles for visual hierarchy, and enable key functions (facility search, booking submission, calendar view) to be completed within five clicks.

### 6. Maintainability
The system should follow a modular architecture with clear separation of concerns (MVC pattern), use standard coding practices (PSR-12 for PHP, Laravel best practices), implement comprehensive inline documentation and API documentation, include automated unit tests with 80%+ code coverage, and provide detailed deployment guides for updates without downtime.

### 7. Auditability
The system should maintain tamper-proof logs of user actions with cryptographic hashing, store timestamps in ISO 8601 format with timezone information, retain audit data for minimum 5 years for compliance purposes, provide searchable audit trails accessible only to Admin users, and generate audit reports for internal reviews and external audits.

### 8. Interoperability
The system should provide RESTful APIs with JSON responses for integration with external systems (Energy Efficiency, Barangay Health Units, etc.), support OAuth 2.0 for secure API authentication, follow OpenAPI 3.0 specification for API documentation, accept webhook notifications from partner systems, and implement data exchange using standard formats (JSON, XML, CSV).

### 9. Data Integrity
The system should validate all user inputs on both client-side (JavaScript) and server-side (Laravel validation), enforce database constraints (foreign keys, unique constraints, check constraints), implement transaction management with rollback capabilities for multi-step operations, maintain referential integrity across related tables, and perform regular data consistency checks.

### 10. Compliance
The system should comply with Republic Act 10173 (Data Privacy Act of 2012) for handling personal information, Republic Act 11032 (Ease of Doing Business Act) for efficient government services, COA (Commission on Audit) guidelines for financial transparency and fund management, and DICT cybersecurity guidelines for government systems.

---

## BUSINESS PROCESS ARCHITECTURE

### Citizen Booking Process Flow

1. **Registration & Login**
   - Citizen registers with email, mobile number, and government ID
   - AI-powered ID verification checks authenticity
   - Email and SMS verification required
   - Login with username/password + optional 2FA

2. **Facility Search & Selection**
   - Browse facilities by location, capacity, amenities, price
   - View facility photos, 360° virtual tours, and ratings
   - Check real-time availability on interactive calendar
   - Compare multiple facilities side-by-side

3. **Booking Request Submission**
   - Select date, time, and duration
   - Specify purpose and expected attendees
   - Upload required documents (event proposal, permits)
   - System calculates fee with transparent breakdown
   - Submit booking request for admin review

4. **Admin Review & Approval**
   - Admin receives notification of new booking
   - Reviews citizen profile, purpose, and documents
   - Checks for schedule conflicts and policy compliance
   - Approves, rejects, or requests modifications
   - Citizen receives email/SMS notification of decision

5. **Payment Processing**
   - If approved, citizen receives payment instructions
   - Selects payment method (GCash, Maya, bank transfer, over-counter)
   - Processes payment with automatic receipt generation
   - Payment confirmation updates booking status to "Confirmed"

6. **Event Day**
   - Citizen receives reminder notifications (3 days, 1 day, 2 hours before)
   - Staff prepares facility and equipment as specified
   - Citizen checks in at facility with QR code
   - Event proceeds as scheduled

7. **Post-Event**
   - Citizen rates and reviews facility within 7 days
   - Staff conducts post-event inspection
   - System generates usage report and updates facility availability

---

### Government Program Integration Process Flow

1. **Inbound Request Reception**
   - Energy Efficiency system sends seminar request to PFRS API
   - PFRS logs request with auto-generated tracking ID (GPR-2026-XXXXX)
   - Request appears in Admin "Government Programs" dashboard
   - Admin receives email notification of new program request

2. **Admin Review & Coordination**
   - Admin views seminar details (title, description, date, attendees)
   - Admin contacts organizer via phone/email (logs call history)
   - Coordinates speaker requirements and equipment needs
   - Discusses budget requirements and itemized breakdown

3. **Facility & Equipment Assignment**
   - Admin selects appropriate facility from available options
   - Assigns equipment from inventory (projectors, microphones, laptops)
   - System validates equipment availability and updates stock status
   - Defines speaker lineup with names and topics
   - Enters pre-event budget breakdown by category

4. **Budget Approval**
   - Admin submits fund request with detailed itemization
   - Finance office reviews and approves budget
   - System records approved amount and finance details
   - Pre-event budget published for transparency

5. **Confirmation Data Transmission**
   - PFRS sends confirmation to Energy Efficiency system database
   - Includes assigned facility details (name, address, capacity)
   - Includes confirmed speakers and equipment provided
   - Includes approved budget and transparency report URL
   - Both systems sync status updates

6. **Event Execution**
   - LGU provides facility, equipment, and logistics support
   - Energy Efficiency organizes participants and seminar content
   - Actual attendance and activities documented

7. **Post-Event Liquidation**
   - Admin uploads receipts and actual expenses
   - System calculates budget variance (planned vs actual)
   - Post-event liquidation report published online
   - Citizens can view full transparency of fund usage

---

### Equipment Allocation Process

1. **Equipment Inventory Management**
   - Admin maintains real-time equipment inventory database
   - Tracks total quantity, available, in-use, and under maintenance
   - Updates equipment status after each booking/return

2. **Equipment Allocation During Booking**
   - Admin selects equipment from categorized dropdown list
   - System displays available quantity for each item
   - System validates quantity doesn't exceed available stock
   - Equipment marked as "in-use" for scheduled dates

3. **Equipment Preparation**
   - Staff receives equipment list for upcoming event
   - Staff prepares and tests equipment before event
   - Checks equipment condition and functionality

4. **Equipment Return & Inspection**
   - After event, staff inspects returned equipment
   - Updates equipment status (available, needs maintenance, damaged)
   - System updates inventory availability automatically

---

## CROSS-SYSTEM INTEGRATION ARCHITECTURE

### Integration with Energy Efficiency & Conservation System

**Phase 1: Local Testing (Current)**
- Direct database connection to `ener_nova_capri` database
- Read seminar requests from `seminars` and `seminar_joins` tables
- Write confirmations to `facility_booking_confirmations` table
- Test data synchronization and validation

**Phase 2: Production API Integration (Future)**
- RESTful API with JSON payloads
- Secure API authentication using OAuth 2.0 tokens
- Webhook notifications for real-time status updates
- Rate limiting and request throttling for stability

**Data Exchange Format:**

**Inbound (EE → PFRS):**
```json
{
  "seminar_id": 32,
  "seminar_title": "LED Lighting: Save Money, Save Energy",
  "description": "Learn about LED efficiency...",
  "seminar_date": "2026-02-20",
  "start_time": "14:00:00",
  "end_time": "16:30:00",
  "location": "Barangay Hall (Facility needed)",
  "expected_attendees": 150,
  "organizer": {
    "user_id": 1,
    "name": "Christian Cando",
    "email": "admin@energyeff.gov.ph",
    "phone": "9085919898",
    "area": "AREA 4"
  }
}
```

**Outbound (PFRS → EE):**
```json
{
  "tracking_id": "GPR-2026-000003",
  "seminar_id": 32,
  "status": "confirmed",
  "assigned_facility": {
    "facility_id": 45,
    "facility_name": "M.I.C.E. Breakout Room 2",
    "address": "Quezon City M.I.C.E. Center, Floor 2",
    "capacity": 40
  },
  "confirmed_date": "2026-02-20",
  "confirmed_start_time": "14:00:00",
  "confirmed_end_time": "16:30:00",
  "speakers": [
    {
      "name": "Engr. Maria Santos",
      "topic": "LED Technology Basics"
    }
  ],
  "equipment_provided": [
    {
      "name": "Projector",
      "quantity": 1
    },
    {
      "name": "Wireless Microphone",
      "quantity": 2
    }
  ],
  "approved_budget": {
    "total_amount": 5000.00,
    "breakdown": [
      {
        "item": "Refreshments",
        "amount": 3000.00
      },
      {
        "item": "Materials",
        "amount": 2000.00
      }
    ]
  },
  "admin_contact": {
    "name": "Llaneta Cristian Pastoril",
    "email": "admin@caloocan.gov.ph"
  },
  "transparency_report_url": "https://facilities.caloocan.gov.ph/transparency/GPR-2026-000003"
}
```

---

## DATA PRIVACY AND SECURITY MEASURES

### Personal Data Protection (RA 10173 Compliance)

1. **Data Minimization**
   - Collect only necessary personal information
   - Allow users to provide optional data voluntarily
   - Delete old booking records after retention period

2. **Consent Management**
   - Explicit user consent during registration
   - Clear privacy policy and terms of service
   - Allow users to withdraw consent and delete account

3. **Access Controls**
   - Role-based access to personal data
   - Logs of who accessed what data and when
   - Two-factor authentication for admin accounts

4. **Data Encryption**
   - HTTPS/TLS 1.3 for all data transmission
   - AES-256 encryption for database storage
   - Encrypted backups with secure key management

5. **Data Breach Response**
   - Incident response plan with notification procedures
   - Regular security audits and penetration testing
   - Vulnerability scanning and patch management

---

## SYSTEM LIMITATIONS AND FUTURE ENHANCEMENTS

### Current Limitations

1. Equipment inventory tracking is manual (requires staff input)
2. Payment limited to Philippine payment gateways
3. Single language support (Filipino/English only)
4. No mobile app (responsive web only)
5. Limited AI features (only ID verification currently)

### Planned Future Enhancements

1. **IoT Integration**
   - RFID equipment tracking for automatic inventory updates
   - Smart locks for facility access control
   - Sensor-based facility monitoring (temperature, occupancy)

2. **Advanced AI Features**
   - Predictive booking demand forecasting
   - Intelligent event conflict resolution
   - Automated document verification and fraud detection
   - Natural language chatbot for booking assistance

3. **Mobile Applications**
   - Native Android and iOS apps
   - Push notifications for real-time updates
   - Offline booking draft capability

4. **Additional Integrations**
   - Barangay Health Units for medical mission coordination
   - Social Welfare for community program facilities
   - Tourism Office for public event management
   - Finance System for automated budget disbursement

5. **Enhanced Transparency**
   - Blockchain-based audit trails
   - Public API for citizen access to transparency data
   - Real-time fund tracking dashboards

---

## CONCLUSION

The Public Facilities Reservation System (PFRS) addresses the critical need for efficient, transparent, and accessible government facility management in Local Government Units. By implementing comprehensive functional requirements covering booking management, cross-system integration, equipment tracking, and financial transparency, combined with robust non-functional requirements ensuring security, scalability, and compliance, the system provides a modern solution that serves both citizens and government agencies effectively.

The successful integration with the Energy Efficiency & Conservation System demonstrates the system's capability for cross-agency collaboration, while maintaining data integrity and public accountability through transparent fund management and liquidation reporting.

---

**Document Version:** 1.0  
**Date:** January 10, 2026  
**System:** Public Facilities Reservation System (PFRS)  
**LGU:** Caloocan City Government

