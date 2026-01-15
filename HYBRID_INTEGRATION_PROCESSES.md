# HYBRID INTEGRATION PROCESSES

**Project:** LGU1 Public Facilities Reservation System  
**Document Type:** Cross-Departmental Process Specifications  
**Created:** December 24, 2025  
**Status:** Planning / Implementation

---

## 📋 OVERVIEW

This document describes processes that involve both **external stakeholders (citizens)** and **internal departments (CTO, CBD, etc.)**, showing how the Facilities Reservation System acts as a bridge between different parties.

---

## 💰 PROCESS 1: PAYMENT VERIFICATION & REVENUE COLLECTION

### Overview
The payment process involves the citizen, the Facilities System, the City Treasurer's Office (CTO), and eventually the City Budget Department (CBD).

### Process Flow

```
┌─────────────────────────────────────────────────────────────────────┐
│                    PAYMENT LIFECYCLE PROCESS                         │
└─────────────────────────────────────────────────────────────────────┘

Step 1: Booking Approved
┌──────────┐
│ Citizen  │ → Receives booking approval notification
└──────────┘   → Invoice with amount due
               → Payment deadline (48 hours)
               
Step 2: Payment Made
┌──────────┐         ┌─────────────┐
│ Citizen  │────────►│     CTO     │
└──────────┘  Pays   │ (Treasurer) │
               at    └─────────────┘
              Office        │
                           ▼
                    Receipt Issued
                           │
                           ▼
┌──────────┐         ┌─────────────────┐
│ Citizen  │────────►│ Facilities      │
└──────────┘ Uploads │ System          │
             Receipt └─────────────────┘
             Photo          │
                           ▼
                    Status: Payment Submitted
                           │
                           ▼
Step 3: Payment Verification
                    ┌─────────────┐
                    │ Treasurer   │
                    │ (In System) │
                    └─────────────┘
                           │
                           ▼
                  Verifies Payment
                           │
                ┌──────────┴──────────┐
                ▼                     ▼
         [Valid]                [Invalid]
                │                     │
                ▼                     ▼
    Generate OR              Request Resubmit
                │                     │
                ▼                     ▼
Step 4: Confirmation
┌──────────────────────┐      ┌──────────┐
│ Facilities System    │─────►│ Citizen  │
└──────────────────────┘      └──────────┘
  • Status: Confirmed          • Receives OR via email
  • OR Number assigned          • Booking confirmed
  • Slot locked                 • Notification sent
  • Equipment reserved
                │
                ▼
Step 5: Revenue Reporting
        ┌─────────────────────┐
        │ Facilities System   │
        └─────────────────────┘
                │
                ▼
        Daily Collection Report
                │
                ▼
        ┌─────────────┐
        │    CTO      │ → For treasury records
        └─────────────┘ → Remittance tracking
                │
                ▼
        Monthly Revenue Report
                │
                ▼
        ┌─────────────┐
        │    CBD      │ → For budget planning
        └─────────────┘ → Revenue analysis
```

### Stakeholder Roles

| Stakeholder | Responsibility | System Access |
|-------------|---------------|---------------|
| **Citizen** | Make payment, upload proof | Citizen Portal |
| **Treasurer** | Verify payment, issue OR | Treasurer Dashboard |
| **Facilities System** | Track payment, generate OR, record revenue | Automated |
| **CTO** | Receive daily reports, reconcile collections | Reports (Email/Export) |
| **CBD** | Receive monthly reports, track revenue | Reports (Email/Export) |

### Data Flow

**Payment Record Structure:**
```json
{
  "booking_id": 123,
  "amount": 15000.00,
  "payment_method": "over_the_counter",
  "payment_date": "2025-12-24 10:30:00",
  "payment_proof_path": "payments/receipt_123.jpg",
  "or_number": "OR-QC-2025-001234",
  "verified_by": "treasurer_user_id",
  "verified_at": "2025-12-24 14:00:00",
  "status": "verified"
}
```

