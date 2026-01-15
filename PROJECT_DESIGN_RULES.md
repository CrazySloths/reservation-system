# 🎨 PROJECT DESIGN & DEVELOPMENT RULES

**Project:** Public Facilities Reservation System (LGU1)  
**Created:** December 8, 2025  
**Last Updated:** December 8, 2025  
**Status:** MANDATORY - All developers must follow

---

## ⚠️ CRITICAL: READ BEFORE CODING

These rules are **NON-NEGOTIABLE** and must be followed for the final defense. Every page, component, and feature MUST comply with these standards.

---

## 🎨 1. THEME COLORS (LGU1 OFFICIAL)

```javascript
// Tailwind config - Use these exact values
colors: {
  'lgu-bg': '#f2f7f5',           // Light mint background
  'lgu-headline': '#00473e',      // Dark teal headlines
  'lgu-paragraph': '#475d5b',     // Muted teal text
  'lgu-button': '#faae2b',        // Golden yellow buttons
  'lgu-button-text': '#00473e',   // Dark teal button text
  'lgu-stroke': '#00332c',        // Darker teal borders
  'lgu-main': '#f2f7f5',         // Main background
  'lgu-highlight': '#faae2b',     // Highlight color
  'lgu-secondary': '#ffa8ba',     // Pink accent
  'lgu-tertiary': '#fa5246'       // Red accent/error
}
```

### Color Usage Guide:
- **Backgrounds:** Use `lgu-bg` or `lgu-main`
- **Headlines/Titles:** Use `lgu-headline`
- **Body Text:** Use `lgu-paragraph`
- **Primary Buttons:** Background `lgu-button`, text `lgu-button-text`
- **Borders/Strokes:** Use `lgu-stroke`
- **Highlights/Active States:** Use `lgu-highlight`
- **Success/Info:** Use `lgu-secondary`
- **Errors/Warnings:** Use `lgu-tertiary`

### ❌ FORBIDDEN:
- **NO GRADIENTS** - Use solid colors only
- **NO bg-gradient-*** classes allowed
- **NO linear-gradient()** in CSS
- All backgrounds must be flat, solid colors

---

## 🔧 2. TECHNICAL STACK (NON-NEGOTIABLE)

| Component | Technology | Notes |
|-----------|-----------|-------|
| **Backend** | Laravel 11 | PHP 8.2+ |
| **Frontend** | Blade + Tailwind CSS | No React/Vue |
| **Database** | **MySQL ONLY** | ❌ No SQLite, PostgreSQL, or others |
| **Icons** | **Lucide Icons ONLY** | ❌ Absolutely no emojis/emoticons |
| **Currency** | **Philippine Peso (₱) ONLY** | ❌ No dollar signs ($) - Use ₱ symbol |
| **Alerts** | **SweetAlert2 ONLY** | All alerts MUST be modal |
| **Font** | **Poppins** | All weights, all text |
| **AI** | TensorFlow.js | Client-side only |

---

## 📂 2.5 SIDEBAR NAVIGATION STRUCTURE (SUBMODULE-BASED)

**Last Updated:** December 21, 2025

### **Design Philosophy**

All portal sidebars (Admin, Staff, Citizen) MUST follow a **submodule-based organization** to:
- Group related features logically
- Make navigation intuitive and scalable
- Maintain consistency across all user roles
- Clearly separate implemented vs. upcoming features

### **Unified Sidebar Structure**

Each sidebar is divided into **submodule sections** with:
1. **Section Header** - Gray uppercase label (e.g., "BOOKING MANAGEMENT")
2. **Feature Links** - Grouped under their parent submodule
3. **Status Badges** - "Soon" for unimplemented features
4. **Active States** - Golden yellow highlight for current page
5. **Notification Badges** - Yellow pill badges for counts (e.g., pending queue)

### **Role-Based Submodules**

#### **👑 Admin Portal Submodules:**
```
MAIN
  └─ Dashboard

BOOKING MANAGEMENT
  └─ Payment Queue
  └─ All Bookings
  └─ Calendar View

FINANCIAL
  └─ Revenue Reports [Soon]
  └─ Payment Analytics [Soon]
  └─ Transactions [Soon]

FACILITIES
  └─ Manage Facilities [Soon]
  └─ Equipment [Soon]
  └─ Pricing [Soon]

USERS
  └─ Staff Accounts [Soon]
  └─ Citizens [Soon]

COMMUNICATIONS
  └─ Email Settings [Soon]
  └─ SMS Settings [Soon]

REPORTS
  └─ Usage Statistics [Soon]
  └─ Audit Trail [Soon]

SYSTEM
  └─ Settings [Soon]
  └─ Backup [Soon]
```

