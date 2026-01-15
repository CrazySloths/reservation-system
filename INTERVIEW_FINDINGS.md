# 📋 INTERVIEW FINDINGS DOCUMENTATION

**Date:** December 2024  
**Locations:** South Caloocan City & Quezon City  
**Purpose:** Validate system requirements and understand real-world facility reservation processes

---

## 🎯 EXECUTIVE SUMMARY

We conducted interviews with two LGUs to understand their current facility reservation processes, challenges, and needs. This document serves as our **real-world validation** while our system builds upon panel-approved specifications.

**Key Finding:** Both LGUs face similar challenges despite different scales and maturity levels.

---

## 📍 INTERVIEW 1: SOUTH CALOOCAN CITY

### A. STAKEHOLDERS & ROLES

**Primary Contact:** Department Secretary  
**Approval Authority:** Department Head (or Supervising Officer if unavailable)  
**Final Approval:** Interviewee is last person in approval line

**User Types Allowed to Reserve:**
- NGOs
- Caloocan residents
- Non-residents of Caloocan (all welcome)

---

### B. CURRENT FACILITIES (4 Total)

1. **Buena Park**
2. **Sports Complex**
3. **Bulwagan Katipunan** (used for city events)
4. **Pacquiao Court** (covered basketball court)

---

### C. CURRENT SYSTEM & PROCESS

**Technology Stack:**
- Google Sheets for schedule management and recording
- Physical bulletin with color-coded entries for verification
- Manual reservation forms

**Booking Volume:**
- ~15 reservations per 30-day month (approximately half the month booked)
- August 2024: Fully booked
- Higher bookings on weekends for private events

**Workflow:**
1. Client comes in person to make reservation
2. Fill out manual form
3. Department head approves
4. CGSD releases order of payment
5. Client pays at City Treasurer's office (downstairs)
6. Cash payment only

---

### D. PRICING MODEL (CURRENT)

**⚠️ NOTE:** Panel requires per-person pricing. Current Caloocan uses hourly:

- **3 hours:** ₱5,000
- **Additional hour:** +₱2,000
- **Most common booking:** 3 hours (matches catering packages)
- **Maximum usual booking:** 5-6 hours

