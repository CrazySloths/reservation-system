# 🏛️ City Event Override System - Feature Documentation

**Feature:** City Event Override with Citizen Choice  
**Created:** January 4, 2026  
**Status:** ✅ Fully Implemented

---

## 📋 Overview

This feature allows **Admins** to schedule government events (city events, emergencies, maintenance) that may conflict with existing citizen bookings. When conflicts occur, affected citizens are given the **democratic choice** to either:

1. **🔄 Reschedule** their booking to another date (no extra charge)
2. **💰 Request a full refund** (processed within 7 business days)

If a citizen doesn't respond by the deadline (7 days), the system **automatically refunds** their booking.

---

## 🎯 Implementation Summary

### ✅ **What Was Built:**

#### **1. Database Tables**
- ✅ `city_events` - Stores government events (facilities_db)
- ✅ `booking_conflicts` - Tracks conflicts and citizen choices (facilities_db)

#### **2. Eloquent Models**
- ✅ `CityEvent` - Manages city events with conflict detection
- ✅ `BookingConflict` - Handles conflict resolution logic

#### **3. Controllers**
- ✅ `Admin\CityEventController` - Full CRUD for city events
  - Create, edit, delete city events
  - Preview conflicting bookings before creation
  - View affected bookings and resolution status
- ✅ `Citizen\BookingConflictController` - Conflict resolution
  - View all conflicts
  - Choose between reschedule or refund
  - Availability checking for rescheduling

#### **4. Views (Following PROJECT_DESIGN_RULES.md)**
- ✅ Admin City Events Management
  - `admin/city-events/index.blade.php` - List with filters, search, pagination
  - `admin/city-events/create.blade.php` - Create with conflict preview
  - Real-time conflict detection (AJAX)
- ✅ Citizen Conflict Resolution
  - `citizen/conflicts/index.blade.php` - View all conflicts
  - `citizen/conflicts/show.blade.php` - Resolve with reschedule/refund choice

#### **5. Scheduled Command**
- ✅ `ProcessExpiredConflicts` - Auto-refunds expired conflicts
  - Runs automatically via Laravel scheduler
  - Command: `php artisan conflicts:process-expired`

#### **6. Routes**
- ✅ Admin routes: `/admin/city-events/*`
- ✅ Citizen routes: `/citizen/conflicts/*`

#### **7. Sidebar Menus**
- ✅ Admin sidebar: "City Events" link added to Booking Management
- ✅ Citizen sidebar: "Booking Conflicts" link added to Bookings

---

## 🔄 How It Works

### **Admin Workflow:**

```
1. Admin creates city event
   ↓
2. System detects conflicting bookings
   ↓
3. Admin previews affected citizens
   ↓
4. Admin confirms creation
   ↓
5. System creates conflict records
   ↓
6. Citizens receive notifications (TODO)
```

### **Citizen Workflow:**

```
1. Citizen receives notification (TODO)
   ↓
2. Citizen views conflict details
   ↓
3. Citizen chooses:
   - Reschedule (pick new date)
   - Request Refund
   ↓
4. System processes choice
   ↓
5. Confirmation sent to citizen
```

### **Auto-Refund Workflow:**

```
1. Conflict created with 7-day deadline
   ↓
2. Citizen doesn't respond
   ↓
3. Deadline passes
   ↓
4. Scheduled command runs (daily)
   ↓
5. Auto-refund processed
   ↓
6. Notification sent (TODO)
```

---

## 🎨 Design Compliance

All views follow **PROJECT_DESIGN_RULES.md**:

✅ **Golden Ratio** - Typography and spacing  
✅ **Lucide Icons** - All icons (no emojis)  
✅ **Philippine Peso (₱)** - No dollar signs  
✅ **SweetAlert2** - All alerts are modal  
✅ **Poppins Font** - Applied everywhere  
✅ **LGU Color Scheme** - Proper colors used  
✅ **Responsive Design** - Mobile-friendly  
✅ **Soft Deletes** - No permanent deletion  

---

## 📊 Database Schema

### **`city_events` Table:**

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| facility_id | bigint | FK to facilities |
| start_time | datetime | Event start |
| end_time | datetime | Event end |
| event_title | string | Event name |
| event_description | text | Event details |
| event_type | enum | government/emergency/maintenance |
| created_by | bigint | Admin user ID |
| status | enum | scheduled/ongoing/completed/cancelled |
| affected_bookings_count | int | Number of conflicts |
| created_at | timestamp | |
| updated_at | timestamp | |
| deleted_at | timestamp | Soft delete |

