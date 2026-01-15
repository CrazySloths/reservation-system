# 💾 Database Setup Guide

## 📋 Database Names for LGU1 Public Facilities

This project uses **2 separate MySQL databases**:

1. **`lgu1_auth`** - Authentication & User Management
2. **`lgu1_facilities`** - Facilities & Booking Data

---

## 🚀 Step 1: Create Databases in phpMyAdmin

### Open phpMyAdmin:
1. Start Laragon
2. Click **"Database"** button in Laragon
3. Or visit: http://localhost/phpmyadmin

### Create Database 1: `lgu1_auth`

```sql
CREATE DATABASE lgu1_auth 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;
```

**Or via phpMyAdmin UI:**
1. Click **"New"** in the left sidebar
2. Database name: `lgu1_auth`
3. Collation: `utf8mb4_unicode_ci`
4. Click **"Create"**

### Create Database 2: `lgu1_facilities`

```sql
CREATE DATABASE lgu1_facilities 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;
```

**Or via phpMyAdmin UI:**
1. Click **"New"** in the left sidebar
2. Database name: `lgu1_facilities`
3. Collation: `utf8mb4_unicode_ci`
4. Click **"Create"**

---

## 🔑 Step 2: Generate Laravel App Key

Run this command in your terminal:

```bash
php artisan key:generate
```

This will populate the `APP_KEY` in your `.env` file.

---

## ✅ Step 3: Test Database Connection

Run this command to test both database connections:

```bash
php artisan tinker
```

Then test:

```php
// Test auth database
DB::connection('auth_db')->getPdo();

// Test facilities database
DB::connection('facilities_db')->getPdo();
```

**Expected output:** PDO connection objects (no errors)

Type `exit` to quit tinker.

---

## 📊 Database Structure

### **lgu1_auth** (Authentication Database)

**Tables to be created:**
- `users` - User accounts (shared across all LGU1 systems)
- `roles` - User roles (Super Admin, Admin, Staff, Citizen)
- `permissions` - Access control
- `user_otps` - OTP verification codes
- `password_resets` - Password reset tokens
- `sessions` - User sessions
- `audit_logs` - Login/activity history

### **lgu1_facilities** (Facilities Database)

**Tables to be created:**
- `facilities` - Facility information
- `facility_images` - Facility photos
- `facility_schedules` - Availability calendar
- `bookings` - Reservation records
- `booking_payments` - Payment transactions
- `booking_statuses` - Booking status history
- `reviews` - Facility reviews
- `reports` - Analytics & reports

---

## 🔧 Laravel Database Configuration

The databases are already configured in `config/database.php`:

```php
'auth_db' => [
    'driver' => 'mysql',
    'host' => env('DB_HOST', '127.0.0.1'),
    'database' => env('DB_DATABASE', 'lgu1_auth'),
    // ... other settings
],

'facilities_db' => [
    'driver' => 'mysql',
    'host' => env('DB_FACILITIES_HOST', '127.0.0.1'),
    'database' => env('DB_FACILITIES_DATABASE', 'lgu1_facilities'),
    // ... other settings
],
```

---

## 🎯 Usage in Code

### Using the Auth Database (Default):

```php
// User model (uses auth_db by default)
$users = User::all();

// Direct query
$users = DB::connection('auth_db')
    ->table('users')
    ->get();
```

### Using the Facilities Database:

```php
// Direct query
$facilities = DB::connection('facilities_db')
    ->table('facilities')
    ->get();

// In models
class Facility extends Model
{
    protected $connection = 'facilities_db';
}
```

---

## 🔐 Environment Variables

Your `.env` file contains:

```env
# Primary Database (Authentication)
DB_CONNECTION=auth_db
DB_DATABASE=lgu1_auth
DB_USERNAME=root
DB_PASSWORD=

# Secondary Database (Facilities)
DB_FACILITIES_CONNECTION=facilities_db
DB_FACILITIES_DATABASE=lgu1_facilities
DB_FACILITIES_USERNAME=root
DB_FACILITIES_PASSWORD=
```

---

## 🚨 Troubleshooting

### Error: "Access denied for user"
- Make sure MySQL is running in Laragon
- Check username/password in `.env` (default: root with no password)

### Error: "Unknown database"
- Make sure you created both databases in phpMyAdmin
- Check database names match exactly (case-sensitive on Linux)

### Error: "SQLSTATE[HY000] [2002]"
- MySQL service not running - Start Laragon

### Can't connect to localhost:3306
- Port conflict - Change `DB_PORT` to 3307 or check Laragon MySQL port

---

## 📝 Next Steps

After creating the databases:

1. ✅ Generate app key: `php artisan key:generate`
2. ✅ Test connections: `php artisan tinker`
3. 🔜 Run migrations to create tables
4. 🔜 Seed initial data (admin user, roles, etc.)
5. 🔜 Test authentication flow

---

**Created:** November 4, 2025  
**Project:** LGU1 Public Facilities Reservation System  
**Databases:** `lgu1_auth` + `lgu1_facilities`

