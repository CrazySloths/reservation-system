# 🗺️ URBAN PLANNING INTEGRATION - FEATURE LIST

**Document Version:** 1.0  
**Date:** December 3, 2025  
**System:** Public Facilities Reservation System  
**External Integration:** Urban Planning and Development System

---

## 📋 INTEGRATION OVERVIEW

### **Integration Purpose:**
Enable Admin to search and select suitable government-owned land when requesting construction of new public facilities.

### **Integration Context:**
This is a **PREREQUISITE STEP** in the Infrastructure Project Management workflow:
```
Step 1: Urban Planning Integration → Find suitable land
Step 2: Infrastructure PM Integration → Build facility on selected land
```

### **Key Principle:**
- **One-time lookup** per facility request
- **Land selection only** - no ongoing integration
- **Government-owned lands only** - clean title, no encumbrances
- **Simple query/response** - search and select

---

## 🔗 SUBMODULE INTEGRATION MAPPING

### **Important Note:**
This integration does NOT map to the 5 core Public Facilities submodules (Facility Directory, Booking, Fee Calculation, Schedule Conflict, Reports). Those handle **existing facilities**.

Urban Planning integration is part of a **NEW FEATURE: "Request New Facility"** which handles facilities that don't exist yet.

### **Integration Map:**

| Public Facilities Feature | ↔️ | Urban Planning Submodule | Data Flow |
|---------------------------|---|-------------------------|-----------|
| **Request New Facility (NEW)** | → | **Zoning Map and Lot Classification** | ONE-WAY: Query land zoning and lot details |
| **Request New Facility (NEW)** | → | **GIS Integration and Map Visualization** | ONE-WAY: View lands on interactive map |
| **Request New Facility (NEW)** | → | **Comprehensive Land Use Planning Tools** | ONE-WAY: Get available land data |

---

## ⚠️ IMPORTANT: ROLE DEFINITIONS

### **EIS SUPER ADMIN** (Lead Programmer - Technical Role)
- **Created by:** EIS Lead Programmer (centralized in lgu1_auth)
- **Access:** All 10 subsystems (technical oversight)
- **Focus:** System configuration, API setup with Urban Planning
- **Time commitment:** Occasional (setup, maintenance)

**NOT responsible for land selection or facility requests.**

### **ADMIN** (Operations Manager - Primary Operational Role)
- **Created in:** Public Facilities subsystem
- **Access:** Public Facilities Reservation System only
- **Focus:** Request new facilities, select land, coordinate construction
- **Time commitment:** Full-time (main working role)

**This is the PRIMARY role that searches and selects land for new facilities.**

---

## 🎯 FEATURES BY ROLE

### **1. ADMIN** ⭐ (Primary User)

#### **A. Search for Government-Owned Lands**
**Location:** `/admin/facilities/request-new` (Land Selection Step)

**Features:**
- ✅ Query Urban Planning system for available lands
- ✅ **Filter by location:**
  - City (Caloocan)
  - District (District 1, 2, etc.)
  - Barangay (specific barangay)
- ✅ **Filter by land specifications:**
  - Minimum area (sqm)
  - Maximum area (sqm)
  - Zoning classification (recreational, commercial, mixed-use)
  - Terrain type (flat, sloped, elevated)
- ✅ **Filter by ownership:**
  - Government-owned only
  - No encumbrances
  - Clean title status
- ✅ **Filter by utilities:**
  - Water availability
  - Electricity availability
  - Drainage system
  - Road access
- ✅ **Filter by availability:**
  - Vacant lands only
  - No planned projects
  - Not reserved by other departments

---

#### **B. View Search Results**
**Location:** Same form, results section

**Features:**
- ✅ **List of suitable lands** with summary:
  - Land ID (LAND-2025-078)
  - Title number (TCT-123456)
  - Address
  - Lot area (sqm)
  - Zoning classification
  - Suitability score (0-100)
  - Thumbnail map
