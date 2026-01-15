# INTERNAL PROCESSES INTEGRATION

**Project:** LGU1 Public Facilities Reservation System  
**Document Type:** Internal Integration Specifications  
**Created:** December 24, 2025  
**Status:** Planning / Future Enhancement

---

## 📋 OVERVIEW

This document describes how the Facilities Reservation System integrates with internal Local Government Unit (LGU) departments for financial oversight, budget planning, and inter-departmental coordination.

---

## 🏛️ CITY BUDGET DEPARTMENT (CBD) INTEGRATION

### Purpose
The City Budget Department requires accurate financial data from the Facilities Reservation System to perform budget planning, fund allocation, and expenditure monitoring.

### CBD Services Relevant to Facilities System

1. **Preparation of the General Fund Budget**
   - Requires: Annual revenue projections from facility rentals
   - Our System Provides: Historical revenue data and utilization trends

2. **Preparation of Allotment Advice**
   - Requires: Monthly collection reports
   - Our System Provides: Automated revenue reports with breakdown by facility

3. **Certification of Appropriation Availability**
   - Requires: Expenditure tracking for facility maintenance
   - Our System Provides: Budget utilization reports

4. **Review of the Barangay Budget**
   - Requires: Barangay facility revenue data (if applicable)
   - Our System Provides: Revenue breakdown by barangay-owned facilities

---

## 🔄 INTEGRATION POINTS

### 1. Revenue Reporting to CBD

**Priority:** High (Part of Priority 5 - Reports & Analytics)

**Frequency:** Monthly, Quarterly, Annually

**Report Contents:**
- Total revenue collected from facility rentals
- Revenue per facility
- Revenue per facility type (gymnasium, convention center, etc.)
- Payment method breakdown (cash, online, bank transfer)
- Discount impact analysis (PWD, Senior, Student discounts)
- Outstanding receivables
- Projected revenue for next period

**Format:** Excel/CSV export compatible with CBD systems

**Implementation:**
```
Admin Dashboard → Reports → "CBD Revenue Report"
- Select date range
- Select report type (Monthly/Quarterly/Annual)
- Generate report
- Export to Excel
- Send to CBD via email or portal
```

---

### 2. Budget Allocation Tracking

**Priority:** Medium (Future Enhancement - Post Priority 5)

**Purpose:** Track how allocated budgets are utilized for facility operations

**Features:**
- **Budget Input:** Admin inputs annual budget allocation from CBD
  - Maintenance budget
  - Equipment purchase budget
  - Operational costs budget
  - Staff budget

- **Budget Monitoring:** System tracks spending against allocation
  - Equipment purchases
  - Maintenance schedules with costs
  - Operational expenses

- **Budget Utilization Reports:** Monthly reports showing:
  - Budget allocated
  - Budget spent
  - Budget remaining
  - Variance analysis
  - Projected burn rate

**Dashboard Widget:**
```
┌─────────────────────────────────┐
│  Budget Utilization (FY 2025)   │
├─────────────────────────────────┤
│  Allocated:   ₱5,000,000.00     │
│  Spent:       ₱3,200,000.00     │
│  Remaining:   ₱1,800,000.00     │
│  Utilization: 64%               │
│  [View Details]                 │
└─────────────────────────────────┘
```

---

### 3. Major Expenditure Approval Workflow

**Priority:** Low (Future Enhancement - Phase 2)

**Purpose:** Major facility expenses require CBD certification before proceeding

**Workflow:**
1. **Admin initiates purchase request:**
   - Major equipment purchase (> ₱100,000)
   - Facility renovation (> ₱500,000)
   - System checks budget availability

2. **System routes to CBD for approval:**
   - Purchase request details
   - Cost estimate
   - Justification
   - Budget line item

3. **CBD staff reviews:**
   - Certifies appropriation availability
   - Approves or denies request
   - Adds notes/conditions

4. **System updates status:**
   - If approved: Procurement can proceed
   - If denied: Admin notified with reason

**Database Tables (Future):**
```sql
CREATE TABLE budget_allocations (
    id BIGINT PRIMARY KEY,
    fiscal_year INT,
    category VARCHAR(100), -- maintenance, equipment, operations
    allocated_amount DECIMAL(12,2),
    spent_amount DECIMAL(12,2),
    created_at TIMESTAMP
);

CREATE TABLE expenditure_requests (
    id BIGINT PRIMARY KEY,
    facility_id BIGINT,
    category VARCHAR(100),
    description TEXT,
    amount DECIMAL(12,2),
    status ENUM('pending_cbd', 'approved', 'denied'),
    cbd_approved_by BIGINT,
    cbd_approved_at TIMESTAMP,
    cbd_notes TEXT,
    created_at TIMESTAMP
);
```

