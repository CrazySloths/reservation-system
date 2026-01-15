# 🏢 FACILITY SEED DATA - REAL LGU FACILITIES

**Purpose:** Realistic demonstration data based on actual interview findings  
**Sources:** Caloocan City & Quezon City interviews  
**Usage:** Database seeding for authentic demos and testing

---

## 📍 CALOOCAN CITY FACILITIES (4 Total)

### **1. BUENA PARK**

**Category:** Public Park / Outdoor Event Space  
**Capacity:** 150-200 people (estimated)  
**Type:** Outdoor  
**Status:** Active

**Features:**
- Open-air venue
- Suitable for community gatherings
- Weekend bookings popular
- Most managed facility (mentioned extensively in interview)

**Pricing (Interview Data - Hourly Model):**
- 3 hours: ₱5,000
- Additional hour: +₱2,000
- Typical booking: 3-6 hours

**Demo Per-Person Pricing (Panel Requirement):**
```
per_person_rate: ₱35
min_capacity: 50
max_capacity: 200
```

**Notes:**
- Previously had double-booking issues
- Now uses 3-month advance reservation limit
- Color-coded physical bulletin for verification

---

### **2. SPORTS COMPLEX**

**Category:** Sports / Multi-purpose Facility  
**Capacity:** 300-500 people (estimated)  
**Type:** Indoor/Covered  
**Status:** Active

**Features:**
- Multi-purpose sports venue
- Suitable for sports events, competitions
- Large gatherings
- Indoor/covered area

**Demo Per-Person Pricing (Panel Requirement):**
```
per_person_rate: ₱40
min_capacity: 100
max_capacity: 500
```

---

### **3. BULWAGAN KATIPUNAN**

**Category:** Convention Hall  
**Capacity:** 200-300 people (estimated)  
**Type:** Indoor  
**Status:** Active (Priority for City Events)

**Features:**
- Used primarily for **city events**
- Formal gathering space
- Indoor air-conditioned
- Suitable for conferences, assemblies

**Demo Per-Person Pricing (Panel Requirement):**
```
per_person_rate: ₱45
min_capacity: 50
max_capacity: 300
```

**Notes:**
- Government priority use
- May have limited availability for public

---

### **4. PACQUIAO COURT**

**Category:** Sports Court  
**Capacity:** 100-150 people (estimated)  
**Type:** Covered Basketball Court  
**Status:** Active

**Features:**
- Covered basketball court
- Named after Manny Pacquiao
- Suitable for sports events, small tournaments
- Weather-protected

**Demo Per-Person Pricing (Panel Requirement):**
```
per_person_rate: ₱30
min_capacity: 30
max_capacity: 150
```

---

## 📍 QUEZON CITY M.I.C.E. CENTER (Multi-venue Complex)

### **5. CONVENTION/EXHIBIT HALL**

**Category:** Large Convention Center  
**Capacity:** 500-1000 people (estimated)  
**Type:** Indoor  
**Status:** Active (LGU departments only - Jan-Aug 2024)

**Features:**
- Large-scale events
- Exhibits, conventions, trade shows
- Professional-grade venue
- Part of 4-storey M.I.C.E. Center

**Booking Volume:**
- <100 programs in 8 months (Jan-Aug 2024)

**Demo Per-Person Pricing (Panel Requirement):**
```
per_person_rate: ₱50
min_capacity: 100
max_capacity: 1000
```

**Notes:**
- Ordinance still being processed
- Currently restricted to QC-LGU departments
- Will open to public once ordinance approved

---

### **6. BREAKOUT ROOMS** (MOST POPULAR!)

**Category:** Meeting/Training Rooms  
**Capacity:** 20-50 people per room  
**Type:** Indoor  
**Status:** Active (High demand!)

**Features:**
- Multiple smaller rooms
- **Most requested venue** according to interview
- Seminar/training venue
- Lower attendee count events
- Flexible configurations

**Booking Volume:**
- **>200 bookings in 8 months** (Jan-Aug 2024)
- Highest utilization rate!