**Payment Methods:**
- ✅ Cash only (at City Treasurer's office)
- ❌ Cashless not allowed for government transactions

---

### E. IDENTIFIED PROBLEMS & PAIN POINTS

#### 1. **Double Booking Issues** 🚨
> "Previously, when the interviewee was not yet handling the scheduling for Buena Park, there were issues with conflicting schedules and double bookings. This was due to the lack of a systematic process, which was previously done manually through letters without a formal reservation form."

**Solution Implemented by Interviewee:**
- Opened reservations for only 3 months at a time
- Introduced formal reservation forms
- Color-coded physical bulletin

**Our System Enhancement:**
- Real-time conflict detection
- Calendar-first availability check
- Automatic blocking of conflicting slots

---

#### 2. **Miscommunication** 📞
> "One of the biggest challenges is when clients do not coordinate properly and there is miscommunication between the admin, the client, and the person in charge at Buena Park."

**Our System Enhancement:**
- Multi-party notifications
- Real-time status updates
- Digital communication trail

---

#### 3. **Schedule Uncertainty** 📅
> "It is also frustrating when clients are unsure of their schedule and have already paid, as there is a no-refund policy, although they can reschedule."

**Our System Enhancement:**
- Pencil booking capability
- Easy rescheduling interface
- Alternative date suggestions

---

#### 4. **Unavailability Conflicts** ⚠️
> "It is also frustrating when clients insist on booking a facility that is already unavailable."

**Our System Enhancement:**
- Only show available slots
- Real-time availability display
- Calendar-first booking approach

---

### F. DESIRED FEATURES (From Interview)

**Social Media Integration:**
> "A system that allows for scheduling through social media would be helpful since many people are active on those platforms."

**Consideration:**
> "However, the interviewee also acknowledges that not all of their clients are tech-savvy, particularly senior citizens."

**Real-time Alerts & Predictions:**
> "Yes, a system with real-time alerts, especially one that can predict and show available facilities and automatically reschedule in case of conflicts, would be beneficial."

---

### G. REPORTS & FEEDBACK

**Current Reporting:**
- Monthly report includes:
  - Number of scheduled events for the month
  - Total amount of payments collected

**Our System Enhancement:**
- Automated monthly reports
- AI-powered usage analytics
- Revenue forecasting
- Pattern recognition (TensorFlow.js)

---

## 📍 INTERVIEW 2: QUEZON CITY M.I.C.E. CENTER

### A. STAKEHOLDERS & ROLES

**Managing Office:** Office of the City Mayor (OCM) + QC Tourism Department (QCTD)  
**Operations Team:** M.I.C.E. Team (staff from Administrative Support and Services Division + Tourism Planning Division)  
**Support Staff:**
- Engineers (electrical, power supply oversight)
- Security personnel (materials/equipment entry, attendee reception)
- Utility staff (cleanliness, ingress/egress assistance)

**Current User Restrictions:**
- ✅ QC-LGU departments and city-sponsored events ONLY
- ❌ NGOs and private companies NOT YET allowed
- **Reason:** Ordinance still being processed (includes rental fees)

---

### B. CURRENT FACILITIES

**QC M.I.C.E. Center** (4-storey building):
- Convention/Exhibit Hall
- Lobbies
- **Breakout Rooms** (MOST REQUESTED!)
- Auditorium
- Clean restrooms on all floors (male, female, all-genders/PWD)

**Construction Status:**
- Floors 1-2: Operational
- Floors 3-4: Under construction

---

### C. CURRENT SYSTEM & PROCESS

**Technology Stack:**
- Google Sheets (centralized database)
- Digital calendar with real-time updates
- Paper-based documents also scanned and uploaded
- Real-time alerts and notifications

**Booking Volume (Jan-Aug 2024 - 8 months only):**
- Main Hall/Convention: <100 programs
- **Breakout Rooms: >200 bookings** (most popular!)

**Workflow:**
1. Request sent via letter (physical or email) to OCM or QCTD
2. Criteria evaluation:
   - Number of attendees
   - Event alignment with QC-LGU programs
   - Venue availability
3. QCTD coordinates with OCM
4. OCM gives final approval
5. If approved → Ocular visit arranged
6. M.I.C.E. Team tags as "pencil book" in system
7. Forms submitted before event

---

### D. PRICING MODEL (CURRENT)

**⚠️ STATUS:** No pricing yet - Ordinance still being processed

**Payment Methods:** Not yet established

---

### E. IDENTIFIED PROBLEMS & PAIN POINTS

#### 1. **Rush/Emergency Programs** 🚨
> "Hindi maiiwasan ang mga biglaang programa na kailangang bigyan ng prayoridad at i-accommodate."

**Our System Enhancement:**
- Priority booking feature
- Emergency request handling
- Fast-track approval workflow

---

#### 2. **Late Form Submissions** 📝
> "May mga pagkakataon na hindi kaagad naipapasa ang forms dahil sa kakulangan ng oras na sagutan ito."

**Our System Enhancement:**
- Digital forms (fillable online)
- Auto-reminders for requirements
- Progress tracking

---

#### 3. **Manual Data Entry** ⌨️
> "Kakailanganin ng M.I.C.E. Team na manually i-tag ang datos sa record sheet at kalendaryo."

**Our System Enhancement:**
- Automatic calendar tagging
- Auto-update on status changes
- Workflow automation

---

### F. DESIRED FEATURES (From Interview)

**Automation:**
> "Nakikita na magiging malaki ang maitutulong ng automation sa pagpapadali ng pagpoproseso ng mga request."

**Specific Need:**
> "Sa tulong ng automation, mas magiging epektibo kapag ang na-record na request ay automatically mata-tag sa kalendaryo."

**Real-time Alerts:**
> "Ang kalendaryo na aming ginagamit ay mayroong real-time alerts na nagbibigay notification sa M.I.C.E. Team. Isa ito sa mga mahahalagang features ng aplikasyon na lubos na nakakatulong sa galaw ng aming operasyon."

---

### G. REPORTS & FEEDBACK

**Current Reporting (Monthly Utilization Report):**
- Electric consumption
- Areas used and frequency
- Number of people per facility
- Problems encountered
- Solutions implemented/planned

**Report Distribution:**
- Discussed in monthly general assembly

**Our System Enhancement:**
- Automated utilization reports
- Real-time analytics dashboard
- AI-powered insights

---

## 🎯 KEY INSIGHTS FOR OUR SYSTEM

### ✅ VALIDATED NEEDS (Both LGUs Agree)

1. **Real-time Availability** - Both want instant schedule visibility
2. **Conflict Prevention** - Double booking is a critical issue
3. **Automation** - Manual tagging and tracking is time-consuming
4. **Digital Forms** - Speed up documentation process
5. **Reporting** - Automated monthly reports needed
6. **Notifications** - Real-time alerts are valuable

---

### 🔄 PROCESS SIMILARITIES

| **Aspect** | **Caloocan** | **Quezon City** |
|------------|--------------|-----------------|
| Scheduling Tool | Google Sheets + Physical Bulletin | Google Sheets + Calendar |
| Main Challenge | Double booking, miscommunication | Manual tagging, rush programs |
| Desired Feature | Real-time alerts, automation | Automation, calendar auto-tag |
| Reporting | Monthly (manual count) | Monthly (detailed utilization) |
| Scale | ~15/month (4 facilities) | ~300 total/8 months (multiple rooms) |

---

### 💡 INNOVATION OPPORTUNITIES

Based on interview findings, our system provides:

1. **Better than Google Sheets:**
   - ✅ Automatic conflict detection (not just recording)
   - ✅ Multi-party coordination (not just admin view)
   - ✅ Citizen-facing portal (not just internal tool)
   - ✅ AI-powered analytics (not just data storage)

2. **Solves Real Problems:**
   - ❌ Caloocan: Had double bookings → ✅ Our system: Impossible to double book
   - ❌ Both: Manual tagging → ✅ Our system: Auto-tag on status change
   - ❌ Caloocan: Clients book unavailable dates → ✅ Our system: Show only available
   - ❌ Both: Manual reports → ✅ Our system: Automated with AI insights

3. **Adds Professional Features:**
   - Multi-user roles & permissions
   - Audit trail
   - Payment tracking
   - Equipment management
   - Discount system (policy-ready)
   - Mobile responsive

---

## 🎓 HOW TO USE THIS IN DEFENSE

### **INTERVIEW = Problem Validation**
"Our interviews with two LGUs revealed these challenges..."

### **PANEL SPECS = Solution Design**
"To address these problems, we designed a system with..."

### **OUR INNOVATION = Value Addition**
"Unlike their current Google Sheets approach, our system adds..."

---

## 📊 COMPARISON: INTERVIEW vs. PANEL REQUIREMENTS

| **Aspect** | **Interview Reality** | **Panel Requirement** | **Our Implementation** |
|------------|----------------------|----------------------|------------------------|
| **Pricing** | Hourly (Caloocan), TBD (QC) | Per-person | Per-person (scalable) |
| **Discounts** | None | 30% resident + 20% identity | Implemented (policy-ready) |
| **Equipment** | Not mentioned | Chairs, tables, sound | Implemented |
| **Approval** | Single (Dept Head) | 2-gate (Staff + Admin) | 2-gate (more robust) |
| **Payment** | Cash only | Flexible | Cash + future cashless |
| **Technology** | Google Sheets | Web system with AI | Laravel + TensorFlow.js |
| **Access** | In-person only | 24/7 online | Web + mobile portal |

---

## 📝 IMPORTANT NOTES

### **Why Interview ≠ System Specs**

1. **Interview shows CURRENT STATE** (what exists)
2. **Panel specs show DESIRED STATE** (what should exist)
3. **Our system bridges the gap** (innovation + enhancement)

### **Defense Strategy**

✅ **DO SAY:**
- "Interview revealed problems we solve"
- "Current manual process causes double bookings"
- "Our AI adds predictive capabilities they don't have"

❌ **DON'T SAY:**
- "We copied their Google Sheets"
- "We just digitized their manual process"
- "Interview dictated our design"

---

## 🔗 RELATED DOCUMENTS

- `PROJECT_DESIGN_RULES.md` - Panel-approved design specifications
- `DEFENSE_STRATEGY.md` - How to present system in defense
- `FACILITY_SEED_DATA.md` - Real facilities for realistic demos
- `INTERNAL_INTEGRATIONS.md` - System architecture (5 submodules)
- `IMPLEMENTATION_ROADMAP.md` - Development plan

---

**Last Updated:** December 8, 2024  
**Status:** Interview insights documented, system implementation follows panel specs  
**Next Step:** Create defense strategy and facility seed data