#### **👤 Staff Portal Submodules:**
```
MAIN
  └─ Dashboard

BOOKING VERIFICATION
  └─ Verification Queue
  └─ All Bookings
  └─ Calendar View

FACILITIES
  └─ View Facilities [Soon]
  └─ Equipment List [Soon]
  └─ Pricing Info [Soon]

REPORTS
  └─ My Statistics [Soon]
  └─ Activity Log [Soon]

COMMUNICATIONS
  └─ Send Notification [Soon]
  └─ Templates [Soon]
```

#### **🏠 Citizen Portal Submodules:**
```
MAIN
  └─ Dashboard

BOOKINGS
  └─ Book Facility
  └─ My Reservations
  └─ Booking History [Soon]

FACILITIES
  └─ Browse All [Soon]
  └─ Favorites [Soon]
  └─ Availability [Soon]

PAYMENTS
  └─ Payment Methods [Soon]
  └─ Transaction History [Soon]

SUPPORT
  └─ Help Center [Soon]
  └─ Contact Us [Soon]
```

### **"Coming Soon" Features**

For unimplemented features:
1. Add `opacity-60` to the link
2. Add "Soon" badge with `bg-gray-500`
3. Call `showComingSoon('Feature Name')` on click
4. Show SweetAlert2 modal with:
   - Rocket icon (Lucide)
   - Feature name
   - "This feature will be available in a future update" message
   - "Go Back" button

**Example Implementation:**
```blade
<li>
    <a href="#" onclick="showComingSoon('Revenue Reports'); return false;" 
       class="sidebar-link opacity-60">
        <i data-lucide="trending-up" class="w-5 h-5"></i>
        <span>Revenue Reports</span>
        <span class="ml-auto text-xs bg-gray-500 text-white px-2 py-0.5 rounded-full">Soon</span>
    </a>
</li>
```

### **Visual Guidelines**

- **Section Headers:** `text-gray-400 text-caption font-semibold uppercase tracking-wider`
- **Spacing:** Use Golden Ratio spacing (`mb-gr-lg`, `space-y-gr-xs`, etc.)
- **Icons:** Lucide icons, 20px (w-5 h-5)
- **Active State:** Yellow background (`bg-lgu-highlight/10`) with dark teal text
- **Hover State:** Lighter background with smooth transition
- **Badges:** Rounded full, small text, appropriate color (yellow for counts, gray for "Soon")

### **Benefits**

✅ **Scalable** - Easy to add new features within existing submodules  
✅ **Intuitive** - Users know where to find related features  
✅ **Consistent** - All three portals follow the same pattern  
✅ **Professional** - Clear visual hierarchy and organization  
✅ **Future-Ready** - "Coming Soon" features set expectations  

---

## 🔱 3. GOLDEN RATIO DESIGN PRINCIPLES

**The Golden Ratio (φ = 1.618)** is the foundation of professional, visually pleasing design.

### **Why Golden Ratio?**
- Creates natural visual harmony
- Guides the eye naturally through content
- Makes designs feel "right" without users knowing why
- Used by Apple, Google, Twitter, and all major design systems

---

### **A. Golden Ratio Typography Scale**

Instead of random font sizes, use the golden ratio scale:

```javascript
// Base size: 16px (body text)
// Each level multiplies by 1.618 (golden ratio)

12px  → Small text, captions       (16 / 1.333)
14px  → Body text, paragraphs      (Base - 1 step down)
16px  → Base size                  (Reference point)
18px  → Large body text            (16 × 1.125)
24px  → H3, Subheadings            (16 × 1.5)
32px  → H2, Section headers        (16 × 2)
40px  → H1, Page headers           (16 × 2.5)
64px  → Display, Hero text         (16 × 4)
```

**For Poppins in LGU1:**
```css
/* Applied Golden Ratio Scale */
.text-xs    { font-size: 12px; }  /* Captions, labels */
.text-sm    { font-size: 14px; }  /* Body text */
.text-base  { font-size: 16px; }  /* Default */
.text-lg    { font-size: 18px; }  /* Emphasis */
.text-xl    { font-size: 24px; }  /* H3 */
.text-2xl   { font-size: 32px; }  /* H2 */
.text-3xl   { font-size: 40px; }  /* H1 */
.text-4xl   { font-size: 64px; }  /* Hero */
```

---

### **B. Golden Ratio Spacing**

Use golden ratio for margins, padding, and gaps:

```javascript
// Base: 8px (spacing unit)
// Golden ratio progression

4px   → Tight spacing (1/2 base)
8px   → Base unit
12px  → Small gaps (8 × 1.5)
16px  → Medium gaps (8 × 2)
24px  → Large gaps (8 × 3)
32px  → Section spacing (8 × 4)
48px  → Major sections (8 × 6)
64px  → Page sections (8 × 8)
96px  → Hero sections (8 × 12)
```