**Demo Per-Person Pricing (Panel Requirement):**
```
per_person_rate: ₱25
min_capacity: 10
max_capacity: 50
```

**Notes:**
- High turnover
- Popular for workshops, trainings
- Multiple rooms = more concurrent bookings

---

### **7. AUDITORIUM**

**Category:** Performance/Presentation Hall  
**Capacity:** 200-300 people (estimated)  
**Type:** Indoor  
**Status:** Active

**Features:**
- Fixed seating
- Stage/presentation area
- Suitable for lectures, performances
- Professional audio/visual setup

**Demo Per-Person Pricing (Panel Requirement):**
```
per_person_rate: ₱40
min_capacity: 50
max_capacity: 300
```

---

### **8. LOBBY SPACES**

**Category:** Multipurpose Open Area  
**Capacity:** 50-100 people (estimated)  
**Type:** Indoor  
**Status:** Active

**Features:**
- Flexible space
- Suitable for small gatherings
- Reception areas
- Informal events

**Demo Per-Person Pricing (Panel Requirement):**
```
per_person_rate: ₱20
min_capacity: 20
max_capacity: 100
```

---

## 🛠️ EQUIPMENT CATALOG (Panel Requirement)

Based on panel specifications (not mentioned in interview, but required):

### **1. CHAIRS**
```
name: Monobloc Chairs
quantity: 500
rate_per_unit: ₱10
category: Seating
```

### **2. TABLES**
```
name: Folding Tables (6-seater)
quantity: 100
rate_per_unit: ₱50
category: Furniture
```

### **3. SOUND SYSTEM**
```
name: Basic PA System
quantity: 10
rate_per_unit: ₱500
category: Audio Equipment
```

---

## 📊 BOOKING VOLUME FOR REALISTIC DEMOS

### **Caloocan City (Monthly)**
```
Average bookings per month: 15
Peak season: August (fully booked)
Popular days: Weekends
Typical duration: 3 hours
```

### **Quezon City (8-month data: Jan-Aug 2024)**
```
Convention Hall: <100 programs
Breakout Rooms: >200 bookings (HIGHEST!)
Average per month: ~37 total bookings
```

### **Suggested Demo Data Volume**
```
Past bookings to seed: 60-80 records (realistic history for AI)
Upcoming bookings: 10-15 (show variety of statuses)
```

---

## 🎨 SUGGESTED DEMO SCENARIOS

### **Scenario 1: High-Demand Weekend**
- Buena Park: Booked (Birthday party)
- Sports Complex: Booked (Basketball tournament)
- Pacquiao Court: Available
- Bulwagan: Reserved (City event)

### **Scenario 2: Weekday Breakout Room Rush**
- Room A: Training (9AM-12PM)
- Room B: Seminar (10AM-2PM)
- Room C: Workshop (1PM-5PM)
- Room D: Available

### **Scenario 3: Conflict Prevention Demo**
- User tries to book Buena Park: Saturday 2PM-5PM
- System shows: "Already booked - Suggest alternatives?"
- Alternative 1: Sports Complex (same time)
- Alternative 2: Buena Park (Sunday 2PM-5PM)

---

## 💾 DATABASE SEEDER STRUCTURE

