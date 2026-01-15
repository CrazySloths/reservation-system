<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LguCitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            [
                'city_name' => 'Caloocan City',
                'city_code' => 'CLCN',
                'description' => 'The primary LGU operating this reservation system. Residents receive automatic discounts.',
                'status' => 'active',
                'has_external_integration' => false,
                'facility_count' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'city_name' => 'Quezon City',
                'city_code' => 'QC',
                'description' => 'Largest city in Metro Manila by population. Integrated facilities available for booking.',
                'status' => 'active',
                'has_external_integration' => false,
                'facility_count' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'city_name' => 'Manila',
                'city_code' => 'MNL',
                'description' => 'The capital city of the Philippines.',
                'status' => 'active',
                'has_external_integration' => false,
                'facility_count' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'city_name' => 'Makati',
                'city_code' => 'MKT',
                'description' => 'Central business district of Metro Manila.',
                'status' => 'coming_soon',
                'has_external_integration' => false,
                'facility_count' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'city_name' => 'Pasig',
                'city_code' => 'PSG',
                'description' => 'Highly urbanized city in Metro Manila.',
                'status' => 'coming_soon',
                'has_external_integration' => false,
                'facility_count' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'city_name' => 'Taguig',
                'city_code' => 'TGG',
                'description' => 'Home to Bonifacio Global City.',
                'status' => 'coming_soon',
                'has_external_integration' => false,
                'facility_count' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::connection('facilities_db')->table('lgu_cities')->insert($cities);
        
        $this->command->info('LGU Cities seeded successfully!');
    }
}

