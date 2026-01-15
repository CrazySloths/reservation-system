# EMAIL NOTIFICATIONS SYSTEM

**Created:** December 28, 2025  
**Status:** IMPLEMENTED (Priority 4 - Complete)  
**System Version:** 1.0

---

## OVERVIEW

Comprehensive email notification system to keep citizens informed throughout the entire booking lifecycle. All notifications are queued for async delivery and stored in the database for tracking.

---

## NOTIFICATION TYPES

### 1. Booking Submitted
- **Trigger:** When citizen submits a booking request
- **Recipient:** Citizen
- **Subject:** "Booking Request Received - [Facility Name]"
- **Purpose:** Confirm booking receipt and explain next steps
- **File:** `app/Notifications/BookingSubmitted.php`
- **Template:** `resources/views/emails/booking-submitted.blade.php`

### 2. Staff Verified
- **Trigger:** When staff approves a booking request
- **Recipient:** Citizen
- **Subject:** "Booking Approved! Pay Within 48 Hours"
- **Purpose:** Notify approval and payment deadline
- **File:** `app/Notifications/StaffVerified.php`
- **Template:** `resources/views/emails/staff-verified.blade.php`
- **Special:** Includes 48-hour countdown timer

### 3. Payment Reminder - 24 Hours
- **Trigger:** AUTOMATED - 24 hours before payment deadline
- **Recipient:** Citizen
- **Subject:** "Payment Deadline: 24 Hours Remaining"
- **Purpose:** Remind citizen to pay before expiration
- **File:** `app/Notifications/PaymentReminder24Hours.php`
- **Template:** `resources/views/emails/payment-reminder-24.blade.php`
- **Command:** `php artisan payments:send-reminders` (hourly)

### 4. Payment Reminder - 6 Hours
- **Trigger:** AUTOMATED - 6 hours before payment deadline
- **Recipient:** Citizen
- **Subject:** "URGENT: Payment Deadline in 6 Hours"
- **Purpose:** Final warning before booking expiration
- **File:** `app/Notifications/PaymentReminder6Hours.php`
- **Template:** `resources/views/emails/payment-reminder-6.blade.php`
- **Command:** `php artisan payments:send-reminders` (hourly)
- **Special:** Red urgent styling

### 5. Payment Submitted
- **Trigger:** When citizen submits payment proof
- **Recipient:** Citizen
- **Subject:** "Payment Received - Under Review"
- **Purpose:** Confirm payment receipt and treasurer review timeline
- **File:** `app/Notifications/PaymentSubmitted.php`
- **Template:** `resources/views/emails/payment-submitted.blade.php`

### 6. Payment Verified
- **Trigger:** When treasurer verifies payment
- **Recipient:** Citizen
- **Subject:** "Payment Confirmed! Booking Reserved"
- **Purpose:** Confirm payment and OR issuance
- **File:** `app/Notifications/PaymentVerified.php`
- **Template:** `resources/views/emails/payment-verified.blade.php`
- **Special:** Includes OR number and download link

### 7. Booking Confirmed
- **Trigger:** When admin confirms booking
- **Recipient:** Citizen
- **Subject:** "Your Booking is Confirmed - [Facility Name]"
- **Purpose:** Final confirmation and event reminders
- **File:** `app/Notifications/BookingConfirmed.php`
- **Template:** `resources/views/emails/booking-confirmed.blade.php`
- **Special:** Includes what to bring and facility rules

### 8. Booking Expired
- **Trigger:** AUTOMATED - When payment deadline passes
- **Recipient:** Citizen
- **Subject:** "Booking Expired - Payment Not Received"
- **Purpose:** Notify expiration and offer rebooking
- **File:** `app/Notifications/BookingExpired.php`
- **Template:** `resources/views/emails/booking-expired.blade.php`
- **Command:** `php artisan bookings:expire-unpaid` (hourly)

### 9. Booking Rejected
- **Trigger:** When staff/admin rejects a booking
- **Recipient:** Citizen
- **Subject:** "Booking Request Declined"
- **Purpose:** Explain rejection and next steps
- **File:** `app/Notifications/BookingRejected.php`
- **Template:** `resources/views/emails/booking-rejected.blade.php`
- **Special:** Includes rejection reason

### 10. Payment Rejected
- **Trigger:** When treasurer rejects payment proof
- **Recipient:** Citizen
- **Subject:** "Payment Verification Failed - Resubmission Required"
- **Purpose:** Request correct payment resubmission
- **File:** `app/Notifications/PaymentRejected.php`
- **Template:** `resources/views/emails/payment-rejected.blade.php`
- **Special:** Includes rejection reason and new deadline

---

## EMAIL TEMPLATE DESIGN

