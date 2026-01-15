<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhilippineCitiesSeeder extends Seeder
{
    /**
     * Seed major Philippine cities and provincial capitals.
     * Source: Philippine Statistics Authority (PSA)
     * 
     * Includes:
     * - All Metro Manila cities (17)
     * - All Highly Urbanized Cities (HUCs)
     * - All Provincial Capitals
     * - Major municipalities
     * 
     * Note: This is a starter set. You can add more municipalities as needed.
     */
    public function run(): void
    {
        $authDb = DB::connection('auth_db');

        $cities = [
            // ==================== NCR - METRO MANILA (Province ID: 1) ====================
            // All 16 cities + 1 municipality (with ZIP codes)
            ['province_id' => 1, 'code' => 'MNL', 'name' => 'Manila', 'type' => 'city', 'has_districts' => true, 'zip_code' => '1000'],
            ['province_id' => 1, 'code' => 'QC', 'name' => 'Quezon City', 'type' => 'city', 'has_districts' => true, 'zip_code' => '1100'],
            ['province_id' => 1, 'code' => 'CAL', 'name' => 'Caloocan', 'type' => 'city', 'has_districts' => false, 'zip_code' => '1400'],
            ['province_id' => 1, 'code' => 'LAS', 'name' => 'Las Piñas', 'type' => 'city', 'has_districts' => false, 'zip_code' => '1740'],
            ['province_id' => 1, 'code' => 'MAK', 'name' => 'Makati', 'type' => 'city', 'has_districts' => false, 'zip_code' => '1200'],
            ['province_id' => 1, 'code' => 'MAL', 'name' => 'Malabon', 'type' => 'city', 'has_districts' => false, 'zip_code' => '1470'],
            ['province_id' => 1, 'code' => 'MAN', 'name' => 'Mandaluyong', 'type' => 'city', 'has_districts' => false, 'zip_code' => '1550'],
            ['province_id' => 1, 'code' => 'MAR', 'name' => 'Marikina', 'type' => 'city', 'has_districts' => false, 'zip_code' => '1800'],
            ['province_id' => 1, 'code' => 'MUN', 'name' => 'Muntinlupa', 'type' => 'city', 'has_districts' => false, 'zip_code' => '1770'],
            ['province_id' => 1, 'code' => 'NAV', 'name' => 'Navotas', 'type' => 'city', 'has_districts' => false, 'zip_code' => '1485'],
            ['province_id' => 1, 'code' => 'PAR', 'name' => 'Parañaque', 'type' => 'city', 'has_districts' => false, 'zip_code' => '1700'],
            ['province_id' => 1, 'code' => 'PAS', 'name' => 'Pasay', 'type' => 'city', 'has_districts' => false, 'zip_code' => '1300'],
            ['province_id' => 1, 'code' => 'PAC', 'name' => 'Pasig', 'type' => 'city', 'has_districts' => false, 'zip_code' => '1600'],
            ['province_id' => 1, 'code' => 'PAT', 'name' => 'Pateros', 'type' => 'municipality', 'has_districts' => false, 'zip_code' => '1620'],
            ['province_id' => 1, 'code' => 'SJU', 'name' => 'San Juan', 'type' => 'city', 'has_districts' => false, 'zip_code' => '1500'],
            ['province_id' => 1, 'code' => 'TAC', 'name' => 'Taguig', 'type' => 'city', 'has_districts' => false, 'zip_code' => '1630'],
            ['province_id' => 1, 'code' => 'VAL', 'name' => 'Valenzuela', 'type' => 'city', 'has_districts' => false, 'zip_code' => '1440'],

            // ==================== CAR - CORDILLERA ====================
            ['province_id' => 2, 'code' => 'BANG', 'name' => 'Bangued', 'type' => 'municipality', 'has_districts' => false], // Capital of Abra
            ['province_id' => 3, 'code' => 'KABUGAO', 'name' => 'Kabugao', 'type' => 'municipality', 'has_districts' => false], // Capital of Apayao
            ['province_id' => 4, 'code' => 'BAGUIO', 'name' => 'Baguio', 'type' => 'city', 'has_districts' => false], // HUC, Capital of Benguet
            ['province_id' => 4, 'code' => 'LABAG', 'name' => 'La Trinidad', 'type' => 'municipality', 'has_districts' => false],
            ['province_id' => 5, 'code' => 'LAGAWE', 'name' => 'Lagawe', 'type' => 'municipality', 'has_districts' => false], // Capital of Ifugao
            ['province_id' => 6, 'code' => 'TABUK', 'name' => 'Tabuk', 'type' => 'city', 'has_districts' => false], // Capital of Kalinga
            ['province_id' => 7, 'code' => 'BONTOC', 'name' => 'Bontoc', 'type' => 'municipality', 'has_districts' => false], // Capital of Mountain Province

            // ==================== REGION I - ILOCOS ====================
            ['province_id' => 8, 'code' => 'LAOAG', 'name' => 'Laoag', 'type' => 'city', 'has_districts' => false], // Capital of Ilocos Norte
            ['province_id' => 8, 'code' => 'BATAC', 'name' => 'Batac', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 9, 'code' => 'VIGAN', 'name' => 'Vigan', 'type' => 'city', 'has_districts' => false], // Capital of Ilocos Sur
            ['province_id' => 9, 'code' => 'CANDON', 'name' => 'Candon', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 10, 'code' => 'SANFERNANDO_LU', 'name' => 'San Fernando', 'type' => 'city', 'has_districts' => false], // Capital of La Union
            ['province_id' => 11, 'code' => 'LINGAYEN', 'name' => 'Lingayen', 'type' => 'municipality', 'has_districts' => false], // Capital of Pangasinan
            ['province_id' => 11, 'code' => 'DAGUPAN', 'name' => 'Dagupan', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 11, 'code' => 'ALAMINOS', 'name' => 'Alaminos', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 11, 'code' => 'URDANETA', 'name' => 'Urdaneta', 'type' => 'city', 'has_districts' => false],

            // ==================== REGION II - CAGAYAN VALLEY ====================
            ['province_id' => 12, 'code' => 'BASCO', 'name' => 'Basco', 'type' => 'municipality', 'has_districts' => false], // Capital of Batanes
            ['province_id' => 13, 'code' => 'TUGUEGARAO', 'name' => 'Tuguegarao', 'type' => 'city', 'has_districts' => false], // Capital of Cagayan
            ['province_id' => 14, 'code' => 'ILAGAN', 'name' => 'Ilagan', 'type' => 'city', 'has_districts' => false], // Capital of Isabela
            ['province_id' => 14, 'code' => 'SANTIAGO', 'name' => 'Santiago', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 14, 'code' => 'CAUAYAN', 'name' => 'Cauayan', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 15, 'code' => 'BAYOMBONG', 'name' => 'Bayombong', 'type' => 'municipality', 'has_districts' => false], // Capital of Nueva Vizcaya
            ['province_id' => 16, 'code' => 'CABARROGUIS', 'name' => 'Cabarroguis', 'type' => 'municipality', 'has_districts' => false], // Capital of Quirino

            // ==================== REGION III - CENTRAL LUZON ====================
            ['province_id' => 17, 'code' => 'BALER', 'name' => 'Baler', 'type' => 'municipality', 'has_districts' => false], // Capital of Aurora
            ['province_id' => 18, 'code' => 'BALANGA', 'name' => 'Balanga', 'type' => 'city', 'has_districts' => false], // Capital of Bataan
            ['province_id' => 19, 'code' => 'MALOLOS', 'name' => 'Malolos', 'type' => 'city', 'has_districts' => false], // Capital of Bulacan
            ['province_id' => 19, 'code' => 'MEYCAUAYAN', 'name' => 'Meycauayan', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 19, 'code' => 'SJDM', 'name' => 'San Jose del Monte', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 20, 'code' => 'PALAYAN', 'name' => 'Palayan', 'type' => 'city', 'has_districts' => false], // Capital of Nueva Ecija
            ['province_id' => 20, 'code' => 'CABANATUAN', 'name' => 'Cabanatuan', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 20, 'code' => 'GAPAN', 'name' => 'Gapan', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 21, 'code' => 'SANFERNANDO_PAM', 'name' => 'San Fernando', 'type' => 'city', 'has_districts' => false], // Capital of Pampanga
            ['province_id' => 21, 'code' => 'ANGELES', 'name' => 'Angeles', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 21, 'code' => 'MABALACAT', 'name' => 'Mabalacat', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 22, 'code' => 'TARLAC_CITY', 'name' => 'Tarlac City', 'type' => 'city', 'has_districts' => false], // Capital of Tarlac
            ['province_id' => 23, 'code' => 'IBA', 'name' => 'Iba', 'type' => 'municipality', 'has_districts' => false], // Capital of Zambales
            ['province_id' => 23, 'code' => 'OLONGAPO', 'name' => 'Olongapo', 'type' => 'city', 'has_districts' => false],

            // ==================== REGION IV-A - CALABARZON ====================
            ['province_id' => 24, 'code' => 'BATANGAS_CITY', 'name' => 'Batangas City', 'type' => 'city', 'has_districts' => false], // Capital of Batangas
            ['province_id' => 24, 'code' => 'LIPA', 'name' => 'Lipa', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 24, 'code' => 'TANAUAN', 'name' => 'Tanauan', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 25, 'code' => 'TRECE', 'name' => 'Trece Martires', 'type' => 'city', 'has_districts' => false], // Capital of Cavite
            ['province_id' => 25, 'code' => 'CAVITE_CITY', 'name' => 'Cavite City', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 25, 'code' => 'DASMARINAS', 'name' => 'Dasmariñas', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 25, 'code' => 'BACOOR', 'name' => 'Bacoor', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 25, 'code' => 'IMUS', 'name' => 'Imus', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 25, 'code' => 'TAGAYTAY', 'name' => 'Tagaytay', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 26, 'code' => 'SANTACRUZ', 'name' => 'Santa Cruz', 'type' => 'municipality', 'has_districts' => false], // Capital of Laguna
            ['province_id' => 26, 'code' => 'CALAMBA', 'name' => 'Calamba', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 26, 'code' => 'BINAN', 'name' => 'Biñan', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 26, 'code' => 'SANPEDRO', 'name' => 'San Pedro', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 26, 'code' => 'CABUYAO', 'name' => 'Cabuyao', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 27, 'code' => 'LUCENA', 'name' => 'Lucena', 'type' => 'city', 'has_districts' => false], // Capital of Quezon
            ['province_id' => 27, 'code' => 'TAYABAS', 'name' => 'Tayabas', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 28, 'code' => 'ANTIPOLO', 'name' => 'Antipolo', 'type' => 'city', 'has_districts' => false], // Capital of Rizal
            ['province_id' => 28, 'code' => 'CAINTA', 'name' => 'Cainta', 'type' => 'municipality', 'has_districts' => false],
            ['province_id' => 28, 'code' => 'TAYTAY', 'name' => 'Taytay', 'type' => 'municipality', 'has_districts' => false],

            // ==================== REGION IV-B - MIMAROPA ====================
            ['province_id' => 29, 'code' => 'BOAC', 'name' => 'Boac', 'type' => 'municipality', 'has_districts' => false], // Capital of Marinduque
            ['province_id' => 30, 'code' => 'MAMBURAO', 'name' => 'Mamburao', 'type' => 'municipality', 'has_districts' => false], // Capital of Occidental Mindoro
            ['province_id' => 31, 'code' => 'CALAPAN', 'name' => 'Calapan', 'type' => 'city', 'has_districts' => false], // Capital of Oriental Mindoro
            ['province_id' => 32, 'code' => 'PUERTO', 'name' => 'Puerto Princesa', 'type' => 'city', 'has_districts' => false], // Capital of Palawan
            ['province_id' => 33, 'code' => 'ROMBLON', 'name' => 'Romblon', 'type' => 'municipality', 'has_districts' => false], // Capital of Romblon

            // ==================== REGION V - BICOL ====================
            ['province_id' => 34, 'code' => 'LEGAZPI', 'name' => 'Legazpi', 'type' => 'city', 'has_districts' => false], // Capital of Albay
            ['province_id' => 34, 'code' => 'LIGAO', 'name' => 'Ligao', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 34, 'code' => 'TABACO', 'name' => 'Tabaco', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 35, 'code' => 'DAET', 'name' => 'Daet', 'type' => 'municipality', 'has_districts' => false], // Capital of Camarines Norte
            ['province_id' => 36, 'code' => 'PILI', 'name' => 'Pili', 'type' => 'municipality', 'has_districts' => false], // Capital of Camarines Sur
            ['province_id' => 36, 'code' => 'NAGA', 'name' => 'Naga', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 36, 'code' => 'IRIGA', 'name' => 'Iriga', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 37, 'code' => 'VIRAC', 'name' => 'Virac', 'type' => 'municipality', 'has_districts' => false], // Capital of Catanduanes
            ['province_id' => 38, 'code' => 'MASBATE_CITY', 'name' => 'Masbate City', 'type' => 'city', 'has_districts' => false], // Capital of Masbate
            ['province_id' => 39, 'code' => 'SORSOGON_CITY', 'name' => 'Sorsogon City', 'type' => 'city', 'has_districts' => false], // Capital of Sorsogon

            // ==================== REGION VI - WESTERN VISAYAS ====================
            ['province_id' => 40, 'code' => 'KALIBO', 'name' => 'Kalibo', 'type' => 'municipality', 'has_districts' => false], // Capital of Aklan
            ['province_id' => 41, 'code' => 'SANJOS', 'name' => 'San Jose de Buenavista', 'type' => 'municipality', 'has_districts' => false], // Capital of Antique
            ['province_id' => 42, 'code' => 'ROXAS', 'name' => 'Roxas City', 'type' => 'city', 'has_districts' => false], // Capital of Capiz
            ['province_id' => 43, 'code' => 'JORDAN', 'name' => 'Jordan', 'type' => 'municipality', 'has_districts' => false], // Capital of Guimaras
            ['province_id' => 44, 'code' => 'ILOILO_CITY', 'name' => 'Iloilo City', 'type' => 'city', 'has_districts' => false], // Capital of Iloilo
            ['province_id' => 44, 'code' => 'PASSI', 'name' => 'Passi', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 45, 'code' => 'BACOLOD', 'name' => 'Bacolod', 'type' => 'city', 'has_districts' => false], // Capital of Negros Occidental
            ['province_id' => 45, 'code' => 'SILAY', 'name' => 'Silay', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 45, 'code' => 'TALISAY', 'name' => 'Talisay', 'type' => 'city', 'has_districts' => false],

            // ==================== REGION VII - CENTRAL VISAYAS ====================
            ['province_id' => 46, 'code' => 'TAGBILARAN', 'name' => 'Tagbilaran', 'type' => 'city', 'has_districts' => false], // Capital of Bohol
            ['province_id' => 47, 'code' => 'CEBU_CITY', 'name' => 'Cebu City', 'type' => 'city', 'has_districts' => false], // Capital of Cebu
            ['province_id' => 47, 'code' => 'LAPULAPU', 'name' => 'Lapu-Lapu', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 47, 'code' => 'MANDAUE', 'name' => 'Mandaue', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 47, 'code' => 'TOLEDO', 'name' => 'Toledo', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 48, 'code' => 'DUMAGUETE', 'name' => 'Dumaguete', 'type' => 'city', 'has_districts' => false], // Capital of Negros Oriental
            ['province_id' => 48, 'code' => 'BAYAWAN', 'name' => 'Bayawan', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 49, 'code' => 'SIQUIJOR', 'name' => 'Siquijor', 'type' => 'municipality', 'has_districts' => false], // Capital of Siquijor

            // ==================== REGION VIII - EASTERN VISAYAS ====================
            ['province_id' => 50, 'code' => 'NAVAL', 'name' => 'Naval', 'type' => 'municipality', 'has_districts' => false], // Capital of Biliran
            ['province_id' => 51, 'code' => 'BORONGAN', 'name' => 'Borongan', 'type' => 'city', 'has_districts' => false], // Capital of Eastern Samar
            ['province_id' => 52, 'code' => 'TACLOBAN', 'name' => 'Tacloban', 'type' => 'city', 'has_districts' => false], // Capital of Leyte
            ['province_id' => 52, 'code' => 'ORMOC', 'name' => 'Ormoc', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 53, 'code' => 'CATARMAN', 'name' => 'Catarman', 'type' => 'municipality', 'has_districts' => false], // Capital of Northern Samar
            ['province_id' => 54, 'code' => 'CALBAYOG', 'name' => 'Calbayog', 'type' => 'city', 'has_districts' => false], // Capital of Western Samar
            ['province_id' => 54, 'code' => 'CATBALOGAN', 'name' => 'Catbalogan', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 55, 'code' => 'MAASIN', 'name' => 'Maasin', 'type' => 'city', 'has_districts' => false], // Capital of Southern Leyte

            // ==================== REGION IX - ZAMBOANGA ====================
            ['province_id' => 56, 'code' => 'DIPOLOG', 'name' => 'Dipolog', 'type' => 'city', 'has_districts' => false], // Capital of Zamboanga del Norte
            ['province_id' => 56, 'code' => 'DAPITAN', 'name' => 'Dapitan', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 57, 'code' => 'PAGADIAN', 'name' => 'Pagadian', 'type' => 'city', 'has_districts' => false], // Capital of Zamboanga del Sur
            ['province_id' => 57, 'code' => 'ZAMBOANGA', 'name' => 'Zamboanga City', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 58, 'code' => 'IPIL', 'name' => 'Ipil', 'type' => 'municipality', 'has_districts' => false], // Capital of Zamboanga Sibugay

            // ==================== REGION X - NORTHERN MINDANAO ====================
            ['province_id' => 59, 'code' => 'MALAYBALAY', 'name' => 'Malaybalay', 'type' => 'city', 'has_districts' => false], // Capital of Bukidnon
            ['province_id' => 59, 'code' => 'VALENCIA', 'name' => 'Valencia', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 60, 'code' => 'MAMBAJAO', 'name' => 'Mambajao', 'type' => 'municipality', 'has_districts' => false], // Capital of Camiguin
            ['province_id' => 61, 'code' => 'TUBOD', 'name' => 'Tubod', 'type' => 'municipality', 'has_districts' => false], // Capital of Lanao del Norte
            ['province_id' => 61, 'code' => 'ILIGAN', 'name' => 'Iligan', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 62, 'code' => 'OROQUIETA', 'name' => 'Oroquieta', 'type' => 'city', 'has_districts' => false], // Capital of Misamis Occidental
            ['province_id' => 62, 'code' => 'OZAMIZ', 'name' => 'Ozamiz', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 62, 'code' => 'TANGUB', 'name' => 'Tangub', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 63, 'code' => 'CAGAYAN', 'name' => 'Cagayan de Oro', 'type' => 'city', 'has_districts' => false], // Capital of Misamis Oriental
            ['province_id' => 63, 'code' => 'GINGOOG', 'name' => 'Gingoog', 'type' => 'city', 'has_districts' => false],

            // ==================== REGION XI - DAVAO ====================
            ['province_id' => 64, 'code' => 'NABUNTURAN', 'name' => 'Nabunturan', 'type' => 'municipality', 'has_districts' => false], // Capital of Davao de Oro
            ['province_id' => 65, 'code' => 'TAGUM', 'name' => 'Tagum', 'type' => 'city', 'has_districts' => false], // Capital of Davao del Norte
            ['province_id' => 66, 'code' => 'DIGOS', 'name' => 'Digos', 'type' => 'city', 'has_districts' => false], // Capital of Davao del Sur
            ['province_id' => 66, 'code' => 'DAVAO', 'name' => 'Davao City', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 67, 'code' => 'MATI', 'name' => 'Mati', 'type' => 'city', 'has_districts' => false], // Capital of Davao Oriental
            ['province_id' => 68, 'code' => 'MALITA', 'name' => 'Malita', 'type' => 'municipality', 'has_districts' => false], // Capital of Davao Occidental

            // ==================== REGION XII - SOCCSKSARGEN ====================
            ['province_id' => 69, 'code' => 'KIDAPAWAN', 'name' => 'Kidapawan', 'type' => 'city', 'has_districts' => false], // Capital of Cotabato
            ['province_id' => 70, 'code' => 'ALABEL', 'name' => 'Alabel', 'type' => 'municipality', 'has_districts' => false], // Capital of Sarangani
            ['province_id' => 70, 'code' => 'GENERAL', 'name' => 'General Santos', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 71, 'code' => 'KORONADAL', 'name' => 'Koronadal', 'type' => 'city', 'has_districts' => false], // Capital of South Cotabato
            ['province_id' => 72, 'code' => 'ISULAN', 'name' => 'Isulan', 'type' => 'municipality', 'has_districts' => false], // Capital of Sultan Kudarat
            ['province_id' => 72, 'code' => 'TACURONG', 'name' => 'Tacurong', 'type' => 'city', 'has_districts' => false],

            // ==================== REGION XIII - CARAGA ====================
            ['province_id' => 73, 'code' => 'CABADBARAN', 'name' => 'Cabadbaran', 'type' => 'city', 'has_districts' => false], // Capital of Agusan del Norte
            ['province_id' => 73, 'code' => 'BUTUAN', 'name' => 'Butuan', 'type' => 'city', 'has_districts' => false],
            ['province_id' => 74, 'code' => 'BAYUGAN', 'name' => 'Bayugan', 'type' => 'city', 'has_districts' => false], // Capital of Agusan del Sur
            ['province_id' => 75, 'code' => 'DAPA', 'name' => 'San Jose', 'type' => 'municipality', 'has_districts' => false], // Capital of Dinagat Islands
            ['province_id' => 76, 'code' => 'SURIGAO', 'name' => 'Surigao City', 'type' => 'city', 'has_districts' => false], // Capital of Surigao del Norte
            ['province_id' => 77, 'code' => 'TANDAG', 'name' => 'Tandag', 'type' => 'city', 'has_districts' => false], // Capital of Surigao del Sur
            ['province_id' => 77, 'code' => 'BISLIG', 'name' => 'Bislig', 'type' => 'city', 'has_districts' => false],

            // ==================== BARMM ====================
            ['province_id' => 78, 'code' => 'ISABELA', 'name' => 'Isabela City', 'type' => 'city', 'has_districts' => false], // Capital of Basilan
            ['province_id' => 79, 'code' => 'MARAWI', 'name' => 'Marawi', 'type' => 'city', 'has_districts' => false], // Capital of Lanao del Sur
            ['province_id' => 80, 'code' => 'COTABATO', 'name' => 'Cotabato City', 'type' => 'city', 'has_districts' => false], // Capital of Maguindanao
            ['province_id' => 81, 'code' => 'JOLO', 'name' => 'Jolo', 'type' => 'municipality', 'has_districts' => false], // Capital of Sulu
            ['province_id' => 82, 'code' => 'BONGAO', 'name' => 'Bongao', 'type' => 'municipality', 'has_districts' => false], // Capital of Tawi-Tawi
        ];

        foreach ($cities as $city) {
            $authDb->table('cities')->insertOrIgnore($city);
        }

        $this->command->info('✓ Philippine Cities seeded successfully! (' . count($cities) . ' cities)');
    }
}
