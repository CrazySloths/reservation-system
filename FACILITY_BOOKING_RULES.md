# 📋 Facility Booking Rules & Constraints

**Document Version:** 1.0  
**Date:** December 28, 2025  
**System:** Public Facilities Reservation System  
**Status:** MANDATORY - All developers must follow

---

## ⚠️ CRITICAL: Business Rules

These rules are **NON-NEGOTIABLE** and must be enforced in all booking flows.

---

## ⏱️ 1. BOOKING DURATION

### **Rule: 3 Hours OR 5 Hours Only**

**Base Duration:**
- Standard: **3 hours**
- This is the default booking duration

**Extension:**
- Can extend by **exactly 2 hours** - ONE TIME ONLY
- Results in **5 hours total**
- NO multiple extensions allowed

**Valid Durations:**
- ✅ 3 hours (standard)
- ✅ 5 hours (standard + one 2-hour extension)

**Invalid Durations:**
- ❌ 1 hour (below minimum)
- ❌ 2 hours (below minimum)
- ❌ 4 hours (not valid - must be 3 or 5)
- ❌ 6 hours (exceeds one extension)
- ❌ 7 hours (exceeds one extension)
- ❌ 8+ hours (exceeds one extension)
- ❌ Any duration other than 3 or 5 hours

**Formula:** `Duration = 3 OR 5 hours only`

**Enforcement Points:**
1. **Frontend Validation** (`step1-select-datetime.blade.php`)
   - Minimum 3 hours required
   - If > 3 hours, extension must be multiple of 2
   - Error message if duration invalid

2. **Backend Validation** (`BookingController.php`)
   - Verify duration follows 3 + (n × 2) rule
   - Reject submissions with invalid durations

3. **Auto-Calculation**
   - When start time is selected, auto-set end time to +3 hours (default)
   - Citizen can manually extend by 2-hour increments

---

## 🕐 2. OPERATING HOURS

**Facility Hours:**
- **Opens:** 8:00 AM
- **Closes:** 10:00 PM

**Booking Constraints:**
- Earliest start: 8:00 AM (earliest end: 10:00 AM)
- Latest start: 8:00 PM (ends exactly at 10:00 PM)
- No bookings before 8:00 AM
- No bookings ending after 10:00 PM

---

## 📅 3. ADVANCE BOOKING REQUIREMENT

**Rule: Minimum 7 Business Days Advance**

**Rationale:**
- Allows staff time to verify documents
- Ensures adequate preparation time
- Prevents last-minute bookings

**Implementation:**
- System automatically blocks dates < 7 business days from today
- Calendar shows blocked dates as disabled
- Error message if citizen attempts to select invalid date

---

## 🔄 4. BUFFER PERIOD BETWEEN BOOKINGS

### **Rule: 2-Hour Buffer After Each Booking**

**Purpose:**
- **Cleanup:** Clear trash, reset equipment, restore setup
- **Inspection:** Check for damage, document condition
- **Preparation:** Set up for next event

**Implementation:**
- Booking 1: 8:00 AM - 10:00 AM
- Buffer: 10:00 AM - 12:00 PM (facility unavailable)
- Next available: 12:00 PM

**Conflict Detection:**
- System checks if requested time overlaps with any booking + its 2-hour buffer
- Suggests next available 2-hour slot after buffer

**Source:** `COMMUNITY_INFRASTRUCTURE_MAINTENANCE_INTEGRATION_FEATURES.md`

---

## 🛠️ 5. EQUIPMENT AVAILABILITY (ENTIRE DAY - ALL FACILITIES)

### **Rule: Equipment Used is Unavailable for the Entire Day Across ALL Facilities**

**Critical Constraint:**
- Equipment is SHARED across all facilities (LED TVs, projectors, chairs, etc.)
- If equipment is used in ANY booking on a specific date, it becomes unavailable for ALL other bookings on that SAME DATE at ANY FACILITY
- Equipment cannot be rotated between bookings on the same day
- Example: If Katipunan Hall books 3 LED TVs on January 5, only 3 LED TVs remain available for Buena Park on January 5

**Rationale:**
1. **Deep Cleaning Required:** Equipment needs thorough sanitization between events
2. **Inspection Time:** Staff must check for damage/wear after each use
3. **Maintenance Window:** Equipment may need repairs or adjustments
4. **Quality Assurance:** Ensures equipment is in pristine condition for each event
5. **No Rush Service:** Staff needs adequate time between bookings

**Example Scenario:**
```
Facility: Bulwagan Katipunan
Date: January 5, 2026

Booking 1: 8:00 AM - 11:00 AM
- Uses: 100 White Monobloc Chairs
- Uses: 2 LED TVs

Booking 2: 2:00 PM - 5:00 PM (different time, same date)
- Available Chairs: Total - 100 = (reduced by 100)
- Available LED TVs: Total - 2 = (reduced by 2)
```

**Implementation:**
- Check ALL bookings for the same facility on the same date (regardless of time)
- Sum up equipment quantities used in those bookings
- Reduce available quantity: `available_now = total_quantity - used_on_same_date`
- Hide equipment with zero availability

**Enforcement:**
- Backend: `BookingController.php` - step2() method
- Frontend: Shows adjusted `quantity_available_now`

---

