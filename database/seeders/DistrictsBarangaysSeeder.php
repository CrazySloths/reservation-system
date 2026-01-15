<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistrictsBarangaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Use the 'auth_db' connection
        $authDb = DB::connection('auth_db');

        // Seed Districts (6 districts total - from lgu1_auth)
        $districts = [
            ['id' => 1, 'district_number' => 1, 'name' => 'District 1'],
            ['id' => 2, 'district_number' => 2, 'name' => 'District 2'],
            ['id' => 3, 'district_number' => 3, 'name' => 'District 3'],
            ['id' => 4, 'district_number' => 4, 'name' => 'District 4'],
            ['id' => 5, 'district_number' => 5, 'name' => 'District 5'],
            ['id' => 6, 'district_number' => 6, 'name' => 'District 6'],
        ];

        foreach ($districts as $district) {
            $authDb->table('districts')->insertOrIgnore($district);
        }

        // Note: Due to the large number of barangays in lgu1_auth (100+),
        // we'll insert a representative sample from each district.
        // For production, you can import the full SQL file or expand this seeder.

        // Quezon City's ID in the cities table
        $quezon_city_id = 2;

        $barangays = [
            // District 1 Barangays (sample - originally has 36 barangays)
            ['city_id' => $quezon_city_id, 'district_id' => 1, 'name' => 'Alicia', 'alternate_name' => 'Bago Bantay'],
            ['city_id' => $quezon_city_id, 'district_id' => 1, 'name' => 'Bagong Pag-asa', 'alternate_name' => 'North-EDSA, Diliman (southern part), Triangle Park (southern triangle)'],
            ['city_id' => $quezon_city_id, 'district_id' => 1, 'name' => 'Bahay Toro', 'alternate_name' => 'Project 8, Pugadlawin'],
            ['city_id' => $quezon_city_id, 'district_id' => 1, 'name' => 'Balingasa', 'alternate_name' => 'Balintawak, Cloverleaf'],
            ['city_id' => $quezon_city_id, 'district_id' => 1, 'name' => 'Bungad', 'alternate_name' => 'Project 7'],
            ['city_id' => $quezon_city_id, 'district_id' => 1, 'name' => 'Damar', 'alternate_name' => 'Balintawak'],
            ['city_id' => $quezon_city_id, 'district_id' => 1, 'name' => 'Damayan', 'alternate_name' => 'San Francisco del Monte, Frisco'],
            ['city_id' => $quezon_city_id, 'district_id' => 1, 'name' => 'Del Monte', 'alternate_name' => 'San Francisco del Monte, Frisco'],
            ['city_id' => $quezon_city_id, 'district_id' => 1, 'name' => 'Katipunan', 'alternate_name' => 'Muñoz'],
            ['city_id' => $quezon_city_id, 'district_id' => 1, 'name' => 'Lourdes', 'alternate_name' => 'Santa Mesa Heights'],

            // District 2 Barangays (originally has 5 barangays)
            ['city_id' => $quezon_city_id, 'district_id' => 2, 'name' => 'Bagong Silangan', 'alternate_name' => 'Payatas'],
            ['city_id' => $quezon_city_id, 'district_id' => 2, 'name' => 'Batasan Hills', 'alternate_name' => 'Constitution Hills'],
            ['city_id' => $quezon_city_id, 'district_id' => 2, 'name' => 'Commonwealth', 'alternate_name' => 'Manggahan, Litex'],
            ['city_id' => $quezon_city_id, 'district_id' => 2, 'name' => 'Holy Spirit', 'alternate_name' => 'Don Antonio, Luzon'],
            ['city_id' => $quezon_city_id, 'district_id' => 2, 'name' => 'Payatas', 'alternate_name' => 'Litex'],

            // District 3 Barangays (sample - originally has 37 barangays)
            ['city_id' => $quezon_city_id, 'district_id' => 3, 'name' => 'Amihan', 'alternate_name' => 'Project 3'],
            ['city_id' => $quezon_city_id, 'district_id' => 3, 'name' => 'Bagumbayan', 'alternate_name' => 'Eastwood, Acropolis, Citybank, Gentex, Libis'],
            ['city_id' => $quezon_city_id, 'district_id' => 3, 'name' => 'Bagumbuhay', 'alternate_name' => 'Project 4'],
            ['city_id' => $quezon_city_id, 'district_id' => 3, 'name' => 'Bayanihan', 'alternate_name' => 'Project 4'],
            ['city_id' => $quezon_city_id, 'district_id' => 3, 'name' => 'Blue Ridge A', 'alternate_name' => 'Project 4'],
            ['city_id' => $quezon_city_id, 'district_id' => 3, 'name' => 'Libis', 'alternate_name' => 'Camp Atienza, Eastwood'],
            ['city_id' => $quezon_city_id, 'district_id' => 3, 'name' => 'Loyola Heights', 'alternate_name' => 'Katipunan'],
            ['city_id' => $quezon_city_id, 'district_id' => 3, 'name' => 'Mangga', 'alternate_name' => 'Cubao, Anonas, T.I.P.'],
            ['city_id' => $quezon_city_id, 'district_id' => 3, 'name' => 'Socorro', 'alternate_name' => 'Cubao, Araneta City'],
            ['city_id' => $quezon_city_id, 'district_id' => 3, 'name' => 'White Plains', 'alternate_name' => 'Camp Aguinaldo, Katipunan'],

            // District 4 Barangays (sample - originally has 37 barangays)
            ['city_id' => $quezon_city_id, 'district_id' => 4, 'name' => 'Bagong Lipunan ng Crame', 'alternate_name' => 'Camp Crame, Philippine National Police (PNP)'],
            ['city_id' => $quezon_city_id, 'district_id' => 4, 'name' => 'Botocan', 'alternate_name' => 'Diliman (northern half)'],
            ['city_id' => $quezon_city_id, 'district_id' => 4, 'name' => 'Central', 'alternate_name' => 'Diliman, Quezon City Hall'],
            ['city_id' => $quezon_city_id, 'district_id' => 4, 'name' => 'Damayang Lagi', 'alternate_name' => 'New Manila'],
            ['city_id' => $quezon_city_id, 'district_id' => 4, 'name' => 'Kamuning', 'alternate_name' => 'Project 1, Scout Area'],
            ['city_id' => $quezon_city_id, 'district_id' => 4, 'name' => 'Krus na Ligas', 'alternate_name' => 'Diliman'],
            ['city_id' => $quezon_city_id, 'district_id' => 4, 'name' => 'Malaya', 'alternate_name' => 'Diliman'],
            ['city_id' => $quezon_city_id, 'district_id' => 4, 'name' => 'U.P. Campus', 'alternate_name' => 'Diliman'],
            ['city_id' => $quezon_city_id, 'district_id' => 4, 'name' => 'U.P. Village', 'alternate_name' => 'Diliman'],
            ['city_id' => $quezon_city_id, 'district_id' => 4, 'name' => 'Valencia', 'alternate_name' => 'New Manila, Gilmore Ave., N. Domingo Ave.'],

            // District 5 Barangays (originally has 14 barangays)
            ['city_id' => $quezon_city_id, 'district_id' => 5, 'name' => 'Bagbag', 'alternate_name' => 'Novaliches District, Sauyo'],
            ['city_id' => $quezon_city_id, 'district_id' => 5, 'name' => 'Capri', 'alternate_name' => 'Novaliches District'],
            ['city_id' => $quezon_city_id, 'district_id' => 5, 'name' => 'Fairview', 'alternate_name' => 'Novaliches District, La Mesa, West Fairview'],
            ['city_id' => $quezon_city_id, 'district_id' => 5, 'name' => 'Gulod', 'alternate_name' => 'Novaliches District, Susano, Nitang'],
            ['city_id' => $quezon_city_id, 'district_id' => 5, 'name' => 'Greater Lagro', 'alternate_name' => 'Novaliches District, Lagro, Fairview'],
            ['city_id' => $quezon_city_id, 'district_id' => 5, 'name' => 'Kaligayahan', 'alternate_name' => 'Novaliches District, Zabarte'],
            ['city_id' => $quezon_city_id, 'district_id' => 5, 'name' => 'Nagkaisang Nayon', 'alternate_name' => 'Novaliches District, General Luis'],
            ['city_id' => $quezon_city_id, 'district_id' => 5, 'name' => 'North Fairview', 'alternate_name' => 'Novaliches District'],
            ['city_id' => $quezon_city_id, 'district_id' => 5, 'name' => 'Novaliches Proper', 'alternate_name' => 'Novaliches Bayan, Glori, Bayan'],
            ['city_id' => $quezon_city_id, 'district_id' => 5, 'name' => 'Pasong Putik Proper', 'alternate_name' => 'Novaliches District, Maligaya Drive, Fairview'],

            // District 6 Barangays (originally has 11 barangays)
            ['city_id' => $quezon_city_id, 'district_id' => 6, 'name' => 'Apolonio Samson', 'alternate_name' => 'Balintawak, Kaingin, Kangkong'],
            ['city_id' => $quezon_city_id, 'district_id' => 6, 'name' => 'Baesa', 'alternate_name' => 'Project 8, Novaliches District'],
            ['city_id' => $quezon_city_id, 'district_id' => 6, 'name' => 'Balon Bato', 'alternate_name' => 'Balintawak'],
            ['city_id' => $quezon_city_id, 'district_id' => 6, 'name' => 'Culiat', 'alternate_name' => 'Tandang Sora'],
            ['city_id' => $quezon_city_id, 'district_id' => 6, 'name' => 'New Era', 'alternate_name' => 'Iglesia ni Cristo/Central, Tandang Sora'],
            ['city_id' => $quezon_city_id, 'district_id' => 6, 'name' => 'Pasong Tamo', 'alternate_name' => 'Pingkian, Philand'],
            ['city_id' => $quezon_city_id, 'district_id' => 6, 'name' => 'Sangandaan', 'alternate_name' => 'Project 8'],
            ['city_id' => $quezon_city_id, 'district_id' => 6, 'name' => 'Sauyo', 'alternate_name' => 'Novaliches District'],
            ['city_id' => $quezon_city_id, 'district_id' => 6, 'name' => 'Talipapa', 'alternate_name' => 'Novaliches District'],
            ['city_id' => $quezon_city_id, 'district_id' => 6, 'name' => 'Tandang Sora', 'alternate_name' => 'Banlat'],
            ['city_id' => $quezon_city_id, 'district_id' => 6, 'name' => 'Unang Sigaw', 'alternate_name' => 'Balintawak, Cloverleaf'],
        ];

        foreach ($barangays as $barangay) {
            $authDb->table('barangays')->insertOrIgnore($barangay);
        }

        $this->command->info('✓ 6 Districts and Barangays seeded successfully!');
    }
}
