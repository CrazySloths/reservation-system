# EXTERNAL INTEGRATIONS - CITIZEN-FACING SERVICES

**Project:** LGU1 Public Facilities Reservation System  
**Document Type:** External Integration Specifications  
**Created:** December 24, 2025  
**Status:** Planning / Future Enhancement

---

## 📋 OVERVIEW

This document describes how the Facilities Reservation System can integrate with other LGU citizen-facing services, particularly the City Treasurer's Office (CTO) services, to create a unified citizen experience.

---

## 💰 CITY TREASURER'S OFFICE (CTO) INTEGRATION

### Current Integration Status

**Priority 3: Payment Processing** ✅ Already Planned

The Facilities Reservation System currently integrates with CTO for:
- Payment verification (Over-the-counter payments)
- Official Receipt (OR) issuance
- Revenue collection reporting

---

## 🏛️ CTO SERVICES CATALOG

The following CTO services were identified as potential integration candidates for a unified LGU Citizen Portal:

### Tax Services
1. Payment of Transfer Tax
2. Payment of Contractor's Tax
3. Business Tax Assessment (In-Person Application)
4. Business Tax Assessment (Online Application)
5. Issuance of Certified True Copy and Verification of Payment Records for Business and Transfer Tax
6. Business Tax Collection
7. Cancelation of Business and Real Property Tax Payment
8. Assessment and Billing of Real Property Tax
9. Real Property Tax Collection (Walk-In)
10. Online Application for Real Property Tax Clearance
11. Online Payment Processing of Real Property Tax
12. Issuance of Real Property Tax Clearance (Walk-In)
13. Collection of Amusement Tax

### Community Services
14. Issuance of Community Tax Certificate (Individual)
15. Issuance of Community Tax Certificates (Corporation)

### Business Services
16. Payment of Miscellaneous Taxes and Fees
17. Collection of Market Business Tax and Other Fees
18. Sealing of Weights and Measures
19. Market Raid
20. Examination of Accounting Records
21. Application for Business Retirement Certificate
22. Online Payment Processing for New Businesses
23. Online Payment Processing of Annual Business Tax (Renewal)
24. Online Payment Processing for Occupational Permits
25. Online Payment Processing for Liquor Permits
26. Online Payment Processing for Building Permits
27. Online Payment Processing of Professional Tax Receipt (PTR)
28. Online Payment Processing for Health Certificates
29. Online Payment Processing for Sanitary Permits
30. Collection of Bid Documents

### Financial Assistance Services
31. Release of Financial Assistance for the Bereaved Family of the Deceased Senior Citizen
32. Release of Quezon City Living Centenarian Recognition Awards and Benefits
33. Releasing Salaries of Contractual and/or Job Order Employees Without ATM Cards
34. Release of Check as Financial Assistance to Deceased Government Officials
35. Release of Financial Burial Assistance
36. Release of Barangay Shares from Real Property Tax (RPT) and Community Tax Collections (CTC)
37. Collection of Payment from Beneficiaries of Socialized Housing Project

### Accounting & Administrative Services
38. Issuance of Accountable Forms
39. Receiving Incoming Correspondence
40. Online Application/Renewal of Fidelity Bond
41. Remittance of Cash Collected
42. Recording Daily Transactions in Cash Books
43. Releasing Prepared Checks

---

## 🔗 INTEGRATION ARCHITECTURE

### Phase 1: Payment Integration (Current System)

**Scope:** Facilities Reservation System ↔ CTO Payment Verification

```
┌──────────────┐         ┌─────────────────────────┐         ┌──────────────┐
│   CITIZEN    │────────►│  Facilities Booking     │────────►│  CTO Staff   │
│              │ Books   │  System                 │ Verify  │  (Treasurer) │
└──────────────┘         └─────────────────────────┘         └──────────────┘
                                    │
                                    ▼
                         ┌─────────────────────────┐
                         │  Official Receipt (OR)  │
                         │  Generation             │
                         └─────────────────────────┘
```

**Features:**
- ✅ Citizen submits booking with payment proof
- ✅ Treasurer verifies payment
- ✅ System generates Official Receipt
- ✅ Revenue recorded for CTO reporting

---

