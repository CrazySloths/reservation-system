<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhilippineProvincesSeeder extends Seeder
{
    /**
     * Seed the Philippine provinces table.
     * Source: Philippine Statistics Authority (PSA)
     * Total: 81 Provinces + Metro Manila
     */
    public function run(): void
    {
        $authDb = DB::connection('auth_db');

        $provinces = [
            // ==================== NCR (Region 1) ====================
            ['region_id' => 1, 'code' => 'MNL', 'name' => 'Metro Manila', 'psgc_code' => '133900000'],

            // ==================== CAR (Region 2) ====================
            ['region_id' => 2, 'code' => 'ABR', 'name' => 'Abra', 'psgc_code' => '140100000'],
            ['region_id' => 2, 'code' => 'APN', 'name' => 'Apayao', 'psgc_code' => '141100000'],
            ['region_id' => 2, 'code' => 'BEN', 'name' => 'Benguet', 'psgc_code' => '141400000'],
            ['region_id' => 2, 'code' => 'IFU', 'name' => 'Ifugao', 'psgc_code' => '142700000'],
            ['region_id' => 2, 'code' => 'KAL', 'name' => 'Kalinga', 'psgc_code' => '143200000'],
            ['region_id' => 2, 'code' => 'MOU', 'name' => 'Mountain Province', 'psgc_code' => '144400000'],

            // ==================== Region I (Region 3) ====================
            ['region_id' => 3, 'code' => 'ILN', 'name' => 'Ilocos Norte', 'psgc_code' => '012800000'],
            ['region_id' => 3, 'code' => 'ILS', 'name' => 'Ilocos Sur', 'psgc_code' => '012900000'],
            ['region_id' => 3, 'code' => 'LUN', 'name' => 'La Union', 'psgc_code' => '013300000'],
            ['region_id' => 3, 'code' => 'PAN', 'name' => 'Pangasinan', 'psgc_code' => '015500000'],

            // ==================== Region II (Region 4) ====================
            ['region_id' => 4, 'code' => 'BTN', 'name' => 'Batanes', 'psgc_code' => '020900000'],
            ['region_id' => 4, 'code' => 'CAG', 'name' => 'Cagayan', 'psgc_code' => '021500000'],
            ['region_id' => 4, 'code' => 'ISA', 'name' => 'Isabela', 'psgc_code' => '023100000'],
            ['region_id' => 4, 'code' => 'NUV', 'name' => 'Nueva Vizcaya', 'psgc_code' => '025000000'],
            ['region_id' => 4, 'code' => 'QUI', 'name' => 'Quirino', 'psgc_code' => '025700000'],

            // ==================== Region III (Region 5) ====================
            ['region_id' => 5, 'code' => 'AUR', 'name' => 'Aurora', 'psgc_code' => '030800000'],
            ['region_id' => 5, 'code' => 'BAN', 'name' => 'Bataan', 'psgc_code' => '030900000'],
            ['region_id' => 5, 'code' => 'BUL', 'name' => 'Bulacan', 'psgc_code' => '031400000'],
            ['region_id' => 5, 'code' => 'NUE', 'name' => 'Nueva Ecija', 'psgc_code' => '034900000'],
            ['region_id' => 5, 'code' => 'PAM', 'name' => 'Pampanga', 'psgc_code' => '035400000'],
            ['region_id' => 5, 'code' => 'TAR', 'name' => 'Tarlac', 'psgc_code' => '036900000'],
            ['region_id' => 5, 'code' => 'ZMB', 'name' => 'Zambales', 'psgc_code' => '037100000'],

            // ==================== Region IV-A (Region 6) ====================
            ['region_id' => 6, 'code' => 'BTG', 'name' => 'Batangas', 'psgc_code' => '041000000'],
            ['region_id' => 6, 'code' => 'CAV', 'name' => 'Cavite', 'psgc_code' => '042100000'],
            ['region_id' => 6, 'code' => 'LAG', 'name' => 'Laguna', 'psgc_code' => '043400000'],
            ['region_id' => 6, 'code' => 'QUE', 'name' => 'Quezon', 'psgc_code' => '045600000'],
            ['region_id' => 6, 'code' => 'RIZ', 'name' => 'Rizal', 'psgc_code' => '045800000'],

            // ==================== Region IV-B (Region 7) ====================
            ['region_id' => 7, 'code' => 'MAD', 'name' => 'Marinduque', 'psgc_code' => '174000000'],
            ['region_id' => 7, 'code' => 'MDC', 'name' => 'Occidental Mindoro', 'psgc_code' => '175100000'],
            ['region_id' => 7, 'code' => 'MDR', 'name' => 'Oriental Mindoro', 'psgc_code' => '175200000'],
            ['region_id' => 7, 'code' => 'PLW', 'name' => 'Palawan', 'psgc_code' => '175300000'],
            ['region_id' => 7, 'code' => 'ROM', 'name' => 'Romblon', 'psgc_code' => '175900000'],

            // ==================== Region V (Region 8) ====================
            ['region_id' => 8, 'code' => 'ALB', 'name' => 'Albay', 'psgc_code' => '050500000'],
            ['region_id' => 8, 'code' => 'CAN', 'name' => 'Camarines Norte', 'psgc_code' => '051600000'],
            ['region_id' => 8, 'code' => 'CAS', 'name' => 'Camarines Sur', 'psgc_code' => '051700000'],
            ['region_id' => 8, 'code' => 'CAT', 'name' => 'Catanduanes', 'psgc_code' => '052000000'],
            ['region_id' => 8, 'code' => 'MAS', 'name' => 'Masbate', 'psgc_code' => '054100000'],
            ['region_id' => 8, 'code' => 'SOR', 'name' => 'Sorsogon', 'psgc_code' => '056200000'],

            // ==================== Region VI (Region 9) ====================
            ['region_id' => 9, 'code' => 'AKL', 'name' => 'Aklan', 'psgc_code' => '060400000'],
            ['region_id' => 9, 'code' => 'ANT', 'name' => 'Antique', 'psgc_code' => '060600000'],
            ['region_id' => 9, 'code' => 'CAP', 'name' => 'Capiz', 'psgc_code' => '061900000'],
            ['region_id' => 9, 'code' => 'GUI', 'name' => 'Guimaras', 'psgc_code' => '067900000'],
            ['region_id' => 9, 'code' => 'ILI', 'name' => 'Iloilo', 'psgc_code' => '063000000'],
            ['region_id' => 9, 'code' => 'NEC', 'name' => 'Negros Occidental', 'psgc_code' => '064500000'],

            // ==================== Region VII (Region 10) ====================
            ['region_id' => 10, 'code' => 'BOH', 'name' => 'Bohol', 'psgc_code' => '071200000'],
            ['region_id' => 10, 'code' => 'CEB', 'name' => 'Cebu', 'psgc_code' => '072200000'],
            ['region_id' => 10, 'code' => 'NER', 'name' => 'Negros Oriental', 'psgc_code' => '074600000'],
            ['region_id' => 10, 'code' => 'SIG', 'name' => 'Siquijor', 'psgc_code' => '076100000'],

            // ==================== Region VIII (Region 11) ====================
            ['region_id' => 11, 'code' => 'BIL', 'name' => 'Biliran', 'psgc_code' => '087800000'],
            ['region_id' => 11, 'code' => 'EAS', 'name' => 'Eastern Samar', 'psgc_code' => '082600000'],
            ['region_id' => 11, 'code' => 'LEY', 'name' => 'Leyte', 'psgc_code' => '083700000'],
            ['region_id' => 11, 'code' => 'NSA', 'name' => 'Northern Samar', 'psgc_code' => '084800000'],
            ['region_id' => 11, 'code' => 'WSA', 'name' => 'Western Samar', 'psgc_code' => '086000000'],
            ['region_id' => 11, 'code' => 'SLE', 'name' => 'Southern Leyte', 'psgc_code' => '086400000'],

            // ==================== Region IX (Region 12) ====================
            ['region_id' => 12, 'code' => 'ZAN', 'name' => 'Zamboanga del Norte', 'psgc_code' => '097200000'],
            ['region_id' => 12, 'code' => 'ZAS', 'name' => 'Zamboanga del Sur', 'psgc_code' => '097300000'],
            ['region_id' => 12, 'code' => 'ZSI', 'name' => 'Zamboanga Sibugay', 'psgc_code' => '098300000'],

            // ==================== Region X (Region 13) ====================
            ['region_id' => 13, 'code' => 'BUK', 'name' => 'Bukidnon', 'psgc_code' => '101300000'],
            ['region_id' => 13, 'code' => 'CAM', 'name' => 'Camiguin', 'psgc_code' => '101800000'],
            ['region_id' => 13, 'code' => 'LAN', 'name' => 'Lanao del Norte', 'psgc_code' => '103500000'],
            ['region_id' => 13, 'code' => 'MSC', 'name' => 'Misamis Occidental', 'psgc_code' => '104200000'],
            ['region_id' => 13, 'code' => 'MSR', 'name' => 'Misamis Oriental', 'psgc_code' => '104300000'],

            // ==================== Region XI (Region 14) ====================
            ['region_id' => 14, 'code' => 'COM', 'name' => 'Davao de Oro', 'psgc_code' => '112300000'],
            ['region_id' => 14, 'code' => 'DAV', 'name' => 'Davao del Norte', 'psgc_code' => '112400000'],
            ['region_id' => 14, 'code' => 'DAS', 'name' => 'Davao del Sur', 'psgc_code' => '112500000'],
            ['region_id' => 14, 'code' => 'DAO', 'name' => 'Davao Oriental', 'psgc_code' => '112600000'],
            ['region_id' => 14, 'code' => 'DVO', 'name' => 'Davao Occidental', 'psgc_code' => '118200000'],

            // ==================== Region XII (Region 15) ====================
            ['region_id' => 15, 'code' => 'NCO', 'name' => 'Cotabato', 'psgc_code' => '124700000'],
            ['region_id' => 15, 'code' => 'SAR', 'name' => 'Sarangani', 'psgc_code' => '126500000'],
            ['region_id' => 15, 'code' => 'SCO', 'name' => 'South Cotabato', 'psgc_code' => '126300000'],
            ['region_id' => 15, 'code' => 'SUK', 'name' => 'Sultan Kudarat', 'psgc_code' => '128000000'],

            // ==================== Region XIII (Region 16) ====================
            ['region_id' => 16, 'code' => 'AGN', 'name' => 'Agusan del Norte', 'psgc_code' => '160200000'],
            ['region_id' => 16, 'code' => 'AGS', 'name' => 'Agusan del Sur', 'psgc_code' => '160300000'],
            ['region_id' => 16, 'code' => 'DIN', 'name' => 'Dinagat Islands', 'psgc_code' => '168500000'],
            ['region_id' => 16, 'code' => 'SUN', 'name' => 'Surigao del Norte', 'psgc_code' => '166700000'],
            ['region_id' => 16, 'code' => 'SUR', 'name' => 'Surigao del Sur', 'psgc_code' => '166800000'],

            // ==================== BARMM (Region 17) ====================
            ['region_id' => 17, 'code' => 'BAS', 'name' => 'Basilan', 'psgc_code' => '150700000'],
            ['region_id' => 17, 'code' => 'LAS', 'name' => 'Lanao del Sur', 'psgc_code' => '153600000'],
            ['region_id' => 17, 'code' => 'MAG', 'name' => 'Maguindanao', 'psgc_code' => '153800000'],
            ['region_id' => 17, 'code' => 'SLU', 'name' => 'Sulu', 'psgc_code' => '156600000'],
            ['region_id' => 17, 'code' => 'TAW', 'name' => 'Tawi-Tawi', 'psgc_code' => '157000000'],
        ];

        foreach ($provinces as $province) {
            $authDb->table('provinces')->insertOrIgnore($province);
        }

        $this->command->info('✓ 81 Philippine Provinces seeded successfully!');
    }
}