### **`booking_conflicts` Table:**

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| booking_id | bigint | FK to bookings |
| city_event_id | bigint | FK to city_events |
| status | enum | pending/resolved |
| citizen_choice | enum | reschedule/refund/no_response |
| response_deadline | datetime | 7 days from creation |
| responded_at | datetime | When citizen responded |
| resolved_at | datetime | When resolved |
| new_booking_id | bigint | If rescheduled |
| admin_notes | text | Admin comments |
| created_at | timestamp | |
| updated_at | timestamp | |
| deleted_at | timestamp | Soft delete |

---

## 🚀 How to Use

### **For Admins:**

1. Navigate to **Booking Management → City Events**
2. Click **"Create City Event"**
3. Fill in:
   - Event Title (e.g., "Annual City Anniversary")
   - Event Description
   - Event Type (Government/Emergency/Maintenance)
   - Facility
   - Start & End Date/Time
4. System shows preview of conflicting bookings
5. Confirm creation
6. View conflicts in the details page

### **For Citizens:**

1. Navigate to **Bookings → Booking Conflicts**
2. See all pending conflicts
3. Click **"Resolve"** on a conflict
4. Choose:
   - **Reschedule:** Select new date/time
   - **Refund:** Request full refund
5. Confirm choice
6. Receive confirmation

---

## ⚙️ Configuration

### **Schedule the Auto-Refund Command:**

Add to `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Process expired conflicts daily at midnight
    $schedule->command('conflicts:process-expired')
             ->daily()
             ->at('00:00');
}
```

### **Manual Command Execution:**

```bash
php artisan conflicts:process-expired
```

---

## 📋 TODO: Notification System (Future Enhancement)

The notification system is marked as **pending** for future implementation. When ready:

### **1. Email Notifications:**
- Citizen: New conflict created
- Citizen: Conflict resolved confirmation
- Citizen: Auto-refund notification
- Admin: Conflict resolution summary

### **2. In-App Notifications:**
- Badge counter on "Booking Conflicts" menu item
- Real-time notification dropdown
- Mark as read functionality

### **3. SMS Notifications (Optional):**
- High-priority conflicts
- Deadline reminders (24 hours before)

---

## 🔒 Security & Permissions

✅ **Admin** - Can create, edit, delete city events  
✅ **Citizen** - Can only view and resolve their own conflicts  
✅ **CSRF Protection** - All forms protected  
✅ **Route Middleware** - Authentication required  
✅ **Authorization** - Ownership verification  

---

## 📈 Testing Checklist

### **Admin Tests:**
- [ ] Create city event with no conflicts
- [ ] Create city event with conflicts (preview shown)
- [ ] Edit scheduled city event
- [ ] Delete city event (with/without unresolved conflicts)
- [ ] View conflict resolution status

### **Citizen Tests:**
- [ ] View conflict list (empty state & with conflicts)
- [ ] Choose reschedule (valid date)
- [ ] Choose reschedule (conflicting date - should error)
- [ ] Choose refund
- [ ] Try to resolve after deadline (should error)
- [ ] Try to access another citizen's conflict (should 403)

### **Scheduled Command Tests:**
- [ ] Run command with no expired conflicts
- [ ] Run command with expired conflicts
- [ ] Verify auto-refund applied
- [ ] Check error handling

---

## 🎯 Benefits

### **For Citizens:**
✅ **Democratic** - They choose what happens to their booking  
✅ **Fair** - Full refund guaranteed  
✅ **Flexible** - Free rescheduling option  
✅ **Transparent** - Clear deadlines and process  

### **For Admins:**
✅ **Efficient** - Automated conflict detection  
✅ **Organized** - Track all resolutions in one place  
✅ **Professional** - Better than manual phone calls  
✅ **Accountable** - Full audit trail  

### **For LGU:**
✅ **Trust** - Citizens feel respected  
✅ **Compliance** - Proper government protocols  
✅ **Scalable** - Works for any number of conflicts  
✅ **Modern** - Digital transformation of government services  

---

## 📞 Support

For questions or issues:
- Refer to **PROJECT_DESIGN_RULES.md** for design standards
- Refer to **ARCHITECTURE.md** for system architecture
- Check **IMPLEMENTATION_ROADMAP.md** for development phases

---

**Last Updated:** January 4, 2026  
**Version:** 1.0  
**Status:** 🚀 Ready for Testing

---

*This feature demonstrates professional government service delivery with citizen-first design principles.*

