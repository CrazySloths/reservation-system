# 🔧 HOW TO DEMOTE SUPER ADMIN TO ADMIN

**When to use:** When EIS Lead Programmer creates the centralized Super Admin account, demote any local super admin users to regular admin role.

---

## ⚠️ IMPORTANT CONTEXT

This system is part of a centralized EIS (Enterprise Information System) with 10 subsystems:
- **1 Super Admin** - Created by EIS Lead Programmer (centralized, can access all 10 subsystems)
- **Multiple Admins** - One per subsystem (operational managers)

Any "Super Admin" created in individual subsystems should be demoted to "Admin" role.

---

## 🎯 QUICK METHOD: Using phpMyAdmin

### **Step 1: Open phpMyAdmin**
```
http://localhost/phpmyadmin
```

### **Step 2: Select Database**
- Click on your database (e.g., `facilities_reservation_db`)
- Click on `users` table

### **Step 3: Find Super Admin Users**
Click "SQL" tab and run:
```sql
SELECT id, username, email, role, subsystem_id 
FROM users 
WHERE role = 'superadmin';
```

### **Step 4: Identify User to Demote**
- Look for users with `subsystem_id = 4` (Public Facilities)
- **DO NOT demote** users with `subsystem_id = NULL` (central EIS Super Admin)

### **Step 5: Demote to Admin**
Run this query (replace `USER_ID` with actual ID):
```sql
UPDATE users 
SET role = 'admin', 
    updated_at = NOW()
WHERE id = [USER_ID] 
  AND subsystem_id = 4;
```

### **Step 6: Verify**
```sql
SELECT id, username, role, subsystem_id 
FROM users 
WHERE id = [USER_ID];
```
Should now show `role = 'admin'`

---

## 🔧 ALTERNATIVE: Using MySQL Command Line

```bash
# Connect to database
mysql -u root -p

# Select database
USE facilities_reservation_db;

# Check existing super admins
SELECT id, username, email, role, subsystem_id 
FROM users 
WHERE role = 'superadmin';

# Demote specific user
UPDATE users 
SET role = 'admin', 
    updated_at = NOW()
WHERE id = [USER_ID] 
  AND subsystem_id = 4;

# Verify
SELECT id, username, role 
FROM users 
WHERE id = [USER_ID];

# Exit
exit
```

---

## ⚠️ SAFETY CHECKS

### **Before Demoting:**

1. **Create Backup:**
```sql
-- Backup users table
CREATE TABLE users_backup AS SELECT * FROM users;
```

2. **Check subsystem_id:**
```sql
-- Only demote users with subsystem_id = 4 (Public Facilities)
-- Never demote users with subsystem_id = NULL (EIS Super Admin)
SELECT id, username, subsystem_id, role 
FROM users 
WHERE role = 'superadmin';
```

3. **Don't demote yourself:**
- Make sure you're not demoting your currently logged-in account
- Check which user you're authenticated as

---

## ✅ WHAT CHANGES

### **Before Demotion:**
```
Role: superadmin
Access: Full system access (all subsystems)
Can: Configure system, manage all users, access everything
```

### **After Demotion:**
```
Role: admin
Access: Public Facilities subsystem only
Can: Manage bookings, coordinate events, create reports
Cannot: System configuration, manage suppliers, update prices
```

### **User Can Still Do:**
- ✅ Log in normally
- ✅ Receive facility requests
- ✅ Coordinate with organizers
- ✅ Manage day-to-day operations
- ✅ Create liquidation and transparency reports

### **User Cannot Do:**
- ❌ Access other subsystems
- ❌ Configure system settings
- ❌ Add/edit suppliers
- ❌ Update product prices
- ❌ Manage users across all subsystems

---

## 📝 WHEN TO DEMOTE

**Timing:**
- After EIS Lead Programmer creates centralized Super Admin account
- Before going live with production system
- During system cleanup/role clarification

**Who Should Remain Super Admin:**
- Only the centralized EIS Super Admin account (created by EIS Lead)
- No one else

**Who Should Be Admin:**
- Operations managers for each subsystem
- People doing day-to-day coordination work

---

## 💡 COMMUNICATION

**Message to demoted user:**
```
"We're clarifying roles in the EIS system. Your role is being 
updated from 'Super Admin' to 'Admin' to reflect that you're 
the Operations Manager for Public Facilities.

This doesn't change your daily work - you'll continue managing 
bookings, coordinating events, and creating reports. It just 
means the technical system configuration is now centrally 
managed by the EIS Lead Programmer.

You'll have all the access you need for your operational work!"
```

---

## 🎯 QUICK REFERENCE CARD

```
┌─────────────────────────────────────────────┐
│ DEMOTE SUPER ADMIN - QUICK STEPS            │
├─────────────────────────────────────────────┤
│                                             │
│ 1. phpMyAdmin → SQL Tab                     │
│                                             │
│ 2. Find user:                               │
│    SELECT * FROM users                      │
│    WHERE role='superadmin';                 │
│                                             │
│ 3. Demote:                                  │
│    UPDATE users                             │
│    SET role='admin'                         │
│    WHERE id=[ID] AND subsystem_id=4;        │
│                                             │
│ 4. Verify:                                  │
│    SELECT id, username, role FROM users     │
│    WHERE id=[ID];                           │
│                                             │
│ Done! ✅                                     │
└─────────────────────────────────────────────┘
```

---

**Last Updated:** December 3, 2025  
**Status:** Save for implementation phase (after brainstorming)

