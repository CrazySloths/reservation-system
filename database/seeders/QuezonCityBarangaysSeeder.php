<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuezonCityBarangaysSeeder extends Seeder
{
    /**
     * Seed Quezon City barangays linked to the comprehensive districts system.
     * 
     * NOTE: This seeder assumes ComprehensiveDistrictsSeeder has already run.
     */
    public function run(): void
    {
        $authDb = DB::connection('auth_db');

        // Get Quezon City's ID
        $quezonCity = $authDb->table('cities')->where('code', 'QC')->first();
        if (!$quezonCity) {
            $this->command->error('Quezon City not found in cities table!');
            return;
        }

        // Get Quezon City's district IDs from the comprehensive districts table
        $qcDistricts = $authDb->table('districts')
            ->where('city_id', $quezonCity->id)
            ->orderBy('district_number')
            ->get();

        if ($qcDistricts->count() === 0) {
            $this->command->error('No districts found for Quezon City! Run ComprehensiveDistrictsSeeder first.');
            return;
        }

        // Map district numbers to actual IDs
        $districtMap = [];
        foreach ($qcDistricts as $district) {
            $districtMap[$district->district_number] = $district->id;
        }

        // Clear existing Quezon City barangays
        $authDb->table('barangays')->where('city_id', $quezonCity->id)->delete();

        // Barangays with ZIP CODES (based on Philippine Postal Corporation data)
        // Quezon City ZIP codes range: 1100-1119
        $barangays = [
            // District 1 Barangays (ZIP: 1100-1106)
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[1], 'name' => 'Alicia', 'alternate_name' => 'Bago Bantay', 'zip_code' => '1105'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[1], 'name' => 'Bagong Pag-asa', 'alternate_name' => 'North-EDSA, Diliman', 'zip_code' => '1105'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[1], 'name' => 'Bahay Toro', 'alternate_name' => 'Project 8, Pugadlawin', 'zip_code' => '1106'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[1], 'name' => 'Balingasa', 'alternate_name' => 'Balintawak, Cloverleaf', 'zip_code' => '1115'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[1], 'name' => 'Bungad', 'alternate_name' => 'Project 7', 'zip_code' => '1105'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[1], 'name' => 'Damayan', 'alternate_name' => 'San Francisco del Monte', 'zip_code' => '1105'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[1], 'name' => 'Del Monte', 'alternate_name' => 'San Francisco del Monte', 'zip_code' => '1105'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[1], 'name' => 'Katipunan', 'alternate_name' => 'Muñoz', 'zip_code' => '1102'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[1], 'name' => 'Lourdes', 'alternate_name' => 'Santa Mesa Heights', 'zip_code' => '1114'],

            // District 2 Barangays (ZIP: 1107-1109, 1119)
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[2], 'name' => 'Bagong Silangan', 'alternate_name' => 'Payatas', 'zip_code' => '1119'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[2], 'name' => 'Batasan Hills', 'alternate_name' => 'Constitution Hills', 'zip_code' => '1126'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[2], 'name' => 'Commonwealth', 'alternate_name' => 'Manggahan, Litex', 'zip_code' => '1121'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[2], 'name' => 'Holy Spirit', 'alternate_name' => 'Don Antonio, Luzon', 'zip_code' => '1127'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[2], 'name' => 'Payatas', 'alternate_name' => 'Litex', 'zip_code' => '1119'],

            // District 3 Barangays (ZIP: 1110-1111)
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[3], 'name' => 'Amihan', 'alternate_name' => 'Project 3', 'zip_code' => '1102'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[3], 'name' => 'Bagumbayan', 'alternate_name' => 'Eastwood, Libis', 'zip_code' => '1110'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[3], 'name' => 'Bagumbuhay', 'alternate_name' => 'Project 4', 'zip_code' => '1109'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[3], 'name' => 'Bayanihan', 'alternate_name' => 'Project 4', 'zip_code' => '1109'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[3], 'name' => 'Blue Ridge A', 'alternate_name' => 'Project 4', 'zip_code' => '1109'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[3], 'name' => 'Libis', 'alternate_name' => 'Camp Atienza, Eastwood', 'zip_code' => '1110'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[3], 'name' => 'Loyola Heights', 'alternate_name' => 'Katipunan', 'zip_code' => '1108'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[3], 'name' => 'Mangga', 'alternate_name' => 'Cubao, Anonas', 'zip_code' => '1109'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[3], 'name' => 'Socorro', 'alternate_name' => 'Cubao, Araneta City', 'zip_code' => '1109'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[3], 'name' => 'White Plains', 'alternate_name' => 'Camp Aguinaldo', 'zip_code' => '1110'],

            // District 4 Barangays (ZIP: 1112-1113)
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[4], 'name' => 'Bagong Lipunan ng Crame', 'alternate_name' => 'Camp Crame, PNP', 'zip_code' => '1111'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[4], 'name' => 'Botocan', 'alternate_name' => 'Diliman (northern half)', 'zip_code' => '1101'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[4], 'name' => 'Central', 'alternate_name' => 'Diliman, QC Hall', 'zip_code' => '1100'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[4], 'name' => 'Damayang Lagi', 'alternate_name' => 'New Manila', 'zip_code' => '1112'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[4], 'name' => 'Kamuning', 'alternate_name' => 'Project 1, Scout Area', 'zip_code' => '1103'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[4], 'name' => 'Krus na Ligas', 'alternate_name' => 'Diliman', 'zip_code' => '1101'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[4], 'name' => 'Malaya', 'alternate_name' => 'Diliman', 'zip_code' => '1101'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[4], 'name' => 'U.P. Campus', 'alternate_name' => 'Diliman', 'zip_code' => '1101'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[4], 'name' => 'U.P. Village', 'alternate_name' => 'Diliman', 'zip_code' => '1101'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[4], 'name' => 'Valencia', 'alternate_name' => 'New Manila, Gilmore', 'zip_code' => '1112'],

            // District 5 Barangays (ZIP: 1115-1118)
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[5], 'name' => 'Bagbag', 'alternate_name' => 'Novaliches District, Sauyo', 'zip_code' => '1116'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[5], 'name' => 'Capri', 'alternate_name' => 'Novaliches District', 'zip_code' => '1117'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[5], 'name' => 'Fairview', 'alternate_name' => 'Novaliches District', 'zip_code' => '1118'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[5], 'name' => 'Gulod', 'alternate_name' => 'Novaliches District', 'zip_code' => '1117'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[5], 'name' => 'Greater Lagro', 'alternate_name' => 'Novaliches District', 'zip_code' => '1118'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[5], 'name' => 'Kaligayahan', 'alternate_name' => 'Novaliches District', 'zip_code' => '1124'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[5], 'name' => 'Nagkaisang Nayon', 'alternate_name' => 'Novaliches District', 'zip_code' => '1125'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[5], 'name' => 'North Fairview', 'alternate_name' => 'Novaliches District', 'zip_code' => '1121'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[5], 'name' => 'Novaliches Proper', 'alternate_name' => 'Novaliches Bayan', 'zip_code' => '1123'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[5], 'name' => 'Pasong Putik Proper', 'alternate_name' => 'Novaliches District', 'zip_code' => '1118'],

            // District 6 Barangays (ZIP: 1114)
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[6], 'name' => 'Apolonio Samson', 'alternate_name' => 'Balintawak, Kaingin', 'zip_code' => '1106'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[6], 'name' => 'Baesa', 'alternate_name' => 'Project 8, Novaliches District', 'zip_code' => '1128'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[6], 'name' => 'Balon Bato', 'alternate_name' => 'Balintawak', 'zip_code' => '1106'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[6], 'name' => 'Culiat', 'alternate_name' => 'Tandang Sora', 'zip_code' => '1128'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[6], 'name' => 'New Era', 'alternate_name' => 'Iglesia ni Cristo/Central', 'zip_code' => '1107'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[6], 'name' => 'Pasong Tamo', 'alternate_name' => 'Pingkian, Philand', 'zip_code' => '1107'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[6], 'name' => 'Sangandaan', 'alternate_name' => 'Project 8', 'zip_code' => '1105'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[6], 'name' => 'Sauyo', 'alternate_name' => 'Novaliches District', 'zip_code' => '1116'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[6], 'name' => 'Talipapa', 'alternate_name' => 'Novaliches District', 'zip_code' => '1116'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[6], 'name' => 'Tandang Sora', 'alternate_name' => 'Banlat', 'zip_code' => '1116'],
            ['city_id' => $quezonCity->id, 'district_id' => $districtMap[6], 'name' => 'Unang Sigaw', 'alternate_name' => 'Balintawak, Cloverleaf', 'zip_code' => '1106'],
        ];

        foreach ($barangays as $barangay) {
            $authDb->table('barangays')->insert($barangay);
        }

        $totalBarangays = count($barangays);
        $this->command->info("✓ Quezon City Barangays seeded successfully! ($totalBarangays barangays across 6 districts)");
    }
}

