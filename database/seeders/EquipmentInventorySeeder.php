<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EquipmentInventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $equipment = [
            // AV Equipment
            ['equipment_name' => 'Projector', 'category' => 'AV Equipment', 'total_quantity' => 15, 'available_quantity' => 12, 'in_use_quantity' => 2, 'maintenance_quantity' => 1],
            ['equipment_name' => 'LCD Screen', 'category' => 'AV Equipment', 'total_quantity' => 10, 'available_quantity' => 8, 'in_use_quantity' => 2, 'maintenance_quantity' => 0],
            ['equipment_name' => 'Sound System', 'category' => 'AV Equipment', 'total_quantity' => 8, 'available_quantity' => 6, 'in_use_quantity' => 2, 'maintenance_quantity' => 0],
            ['equipment_name' => 'Wireless Microphone', 'category' => 'AV Equipment', 'total_quantity' => 25, 'available_quantity' => 20, 'in_use_quantity' => 5, 'maintenance_quantity' => 0],
            ['equipment_name' => 'Wired Microphone', 'category' => 'AV Equipment', 'total_quantity' => 30, 'available_quantity' => 25, 'in_use_quantity' => 3, 'maintenance_quantity' => 2],
            ['equipment_name' => 'Stage Lights', 'category' => 'AV Equipment', 'total_quantity' => 20, 'available_quantity' => 18, 'in_use_quantity' => 2, 'maintenance_quantity' => 0],
            
            // Computing
            ['equipment_name' => 'Laptop', 'category' => 'Computing', 'total_quantity' => 12, 'available_quantity' => 10, 'in_use_quantity' => 2, 'maintenance_quantity' => 0],
            ['equipment_name' => 'Desktop Computer', 'category' => 'Computing', 'total_quantity' => 8, 'available_quantity' => 7, 'in_use_quantity' => 1, 'maintenance_quantity' => 0],
            
            // Presentation
            ['equipment_name' => 'Whiteboard', 'category' => 'Presentation', 'total_quantity' => 15, 'available_quantity' => 12, 'in_use_quantity' => 3, 'maintenance_quantity' => 0],
            ['equipment_name' => 'Flip Chart', 'category' => 'Presentation', 'total_quantity' => 20, 'available_quantity' => 18, 'in_use_quantity' => 2, 'maintenance_quantity' => 0],
            ['equipment_name' => 'Podium', 'category' => 'Presentation', 'total_quantity' => 5, 'available_quantity' => 4, 'in_use_quantity' => 1, 'maintenance_quantity' => 0],
            
            // Furniture
            ['equipment_name' => 'Tables', 'category' => 'Furniture', 'total_quantity' => 200, 'available_quantity' => 180, 'in_use_quantity' => 20, 'maintenance_quantity' => 0],
            ['equipment_name' => 'Chairs', 'category' => 'Furniture', 'total_quantity' => 800, 'available_quantity' => 750, 'in_use_quantity' => 50, 'maintenance_quantity' => 0],
            
            // Power & Climate
            ['equipment_name' => 'Extension Cords', 'category' => 'Power', 'total_quantity' => 50, 'available_quantity' => 45, 'in_use_quantity' => 5, 'maintenance_quantity' => 0],
            ['equipment_name' => 'Generator', 'category' => 'Power', 'total_quantity' => 3, 'available_quantity' => 2, 'in_use_quantity' => 1, 'maintenance_quantity' => 0],
            ['equipment_name' => 'Air Conditioning Unit', 'category' => 'Climate Control', 'total_quantity' => 25, 'available_quantity' => 23, 'in_use_quantity' => 1, 'maintenance_quantity' => 1],
            ['equipment_name' => 'Electric Fan', 'category' => 'Climate Control', 'total_quantity' => 60, 'available_quantity' => 55, 'in_use_quantity' => 5, 'maintenance_quantity' => 0],
            
            // Event
            ['equipment_name' => 'Tent/Canopy', 'category' => 'Event', 'total_quantity' => 10, 'available_quantity' => 8, 'in_use_quantity' => 2, 'maintenance_quantity' => 0],
        ];

        foreach ($equipment as $item) {
            DB::connection('facilities_db')->table('equipment_inventory')->insert(array_merge($item, [
                'condition' => 'good',
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}

