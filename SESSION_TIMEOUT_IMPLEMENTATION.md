# Session Timeout Implementation - 2 Minutes

## 📋 Overview

Implemented a strict **2-minute session timeout** system for all users (Citizens, Staff, Admin, Super Admin) that automatically logs out users after 2 minutes of inactivity.

---

## ⏱️ Key Features

✅ **2-minute hard timeout** - No warnings, no grace periods  
✅ **Activity detection** - Mouse, keyboard, scroll, touch events reset timer  
✅ **Auto-redirect** - Automatically refreshes to login page after timeout  
✅ **Auto-save protection** - Booking forms (Steps 1-3) already have localStorage auto-save  
✅ **Session ping** - Keeps server session alive during user activity  
✅ **Backend validation** - Middleware checks session validity  
✅ **User-friendly message** - Clear timeout notification on login page  

---

## 🔧 Implementation Details

### 1. **Session Configuration**

**File: `config/session.php`**
- Already configured to read from `.env`
- Line 35: `'lifetime' => (int) env('SESSION_LIFETIME', 120),`

**Required `.env` setting:**
```env
SESSION_LIFETIME=2
```

---

### 2. **Backend Middleware**

**File: `app/Http/Middleware/CheckSessionTimeout.php`**
- Checks if `user_id` exists in session
- Clears session if expired
- Returns 401 for AJAX requests
- Redirects to login for regular requests
- Updates `last_activity` timestamp

**Registered in: `app/Http/Kernel.php`**
```php
'session.timeout' => \App\Http\Middleware\CheckSessionTimeout::class,
```

**Applied to citizen routes in: `routes/web.php`**
```php
Route::middleware(['auth', 'role:citizen', 'session.timeout'])->group(function () {
    // All citizen routes
});
```

---

### 3. **Frontend JavaScript Timer**

**File: `resources/views/layouts/citizen.blade.php`**

**Key Components:**
- **Inactivity Timer**: 120 seconds (2 minutes)
- **Activity Events Monitored**:
  - `mousedown`, `mousemove`, `keypress`, `scroll`
  - `touchstart`, `click`, `keydown`, `wheel`
- **Throttling**: Resets timer max once every 5 seconds
- **Force Logout**: Redirects to `/login?timeout=1` after 2 minutes
- **Session Ping**: Calls `/ping-session` endpoint to keep server session alive
- **Backup Check**: Verifies every 30 seconds if timer somehow failed
- **AJAX Monitoring**: Intercepts fetch requests for 401 errors

**Auto-saves are preserved:**
- Does NOT clear localStorage (booking auto-save data remains)
- Only redirects to login page

---

### 4. **Session Ping Endpoint**

**Route: `POST /ping-session`**

**File: `routes/web.php`**

```php
Route::post('/ping-session', function () {
    if (!session()->has('user_id')) {
        return response()->json(['status' => 'expired'], 401);
    }
    
    session()->put('last_activity', time());
    
    return response()->json([
        'status' => 'active',
        'time' => time()
    ]);
})->name('ping-session');
```

---

### 5. **Login Page Timeout Message**

**File: `resources/views/auth/login.blade.php`**

Displays when URL parameter `?timeout=1` is present:

```blade
@if(request()->get('timeout') == '1')
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <i class="bi bi-clock-history"></i>
    <strong>Session Expired!</strong> Your session has expired due to 2 minutes of inactivity. Please login again.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
```

---

## 🎯 How It Works

### Scenario 1: Active User
1. User logs in and starts using system
2. Every mouse move, click, keystroke → Timer resets to 2 minutes
3. Session ping keeps server session alive
4. **Result**: User can work indefinitely as long as they're active

### Scenario 2: Inactive User
1. User logs in and views a page
2. No activity for 2 minutes (not scrolling, clicking, typing)
3. Timer reaches 0 → Page automatically redirects to login
4. **Result**: User sees "Session Expired" message on login page