- ✅ **Sort results by:**
  - Suitability score (highest first)
  - Area (largest/smallest)
  - Distance from city center
  - Availability date
- ✅ **View on interactive map:**
  - All search results plotted on map
  - Compare locations visually
  - See nearby facilities and infrastructure
  - Measure distances

---

#### **C. View Detailed Land Information**
**Location:** Modal or separate page for selected land

**Features:**

**Basic Information:**
- ✅ Land ID and reference number
- ✅ Title number
- ✅ Full address
- ✅ GPS coordinates (latitude, longitude)

**Physical Details:**
- ✅ Total lot area (sqm)
- ✅ Lot dimensions (length × width)
- ✅ Terrain description
- ✅ Elevation level
- ✅ Soil type
- ✅ Topography

**Zoning & Legal:**
- ✅ Zoning classification
- ✅ Allowed land uses
- ✅ Building height restrictions
- ✅ Maximum building coverage (%)
- ✅ Setback requirements
- ✅ Density limitations

**Ownership & Title:**
- ✅ Current owner (City Government of Caloocan)
- ✅ Title status (clean, no encumbrances)
- ✅ Acquisition date
- ✅ Previous use history
- ✅ Any restrictions or conditions

**Utilities & Infrastructure:**
- ✅ Water connection status
- ✅ Electricity connection status
- ✅ Drainage system availability
- ✅ Sewerage system
- ✅ Road access (primary/secondary)
- ✅ Distance to main road

**Surroundings & Context:**
- ✅ Nearby landmarks
- ✅ Nearby facilities (schools, hospitals, markets)
- ✅ Distance to residential areas
- ✅ Population density in area
- ✅ Accessibility via public transport

**Suitability Assessment:**
- ✅ Overall suitability score (0-100)
- ✅ Suitability factors breakdown:
  - Location accessibility (score)
  - Utilities availability (score)
  - Zoning compliance (score)
  - Size adequacy (score)
  - Development potential (score)
- ✅ Pros and cons summary
- ✅ Recommendations from Urban Planning

**Documents & Visuals:**
- ✅ Title document (PDF download)
- ✅ Survey plan (PDF download)
- ✅ Zoning map (image/PDF)
- ✅ Site photos (multiple images)
- ✅ Aerial view/satellite image
- ✅ Location map with context

---

#### **D. Compare Multiple Lands**
**Location:** Same form, comparison view

**Features:**
- ✅ Select 2-3 lands to compare side-by-side
- ✅ **Compare key features:**
  - Area (which is larger?)
  - Location/accessibility
  - Utilities availability
  - Zoning restrictions
  - Suitability scores
  - Pros and cons
- ✅ **Visual comparison:**
  - Side-by-side maps
  - Specification table
- ✅ Make informed selection decision

---

#### **E. Select Land**
**Location:** After viewing details/comparison

**Features:**
- ✅ **Choose land** from search results
- ✅ **Confirm selection:**
  - Review key details
  - Confirm land is suitable
  - Check for any restrictions
- ✅ **Auto-populate facility request form:**
  - Land ID
  - Address
  - GPS coordinates (latitude, longitude)
  - Lot area (sqm)
  - Zoning classification
  - Ownership status
  - Utilities availability
- ✅ **Validate land suitability:**
  - System checks if land area ≥ facility needs
  - System checks if zoning allows facility type
  - System confirms utilities are adequate
  - Alert if any issues found
- ✅ **Save land selection** for Infrastructure PM request

---

### **2. EIS SUPER ADMIN** ⭐ (Technical Setup Only)

#### **A. API Configuration**
**Location:** `/superadmin/settings/urban-planning-integration`

**Features:**
- ✅ Configure API connection to Urban Planning system
- ✅ Manage API keys and authentication
- ✅ Set up GIS map integration
- ✅ Configure integration settings
- ✅ Monitor API health and performance
- ✅ View integration logs
- ✅ Handle technical issues

**Note:** EIS Super Admin does NOT search for or select lands. Admin handles all land selection

---