**Daily Collection Report (to CTO):**
```json
{
  "report_date": "2025-12-24",
  "total_collections": 145000.00,
  "transaction_count": 12,
  "payment_methods": {
    "cash": 85000.00,
    "gcash": 40000.00,
    "bank_transfer": 20000.00
  },
  "transactions": [...]
}
```

**Monthly Revenue Report (to CBD):**
```json
{
  "report_month": "2025-12",
  "total_revenue": 1850000.00,
  "total_bookings": 156,
  "revenue_by_facility": {...},
  "discounts_applied": 285000.00,
  "refunds_issued": 35000.00,
  "net_revenue": 1530000.00
}
```

---

## 🏢 PROCESS 2: BUDGET REQUEST & ALLOCATION

### Overview
When facilities need maintenance or new equipment, the request flows from Facilities Management → CBD for approval → CTO for fund release.

### Process Flow

```
┌─────────────────────────────────────────────────────────────────────┐
│                BUDGET REQUEST & ALLOCATION PROCESS                   │
└─────────────────────────────────────────────────────────────────────┘

Step 1: Identify Need
┌──────────────────────┐
│ Facility Manager     │ → Identifies maintenance need
│ (Admin)              │ → Or equipment shortage
└──────────────────────┘
         │
         ▼
Step 2: Create Budget Request
┌──────────────────────┐
│ Facilities System    │ → Admin creates request
└──────────────────────┘   → Justification
         │                 → Cost estimate
         ▼                 → Priority level
┌─────────────────────────────────────────────┐
│ Budget Request Details:                      │
│ • Category: Equipment Purchase               │
│ • Item: 10 Additional Folding Chairs        │
│ • Cost: ₱25,000.00                          │
│ • Justification: High demand, frequent       │
│   shortage based on utilization data         │
│ • Supporting Data: 85% equipment utilization │
└─────────────────────────────────────────────┘
         │
         ▼
Step 3: Route to CBD
┌─────────────┐
│     CBD     │ → Reviews request
└─────────────┘ → Checks budget availability
         │       → Verifies justification
         │
    ┌────┴────┐
    ▼         ▼
[Approve]  [Deny]
    │         │
    ▼         └─────────────┐
Certify Funds               │
Available                   ▼
    │                 Notify Admin
    │                 (Request Denied)
    ▼
Step 4: Procurement Authorization
┌──────────────────────┐
│ Facilities System    │ → Status: CBD Approved
└──────────────────────┘ → Can proceed with procurement
         │
         ▼
Step 5: Procurement & Purchase
┌──────────────────────┐
│ Admin                │ → Purchase equipment
└──────────────────────┘ → Submit invoice
         │
         ▼
Step 6: Fund Release Request
┌──────────────────────┐
│ Facilities System    │ → Request sent to CTO
└──────────────────────┘ → Invoice attached
         │
         ▼
┌─────────────┐
│     CTO     │ → Verifies invoice
└─────────────┘ → Releases check/payment
         │
         ▼
Step 7: Update Budget Tracking
┌──────────────────────┐
│ Facilities System    │ → Budget spent updated
└──────────────────────┘ → Equipment inventory updated
         │                → Asset tracking initiated
         ▼
   Complete
```

### Stakeholder Roles

| Stakeholder | Responsibility | Timing |
|-------------|---------------|---------|
| **Admin** | Identify need, create request | As needed |
| **CBD** | Review request, certify funds | Within 5 working days |
| **Admin** | Procure equipment | After CBD approval |
| **CTO** | Release funds | After invoice submission |
| **Facilities System** | Track entire process | Real-time |

---

## 📊 PROCESS 3: FINANCIAL ASSISTANCE COORDINATION

### Overview
When facilities are used for government events or community programs that qualify for financial assistance, coordination between Facilities, CTO, and program offices is required.

### Process Flow

