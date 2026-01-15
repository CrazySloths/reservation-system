<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FacilitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get city IDs
        $caloocanCity = DB::connection('facilities_db')
            ->table('lgu_cities')
            ->where('city_code', 'CLCN')
            ->first();
            
        $quezonCity = DB::connection('facilities_db')
            ->table('lgu_cities')
            ->where('city_code', 'QC')
            ->first();
            
        $manilaCity = DB::connection('facilities_db')
            ->table('lgu_cities')
            ->where('city_code', 'MNL')
            ->first();

        $facilities = [
            // Caloocan City Facilities
            [
                'name' => 'Caloocan City Convention Center',
                'description' => 'A modern convention center perfect for large events, conferences, and exhibitions. Features air conditioning, stage, and audio-visual equipment.',
                'address' => '123 Rizal Avenue Extension, Caloocan City',
                'capacity' => 500,
                'rate_per_hour' => 2333.33, // ₱7,000 / 3 hours
                'lgu_city_id' => $caloocanCity->id ?? null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Caloocan Sports Complex - Covered Court',
                'description' => 'Indoor basketball court suitable for sports events, tournaments, and large gatherings. Includes locker rooms and seating area.',
                'address' => '456 10th Avenue, Grace Park, Caloocan City',
                'capacity' => 300,
                'rate_per_hour' => 2333.33,
                'lgu_city_id' => $caloocanCity->id ?? null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Caloocan City Hall Function Room',
                'description' => 'Elegant function room ideal for seminars, training sessions, and formal meetings. Equipped with projector and comfortable seating.',
                'address' => 'Caloocan City Hall, 8th Avenue, Caloocan City',
                'capacity' => 100,
                'rate_per_hour' => 2333.33,
                'lgu_city_id' => $caloocanCity->id ?? null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Barangay 1 Community Center',
                'description' => 'Community hall perfect for barangay events, birthday parties, and small gatherings. Basic amenities included.',
                'address' => 'Barangay 1, Caloocan City',
                'capacity' => 150,
                'rate_per_hour' => 2333.33,
                'lgu_city_id' => $caloocanCity->id ?? null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            
            // Quezon City Facilities
            [
                'name' => 'Quezon City Memorial Circle Events Pavilion',
                'description' => 'Open-air pavilion in the heart of QC Memorial Circle, perfect for outdoor events, concerts, and exhibitions.',
                'address' => 'Quezon Memorial Circle, Quezon City',
                'capacity' => 1000,
                'rate_per_hour' => 2333.33,
                'lgu_city_id' => $quezonCity->id ?? null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'QC Serbisyo Hall',
                'description' => 'Multi-purpose hall suitable for weddings, corporate events, and social gatherings. Fully air-conditioned with ample parking.',
                'address' => 'Quezon Avenue, Quezon City',
                'capacity' => 400,
                'rate_per_hour' => 2333.33,
                'lgu_city_id' => $quezonCity->id ?? null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Quezon City Sports Club Gymnasium',
                'description' => 'Indoor gym facility perfect for sports events, fitness activities, and athletic competitions.',
                'address' => 'Araneta Center, Cubao, Quezon City',
                'capacity' => 250,
                'rate_per_hour' => 2333.33,
                'lgu_city_id' => $quezonCity->id ?? null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            
            // Manila Facilities
            [
                'name' => 'Manila City Hall Assembly Hall',
                'description' => 'Historic assembly hall perfect for formal events, government functions, and cultural activities.',
                'address' => 'Arroceros Street, Manila',
                'capacity' => 300,
                'rate_per_hour' => 2333.33,
                'lgu_city_id' => $manilaCity->id ?? null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Rizal Park Open Grounds',
                'description' => 'Spacious outdoor venue ideal for festivals, exhibitions, and large public events. Located in historic Rizal Park.',
                'address' => 'Roxas Boulevard, Ermita, Manila',
                'capacity' => 2000,
                'rate_per_hour' => 2333.33,
                'lgu_city_id' => $manilaCity->id ?? null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Manila Youth Center',
                'description' => 'Modern facility designed for youth activities, workshops, and community programs. Features multimedia equipment.',
                'address' => 'Pedro Gil Street, Manila',
                'capacity' => 120,
                'rate_per_hour' => 2333.33,
                'lgu_city_id' => $manilaCity->id ?? null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::connection('facilities_db')->table('facilities')->insert($facilities);
        
        $this->command->info('Facilities seeded successfully!');
        $this->command->info('Created ' . count($facilities) . ' facilities across 3 cities.');
    }
}