### **3. STAFF** 👀 (View-Only)

#### **A. View Selected Land (Read-Only)**
**Location:** `/staff/construction-projects/{id}`

**Features:**
- ✅ **View land information** for approved construction projects
- ✅ **See selected land details:**
  - Address and location
  - Lot area
  - GPS coordinates
  - View on map
- ✅ **View land documents** (if authorized)
- ❌ **Cannot search** for new lands
- ❌ **Cannot select** different lands

**Purpose:**
- Staff can see project locations
- Staff can provide information to citizens
- Staff can reference land info for coordination

---

### **4. CITIZEN** 💚 (Indirect Benefit)

#### **No Direct Interaction**

**Citizens benefit indirectly through:**

**Location information in "Coming Soon" facilities:**
- ✅ See facility address (from selected land)
- ✅ See lot area (from selected land)
- ✅ View on map
- ✅ Get directions
- ✅ See accessibility information

**Citizens do NOT:**
- ❌ Search for lands
- ❌ View land selection process
- ❌ Access Urban Planning system

---

## 🔗 API INTEGRATION

### **OUTBOUND (Public Facilities → Urban Planning)**

#### **1. Search Available Lands**
```
POST /api/urban-planning/lands/search
Content-Type: application/json
Authorization: Bearer {api_key}

Request Body:
{
  "search_criteria": {
    "location": {
      "city": "Caloocan",
      "district": "District 1",
      "barangay": "Barangay 188"
    },
    "area": {
      "min_sqm": 5000,
      "max_sqm": 15000
    },
    "zoning_types": ["recreational", "commercial", "mixed-use"],
    "ownership_status": "government-owned",
    "title_status": "clean",
    "availability": "vacant",
    "utilities_required": {
      "water": true,
      "electricity": true,
      "drainage": true
    },
    "terrain_preference": "flat"
  },
  "requested_by_system": "Public Facilities Reservation",
  "requested_by_user_id": 1,
  "request_date": "2025-12-03"
}

Response:
{
  "success": true,
  "total_results": 3,
  "lands": [
    {
      "land_id": "LAND-2025-078",
      "title_number": "TCT-123456",
      "location": {
        "address": "Camarin Road, Barangay 188, Caloocan City",
        "city": "Caloocan",
        "district": "District 1",
        "barangay": "Barangay 188",
        "latitude": 14.7231,
        "longitude": 120.9612
      },
      "land_details": {
        "lot_area_sqm": 8000,
        "lot_dimensions": "100m x 80m",
        "terrain": "flat",
        "elevation": "10m above sea level",
        "soil_type": "clay loam"
      },
      "zoning": {
        "classification": "Commercial/Recreational",
        "allowed_uses": ["sports facility", "community center", "park", "plaza"],
        "height_restriction": "3 floors max",
        "building_coverage_max": "60%",
        "setback_requirements": {
          "front": "3m",
          "rear": "2m",
          "sides": "1.5m"
        }
      },
      "ownership": {
        "status": "government-owned",
        "title_holder": "City Government of Caloocan",
        "acquisition_date": "2020-01-15",
        "encumbrances": "none",
        "restrictions": "none"
      },
      "availability": {
        "status": "vacant",
        "reserved": false,
        "planned_projects": null,
        "available_since": "2020-01-15"
      },
      "utilities": {
        "water": "available",
        "water_provider": "Manila Water",
        "electricity": "available",
        "electricity_provider": "Meralco",
        "drainage": "available",
        "sewerage": "available",
        "road_access": "primary road",
        "distance_to_main_road": "50m"
      },
      "surroundings": {
        "nearest_landmark": "Caloocan City Hall (2km)",
        "nearest_residential": "500m",
        "nearest_school": "Barangay 188 Elementary (300m)",
        "population_density": "high",
        "public_transport": "jeepney, tricycle"
      },
      "suitability": {
        "overall_score": 95,
        "location_score": 90,
        "utilities_score": 100,
        "zoning_score": 95,
        "size_score": 90,
        "development_potential": 95,
        "pros": [
          "Excellent location in populated area",
          "All utilities available",
          "Adequate size for proposed facility",
          "Good road access",
          "Flat terrain - easy to develop"
        ],
        "cons": [
          "High traffic area - may need traffic management"
        ],
        "recommendation": "Highly suitable for sports complex development"
      },
      "documents": {
        "title_document_url": "https://urbanplanning.caloocan.gov.ph/docs/title-LAND-2025-078.pdf",
        "survey_plan_url": "https://urbanplanning.caloocan.gov.ph/docs/survey-LAND-2025-078.pdf",
        "zoning_map_url": "https://urbanplanning.caloocan.gov.ph/maps/zoning-D1-B188.pdf",
        "site_photos": [
          "https://urbanplanning.caloocan.gov.ph/photos/LAND-2025-078-1.jpg",
          "https://urbanplanning.caloocan.gov.ph/photos/LAND-2025-078-2.jpg"
        ],
        "aerial_view_url": "https://urbanplanning.caloocan.gov.ph/aerial/LAND-2025-078.jpg"
      },
      "map_url": "https://urbanplanning.caloocan.gov.ph/map?land=LAND-2025-078"
    },
    {
      "land_id": "LAND-2025-091",
      ...
    },
    {
      "land_id": "LAND-2025-103",
      ...
    }
  ]
}
```