```
┌─────────────────────────────────────────────────────────────────────┐
│            GOVERNMENT EVENT WITH FINANCIAL ASSISTANCE                │
└─────────────────────────────────────────────────────────────────────┘

Step 1: Government Event Request
┌──────────────────────┐
│ Government Office    │ → Requests facility for event
│ (e.g., Social Welfare)│ → Marks as government event
└──────────────────────┘ → No payment required
         │
         ▼
Step 2: Admin Review
┌──────────────────────┐
│ Facility Admin       │ → Verifies government status
└──────────────────────┘ → Approves free booking
         │
         ▼
Step 3: Budget Allocation
┌─────────────┐
│     CBD     │ → Allocates budget for event
└─────────────┘ → Charges to department's budget
         │       → Not to facilities revenue
         ▼
Step 4: Event Execution
┌──────────────────────┐
│ Facilities System    │ → Facility booked
└──────────────────────┘ → Equipment allocated
         │                → No payment required
         ▼
Step 5: Cost Accounting
┌──────────────────────┐
│ Facilities System    │ → Calculates opportunity cost
└──────────────────────┘ → Records foregone revenue
         │
         ▼
┌─────────────┐
│     CBD     │ → Receives report
└─────────────┘ → Tracks government event costs
         │       → Budget planning for next FY
         ▼
┌─────────────┐
│     CTO     │ → Receives revenue report
└─────────────┘ → Notes: Government event (no collection)
```

### Key Considerations

**Revenue Recognition:**
- Government events: ₱0 revenue but tracked separately
- Shows "foregone revenue" for CBD planning
- Helps justify budget allocations for next fiscal year

**Priority Handling:**
- Government events have higher priority
- Can override citizen bookings (with proper notice and refund)
- Must be scheduled with advance notice

---

## 🔄 PROCESS 4: REFUND & CANCELLATION COORDINATION

### Overview
When bookings are cancelled (by citizen or admin), refund processing involves multiple departments.

### Process Flow

```
┌─────────────────────────────────────────────────────────────────────┐
│                    REFUND PROCESSING FLOW                            │
└─────────────────────────────────────────────────────────────────────┘

Scenario A: Citizen-Initiated Cancellation
┌──────────┐
│ Citizen  │ → Requests cancellation
└──────────┘   → Provides reason
         │
         ▼
┌──────────────────────┐
│ Facilities System    │ → Checks cancellation policy
└──────────────────────┘   → 7 days before: Full refund
         │                 → 3-6 days: 50% refund
         │                 → < 3 days: No refund
         ▼
┌──────────────────────┐
│ Admin                │ → Reviews request
└──────────────────────┘ → Approves/Denies
         │
         ▼ [Approved]
┌──────────────────────┐
│ Facilities System    │ → Status: Refund Approved
└──────────────────────┘ → Amount: ₱X,XXX.XX
         │
         ▼
┌─────────────┐
│     CTO     │ → Processes refund
└─────────────┘ → Issues check or bank transfer
         │       → Updates OR (marked as refunded)
         ▼
┌──────────┐
│ Citizen  │ → Receives refund
└──────────┘   → Notification sent


Scenario B: Admin-Initiated Cancellation (Override)
┌──────────────────────┐
│ Admin                │ → Cancels citizen booking
└──────────────────────┘   → Reason: Government event
         │
         ▼
┌──────────────────────┐
│ Facilities System    │ → Auto-approves full refund
└──────────────────────┘   → Priority: Urgent
         │
         ▼
┌─────────────┐
│     CTO     │ → Immediate refund processing
└─────────────┘ → Expedited release
         │
         ▼
┌──────────┐
│ Citizen  │ → Receives refund + apology
└──────────┘   → Offered alternative dates
         │       → Possible discount on rebooking
         ▼
┌─────────────┐
│     CBD     │ → Receives refund report
└─────────────┘   → Tracks refund expenses
                  → Budget impact analysis
```

### Refund Policy Matrix

| Days Before Event | Refund Amount | Processing Time | Approval Required |
|-------------------|---------------|-----------------|-------------------|
| 7+ days | 100% | 3-5 working days | Auto-approved |
| 4-6 days | 50% | 3-5 working days | Admin approval |
| 1-3 days | 25% | 5-7 working days | Admin approval |
| Same day | 0% | N/A | N/A |
| Admin Override | 100% + benefit | Immediate | Auto-approved |

### Financial Tracking