### Phase 2: Unified Payment Gateway (Future)

**Scope:** Single payment portal for all CTO services

```
┌──────────────────────────────────────────────────────────┐
│              UNIFIED LGU PAYMENT GATEWAY                  │
├──────────────────────────────────────────────────────────┤
│  Payment Methods:                                         │
│  • GCash / PayMaya / PayMongo                            │
│  • Bank Transfer                                          │
│  • Over-the-Counter                                       │
│  • Credit/Debit Card                                      │
└──────────────────────────────────────────────────────────┘
         │              │              │              │
         ▼              ▼              ▼              ▼
┌─────────────┐ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐
│ Facilities  │ │ Real Property│ │  Business   │ │ Community   │
│ Booking     │ │ Tax          │ │  Permits    │ │ Tax Cert    │
└─────────────┘ └─────────────┘ └─────────────┘ └─────────────┘
```

**Benefits:**
- Citizens pay for multiple services in one transaction
- Single Official Receipt for multiple services
- Unified payment history
- Reduced queue times at CTO
- Better revenue tracking

---

### Phase 3: Unified Citizen Portal (Future)

**Scope:** All LGU services in one portal

```
┌────────────────────────────────────────────────────────────┐
│          QUEZON CITY CITIZEN SERVICES PORTAL                │
├────────────────────────────────────────────────────────────┤
│  My Dashboard:                                              │
│  • My Bookings (Facilities)                                 │
│  • My Business Permits                                      │
│  • My Property Tax Records                                  │
│  • My Community Tax Certificates                            │
│  • My Payment History                                       │
│  • My Official Receipts                                     │
│  • My Applications (Pending/Approved)                       │
└────────────────────────────────────────────────────────────┘
```

---

## 🎯 INTEGRATION PRIORITIES

### Current System (Priority 3)

**Treasurer Role Implementation:**

**Features:**
1. **Payment Verification Interface**
   - View pending payments
   - Search by booking reference
   - Verify payment proof (uploaded receipt)
   - Mark as verified or request resubmission

2. **Official Receipt Issuance**
   - Auto-generate OR number
   - OR format complies with CTO standards
   - Digital signature/seal
   - Email OR to citizen
   - Store OR in system for reprinting

3. **Daily Collection Reporting**
   - Daily remittance summary
   - Payment method breakdown
   - Reconciliation reports
   - Export for CTO accounting system

4. **Revenue Dashboard**
   - Total collections (daily/weekly/monthly)
   - Collection trends
   - Outstanding payments
   - Refund tracking

---

### Future Enhancement: Appointment Booking for CTO Services

**Concept:** Extend booking system to handle CTO service appointments

**Services That Could Use Appointment Booking:**
1. Business Tax Assessment (In-Person Application)
2. Issuance of Certified True Copy of Payment Records
3. Issuance of Community Tax Certificate (Walk-in)
4. Issuance of Real Property Tax Clearance (Walk-in)
5. Application for Business Retirement Certificate
6. Examination of Accounting Records

**Implementation:**
```
Citizen → Select CTO Service → Choose Date/Time → Submit Requirements → 
Receive Appointment Confirmation → Visit CTO on Scheduled Date
```

**Benefits:**
- No more long queues at CTO
- Citizens schedule at their convenience
- CTO can manage staff workload better
- Reduced processing time
- Better citizen satisfaction

---

### Future Enhancement: Unified SSO (Single Sign-On)

**Concept:** One account for all LGU services

**Architecture:**
```
┌─────────────────────────────────────────────────────┐
│         LGU CENTRAL AUTHENTICATION SYSTEM            │
│         (Single Sign-On - SSO)                       │
└─────────────────────────────────────────────────────┘
         │              │              │
         ▼              ▼              ▼
┌─────────────┐ ┌─────────────┐ ┌─────────────┐
│ Facilities  │ │   CTO       │ │   Other     │
│ System      │ │  Services   │ │   Depts     │
└─────────────┘ └─────────────┘ └─────────────┘
```

**Features:**
- Citizen registers once
- Login once, access all services
- Shared profile data (name, address, ID, etc.)
- Unified notification system
- Cross-service analytics

---

## 💳 PAYMENT GATEWAY SPECIFICATIONS