---

#### **2. Get Land Details**
```
GET /api/urban-planning/lands/{land_id}
Authorization: Bearer {api_key}

Response:
{
  "success": true,
  "land": {
    // Complete land details (same structure as search results)
  }
}
```

---

#### **3. Verify Land Availability**
```
POST /api/urban-planning/lands/{land_id}/verify-availability
Authorization: Bearer {api_key}

Request Body:
{
  "intended_use": "sports facility construction",
  "estimated_area_needed": 5000,
  "requesting_system": "Public Facilities Reservation"
}

Response:
{
  "success": true,
  "is_available": true,
  "is_suitable": true,
  "zoning_compliant": true,
  "size_adequate": true,
  "notes": "Land is available and suitable for proposed use",
  "valid_until": "2025-12-31"
}
```

---

## 💾 DATABASE CHANGES

### **Add to `construction_projects` table:**

```sql
ALTER TABLE construction_projects
ADD COLUMN selected_land_id VARCHAR(50) NULL AFTER facility_name,
ADD COLUMN land_details JSON NULL AFTER selected_land_id;

-- Example of land_details JSON:
/*
{
  "land_id": "LAND-2025-078",
  "title_number": "TCT-123456",
  "address": "Camarin Road, Barangay 188, Caloocan City",
  "lot_area_sqm": 8000,
  "lot_dimensions": "100m x 80m",
  "zoning": "Commercial/Recreational",
  "ownership": "Government-owned",
  "latitude": 14.7231,
  "longitude": 120.9612,
  "utilities": {
    "water": true,
    "electricity": true,
    "drainage": true
  },
  "suitability_score": 95,
  "selected_date": "2025-12-03",
  "selected_by_user_id": 1
}
*/
```

---

## 🔄 COMPLETE WORKFLOW

### **When Super Admin Requests New Facility:**