**Tailwind Spacing (Already Golden Ratio Based!):**
```css
gap-1  = 4px   (0.25rem)
gap-2  = 8px   (0.5rem)
gap-3  = 12px  (0.75rem)
gap-4  = 16px  (1rem)
gap-6  = 24px  (1.5rem)
gap-8  = 32px  (2rem)
gap-12 = 48px  (3rem)
gap-16 = 64px  (4rem)
gap-24 = 96px  (6rem)
```

**Usage in LGU1:**
```html
<!-- Card with golden ratio spacing -->
<div class="p-6">          <!-- 24px padding -->
  <h3 class="text-xl mb-4"> <!-- 16px bottom margin -->
    Title
  </h3>
  <p class="text-sm mb-6">  <!-- 24px bottom margin -->
    Content
  </p>
</div>
```

---

### **C. Golden Ratio Layout Proportions**

**Content Width vs Sidebar:**
```
Sidebar : Content = 1 : 1.618
260px   : 420px   ≈ 1 : 1.618 ✅

Example for LGU1:
- Sidebar: 256px (w-64 in Tailwind)
- Content: 414px minimum
```

**Card Dimensions:**
```
Width : Height = 1.618 : 1 (landscape)
Height : Width = 1.618 : 1 (portrait)

Example card:
- Width: 320px
- Height: 198px (320 / 1.618 ≈ 198)
```

**Image Aspect Ratios:**
```
Golden Rectangle: 1.618:1
- 1600px × 989px
- 1200px × 742px
- 800px × 494px
- 400px × 247px
```

---

### **D. Golden Ratio in Action (LGU1 Examples)**

**Dashboard Card:**
```html
<div class="bg-white rounded-xl shadow-lg p-6 w-80">
  <!-- Width: 320px (w-80) -->
  <!-- Padding: 24px (p-6) -->
  <!-- Height: ~198px (natural golden ratio) -->
  
  <div class="flex items-center justify-between mb-4">
    <!-- Margin: 16px (golden ratio from 24px) -->
    <h3 class="text-xl font-semibold">
      <!-- Font: 24px (golden step from 16px) -->
      Total Bookings
    </h3>
    <div class="w-12 h-12">
      <!-- Icon: 48px (golden ratio) -->
      <svg>...</svg>
    </div>
  </div>
  
  <p class="text-3xl font-bold mb-2">
    <!-- Font: 40px (hero number) -->
    <!-- Margin: 8px (base unit) -->
    1,234
  </p>
  
  <p class="text-sm text-gray-600">
    <!-- Font: 14px (body text) -->
    This month
  </p>
</div>
```

**Form Fields:**
```html
<div class="mb-6">
  <!-- Margin bottom: 24px (golden ratio spacing) -->
  
  <label class="block text-sm font-medium mb-2">
    <!-- Label: 14px -->
    <!-- Margin: 8px -->
    Email Address
  </label>
  
  <input class="w-full px-4 py-3 rounded-lg">
    <!-- Padding X: 16px -->
    <!-- Padding Y: 12px -->
    <!-- Border radius: 8px (half of padding) -->
  </input>
</div>
```

**Button Hierarchy:**
```html
<!-- Primary button (golden ratio sizing) -->
<button class="px-6 py-3 text-base">
  <!-- Padding: 24px × 12px -->
  <!-- Font: 16px -->
  Primary Action
</button>

<!-- Secondary button (scaled down by golden ratio) -->
<button class="px-4 py-2 text-sm">
  <!-- Padding: 16px × 8px -->
  <!-- Font: 14px -->
  Secondary
</button>

<!-- Tertiary button (scaled down again) -->
<button class="px-3 py-1.5 text-xs">
  <!-- Padding: 12px × 6px -->
  <!-- Font: 12px -->
  Tertiary
</button>
```

---

### **E. Golden Ratio Quick Reference**

**Multiply by 1.618 to go UP:**
```
8px  × 1.618 = 12.944px  ≈ 12px
12px × 1.618 = 19.416px  ≈ 20px
16px × 1.618 = 25.888px  ≈ 24px
24px × 1.618 = 38.832px  ≈ 40px
40px × 1.618 = 64.720px  ≈ 64px
```

**Divide by 1.618 to go DOWN:**
```
64px ÷ 1.618 = 39.555px  ≈ 40px
40px ÷ 1.618 = 24.722px  ≈ 24px
24px ÷ 1.618 = 14.833px  ≈ 16px
16px ÷ 1.618 = 9.888px   ≈ 12px
12px ÷ 1.618 = 7.416px   ≈ 8px
```

---

### **F. Common Mistakes to AVOID**

❌ **DON'T: Random sizes**
```css
font-size: 17px;
margin: 13px;
padding: 19px;
```

✅ **DO: Golden ratio progression**
```css
font-size: 16px;  /* Base */
margin: 12px;     /* Golden step */
padding: 24px;    /* Golden step */
```