```php
// database/seeders/RealFacilitiesSeeder.php

$facilities = [
    // Caloocan City
    [
        'name' => 'Buena Park',
        'description' => 'Open-air community park suitable for outdoor gatherings and events',
        'location' => 'Caloocan City',
        'capacity_min' => 50,
        'capacity_max' => 200,
        'per_person_rate' => 35.00,
        'type' => 'outdoor',
        'status' => 'active',
        'amenities' => json_encode(['Open Space', 'Outdoor', 'Community Friendly']),
    ],
    [
        'name' => 'Sports Complex',
        'description' => 'Multi-purpose indoor sports facility for athletic events and large gatherings',
        'location' => 'Caloocan City',
        'capacity_min' => 100,
        'capacity_max' => 500,
        'per_person_rate' => 40.00,
        'type' => 'indoor',
        'status' => 'active',
        'amenities' => json_encode(['Sports Facilities', 'Indoor', 'Large Capacity']),
    ],
    [
        'name' => 'Bulwagan Katipunan',
        'description' => 'Convention hall primarily used for city events and formal gatherings',
        'location' => 'Caloocan City',
        'capacity_min' => 50,
        'capacity_max' => 300,
        'per_person_rate' => 45.00,
        'type' => 'indoor',
        'status' => 'active',
        'priority' => 'city_events',
        'amenities' => json_encode(['Air-conditioned', 'Conference Setup', 'Audio System']),
    ],
    [
        'name' => 'Pacquiao Court',
        'description' => 'Covered basketball court suitable for sports events and tournaments',
        'location' => 'Caloocan City',
        'capacity_min' => 30,
        'capacity_max' => 150,
        'per_person_rate' => 30.00,
        'type' => 'covered',
        'status' => 'active',
        'amenities' => json_encode(['Covered Court', 'Basketball', 'Weather Protected']),
    ],
    
    // Quezon City M.I.C.E. Center
    [
        'name' => 'Convention/Exhibit Hall',
        'description' => 'Large-scale venue for conventions, exhibits, and major events',
        'location' => 'Quezon City M.I.C.E. Center',
        'capacity_min' => 100,
        'capacity_max' => 1000,
        'per_person_rate' => 50.00,
        'type' => 'indoor',
        'status' => 'active',
        'amenities' => json_encode(['Professional Grade', 'Large Scale', 'Exhibit Space']),
    ],
    [
        'name' => 'Breakout Room A',
        'description' => 'Intimate meeting space perfect for seminars and training sessions',
        'location' => 'Quezon City M.I.C.E. Center',
        'capacity_min' => 10,
        'capacity_max' => 50,
        'per_person_rate' => 25.00,
        'type' => 'indoor',
        'status' => 'active',
        'popular' => true, // Most requested!
        'amenities' => json_encode(['Training Setup', 'Projector', 'Whiteboard']),
    ],
    [
        'name' => 'Auditorium',
        'description' => 'Fixed-seating venue with stage for performances and presentations',
        'location' => 'Quezon City M.I.C.E. Center',
        'capacity_min' => 50,
        'capacity_max' => 300,
        'per_person_rate' => 40.00,
        'type' => 'indoor',
        'status' => 'active',
        'amenities' => json_encode(['Fixed Seating', 'Stage', 'Audio/Visual']),
    ],
    [
        'name' => 'Lobby Space',
        'description' => 'Flexible open area for small gatherings and informal events',
        'location' => 'Quezon City M.I.C.E. Center',
        'capacity_min' => 20,
        'capacity_max' => 100,
        'per_person_rate' => 20.00,
        'type' => 'indoor',
        'status' => 'active',
        'amenities' => json_encode(['Flexible Space', 'Open Area', 'Reception Ready']),
    ],
];
```

---

## 🎯 USAGE NOTES

### **For Development:**
- Use this data to seed `facilities` table
- Create realistic booking history (60-80 records)
- Ensure mix of statuses: approved, pending, rejected, completed

### **For Demo:**
- Show variety of facility types
- Demonstrate conflict detection with overlapping bookings
- Show AI analytics with realistic usage patterns
- Breakout rooms should have most bookings (matches interview)

### **For Defense:**
- Reference these as "real facilities from our interview"
- Explain why we used per-person pricing (panel requirement) vs. their hourly
- Show system flexibility can accommodate different pricing models

---

## 📝 IMPORTANT REMINDERS

✅ **These are REAL facilities** from interview  
✅ **Pricing is PANEL-SPECIFIED** (per-person, not their hourly)  
✅ **Equipment is PANEL-REQUIRED** (not mentioned in interview)  
✅ **Use for authentic demos** but follow panel specs  

❌ **Don't say** "We copied their pricing"  
✅ **Do say** "We used real facilities from our interview with per-person pricing as recommended by our panel"

---

**Last Updated:** December 8, 2024  
**Status:** Ready for database seeding  
**Next Step:** Create migration and seeder files