```
Step 1: Fill Facility Details
├─ Facility name: "Nueva Caloocan Sports Complex"
├─ Facility type: Sports Complex
├─ Target capacity: 1,500 people
├─ Estimated area: 5,000 sqm
└─ Required amenities: Basketball court, bleachers, etc.

Step 2: Search for Suitable Land
├─ Set search criteria:
│  ├─ Location: District 1, Barangay 188
│  ├─ Min area: 5,000 sqm
│  ├─ Zoning: Recreational
│  └─ Utilities: Water, Electricity
│
├─ Click "Search Available Lands"
│
├─ System queries Urban Planning API
│  └─ POST /api/urban-planning/lands/search
│
└─ Receive 3 results

Step 3: Review and Compare Lands
├─ View search results:
│  ├─ LAND-2025-078 (8,000 sqm) - Score: 95 ⭐
│  ├─ LAND-2025-091 (6,500 sqm) - Score: 88
│  └─ LAND-2025-103 (10,000 sqm) - Score: 82
│
├─ Click on LAND-2025-078 for details
│  ├─ Review location (good! Near residential)
│  ├─ Check utilities (all available!)
│  ├─ Check zoning (allows sports facility!)
│  └─ Check title (clean, government-owned!)
│
└─ Compare with LAND-2025-091 (decide 078 is better)

Step 4: Select Land
├─ Click "Select LAND-2025-078"
│
├─ System validates:
│  ├─ Area adequate? ✅ (8,000 > 5,000 needed)
│  ├─ Zoning allows? ✅ (recreational use allowed)
│  └─ Utilities adequate? ✅ (all available)
│
└─ Land details auto-populate in form

Step 5: Complete Request
├─ Set budget estimate: ₱14,500,000
├─ Set priority: High
├─ Add justification: "High demand for sports facilities"
└─ Click "Submit to Infrastructure PM"

Step 6: Request Sent to Infrastructure PM
└─ Includes:
   ├─ Facility specifications
   └─ Selected land details (LAND-2025-078)
```

---

## ✅ IMPLEMENTATION PRIORITY

### **Phase 1 - Core Functionality (MVP):**
1. ✅ API integration with Urban Planning (search endpoint)
2. ✅ Land search interface with filters
3. ✅ Display search results with basic info
4. ✅ View detailed land information
5. ✅ Select land and auto-populate form
6. ✅ Pass selected land to Infrastructure PM

### **Phase 2 - Enhanced Features:**
7. ✅ Interactive map visualization (GIS integration)
8. ✅ Compare multiple lands side-by-side
9. ✅ Download land documents (title, survey plan)
10. ✅ View site photos and aerial views
11. ✅ Suitability scoring and recommendations

### **Phase 3 - Advanced:**
12. ✅ Real-time availability checking
13. ✅ Distance calculations and accessibility analysis
14. ✅ Land reservation (temporary hold while request is processed)
15. ✅ Historical land usage data

---

## 💡 KEY PRINCIPLES

### **1. Simple Lookup Integration**
- ✅ One-time use per facility request
- ✅ Query and select only
- ✅ No complex workflows
- ✅ No ongoing data sync

### **2. Government-Owned Lands Only**
- ✅ Filter for government ownership
- ✅ Clean title required
- ✅ No encumbrances
- ✅ Verified availability

### **3. Data-Driven Selection**
- ✅ Suitability scores guide decisions
- ✅ Multiple criteria considered
- ✅ Visual map aids comparison
- ✅ Documented recommendations

### **4. Integration with Infrastructure**
- ✅ Selected land data flows to Infrastructure PM
- ✅ Infrastructure uses land info for construction
- ✅ Seamless workflow from land → construction → operation

---

## 🎯 SUCCESS METRICS

**System Performance:**
- ⏱️ Search results returned < 3 seconds
- 🗺️ Map loads and displays < 5 seconds
- ✅ 99% uptime for Urban Planning API
- 📊 Accurate suitability scoring

**User Experience:**
- ⭐ Super Admin finds suitable land in < 10 minutes
- ✅ Clear land information presentation
- 🗺️ Easy to compare multiple options
- 📄 All necessary documents accessible

**Data Quality:**
- ✅ 100% government-owned lands verified
- ✅ Up-to-date availability status
- ✅ Accurate zoning information
- ✅ Current utility connection data

---

**END OF DOCUMENT**

**Last Updated:** December 3, 2025  
**Prepared By:** AI Assistant  
**Status:** Prerequisite step for Infrastructure Integration

---

## 📋 CHANGE LOG

| Version | Date | Changes |
|---------|------|---------|
| 1.0 | Dec 3, 2025 | Initial document - Land selection integration for new facility requests |