### Base Layout
- **File:** `resources/views/emails/layout.blade.php`
- **Features:**
  - LGU branding with logo
  - Responsive design (mobile-friendly)
  - Professional color scheme (LGU green: #0f3d3e)
  - Consistent header and footer
  - No gradients (solid colors only per design rules)
  - No emoticons (design rules compliant)

### Color Coding
- **Success:** Green boxes for positive actions
- **Warning:** Yellow/amber boxes for deadlines
- **Error:** Red boxes for rejections/failures
- **Info:** Blue boxes for general information

### Components
- Info boxes (success, warning, error)
- Countdown timers for urgency
- Call-to-action buttons
- Facility/booking details tables
- Contact information footer

---

## DATABASE SCHEMA

### Notifications Table
```sql
CREATE TABLE `notifications` (
  `id` char(36) NOT NULL PRIMARY KEY,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL
);
```

### Payment Slips - Reminder Tracking
```sql
ALTER TABLE `payment_slips` ADD COLUMN
  `reminder_24h_sent_at` timestamp NULL,
  `reminder_6h_sent_at` timestamp NULL;
```

---

## SCHEDULED COMMANDS

### Payment Reminders
```bash
php artisan payments:send-reminders
```
- **Schedule:** Hourly
- **Purpose:** Send 24h and 6h payment deadline reminders
- **Location:** `app/Console/Commands/SendPaymentReminders.php`
- **Registered in:** `bootstrap/app.php`

### Booking Expiration
```bash
php artisan bookings:expire-unpaid
```
- **Schedule:** Hourly
- **Purpose:** Expire bookings with unpaid deadlines, send expiration email
- **Location:** `app/Console/Commands/ExpireUnpaidBookings.php`
- **Sends:** `BookingExpired` notification

### Booking Completion
```bash
php artisan bookings:complete-finished
```
- **Schedule:** Hourly
- **Purpose:** Mark confirmed bookings as completed after event end time
- **Location:** `app/Console/Commands/CompleteFinishedBookings.php`

---

## INTEGRATION POINTS

### Where Notifications Are Sent

1. **Booking Submitted**
   - `app/Http/Controllers/Citizen/BookingController.php` → `store()` method
   - After booking creation

2. **Staff Verified**
   - `app/Http/Controllers/Staff/BookingVerificationController.php` → `verify()` method
   - After staff approval

3. **Payment Submitted**
   - `app/Http/Controllers/Citizen/PaymentController.php` → `submitCashless()` method
   - After payment proof submission

4. **Payment Verified**
   - `app/Http/Controllers/Treasurer/PaymentVerificationController.php` → `verify()` method
   - After treasurer approval

5. **Booking Confirmed**
   - `app/Http/Controllers/Admin/BookingManagementController.php` → `confirm()` method
   - After admin final confirmation

6. **Booking Rejected**
   - `app/Http/Controllers/Staff/BookingVerificationController.php` → `reject()` method
   - After staff/admin rejection

7. **Payment Rejected**
   - `app/Http/Controllers/Treasurer/PaymentVerificationController.php` → `reject()` method
   - After treasurer rejection

8. **Payment Reminders** (Automated)
   - `app/Console/Commands/SendPaymentReminders.php`
   - Runs hourly via Laravel scheduler

9. **Booking Expired** (Automated)
   - `app/Console/Commands/ExpireUnpaidBookings.php`
   - Runs hourly via Laravel scheduler

---

## MAIL CONFIGURATION

### Required .env Settings
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@lgu.gov.ph
MAIL_FROM_NAME="LGU Facilities"
```

### Testing Configuration (Optional)
```env
MAIL_MAILER=log  # For testing - emails written to storage/logs/laravel.log
```

---

## NOTIFICATION WORKFLOW DIAGRAM

```
CITIZEN BOOKS FACILITY
  ↓
1. BookingSubmitted ✉️
  ↓
STAFF REVIEWS
  ↓
2. StaffVerified ✉️ (48h countdown starts)
  ↓ (24h later)
3. PaymentReminder24Hours ✉️ (automated)
  ↓ (18h later)
4. PaymentReminder6Hours ✉️ (automated)
  ↓
CITIZEN PAYS
  ↓
5. PaymentSubmitted ✉️
  ↓
TREASURER VERIFIES
  ↓
6. PaymentVerified ✉️ (OR issued)
  ↓
ADMIN CONFIRMS
  ↓
7. BookingConfirmed ✉️ (final)

--- OR ALTERNATE PATHS ---

STAFF REJECTS
  ↓
9. BookingRejected ✉️

TREASURER REJECTS PAYMENT
  ↓
10. PaymentRejected ✉️

48H DEADLINE EXPIRES
  ↓
8. BookingExpired ✉️
```

---

## TESTING NOTIFICATIONS

### Manual Testing
```php
// In tinker or test controller
use App\Notifications\BookingSubmitted;

$user = \App\Models\User::find(1);
$booking = DB::connection('facilities_db')->table('bookings')->first();

$user->notify(new BookingSubmitted($booking));
```

### Queue Workers
```bash
# Start queue worker for async notifications
php artisan queue:work

# Or run synchronously for testing
# Set QUEUE_CONNECTION=sync in .env
```

### Test Scheduled Commands
```bash
# Test payment reminders
php artisan payments:send-reminders

# Test booking expiration
php artisan bookings:expire-unpaid

# Test booking completion
php artisan bookings:complete-finished
```

---

## TROUBLESHOOTING

### Notifications Not Sending
1. Check `.env` mail configuration
2. Verify `QUEUE_CONNECTION` is set correctly
3. Run `php artisan queue:work` if using queues
4. Check `storage/logs/laravel.log` for errors

### Reminders Not Working
1. Verify Laravel scheduler is running: `php artisan schedule:work`
2. Check `reminder_24h_sent_at` and `reminder_6h_sent_at` columns exist
3. Verify payment_deadline times are correct in database

### Emails in Spam
1. Use proper email authentication (SPF, DKIM)
2. Use a verified sending domain
3. Avoid spam trigger words in subject lines
4. Consider using a dedicated email service (SendGrid, Mailgun)

---

## FUTURE ENHANCEMENTS

- SMS notifications via Semaphore API
- Push notifications for mobile app
- Email preferences/unsubscribe options
- Notification delivery tracking and analytics
- Multi-language email templates
- Custom email templates per LGU

---

## FILES CREATED

### Notification Classes (10 files)
- `app/Notifications/BookingSubmitted.php`
- `app/Notifications/StaffVerified.php`
- `app/Notifications/PaymentReminder24Hours.php`
- `app/Notifications/PaymentReminder6Hours.php`
- `app/Notifications/PaymentSubmitted.php`
- `app/Notifications/PaymentVerified.php`
- `app/Notifications/BookingConfirmed.php`
- `app/Notifications/BookingExpired.php`
- `app/Notifications/BookingRejected.php`
- `app/Notifications/PaymentRejected.php`

### Email Templates (11 files)
- `resources/views/emails/layout.blade.php` (base layout)
- `resources/views/emails/booking-submitted.blade.php`
- `resources/views/emails/staff-verified.blade.php`
- `resources/views/emails/payment-reminder-24.blade.php`
- `resources/views/emails/payment-reminder-6.blade.php`
- `resources/views/emails/payment-submitted.blade.php`
- `resources/views/emails/payment-verified.blade.php`
- `resources/views/emails/booking-confirmed.blade.php`
- `resources/views/emails/booking-expired.blade.php`
- `resources/views/emails/booking-rejected.blade.php`
- `resources/views/emails/payment-rejected.blade.php`

### Commands
- `app/Console/Commands/SendPaymentReminders.php`

### Migrations
- `database/migrations/xxxx_create_notifications_table.php`
- `database/migrations/2025_12_28_085935_add_reminder_tracking_to_payment_slips_table.php`

### Configuration
- `bootstrap/app.php` (scheduler updated)

---

## NOTES

- All notifications implement `ShouldQueue` for async sending
- Database notifications stored for in-app notification center (future)
- Email templates are mobile-responsive
- Design compliant with PROJECT_DESIGN_RULES.md (no emoticons, no gradients)
- Follows LGU color scheme and branding
- Professional tone and clear call-to-actions

---

**Last Updated:** December 28, 2025  
**Status:** FULLY INTEGRATED & OPERATIONAL  
**Priority:** 4 of 5 (Email Notifications)

## FACEBOOK-STYLE NOTIFICATION BELL

### Features
- Real-time notification bell icon in all user layouts (Citizen, Staff, Treasurer, Admin)
- Red badge showing unread notification count
- Dropdown menu with recent notifications
- Auto-refresh every 30 seconds
- Mark as read functionality
- "View all notifications" page
- Lucide icons (design-compliant)
- Alpine.js powered (no jQuery)

### Files Created
- `resources/views/components/notification-bell.blade.php` - Reusable bell component
- `app/Http/Controllers/NotificationController.php` - API endpoints
- Routes added to `routes/web.php`

### Integration
Added to all 4 main layouts:
- `resources/views/layouts/citizen.blade.php`
- `resources/views/layouts/staff.blade.php`
- `resources/views/layouts/treasurer.blade.php`
- `resources/views/layouts/admin.blade.php`


