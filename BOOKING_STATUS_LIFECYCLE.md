# 📋 Booking Status Lifecycle Guide

## Overview
This document explains how booking statuses automatically change throughout the reservation process.

---

## 🔄 Status Flow Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                     BOOKING LIFECYCLE                        │
└─────────────────────────────────────────────────────────────┘

pending
  ↓ (Staff reviews & approves)
staff_verified + Payment Slip Generated (48-hour deadline)
  ↓ (Citizen pays via cashless/OTC)
staff_verified (with payment proof submitted)
  ↓ (Treasurer verifies payment)
paid + Official Receipt Generated
  ↓ (Admin final confirmation)
confirmed
  ↓ (Event end time passes - AUTO)
completed ✅
```

---

## 📍 Status Descriptions

### 1. **pending**
- Citizen just submitted the booking
- Waiting for staff document verification
- **Action Required:** Staff review

### 2. **staff_verified**
- Staff approved the booking
- Payment slip auto-generated with 48-hour deadline
- **Action Required:** Citizen must pay within 48 hours

### 3. **paid**
- Treasurer verified the payment
- Official Receipt auto-generated
- **Action Required:** Admin final confirmation

### 4. **confirmed**
- Admin gave final approval
- Booking is 100% locked in
- Event is ready to proceed
- **Auto-transition:** After event end time → `completed`

### 5. **completed**
- Event has finished (end_time passed)
- Booking moved to "Booking History"
- **Auto-transition:** Done by scheduled task every hour

### 6. **expired**
- Citizen didn't pay within 48 hours
- Booking automatically cancelled
- **Auto-transition:** Done by scheduled task every hour

### 7. **cancelled** / **rejected**
- Manually cancelled by staff/admin or citizen
- Permanently closed

---

## ⚙️ Automated Tasks

### 1. **Auto-Expire Unpaid Bookings**
**Command:** `php artisan bookings:expire-unpaid`  
**Schedule:** Every hour  
**What it does:**
- Finds bookings with status `staff_verified`
- Checks if 48 hours passed since `staff_verified_at`
- Changes status to `expired`
- Releases reserved time slot and equipment

**Location:** `app/Console/Commands/ExpireUnpaidBookings.php`

---

### 2. **Auto-Complete Finished Bookings** ✨ NEW
**Command:** `php artisan bookings:complete-finished`  
**Schedule:** Every hour  
**What it does:**
- Finds bookings with status `confirmed`
- Checks if current time > `end_time`
- Changes status to `completed`
- Moves booking to "Booking History"

**Location:** `app/Console/Commands/CompleteFinishedBookings.php`

---

## 📂 Where Bookings Appear

### **"My Reservations"** (Citizen Portal)
Shows bookings with status:
- `pending`
- `staff_verified`
- `paid`
- `confirmed`

**Excludes:**
- `completed`
- `expired`
- `cancelled`
- `rejected`

---

### **"Booking History"** (Citizen Portal)
Shows bookings with status:
- ✅ `completed` (event finished)
- ⏰ `expired` (payment deadline passed)
- ❌ `cancelled` (manually cancelled)
- ❌ `rejected` (staff/admin rejected)

---

## 🔧 Manual Commands

### Test Auto-Complete Now
```bash
php artisan bookings:complete-finished
```

### Test Auto-Expire Now
```bash
php artisan bookings:expire-unpaid
```

### Check Scheduled Tasks
```bash
php artisan schedule:list
```

### Run Scheduler (in production)
```bash
php artisan schedule:run
```

Or add to crontab:
```cron
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🎯 Key Points

1. **Confirmed ≠ Completed**
   - `confirmed` = Admin approved, event upcoming
   - `completed` = Event finished, moved to history

2. **Automatic Transitions**
   - `staff_verified` → `expired` (after 48 hours no payment)
   - `confirmed` → `completed` (after event end time)

3. **Manual Transitions**
   - `pending` → `staff_verified` (Staff approval)
   - `staff_verified` → `paid` (Treasurer verification)
   - `paid` → `confirmed` (Admin confirmation)

4. **Scheduler Setup**
   - Both commands run every hour
   - Configured in `bootstrap/app.php`
   - Requires cron job in production

---

## 🐛 Troubleshooting

### Booking Not in History?
**Problem:** Confirmed booking not showing in "Booking History"  
**Solution:** Run `php artisan bookings:complete-finished` manually

### Payment Deadline Not Expiring?
**Problem:** Unpaid bookings still active after 48 hours  
**Solution:** Run `php artisan bookings:expire-unpaid` manually

### Scheduler Not Running?
**Problem:** No automatic transitions happening  
**Solution:** 
1. Check if cron job is set up (production)
2. Run `php artisan schedule:list` to verify commands are registered
3. Run `php artisan schedule:run` manually to test

---

## 📝 Database Schema

### Relevant Columns in `bookings` Table
```sql
status               VARCHAR(50)  -- Current booking status
start_time           DATETIME     -- Event start time
end_time             DATETIME     -- Event end time (used for auto-complete)
staff_verified_at    DATETIME     -- Used for 48-hour deadline
paid_at              DATETIME     -- Payment verification time
admin_approved_at    DATETIME     -- Final confirmation time
updated_at           DATETIME     -- Last status change
```

---

**Last Updated:** December 28, 2025  
**Version:** 1.0  

