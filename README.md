# LGU1 Public Facilities Reservation System

> Laravel 11 application for managing public facility reservations

## 🏗️ Technology Stack

- **Backend**: Laravel 11.46.1
- **Frontend**: Tailwind CSS + Alpine.js
- **Icons**: Lucide Icons
- **Database**: MySQL (Dual database setup)
- **Fonts**: Merriweather (headings) + Inter (body)

## 🎨 Color Palette

```css
Background:    #f2f7f5  /* Mint green */
Headline:      #00473e  /* Dark forest green */
Paragraph:     #475d5b  /* Slate gray */
Button:        #faae2b  /* Golden yellow */
Highlight:     #faae2b  /* Golden yellow */
Secondary:     #ffa8ba  /* Soft pink */
Tertiary:      #fa5246  /* Coral red */
```

## 🗄️ Database Configuration

This project uses **two databases**:

### Auth Database (Shared)
- **Connection**: `auth_db`
- **Database**: `lgu1_auth_db`
- **Purpose**: User authentication, roles, permissions

### Facilities Database (This System)
- **Connection**: `facilities_db`  
- **Database**: `lgu1_facilities_db`
- **Purpose**: Facilities, bookings, payments, schedules

## 📦 Installation

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Build assets
npm run dev
```

## 🚀 Development

```bash
# Start Laravel development server
php artisan serve

# Watch and compile assets
npm run dev

# Run tests
php artisan test
```

## 📂 Project Structure

```
├── app/              # Application logic
├── config/           # Configuration files
├── database/         # Migrations, seeders
├── public/           # Web root
│   └── assets/       # Logo and images
├── resources/        # Blade views, CSS, JS
├── routes/           # Route definitions
├── storage/          # Logs, cache, sessions
├── uploads/          # User uploaded files
└── vendor/           # Composer dependencies
```

## 🔐 Environment Setup

Copy `.env.example` to `.env` and configure:

```env
DB_CONNECTION=auth_db
DB_DATABASE=lgu1_auth_db

FACILITIES_DB_CONNECTION=mysql
FACILITIES_DB_DATABASE=lgu1_facilities_db
```

## 🔗 Inter-Departmental Integration

This system is designed to integrate with other LGU departments:

### City Treasurer's Office (CTO) Integration
- **Payment verification** and processing
- **Official Receipt (OR)** generation and issuance
- **Daily collection reports** for treasury records
- **54 CTO services** identified for future unified portal

📄 **See:** [EXTERNAL_INTEGRATIONS.md](EXTERNAL_INTEGRATIONS.md)

### City Budget Department (CBD) Integration
- **Monthly/Quarterly revenue reports** for budget planning
- **Budget allocation tracking** and utilization monitoring
- **Financial oversight** and expenditure approval workflows

📄 **See:** [INTERNAL_PROCESSES.md](INTERNAL_PROCESSES.md)

### Cross-Department Workflows
- **Payment lifecycle:** Citizen → CTO → System → CBD
- **Budget requests:** Admin → CBD → CTO → System
- **Annual reconciliation** processes

📄 **See:** [HYBRID_INTEGRATION_PROCESSES.md](HYBRID_INTEGRATION_PROCESSES.md)

## 📚 Documentation

- **[NEXT_STEPS_ROADMAP.md](NEXT_STEPS_ROADMAP.md)** - Development priorities and timeline
- **[ARCHITECTURE.md](ARCHITECTURE.md)** - System architecture and design
- **[DATABASE_SCHEMA.md](DATABASE_SCHEMA.md)** - Database structure and relationships
- **[PROJECT_DESIGN_RULES.md](PROJECT_DESIGN_RULES.md)** - UI/UX design standards
- **[FEATURES_CHECKLIST.md](FEATURES_CHECKLIST.md)** - Feature implementation checklist

## 📝 License

Proprietary - Local Government Unit 1