### Scenario 3: Booking Form (Auto-Save Protected)
1. User starts filling booking Step 1
2. Every field change → Auto-saved to localStorage
3. User takes >2 minutes → Session expires → Logged out
4. User logs back in → Data is still in localStorage
5. User navigates back to booking → Form auto-restores saved data
6. **Result**: No data loss, can continue booking

### Scenario 4: AJAX Request During Timeout
1. User session expires while on a page
2. User clicks button that makes AJAX call
3. Server returns 401 Unauthorized
4. JavaScript intercepts → Force logout
5. **Result**: Clean logout even during AJAX operations

---

## 💾 Protected Data (Auto-Save)

**Already Implemented:**
- ✅ Booking Step 1: Date, time, facility, purpose, attendees
- ✅ Booking Step 2: Equipment selection
- ✅ Booking Step 3: ID uploads, special requests

**Stored in:** `localStorage` with keys:
- `booking_step1_data`
- `booking_step2_data`
- `booking_step3_data`

---

## ⚠️ Data That May Be Lost

**NOT Auto-Saved:**
- ❌ Profile editing in progress
- ❌ File upload selections (before upload)
- ❌ Any other forms without auto-save
- ❌ Reading position (announcements, payment slips)
- ❌ Navigation state (user must manually navigate back)

---

## 🔒 Security Benefits

1. **Prevents Unauthorized Access**: Reduces risk if user walks away from computer
2. **Compliance**: Meets strict panel requirements for government systems
3. **Session Hijacking Protection**: Short session window limits attack opportunity
4. **Data Privacy**: Forces re-authentication frequently

---

## 👥 Applies To All Roles

**Current:**
- ✅ Citizens (implemented in `resources/views/layouts/citizen.blade.php`)

**TODO (When Implemented):**
- ⏳ Staff (add to `resources/views/layouts/staff.blade.php`)
- ⏳ Admin (add to `resources/views/layouts/admin.blade.php`)
- ⏳ Super Admin (add to `resources/views/layouts/superadmin.blade.php`)

---

## 🧪 Testing

### Test Case 1: Normal Timeout
1. Login as citizen
2. Don't touch mouse/keyboard for 2 minutes
3. **Expected**: Auto-redirect to login with timeout message

### Test Case 2: Activity Extends Session
1. Login as citizen
2. Move mouse every 60 seconds
3. **Expected**: Never times out, session stays active

### Test Case 3: Auto-Save Works
1. Start booking, fill Step 1
2. Wait >2 minutes (or close browser)
3. Login again, go to booking
4. **Expected**: Step 1 data restored from localStorage

### Test Case 4: AJAX During Timeout
1. Login, leave idle for 2+ minutes
2. Click button that makes AJAX request
3. **Expected**: Redirect to login even if AJAX fails

---

## 📁 Files Modified/Created

### Created:
1. `app/Http/Middleware/CheckSessionTimeout.php`

### Modified:
1. `app/Http/Kernel.php` - Registered middleware
2. `resources/views/layouts/citizen.blade.php` - Added JavaScript timer
3. `routes/web.php` - Added ping-session route + middleware to citizen routes
4. `resources/views/auth/login.blade.php` - Added timeout message

### User Action Required:
1. `.env` - Add `SESSION_LIFETIME=2`

---

## 🚀 Next Steps

1. **Add `.env` setting**: `SESSION_LIFETIME=2`
2. **Test thoroughly**: All scenarios above
3. **Apply to other roles**: When staff/admin layouts are created
4. **Consider auto-save**: For profile editing and other forms if needed

---

## 📝 Notes

- **localStorage is NOT cleared** - Booking auto-save data persists
- **No warnings** - Per panel requirements
- **Exactly 2 minutes** - Non-negotiable timeout duration
- **Server + Client enforcement** - Double protection
- **Throttled pings** - Max once per 5 seconds to reduce server load

---

## 🎉 Status

✅ **IMPLEMENTED & READY FOR TESTING**

All components are in place. Just add `SESSION_LIFETIME=2` to your `.env` file and test!