### Current Implementation (Priority 3)

**Payment Methods Supported:**
1. **Over-the-Counter (CTO Treasurer's Office)**
   - Citizen pays in cash at CTO
   - Receives OR
   - Uploads OR photo to system
   - Treasurer verifies in system

2. **Online Payment (Mock Gateway)**
   - GCash / PayMaya / Bank Transfer
   - Payment proof uploaded
   - Treasurer verifies transaction

### Future Implementation

**Integrated Payment Gateway:**
- Real-time payment processing
- Automatic verification
- Instant OR generation
- No manual treasurer verification needed

**Payment Gateway Options:**
1. **PayMongo** (Philippines-focused)
2. **GCash Business API**
3. **PayMaya Checkout**
4. **Dragonpay** (supports multiple banks)
5. **Xendit** (enterprise option)

---

## 📊 CTO REPORTING REQUIREMENTS

### Daily Reports (Auto-Generated)

**Filename:** `CTO_Daily_Collection_YYYY-MM-DD.xlsx`

**Contents:**
| OR Number | Time | Booking Ref | Citizen Name | Service | Amount (₱) | Payment Method |
|-----------|------|-------------|--------------|---------|-----------|----------------|
| OR-QC-2025-001234 | 09:30 AM | LGU-QC-2025-000123 | Juan Dela Cruz | Facility Rental | 15,000.00 | GCash |

**Summary:**
- Total Collections: ₱XXX,XXX.XX
- Number of Transactions: XX
- Cash: ₱XXX,XXX.XX
- Online: ₱XXX,XXX.XX

### Monthly Reports

**Revenue Report for CBD** (See INTERNAL_PROCESSES.md)

---

## 🔐 SECURITY & COMPLIANCE

### Data Sharing Between Systems

**Principle:** Minimal data sharing, maximum security

**What's Shared:**
- ✅ Payment transaction ID
- ✅ Amount paid
- ✅ Service type (facility booking)
- ✅ Official Receipt number

**What's NOT Shared:**
- ❌ Full personal details (GDPR/Data Privacy Act compliance)
- ❌ Booking details (unless CTO needs for verification)
- ❌ Uploaded documents (stays in facilities system)

### API Security

**Authentication:**
- OAuth 2.0 for inter-system authentication
- API keys for trusted systems
- JWT tokens for session management

**Encryption:**
- HTTPS/TLS for all data transmission
- Database encryption for sensitive data
- Secure key management

---

## 📱 MOBILE APP INTEGRATION (Future)

**Quezon City LGU Mobile App:**

Features:
- View all available LGU services
- Book facilities
- Pay taxes and fees
- Apply for permits
- Track applications
- Receive notifications
- Download ORs and certificates

Platform:
- iOS and Android
- Progressive Web App (PWA) option
- Offline capability for viewing history

---

## 🎯 IMPLEMENTATION ROADMAP

### Phase 1 (Current - Priority 3) ✅
- Treasurer role in Facilities System
- Manual payment verification
- OR generation
- Basic CTO reporting

### Phase 2 (Post-Launch)
- Real online payment gateway
- Automated payment verification
- API for CTO system integration
- Enhanced reporting

### Phase 3 (Future)
- Unified payment gateway
- Multi-service payment
- Appointment booking for CTO services
- SSO integration

### Phase 4 (Vision)
- Unified LGU Citizen Portal
- Mobile app
- AI-powered service recommendations
- Chatbot support

---

## 📝 INTEGRATION BENEFITS

### For Citizens
- ✅ One-stop portal for all LGU services
- ✅ Convenient online payments
- ✅ Reduced waiting time
- ✅ 24/7 access to services
- ✅ Unified service history

### For CTO
- ✅ Reduced manual verification workload
- ✅ Faster payment processing
- ✅ Accurate revenue tracking
- ✅ Better cash flow management
- ✅ Digital records (less paper)

### For LGU Administration
- ✅ Improved citizen satisfaction
- ✅ Better revenue collection
- ✅ Data-driven decision making
- ✅ Reduced operational costs
- ✅ Modernized government services

---

**Last Updated:** December 24, 2025  
**Next Review:** After Priority 3 completion