**For CTO:**
- Refunds tracked separately from collections
- Monthly refund report
- Reconciliation with OR records

**For CBD:**
- Refund expenses tracked
- Impact on net revenue
- Policy effectiveness analysis

---

## 📋 PROCESS 5: ANNUAL REVENUE RECONCILIATION

### Overview
End-of-year process to reconcile all financial records between Facilities System, CTO, and CBD.

### Process Flow

```
┌─────────────────────────────────────────────────────────────────────┐
│              ANNUAL REVENUE RECONCILIATION PROCESS                   │
└─────────────────────────────────────────────────────────────────────┘

Step 1: System-Generated Annual Report
┌──────────────────────┐
│ Facilities System    │ → Generates FY 2025 report
└──────────────────────┘   → All transactions
         │                 → All payments
         │                 → All refunds
         ▼                 → All discounts
┌─────────────────────────────────────────┐
│ Annual Revenue Summary FY 2025:         │
│ • Total Bookings: 1,856                 │
│ • Gross Revenue: ₱18,560,000.00        │
│ • Discounts: (₱2,140,000.00)           │
│ • Refunds: (₱420,000.00)               │
│ • Net Revenue: ₱16,000,000.00          │
└─────────────────────────────────────────┘
         │
         ▼
Step 2: CTO Reconciliation
┌─────────────┐
│     CTO     │ → Compares with treasury records
└─────────────┘ → Matches OR numbers
         │       → Verifies collections
         │       → Checks refunds
         ▼
  ┌──────────────┐
  │ Discrepancies?│
  └──────────────┘
    │         │
    NO       YES
    │         │
    │         ▼
    │    ┌──────────────────────┐
    │    │ Investigation        │
    │    │ • Missing ORs?       │
    │    │ • Unrecorded payment?│
    │    │ • Data entry error?  │
    │    └──────────────────────┘
    │         │
    │         ▼
    │    Resolve & Recon cile
    │         │
    └────┬────┘
         ▼
Step 3: CBD Review
┌─────────────┐
│     CBD     │ → Reviews revenue vs. budget
└─────────────┘ → Analyzes trends
         │       → Plans next FY budget
         ▼
┌─────────────────────────────────────────┐
│ Budget Performance Analysis:             │
│ • Projected: ₱15,000,000.00             │
│ • Actual: ₱16,000,000.00                │
│ • Variance: +₱1,000,000.00 (+6.7%)     │
│ • Recommendation: Increase FY 2026      │
│   budget allocation for maintenance     │
└─────────────────────────────────────────┘
         │
         ▼
Step 4: Audit Trail
┌──────────────────────┐
│ All Systems          │ → Final reports archived
└──────────────────────┘ → Audit-ready
         │                → Compliance verified
         ▼
   Annual Audit Complete
```

---

## 🎯 SUCCESS METRICS

### For Hybrid Processes

**Efficiency Metrics:**
- Average payment verification time: < 24 hours
- Average refund processing time: < 5 days
- Budget request approval time: < 7 days
- Annual reconciliation accuracy: > 99.9%

**Quality Metrics:**
- Payment discrepancy rate: < 0.1%
- Refund dispute rate: < 1%
- Citizen satisfaction with payment process: > 4.5/5
- Department collaboration score: > 4/5

**Financial Metrics:**
- Revenue collection rate: > 95%
- On-time payment rate: > 90%
- Refund rate: < 5%
- Budget utilization rate: 80-95% (optimal)

---

## 📝 NOTES

### Key Principles

1. **Transparency:** All stakeholders see relevant transaction data
2. **Accountability:** Every action logged and auditable
3. **Efficiency:** Minimal manual intervention
4. **Accuracy:** Automated calculations reduce errors
5. **Compliance:** Follows government accounting standards

### Future Enhancements

- **Real-time integration:** API connections between systems
- **Automated reconciliation:** AI-powered discrepancy detection
- **Blockchain:** Immutable audit trail for all transactions
- **Mobile apps:** Officers can approve on-the-go

---

**Last Updated:** December 24, 2025  
**Next Review:** Quarterly