❌ **DON'T: Equal spacing everywhere**
```html
<div class="p-4 m-4 gap-4">
  <!-- Too uniform, no visual hierarchy -->
</div>
```

✅ **DO: Varied golden ratio spacing**
```html
<div class="p-6 mb-8 space-y-4">
  <!-- Creates rhythm: 24px, 32px, 16px -->
</div>
```

❌ **DON'T: Arbitrary card sizes**
```html
<div class="w-72 h-64">
  <!-- 288px × 256px = 1.125:1 (not golden ratio) -->
</div>
```

✅ **DO: Golden ratio dimensions**
```html
<div class="w-80 h-auto">
  <!-- 320px width, let content flow to ~198px height naturally -->
</div>
```

---

### **G. Golden Ratio Checklist**

Before finalizing any design, verify:

- [ ] Font sizes follow golden ratio scale (12, 14, 16, 24, 32, 40, 64)
- [ ] Spacing uses golden ratio progression (4, 8, 12, 16, 24, 32, 48, 64)
- [ ] Margins between elements create visual rhythm
- [ ] Padding inside cards follows the scale
- [ ] Button sizes are proportional
- [ ] Card dimensions approach 1.618:1 ratio
- [ ] Icons sized using golden ratio (12, 16, 24, 32, 48px)
- [ ] Line heights are 1.5-1.8× font size
- [ ] Section spacing increases progressively

---

## 📐 4. TYPOGRAPHY RULES

### Font Hierarchy (Poppins + Golden Ratio)

```css
/* Display / Hero (64px - Golden Ratio × 4) */
.display, .hero-text {
  font-size: 64px;
  font-weight: 700; /* Bold */
  color: #00473e; /* lgu-headline */
  line-height: 1.2;
  letter-spacing: -0.02em;
}

/* H1 - Page Headers (40px - Golden Ratio × 2.5) */
h1, .heading-1 {
  font-size: 40px;
  font-weight: 700; /* Bold */
  color: #00473e; /* lgu-headline */
  line-height: 1.3;
}

/* H2 - Section Headers (32px - Golden Ratio × 2) */
h2, .heading-2 {
  font-size: 32px;
  font-weight: 600; /* Semibold */
  color: #00473e; /* lgu-headline */
  line-height: 1.4;
}

/* H3 - Subheadings (24px - Golden Ratio × 1.5) */
h3, .heading-3, .subheadline {
  font-size: 24px;
  font-weight: 600; /* Semibold */
  color: #00473e; /* lgu-headline */
  line-height: 1.4;
}

/* Large Body (18px) */
.lead, .intro-text {
  font-size: 18px;
  font-weight: 400; /* Regular */
  color: #475d5b; /* lgu-paragraph */
  line-height: 1.7;
}

/* Body Text - Base (16px) */
p, .body-text {
  font-size: 16px;
  font-weight: 400; /* Regular */
  color: #475d5b; /* lgu-paragraph */
  line-height: 1.6;
}

/* Small Body (14px) */
.text-small, small {
  font-size: 14px;
  font-weight: 400; /* Regular */
  color: #475d5b; /* lgu-paragraph */
  line-height: 1.5;
}

/* Captions / Labels (12px) */
.caption, .label-text {
  font-size: 12px;
  font-weight: 500; /* Medium */
  color: #475d5b; /* lgu-paragraph */
  line-height: 1.4;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

/* Buttons (16px - Base) */
button, .btn {
  font-size: 16px;
  font-weight: 600; /* Semibold */
  color: #00473e; /* lgu-button-text */
  letter-spacing: 0.02em;
}

/* Labels (14px) */
label, .form-label {
  font-size: 14px;
  font-weight: 500; /* Medium */
  color: #00473e; /* lgu-headline */
}
```

### Typography Principles:
✅ **DO:**
- Establish clear hierarchy with size, weight, color, and contrast
- Use proper line-height (1.5-1.8 for body text)
- Keep line-length between 40-75 characters
- Make differences obvious and intentional
- Heading fonts can be stylized, body fonts MUST be legible

❌ **DON'T:**
- Use too many different font families (stick to Poppins)
- Make differences hard to notice
- Ignore line-height (causes poor readability)
- Use lines that are too long (readers lose focus)
- Use lines that are too short (disrupts reading flow)

---

## 🔄 5. CODE QUALITY & ARCHITECTURE PRINCIPLES

### A. Avoid Redundancy (Single Source of Truth)

**CRITICAL FOR CAPSTONE:** Excessive redundancy is strictly forbidden in academic projects and professional development.

**What is Code Redundancy?**
- Duplicate logic in multiple places
- Copy-pasted code blocks
- Multiple functions doing the same thing
- Repeated database queries
- Identical validation rules scattered across files

---

### **Why Avoid Redundancy?**

