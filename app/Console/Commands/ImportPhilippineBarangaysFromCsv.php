<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportPhilippineBarangaysFromCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:barangays 
                            {file : Path to CSV file} 
                            {--clear : Clear existing barangays before import}
                            {--batch=1000 : Number of records to insert per batch}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import all Philippine barangays with zip codes from CSV file';

    /**
     * Execute the console command.
     * 
     * CSV Format Required:
     * region_code,region_name,province_code,province_name,city_code,city_name,
     * district_number,district_name,barangay_name,barangay_alternate_name,zip_code
     */
    public function handle()
    {
        $filePath = $this->argument('file');
        $batchSize = (int) $this->option('batch');
        $shouldClear = $this->option('clear');

        // Validate file exists
        if (!File::exists($filePath)) {
            $this->error("❌ File not found: {$filePath}");
            return 1;
        }

        $this->info('');
        $this->info('🇵🇭 IMPORTING ALL PHILIPPINE BARANGAYS WITH ZIP CODES');
        $this->info(str_repeat('=', 70));
        $this->info('');

        // Clear existing barangays if flag is set
        if ($shouldClear) {
            $this->warn('⚠️  Clearing existing barangays...');
            DB::connection('auth_db')->table('barangays')->truncate();
            $this->info('✓ Barangays table cleared');
            $this->info('');
        }

        // Open CSV file
        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            $this->error('❌ Could not open CSV file');
            return 1;
        }

        // Skip header row
        $header = fgetcsv($handle);
        $this->info('📋 CSV Headers: ' . implode(', ', $header));
        $this->info('');

        $barangaysToInsert = [];
        $processedCount = 0;
        $skippedCount = 0;
        $errorCount = 0;
        $totalLines = 0;

        $this->info('📥 Processing CSV data...');
        $progressBar = $this->output->createProgressBar();
        $progressBar->start();

        // Process each row
        while (($row = fgetcsv($handle)) !== false) {
            $totalLines++;
            $progressBar->advance();

            try {
                // Parse CSV row (adjust indices based on your CSV structure)
                $regionCode = $row[0] ?? null;
                $regionName = $row[1] ?? null;
                $provinceCode = $row[2] ?? null;
                $provinceName = $row[3] ?? null;
                $cityCode = $row[4] ?? null;
                $cityName = $row[5] ?? null;
                $districtNumber = $row[6] ?? null;
                $districtName = $row[7] ?? null;
                $barangayName = $row[8] ?? null;
                $barangayAlternateName = $row[9] ?? null;
                $zipCode = $row[10] ?? null;

                // Validate required fields
                if (empty($cityCode) || empty($barangayName)) {
                    $skippedCount++;
                    continue;
                }

                // Get city_id from database
                $city = DB::connection('auth_db')
                    ->table('cities')
                    ->where('code', $cityCode)
                    ->first();

                if (!$city) {
                    $skippedCount++;
                    continue;
                }

                // Get district_id (if district exists)
                $districtId = null;
                if ($districtNumber) {
                    $district = DB::connection('auth_db')
                        ->table('districts')
                        ->where('city_id', $city->id)
                        ->where('district_number', $districtNumber)
                        ->first();
                    
                    $districtId = $district ? $district->id : null;
                }

                // Prepare barangay data
                $barangaysToInsert[] = [
                    'city_id' => $city->id,
                    'district_id' => $districtId,
                    'name' => trim($barangayName),
                    'alternate_name' => $barangayAlternateName ? trim($barangayAlternateName) : null,
                    'zip_code' => $zipCode ? trim($zipCode) : null,
                ];

                $processedCount++;

                // Insert in batches
                if (count($barangaysToInsert) >= $batchSize) {
                    DB::connection('auth_db')->table('barangays')->insert($barangaysToInsert);
                    $barangaysToInsert = [];
                }

            } catch (\Exception $e) {
                $errorCount++;
                $this->newLine();
                $this->error("Error on line {$totalLines}: " . $e->getMessage());
            }
        }

        // Insert remaining barangays
        if (count($barangaysToInsert) > 0) {
            DB::connection('auth_db')->table('barangays')->insert($barangaysToInsert);
        }

        $progressBar->finish();
        $this->newLine(2);

        fclose($handle);

        // Display summary
        $this->info(str_repeat('=', 70));
        $this->info('✅ IMPORT COMPLETE!');
        $this->info('');
        $this->info("📊 SUMMARY:");
        $this->info("   • Total Lines Processed: {$totalLines}");
        $this->info("   • Successfully Imported: {$processedCount} barangays");
        $this->info("   • Skipped: {$skippedCount}");
        $this->info("   • Errors: {$errorCount}");
        $this->info('');
        $this->info(str_repeat('=', 70));

        return 0;
    }
}