---

### 4. Data Analytics for Budget Planning

**Priority:** Medium (Enhancement to Priority 5)

**Purpose:** Provide CBD with insights for next fiscal year budget planning

**Analytics Provided:**
- **Facility Utilization Trends:**
  - Peak usage months (higher revenue potential)
  - Low usage months (budget optimization opportunity)
  - Facility popularity rankings

- **Revenue Growth Analysis:**
  - Year-over-year growth rate
  - Seasonal patterns
  - Event type trends

- **Equipment ROI Analysis:**
  - Equipment rental revenue vs. maintenance costs
  - High-demand equipment (justify additional purchases)
  - Underutilized equipment (budget reallocation opportunity)

- **Capacity Planning:**
  - Current capacity utilization percentage
  - Projected demand growth
  - Need for new facilities or expansion

**CBD Budget Planning Dashboard (Future):**
```
┌────────────────────────────────────────────────────┐
│  Facilities Budget Planning FY 2026                │
├────────────────────────────────────────────────────┤
│  Revenue Projection: ₱15,000,000 (+12% YoY)       │
│  Recommended Allocations:                          │
│  • Maintenance:   ₱2,500,000 (17% of revenue)     │
│  • Equipment:     ₱1,000,000 (7% of revenue)      │
│  • Operations:    ₱1,500,000 (10% of revenue)     │
│  • Surplus:       ₱10,000,000 → General Fund      │
└────────────────────────────────────────────────────┘
```

---

## 📊 REPORT SPECIFICATIONS

### Monthly CBD Revenue Report

**Filename Format:** `CBD_Revenue_Report_YYYY-MM.xlsx`

**Sheet 1: Summary**
| Metric | Amount (₱) |
|--------|-----------|
| Total Revenue | X,XXX,XXX.XX |
| Facility Rental | X,XXX,XXX.XX |
| Equipment Rental | XXX,XXX.XX |
| Deposits Collected | XXX,XXX.XX |
| Refunds Issued | (XXX,XXX.XX) |
| Net Revenue | X,XXX,XXX.XX |

**Sheet 2: Revenue by Facility**
| Facility Name | City | Bookings | Revenue (₱) | Avg Booking Value (₱) |
|---------------|------|----------|-------------|---------------------|
| Quezon City Gymnasium | QC | 15 | 500,000.00 | 33,333.33 |
| ... | ... | ... | ... | ... |

**Sheet 3: Payment Methods**
| Method | Transactions | Amount (₱) | Percentage |
|--------|--------------|-----------|-----------|
| Online Payment | 45 | 1,200,000.00 | 60% |
| Over-the-Counter | 30 | 800,000.00 | 40% |

**Sheet 4: Discounts Applied**
| Discount Type | Count | Amount Discounted (₱) |
|---------------|-------|---------------------|
| PWD | 8 | 120,000.00 |
| Senior Citizen | 12 | 180,000.00 |
| Student | 5 | 50,000.00 |
| Resident Discount | 25 | 250,000.00 |

---

## 🔗 API ENDPOINTS (Future)

For automated integration with CBD systems:

```
POST /api/cbd/revenue-report
GET  /api/cbd/budget-utilization
POST /api/cbd/expenditure-request
GET  /api/cbd/analytics
```

---

## 🎯 IMPLEMENTATION ROADMAP

### Phase 1 (Current - Priority 5)
- ✅ Basic revenue reports
- ✅ Excel export functionality
- ✅ Manual submission to CBD

### Phase 2 (Post-Launch)
- ⏳ Budget allocation tracking
- ⏳ Budget utilization monitoring
- ⏳ Automated monthly report generation

### Phase 3 (Future Enhancement)
- ⏳ Expenditure approval workflow
- ⏳ CBD portal access
- ⏳ API integration with CBD systems
- ⏳ Real-time budget monitoring

---

## 📝 NOTES

- CBD integration focuses on **financial oversight** and **budget planning**
- All financial data must be accurate and auditable
- Reports must follow government accounting standards
- Integration is primarily **reporting-based** (not real-time transactional)
- CBD staff do not need direct system access in Phase 1
- Future phases may provide CBD portal for self-service report generation

---

**Last Updated:** December 24, 2025  
**Next Review:** After Priority 5 completion