✅ **Maintainability:** Change logic once, not in 10 places  
✅ **Consistency:** One source = no conflicting behaviors  
✅ **Debugging:** Fix bugs in one place  
✅ **Performance:** Query database once, reuse result  
✅ **Defense Success:** Panel looks for clean, professional code  

---

### **DRY Principle: Don't Repeat Yourself**

❌ **BAD: Redundant Code**
```php
// Conflict check in Controller A
$conflicts = Booking::where('facility_id', $facilityId)
    ->where('event_date', $date)
    ->whereIn('status', ['approved', 'paid'])
    ->where(function($q) use ($start, $end) {
        $q->where('start_time', '<', $end)
          ->where('end_time', '>', $start);
    })->get();

// SAME logic repeated in Controller B
$conflicts = Booking::where('facility_id', $facilityId)
    ->where('event_date', $date)
    ->whereIn('status', ['approved', 'paid'])
    ->where(function($q) use ($start, $end) {
        $q->where('start_time', '<', $end)
          ->where('end_time', '>', $start);
    })->get();

// SAME logic repeated in Service C
// ... repeated again!
```

✅ **GOOD: Single Source of Truth**
```php
// In Booking model (ONE place)
public function checkScheduleConflicts(): array
{
    $conflicts = self::where('facility_id', $this->facility_id)
        ->where('event_date', $this->event_date)
        ->whereIn('status', ['approved', 'paid'])
        ->where(function($query) {
            $query->where('start_time', '<', $this->end_time)
                  ->where('end_time', '>', $this->start_time);
        })->get();

    return [
        'hasConflict' => $conflicts->isNotEmpty(),
        'conflicts' => $conflicts
    ];
}

// Now use EVERYWHERE (Controller A, B, C, etc.)
$conflictCheck = $booking->checkScheduleConflicts();
```

---

### **Common Redundancy Patterns to Avoid**

#### **1. Database Queries**

❌ **DON'T:** Query the same data multiple times
```php
// Bad: 3 separate queries
$user = User::find($id);
$userName = User::find($id)->name;
$userEmail = User::find($id)->email;
```

✅ **DO:** Query once, reuse
```php
// Good: 1 query
$user = User::find($id);
$userName = $user->name;
$userEmail = $user->email;
```

---

#### **2. Validation Rules**

❌ **DON'T:** Duplicate validation across controllers
```php
// Controller A
$request->validate([
    'email' => 'required|email|max:255',
    'phone' => 'required|regex:/^[0-9]{10}$/',
]);

// Controller B - SAME rules copied
$request->validate([
    'email' => 'required|email|max:255',
    'phone' => 'required|regex:/^[0-9]{10}$/',
]);
```

✅ **DO:** Use Form Requests
```php
// app/Http/Requests/UserRequest.php (ONE place)
class UserRequest extends FormRequest
{
    public function rules()
    {
        return [
            'email' => 'required|email|max:255',
            'phone' => 'required|regex:/^[0-9]{10}$/',
        ];
    }
}

// Use everywhere
public function store(UserRequest $request) { ... }
```

---

#### **3. Blade Components**

❌ **DON'T:** Copy-paste HTML
```blade
<!-- Page A -->
<div class="bg-white rounded-xl shadow-sm p-6">
    <h3 class="text-xl font-bold mb-4">Title</h3>
    <p>Content</p>
</div>

<!-- Page B - SAME card copied -->
<div class="bg-white rounded-xl shadow-sm p-6">
    <h3 class="text-xl font-bold mb-4">Title</h3>
    <p>Content</p>
</div>
```

✅ **DO:** Create reusable components
```blade
<!-- resources/views/components/card.blade.php -->
<div class="bg-white rounded-xl shadow-sm p-6">
    <h3 class="text-xl font-bold mb-4">{{ $title }}</h3>
    <p>{{ $slot }}</p>
</div>

<!-- Use everywhere -->
<x-card title="Title">Content</x-card>
```

---

#### **4. JavaScript Functions**

❌ **DON'T:** Duplicate functions
```javascript
// Page 1
function formatCurrency(amount) {
    return '₱' + amount.toFixed(2);
}

// Page 2 - SAME function copied
function formatCurrency(amount) {
    return '₱' + amount.toFixed(2);
}
```

✅ **DO:** Create utility file
```javascript
// resources/js/utils.js (ONE place)
export function formatCurrency(amount) {
    return '₱' + amount.toFixed(2);
}

// Import everywhere
import { formatCurrency } from './utils.js';
```

---

### **How to Identify Redundancy**

Ask yourself:
1. **Am I copying code from another file?** → Create shared function
2. **Does this logic exist elsewhere?** → Use the existing one
3. **Will I need this in multiple places?** → Make it reusable
4. **Am I querying the same data twice?** → Query once, store in variable
5. **Are these validation rules similar?** → Extract to Form Request

---

### **Refactoring Checklist**

Before committing code, check:

