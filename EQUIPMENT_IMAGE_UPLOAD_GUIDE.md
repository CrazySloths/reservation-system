# Equipment Image Upload Guide

## Overview
Equipment items now display as cards with images. This guide explains how to add images to your equipment items.

## Database Migration
The `image_path` column has been added to the `equipment_items` table via migration:
- Location: `database/migrations/2025_11_18_163959_add_image_path_to_equipment_items_table.php`
- Database: `facilities_db`
- Table: `equipment_items`

## Image Storage Location
Images should be stored in: `storage/app/public/equipment/`

## Adding Images to Equipment

### Method 1: Manual Upload + Database Update

1. **Upload your equipment images** to `storage/app/public/equipment/`
   - Example: `LED-Projector.jpg`, `LED-TV.jpg`, `PA-System.jpg`

2. **Run this SQL to update equipment with images**:

```sql
-- Connect to facilities_db database
USE faci_facility;

-- Update specific equipment items with image paths
UPDATE equipment_items SET image_path = 'equipment/LED-Projector.jpg' WHERE name = 'LED Projector + Screen Package';
UPDATE equipment_items SET image_path = 'equipment/LED-TV.jpg' WHERE name = 'LED TV (55 inch)';
UPDATE equipment_items SET image_path = 'equipment/PA-System.jpg' WHERE name = 'Portable PA System';
UPDATE equipment_items SET image_path = 'equipment/Sound-System.jpg' WHERE name = 'Sound System Package';
UPDATE equipment_items SET image_path = 'equipment/Microphone.jpg' WHERE name = 'Wireless Microphone Set';
UPDATE equipment_items SET image_path = 'equipment/Chairs.jpg' WHERE name = 'Monobloc Chairs (Set of 50)';
UPDATE equipment_items SET image_path = 'equipment/Tables.jpg' WHERE name = 'Folding Tables (Set of 10)';
UPDATE equipment_items SET image_path = 'equipment/Canopy.jpg' WHERE name = 'Tent/Canopy (10x10)';
UPDATE equipment_items SET image_path = 'equipment/Stage-Platform.jpg' WHERE name = 'Stage Platform';
UPDATE equipment_items SET image_path = 'equipment/Backdrop.jpg' WHERE name = 'Backdrop Stand';
```

### Method 2: Using Laravel Tinker

1. **Open terminal** in your project directory

2. **Run tinker**:
```bash
php artisan tinker
```

3. **Update equipment images**:
```php
use Illuminate\Support\Facades\DB;

// Update a single equipment item
DB::connection('facilities_db')->table('equipment_items')
    ->where('name', 'LED Projector + Screen Package')
    ->update(['image_path' => 'equipment/LED-Projector.jpg']);

// Or update all at once
$updates = [
    'LED Projector + Screen Package' => 'equipment/LED-Projector.jpg',
    'LED TV (55 inch)' => 'equipment/LED-TV.jpg',
    'Portable PA System' => 'equipment/PA-System.jpg',
    // ... add more
];

foreach ($updates as $name => $path) {
    DB::connection('facilities_db')->table('equipment_items')
        ->where('name', $name)
        ->update(['image_path' => $path]);
}
```

## Image Requirements

- **Format**: JPG, JPEG, or PNG
- **Recommended Size**: 800x600 pixels or higher
- **Aspect Ratio**: 4:3 or 16:9 works best
- **File Size**: Under 2MB per image
- **Naming**: Use descriptive names (e.g., `LED-Projector.jpg`, `PA-System.jpg`)

## Testing

After adding images, refresh the booking page (Step 2: Select Equipment) to see the equipment cards with images.

If no image is set, a placeholder icon will be shown with a gradient background.

## Future Enhancement

For a more user-friendly approach, consider creating an admin panel where you can upload equipment images directly through a web interface.

