<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EquipmentItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $equipment = [
            // Chairs
            [
                'name' => 'Monobloc Chairs (White)',
                'category' => 'chairs',
                'description' => 'Standard white plastic monobloc chairs, stackable and lightweight.',
                'price_per_unit' => 15.00,
                'quantity_available' => 500,
                'is_available' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Upholstered Chairs',
                'category' => 'chairs',
                'description' => 'Padded upholstered chairs for more formal events.',
                'price_per_unit' => 50.00,
                'quantity_available' => 100,
                'is_available' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            
            // Tables
            [
                'name' => 'Round Tables (6-seater)',
                'category' => 'tables',
                'description' => 'Round banquet tables that seat 6 people comfortably.',
                'price_per_unit' => 250.00,
                'quantity_available' => 50,
                'is_available' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Rectangular Tables (8-seater)',
                'category' => 'tables',
                'description' => 'Long rectangular tables ideal for buffet setups or seating 8 people.',
                'price_per_unit' => 300.00,
                'quantity_available' => 40,
                'is_available' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Cocktail Tables (Standing)',
                'category' => 'tables',
                'description' => 'High cocktail tables for standing events and networking.',
                'price_per_unit' => 200.00,
                'quantity_available' => 30,
                'is_available' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            
            // Sound System
            [
                'name' => 'PA System (Basic)',
                'category' => 'sound_system',
                'description' => 'Basic PA system with 2 speakers, 2 microphones, and mixer. Good for small to medium events.',
                'price_per_unit' => 2500.00,
                'quantity_available' => 5,
                'is_available' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'PA System (Premium)',
                'category' => 'sound_system',
                'description' => 'Premium PA system with 4 speakers, 4 wireless microphones, mixer, and subwoofer for large events.',
                'price_per_unit' => 5000.00,
                'quantity_available' => 3,
                'is_available' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Wireless Microphone (Additional)',
                'category' => 'sound_system',
                'description' => 'Extra wireless microphone unit.',
                'price_per_unit' => 500.00,
                'quantity_available' => 10,
                'is_available' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            
            // Lighting
            [
                'name' => 'LED Stage Lights (Set of 4)',
                'category' => 'lighting',
                'description' => 'Colorful LED stage lights for events and performances.',
                'price_per_unit' => 1500.00,
                'quantity_available' => 8,
                'is_available' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'String Lights (50 meters)',
                'category' => 'lighting',
                'description' => 'Warm white string lights for decoration.',
                'price_per_unit' => 800.00,
                'quantity_available' => 15,
                'is_available' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            
            // Decorations
            [
                'name' => 'Table Linens (White)',
                'category' => 'decorations',
                'description' => 'White table cloths for round or rectangular tables.',
                'price_per_unit' => 100.00,
                'quantity_available' => 100,
                'is_available' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Chair Covers (White)',
                'category' => 'decorations',
                'description' => 'Elegant white chair covers with sash options.',
                'price_per_unit' => 50.00,
                'quantity_available' => 200,
                'is_available' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            
            // Miscellaneous
            [
                'name' => 'Projector and Screen',
                'category' => 'av_equipment',
                'description' => 'HD projector with 10ft projection screen for presentations.',
                'price_per_unit' => 3000.00,
                'quantity_available' => 4,
                'is_available' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Portable Stage Platform (4x8 ft)',
                'category' => 'staging',
                'description' => 'Modular stage platform sections that can be combined.',
                'price_per_unit' => 1000.00,
                'quantity_available' => 20,
                'is_available' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Generator (5KVA)',
                'category' => 'power',
                'description' => 'Backup generator for outdoor events.',
                'price_per_unit' => 3500.00,
                'quantity_available' => 3,
                'is_available' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::connection('facilities_db')->table('equipment_items')->insert($equipment);
        
        $this->command->info('Equipment items seeded successfully!');
    }
}