- [ ] No copy-pasted code blocks
- [ ] No duplicate database queries
- [ ] Validation rules in Form Requests, not controllers
- [ ] Common UI patterns extracted to components
- [ ] Utility functions in shared files
- [ ] Business logic in Models, not Controllers
- [ ] Single source of truth for each feature

---

## 🎯 6. UI/UX DESIGN PRINCIPLES

### A. Error Handling (Form Validation)

✅ **GOOD:** Specific error messages
```html
<!-- Show which field has the error -->
<input class="border-2 border-lgu-tertiary" />
<p class="text-lgu-tertiary text-sm mt-1">Please enter a valid card number</p>
```

❌ **BAD:** Generic error messages
```html
<p class="text-red-500">Incorrect data. Please check your info</p>
```

### B. Form Field Grouping

✅ **GOOD:** Group related fields together
```
Card Number: [________________]

Exp. Date: [08/2024]    CVV: [456]

Phone Number: [+91] [5834567892]
```

❌ **BAD:** Random field order
```
Card Number: [________________]
CVV: [456]
Phone Number: [5834567892]
Exp. Date: [08/2024]
```

### C. Border Radius Consistency

✅ **GOOD:** Outer radius = Inner radius + Padding
```css
.card {
  padding: 10px;
  border-radius: 24px; /* Outer */
}

.card-inner {
  border-radius: 14px; /* Inner = 24px - 10px */
}
```

❌ **BAD:** Same radius for inner and outer
```css
.card { border-radius: 24px; }
.card-inner { border-radius: 24px; } /* ❌ Looks awkward */
```

### D. Visual Hierarchy

✅ **DO:**
- Bold important data (e.g., balance amount: `$7,485`)
- Use icons & indicators to show status/growth
- Add shadows for depth and modern look
- Use friendly relative dates ("3 Week Ago" vs "8/7/2024")
- Guide users' eyes with size, weight, color, contrast

❌ **DON'T:**
- Make everything the same visual weight
- Use dates that are hard to parse quickly
- Ignore visual emphasis on key metrics
- Create flat, lifeless interfaces

### E. Card Component Design

Every card should follow this structure:

```html
<div class="card bg-white rounded-2xl shadow-lg p-6">
  <!-- 1. Image (optional) -->
  <img src="..." class="rounded-xl mb-4" />
  
  <!-- 2. Title -->
  <h3 class="text-xl font-bold text-lgu-headline mb-2">Card Title</h3>
  
  <!-- 3. Description -->
  <p class="text-sm text-lgu-paragraph mb-4">
    Card description goes here...
  </p>
  
  <!-- 4. Actions -->
  <div class="flex gap-2">
    <button class="btn-secondary">Learn More</button>
    <button class="btn-primary">Buy Now</button>
  </div>
</div>
```

---

## 🛠️ 7. MANDATORY FEATURES (ALL SYSTEMS)

Every system page MUST implement these features where applicable:

### **A. SORTING**
- All data tables must have sortable columns
- Default: Sort by most recent (created_at DESC)
- Show sort direction indicators (↑ ↓)

### **B. FILTERS**
- All data tables must have filters
- Minimum: Status filter, Date range filter
- Show active filter count badge

### **C. SEARCH**
- All data tables must have search
- Debounce: 300ms
- Search across relevant fields
- Show "No results found" state

### **D. 1-MINUTE OTP**
- OTP expires in exactly 60 seconds
- Show countdown timer
- Allow "Resend OTP" after expiry
- Maximum 3 attempts before cooldown

### **E. USERNAME: EMAIL**
- Username field MUST accept email format
- Validate email format on blur
- No separate username field needed

### **F. 2-MINUTE SESSION TIMEOUT**
- **CRITICAL FOR FINAL DEFENSE**
- Auto-logout after 2 minutes of inactivity
- Show warning modal at 1:45 (15 seconds before)
- Reset timer on any user interaction

```javascript
// Session timeout implementation
let sessionTimeout;
const SESSION_DURATION = 120000; // 2 minutes

function resetSessionTimer() {
  clearTimeout(sessionTimeout);
  sessionTimeout = setTimeout(logoutUser, SESSION_DURATION);
}

// Reset on user activity
['click', 'mousemove', 'keypress'].forEach(event => {
  document.addEventListener(event, resetSessionTimer);
});
```

### **G. PAGINATION**
- Show max 15 items per page (default)
- Allow page size selection: 10, 15, 25, 50
- Show total count and current range
- Example: "Showing 1-15 of 234 results"

### **H. CSV AND PDF FOR REPORTS**
- All reports MUST export to both CSV and PDF
- CSV: Use Laravel Excel
- PDF: Use DomPDF or similar
- Include filters/date range in filename

### **I. ARCHIVES (NO PERMANENT DELETION)**
- **NEVER use `->delete()`**
- **ALWAYS use soft deletes (`deleted_at`)**
- All models must use `SoftDeletes` trait
- Provide "Archive" and "Restore" actions
- Admin can view archived items

