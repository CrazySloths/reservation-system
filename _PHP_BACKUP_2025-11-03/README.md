# PHP System Backup - November 3, 2025

## Contents
This folder contains the original PHP-based authentication system before Laravel conversion.

### Backed Up Files:
- `login.php` - Original login with OTP
- `register.php` - Multi-step registration
- `verify.php` - Email verification
- `forgot_password.php` - Password reset
- `logout.php` - Logout functionality
- `api/` - All API endpoints
- `config/` - Database and environment configuration
- `database/` - SQL schemas and migration scripts
- `Citizen/` - Citizen portal files
- `SuperAdmin/` - Super admin dashboard files

### Restoration
If you need to restore this system:
1. Copy all files back to their original locations
2. Restore database using `database/lgu1-auth_db.sql`
3. Update `config/env.local.php` with database credentials

### Notes
- Backup created before Laravel conversion
- Database structure documented in `database/` folder
- Original color scheme: #f2f7f5, #00473e, #faae2b
- Used Bootstrap 5 + Bootstrap Icons

