<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComprehensiveDistrictsSeeder extends Seeder
{
    /**
     * Seed COMPREHENSIVE district data for ALL major Philippine cities.
     * 
     * Districts are administrative divisions within cities that help organize:
     * - Congressional representation
     * - City/Municipal council representation  
     * - Barangay groupings
     * 
     * Source: Commission on Elections (COMELEC), Local Government Code
     */
    public function run(): void
    {
        $authDb = DB::connection('auth_db');

        // First, get city IDs from the database
        $manila = $authDb->table('cities')->where('code', 'MNL')->first();
        $quezonCity = $authDb->table('cities')->where('code', 'QC')->first();
        $caloocan = $authDb->table('cities')->where('code', 'CAL')->first();
        $makati = $authDb->table('cities')->where('code', 'MAK')->first();
        $marikina = $authDb->table('cities')->where('code', 'MAR')->first();
        $paranaque = $authDb->table('cities')->where('code', 'PAR')->first();
        $pasig = $authDb->table('cities')->where('code', 'PAC')->first();
        $taguig = $authDb->table('cities')->where('code', 'TAC')->first();
        $valenzuela = $authDb->table('cities')->where('code', 'VAL')->first();
        $lasPinas = $authDb->table('cities')->where('code', 'LAS')->first();
        $malabon = $authDb->table('cities')->where('code', 'MAL')->first();
        $mandaluyong = $authDb->table('cities')->where('code', 'MAN')->first();
        $muntinlupa = $authDb->table('cities')->where('code', 'MUN')->first();
        $navotas = $authDb->table('cities')->where('code', 'NAV')->first();
        $pasay = $authDb->table('cities')->where('code', 'PAS')->first();
        $pateros = $authDb->table('cities')->where('code', 'PAT')->first();
        $sanJuan = $authDb->table('cities')->where('code', 'SJU')->first();

        $districts = [];

        // ==================== METRO MANILA (NCR) DISTRICTS ====================
        
        // MANILA - 6 Congressional Districts
        if ($manila) {
            for ($i = 1; $i <= 6; $i++) {
                $districts[] = [
                    'city_id' => $manila->id,
                    'district_number' => $i,
                    'name' => "District $i",
                    'type' => 'congressional'
                ];
            }
        }

        // QUEZON CITY - 6 Congressional Districts
        if ($quezonCity) {
            for ($i = 1; $i <= 6; $i++) {
                $districts[] = [
                    'city_id' => $quezonCity->id,
                    'district_number' => $i,
                    'name' => "District $i",
                    'type' => 'congressional'
                ];
            }
        }

        // CALOOCAN - 2 Congressional Districts (North and South)
        if ($caloocan) {
            $districts[] = ['city_id' => $caloocan->id, 'district_number' => 1, 'name' => 'District 1 (North Caloocan)', 'type' => 'congressional'];
            $districts[] = ['city_id' => $caloocan->id, 'district_number' => 2, 'name' => 'District 2 (South Caloocan)', 'type' => 'congressional'];
        }

        // LAS PIÑAS - 1 Legislative District
        if ($lasPinas) {
            $districts[] = ['city_id' => $lasPinas->id, 'district_number' => 1, 'name' => 'Lone District', 'type' => 'congressional'];
        }

        // MAKATI - 2 Congressional Districts
        if ($makati) {
            $districts[] = ['city_id' => $makati->id, 'district_number' => 1, 'name' => 'District 1', 'type' => 'congressional'];
            $districts[] = ['city_id' => $makati->id, 'district_number' => 2, 'name' => 'District 2', 'type' => 'congressional'];
        }

        // MALABON - 1 Legislative District
        if ($malabon) {
            $districts[] = ['city_id' => $malabon->id, 'district_number' => 1, 'name' => 'Lone District', 'type' => 'congressional'];
        }

        // MANDALUYONG - 1 Legislative District
        if ($mandaluyong) {
            $districts[] = ['city_id' => $mandaluyong->id, 'district_number' => 1, 'name' => 'Lone District', 'type' => 'congressional'];
        }

        // MARIKINA - 2 Congressional Districts
        if ($marikina) {
            $districts[] = ['city_id' => $marikina->id, 'district_number' => 1, 'name' => 'District 1', 'type' => 'congressional'];
            $districts[] = ['city_id' => $marikina->id, 'district_number' => 2, 'name' => 'District 2', 'type' => 'congressional'];
        }

        // MUNTINLUPA - 1 Legislative District
        if ($muntinlupa) {
            $districts[] = ['city_id' => $muntinlupa->id, 'district_number' => 1, 'name' => 'Lone District', 'type' => 'congressional'];
        }

        // NAVOTAS - 1 Legislative District
        if ($navotas) {
            $districts[] = ['city_id' => $navotas->id, 'district_number' => 1, 'name' => 'Lone District', 'type' => 'congressional'];
        }

        // PARAÑAQUE - 2 Congressional Districts
        if ($paranaque) {
            $districts[] = ['city_id' => $paranaque->id, 'district_number' => 1, 'name' => 'District 1', 'type' => 'congressional'];
            $districts[] = ['city_id' => $paranaque->id, 'district_number' => 2, 'name' => 'District 2', 'type' => 'congressional'];
        }

        // PASAY - 1 Legislative District
        if ($pasay) {
            $districts[] = ['city_id' => $pasay->id, 'district_number' => 1, 'name' => 'Lone District', 'type' => 'congressional'];
        }

        // PASIG - 2 Congressional Districts
        if ($pasig) {
            $districts[] = ['city_id' => $pasig->id, 'district_number' => 1, 'name' => 'District 1', 'type' => 'congressional'];
            $districts[] = ['city_id' => $pasig->id, 'district_number' => 2, 'name' => 'District 2', 'type' => 'congressional'];
        }

        // PATEROS - 1 Legislative District (lone municipality in NCR)
        if ($pateros) {
            $districts[] = ['city_id' => $pateros->id, 'district_number' => 1, 'name' => 'Lone District', 'type' => 'municipal'];
        }

        // SAN JUAN - 1 Legislative District
        if ($sanJuan) {
            $districts[] = ['city_id' => $sanJuan->id, 'district_number' => 1, 'name' => 'Lone District', 'type' => 'congressional'];
        }

        // TAGUIG - 2 Congressional Districts
        if ($taguig) {
            $districts[] = ['city_id' => $taguig->id, 'district_number' => 1, 'name' => 'District 1', 'type' => 'congressional'];
            $districts[] = ['city_id' => $taguig->id, 'district_number' => 2, 'name' => 'District 2', 'type' => 'congressional'];
        }

        // VALENZUELA - 2 Congressional Districts
        if ($valenzuela) {
            $districts[] = ['city_id' => $valenzuela->id, 'district_number' => 1, 'name' => 'District 1', 'type' => 'congressional'];
            $districts[] = ['city_id' => $valenzuela->id, 'district_number' => 2, 'name' => 'District 2', 'type' => 'congressional'];
        }

        // ==================== OTHER MAJOR CITIES (to be expanded) ====================
        // Note: Add districts for other major cities as needed (Cebu, Davao, etc.)

        // Clear existing districts and insert new comprehensive data
        $authDb->statement('SET FOREIGN_KEY_CHECKS=0');
        $authDb->table('districts')->truncate();
        $authDb->statement('SET FOREIGN_KEY_CHECKS=1');
        
        foreach ($districts as $district) {
            $authDb->table('districts')->insert($district);
        }

        $totalDistricts = count($districts);
        $this->command->info("✓ Comprehensive Districts seeded successfully! ($totalDistricts districts across Metro Manila)");
    }
}