```php
// Correct approach
use Illuminate\Database\Eloquent\SoftDeletes;

class Facility extends Model {
    use SoftDeletes;
}

// Archive (soft delete)
$facility->delete(); // Sets deleted_at

// Restore
$facility->restore();

// View archived
$archived = Facility::onlyTrashed()->get();
```

### **J. PERMISSION ROLE-BASED ACCESS (RBAC)**
- 4 Roles: EIS Super Admin, Admin, Staff, Citizen
- Use Laravel Gates or Policies
- Check permissions in controllers AND views

```php
// Controller
if (!auth()->user()->can('approve-bookings')) {
    abort(403);
}

// Blade
@can('approve-bookings')
    <button>Approve</button>
@endcan
```

### **K. AUTHENTICATION**
- Use Laravel Sanctum or Passport
- JWT tokens for API
- Secure password hashing (bcrypt)
- Email verification required
- "Remember Me" functionality

### **L. NOTIFICATIONS**
- Real-time notifications
- Show count badge on bell icon
- Mark as read functionality
- Notification types: Success, Info, Warning, Error
- Store in database for history

### **M. RESPONSIVE DESIGN**
- Mobile-first approach
- Breakpoints: 
  - Mobile: < 640px
  - Tablet: 640px - 1024px
  - Desktop: > 1024px
- Test on actual devices before defense

### **N. ALERTS (SWEETALERT2)**
- **ALL alerts MUST use SweetAlert2**
- **ALL alerts MUST be modal (no toast/inline)**

```javascript
// Success alert
Swal.fire({
  icon: 'success',
  title: 'Booking Confirmed!',
  text: 'Your facility has been reserved.',
  confirmButtonColor: '#faae2b',
  confirmButtonText: 'OK'
});

// Error alert
Swal.fire({
  icon: 'error',
  title: 'Validation Failed',
  text: 'Please fill in all required fields.',
  confirmButtonColor: '#fa5246'
});

// Confirmation dialog
Swal.fire({
  title: 'Are you sure?',
  text: "You won't be able to revert this!",
  icon: 'warning',
  showCancelButton: true,
  confirmButtonColor: '#faae2b',
  cancelButtonColor: '#fa5246',
  confirmButtonText: 'Yes, archive it!'
}).then((result) => {
  if (result.isConfirmed) {
    // Perform action
  }
});
```

### **O. AUDIT LOGS**
- Log ALL CRUD operations
- Store: User ID, Action, Model, Old Value, New Value, IP, Timestamp
- Admin can view audit trail

```php
// Create audit_logs table
Schema::create('audit_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->nullable();
    $table->string('action'); // created, updated, deleted, restored
    $table->string('model'); // Facility, Booking, etc.
    $table->unsignedBigInteger('model_id');
    $table->json('old_values')->nullable();
    $table->json('new_values')->nullable();
    $table->ipAddress('ip_address');
    $table->timestamps();
});
```

### **P. FONT: POPPINS**
- Import from Google Fonts
- Use all weights: 400 (Regular), 500 (Medium), 600 (Semibold), 700 (Bold)
- Apply to ALL text in the system

```html
<!-- In <head> -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
```

```css
/* In Tailwind config */
fontFamily: {
  'sans': ['Poppins', 'sans-serif'],
}
```

---

## 🚦 8. PRE-COMMIT CHECKLIST

Before you commit ANY code, verify:

- [ ] Poppins font applied everywhere
- [ ] LGU color scheme used correctly (no random colors)
- [ ] Lucide icons used (no emojis ❌)
- [ ] Proper typography hierarchy
- [ ] SweetAlert2 for all alerts (no native alerts)
- [ ] Responsive on mobile/tablet/desktop
- [ ] Clear, specific error messages
- [ ] Proper form field grouping
- [ ] Good line-height and spacing
- [ ] 2-minute session timeout implemented
- [ ] Search/sort/filter on data tables
- [ ] Pagination implemented
- [ ] Soft deletes (no permanent deletion)
- [ ] RBAC permissions checked
- [ ] Audit logging on CRUD operations

---

## 🎯 9. FINAL DEFENSE REQUIREMENTS

The panel MUST see these working:

### **For Admin Portal:**
✅ Dashboard with graphs/analytics (from AI)  
✅ Data analytics visualization  
✅ Email/password authentication  
✅ 2-minute session timeout **(CRITICAL)**  
✅ Report generation (CSV + PDF)  
✅ Cloud/local server indicator  
✅ All RBAC permissions working  
✅ Audit logs visible  
✅ Responsive design demonstration  

### **User Roles Demonstration:**
1. **EIS Super Admin** - System configuration only
2. **Admin** - Operational features (approve, manage, configure)
3. **Staff** - Verification and support
4. **Citizen** - Booking and viewing