**Per Facility:**
- Each facility has `capacity_min` and `capacity_max`
- Booking must specify `expected_attendees`
- `capacity_min ≤ expected_attendees ≤ capacity_max`

**Validation:**
- Enforce min/max in form
- Show capacity range clearly
- Error if attendees out of range

---

## 💰 7. PRICING MODEL

### **Rule: Per-Person Pricing**

**Formula:**
```
Total Amount = expected_attendees × facility.per_person_rate
```

**NOT Hourly:**
- Duration is fixed at 2 hours
- Price depends ONLY on attendee count
- No hourly rate multiplier

**Example:**
```
Facility: Sports Complex
Per-Person Rate: ₱40
Expected Attendees: 100

Calculation:
100 × ₱40 = ₱4,000

Duration: 2 hours (fixed)
Total: ₱4,000
```

---

## 📝 8. DOCUMENT REQUIREMENTS

**Mandatory Uploads:**
1. Valid Government ID
2. Letter of Intent/Event Proposal
3. (Optional) Other supporting documents

**Validation:**
- At least 2 documents required
- File types: PDF, JPG, PNG
- Max size: 5MB per file

---

## 🚫 9. CONFLICT RULES

**No Double-Booking:**
- Same facility cannot have overlapping bookings
- Must account for 2-hour buffer

**Status-Based Conflicts:**
- Check bookings with status:
  - `pending`
  - `staff_verified`
  - `reserved`
  - `payment_pending`
  - `confirmed`
  - `paid`

**Ignore for Conflicts:**
- `completed`
- `expired`
- `cancelled`
- `rejected`

---

## ⏰ 10. PAYMENT DEADLINE

**Rule: 48 Hours After Staff Verification**

**Process:**
1. Citizen submits booking → `pending`
2. Staff verifies documents → `staff_verified`
3. Payment slip generated with 48-hour deadline
4. If not paid within 48 hours → Auto-expires
5. If paid within 48 hours → `paid` → Admin confirms → `confirmed`

**Auto-Expiration:**
- Command: `php artisan bookings:expire-unpaid`
- Runs: Every hour (cron job)

---

## ✅ 11. STATUS LIFECYCLE

**Progression:**
```
pending
  ↓ (Staff verifies docs)
staff_verified + Payment Slip Generated
  ↓ (Citizen pays within 48h)
paid
  ↓ (Admin final confirmation)
confirmed
  ↓ (Event end time passes)
completed
```

**See:** `BOOKING_STATUS_LIFECYCLE.md` for full details

---

## 🎯 12. VALIDATION SUMMARY

### **Frontend Checks:**
- ✅ Duration = exactly 2 hours
- ✅ Start time ≥ 8:00 AM
- ✅ End time ≤ 10:00 PM
- ✅ Date ≥ 7 business days from now
- ✅ Attendees within facility capacity
- ✅ No conflicts detected
- ✅ Required documents uploaded

### **Backend Checks:**
- ✅ Re-validate all frontend checks
- ✅ Verify facility exists and is active
- ✅ Check for schedule conflicts (including buffer)
- ✅ Validate equipment availability
- ✅ Calculate correct pricing

---

## 🔧 13. IMPLEMENTATION CHECKLIST

**Time Validation:**
- [ ] Auto-set end time to start time + 2 hours
- [ ] Validate duration = 2 hours on end time change
- [ ] Show error modal if duration ≠ 2 hours
- [ ] Block manual end time selection (or validate strictly)

**Buffer Time:**
- [ ] Add 2-hour buffer to conflict detection
- [ ] Show buffer period in conflict messages
- [ ] Calculate available slots after buffer

**Suggestion System:**
- [ ] Suggest 2-hour slots only (not entire gap)
- [ ] Account for buffer when finding gaps
- [ ] Show clear time ranges in suggestions

---

## 📚 14. RELATED DOCUMENTATION

- `PROJECT_DESIGN_RULES.md` - UI/UX standards
- `BOOKING_STATUS_LIFECYCLE.md` - Status flow
- `COMMUNITY_INFRASTRUCTURE_MAINTENANCE_INTEGRATION_FEATURES.md` - Buffer period rationale
- `SMART_TIME_SLOT_SUGGESTIONS.md` - Availability checking
- `DEFENSE_STRATEGY.md` - Panel requirements & justifications

---

## 🎓 15. DEFENSE TALKING POINTS

### **Q: Why 3 hours minimum?**
**A:** "A 3-hour booking duration provides:
- Adequate time for event setup, execution, and breakdown
- Predictable scheduling for facility management
- Fair access - allows reasonable event time while serving more citizens
- Aligns with typical community event durations"

### **Q: Why only 2-hour extensions?**
**A:** "Fixed 2-hour extension blocks ensure:
- Consistent time slot management
- Simplified scheduling and conflict detection
- Prevents arbitrary time requests that complicate operations
- Maintains fairness in high-demand facility access"

### **Q: What if citizens need more or less time?**
**A:** "Citizens can:
- Use the minimum 3-hour slot for shorter events
- Extend by 2-hour blocks: 5, 7, 9 hours for longer events
- Choose appropriate facility size based on event needs
- Request special accommodation through direct LGU contact for exceptional cases (weddings, city events)"

---

**Last Updated:** December 28, 2025  
**Version:** 1.0  
**Author:** Development Team  