---

## 📚 10. COMPONENT LIBRARY

### Button Styles

```html
<!-- Primary Button -->
<button class="px-6 py-3 bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:opacity-90 transition">
  Primary Action
</button>

<!-- Secondary Button -->
<button class="px-6 py-3 border-2 border-lgu-stroke text-lgu-headline font-semibold rounded-lg hover:bg-lgu-bg transition">
  Secondary Action
</button>

<!-- Danger Button -->
<button class="px-6 py-3 bg-lgu-tertiary text-white font-semibold rounded-lg hover:opacity-90 transition">
  Delete
</button>
```

### Input Styles

```html
<!-- Text Input -->
<div class="mb-4">
  <label class="block text-sm font-medium text-lgu-headline mb-2">
    Full Name
  </label>
  <input 
    type="text" 
    class="w-full px-4 py-3 border-2 border-lgu-stroke rounded-lg focus:border-lgu-highlight focus:outline-none text-lgu-paragraph"
    placeholder="Enter your full name"
  />
</div>

<!-- Input with Error -->
<div class="mb-4">
  <label class="block text-sm font-medium text-lgu-headline mb-2">
    Email Address
  </label>
  <input 
    type="email" 
    class="w-full px-4 py-3 border-2 border-lgu-tertiary rounded-lg focus:border-lgu-tertiary focus:outline-none text-lgu-paragraph"
    placeholder="Enter your email"
  />
  <p class="text-lgu-tertiary text-sm mt-1">
    Please enter a valid email address
  </p>
</div>
```

### Data Table Template

```html
<div class="bg-white rounded-xl shadow-lg p-6">
  <!-- Header with Search, Filter, Export -->
  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-lgu-headline">Bookings</h2>
    
    <div class="flex gap-3">
      <!-- Search -->
      <input 
        type="search" 
        placeholder="Search bookings..."
        class="px-4 py-2 border-2 border-lgu-stroke rounded-lg"
      />
      
      <!-- Filter -->
      <select class="px-4 py-2 border-2 border-lgu-stroke rounded-lg">
        <option>All Status</option>
        <option>Pending</option>
        <option>Confirmed</option>
      </select>
      
      <!-- Export -->
      <button class="btn-primary">Export CSV</button>
      <button class="btn-primary">Export PDF</button>
    </div>
  </div>
  
  <!-- Table -->
  <table class="w-full">
    <thead class="bg-lgu-bg">
      <tr>
        <th class="px-4 py-3 text-left text-sm font-semibold text-lgu-headline">
          ID
        </th>
        <!-- More columns -->
      </tr>
    </thead>
    <tbody>
      <tr class="border-b border-lgu-stroke hover:bg-lgu-bg">
        <td class="px-4 py-3 text-sm text-lgu-paragraph">001</td>
        <!-- More cells -->
      </tr>
    </tbody>
  </table>
  
  <!-- Pagination -->
  <div class="flex justify-between items-center mt-6">
    <p class="text-sm text-lgu-paragraph">Showing 1-15 of 234 results</p>
    <div class="flex gap-2">
      <button class="px-3 py-1 border border-lgu-stroke rounded">Previous</button>
      <button class="px-3 py-1 bg-lgu-button text-lgu-button-text rounded">1</button>
      <button class="px-3 py-1 border border-lgu-stroke rounded">2</button>
      <button class="px-3 py-1 border border-lgu-stroke rounded">Next</button>
    </div>
  </div>
</div>
```

---

## ⚠️ COMMON MISTAKES TO AVOID

❌ **DON'T:**
1. Use emojis in the UI (use Lucide icons)
2. Use dollar signs ($) for currency (use Philippine Peso ₱ ONLY)
3. Use `confirm()` or `alert()` (use SweetAlert2)
4. Hard delete records (use soft deletes)
5. Forget session timeout (critical for defense)
6. Skip responsive testing
7. Use random colors (stick to theme)
8. Forget audit logging
9. Skip form validation
10. Ignore error states
11. Use inconsistent spacing

✅ **DO:**
1. Follow the design system religiously
2. Test on mobile before committing
3. Use semantic HTML
4. Write accessible markup
5. Add loading states
6. Show empty states
7. Provide helpful error messages
8. Log all important actions
9. Check RBAC permissions everywhere
10. Keep it consistent across all pages

---

## 📞 QUESTIONS?

If you're unsure about:
- **Design:** Refer to sections 3-4
- **Features:** Refer to section 5
- **Colors:** Refer to section 1
- **Tech Stack:** Refer to section 2

**When in doubt, ask the team lead!**

---

**Last Updated:** December 8, 2025  
**Version:** 1.0  
**Status:** 🔒 LOCKED FOR FINAL DEFENSE

---

*Remember: These rules ensure consistency, professionalism, and success in your final defense. Follow them strictly!* 🎯

